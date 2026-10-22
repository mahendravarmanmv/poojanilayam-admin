<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Inventory;
use App\Models\InventoryLocation;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'prices' => fn ($q) => $q->where('is_default', true)->where('active', true), 'inventories' => fn ($q) => $q->where('active', true), 'images' => fn ($q) => $q->where('active', true)->orderBy('sort_order')])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = trim((string) $request->input('search'));
                $q->where(fn ($sub) => $sub->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%"));
            })
            ->when($request->filled('category_id'), fn ($q) => $q->where('product_category_id', $request->integer('category_id')))
            ->when($request->input('status') === 'active', fn ($q) => $q->where('active', true))
            ->when($request->input('status') === 'inactive', fn ($q) => $q->where('active', false))
            ->latest('id')->paginate(15)->withQueryString();

        $categories = ProductCategory::query()->where('active', true)->orderBy('name')->get();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductCategory::query()->where('active', true)->orderBy('name')->get();
        return view('admin.products.create', ['product' => new Product(), 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $currency = $this->storeCurrency();
        $imagePath = $this->saveImage($request->file('image'));

        try {
            DB::transaction(function () use ($data, $currency, $imagePath) {
                $product = Product::create($this->productAttributes($data));
                $product->prices()->create([
                    'currency_id' => $currency->id, 'pricing_type' => 'regular',
                    'amount' => $data['price'], 'compare_at_amount' => $data['compare_at_amount'] ?? null,
                    'is_default' => true, 'active' => true,
                ]);
                $this->saveInventory($product, (int) $data['stock_quantity']);
                if ($imagePath) {
                    $product->images()->create(['media_type' => 'image', 'file_path' => $imagePath, 'alt_text' => $data['name'], 'title' => $data['name'], 'featured' => true, 'active' => true, 'sort_order' => 0]);
                }
            });
        } catch (\Throwable $e) {
            $this->removeSavedImage($imagePath);
            throw $e;
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully. It will appear on the Store when Active is enabled.');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'prices' => fn ($q) => $q->where('is_default', true)->where('active', true), 'inventories' => fn ($q) => $q->where('active', true), 'images' => fn ($q) => $q->where('active', true)->orderBy('sort_order')]);
        $categories = ProductCategory::query()->where('active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validatedData($request, $product);
        $currency = $this->storeCurrency();
        $newImagePath = $this->saveImage($request->file('image'));
        $oldImagePath = null;

        try {
            DB::transaction(function () use ($data, $product, $currency, $newImagePath, &$oldImagePath) {
                $product->update($this->productAttributes($data, $product));

                $price = $product->prices()->where('is_default', true)->first();
                if ($price) {
                    $price->update(['currency_id' => $currency->id, 'amount' => $data['price'], 'compare_at_amount' => $data['compare_at_amount'] ?? null, 'active' => true]);
                } else {
                    $product->prices()->create(['currency_id' => $currency->id, 'pricing_type' => 'regular', 'amount' => $data['price'], 'compare_at_amount' => $data['compare_at_amount'] ?? null, 'is_default' => true, 'active' => true]);
                }

                $this->saveInventory($product, (int) $data['stock_quantity']);

                if ($newImagePath) {
                    $oldImages = $product->images()->where('active', true)->get();
                    foreach ($oldImages as $image) {
                        if ($oldImagePath === null) $oldImagePath = $image->file_path;
                        $image->delete();
                    }
                    $product->images()->create(['media_type' => 'image', 'file_path' => $newImagePath, 'alt_text' => $data['name'], 'title' => $data['name'], 'featured' => true, 'active' => true, 'sort_order' => 0]);
                }
            });
        } catch (\Throwable $e) {
            $this->removeSavedImage($newImagePath);
            throw $e;
        }

        if ($newImagePath && $oldImagePath) $this->removeSavedImage($oldImagePath);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Soft-delete keeps historical order/cart references intact while hiding it from the storefront.
        $product->update(['active' => false]);
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product archived and removed from the Store.');
    }

    private function validatedData(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'product_category_id' => ['required', 'integer', Rule::exists('product_categories', 'id')->where('active', true)],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product?->id)],
            'product_code' => ['nullable', 'string', 'max:50', Rule::unique('products', 'product_code')->ignore($product?->id)],
            'brand' => ['nullable', 'string', 'max:191'],
            'product_type' => ['nullable', 'string', 'max:50'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'unit' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:9999999.999'],
            'weight_unit' => ['nullable', 'string', 'max:20'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
            'compare_at_amount' => ['nullable', 'numeric', 'gte:price', 'max:999999999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'featured' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
            'is_digital' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function productAttributes(array $data, ?Product $product = null): array
    {
        $name = trim($data['name']);
        $slugBase = Str::slug($name) ?: 'product';
        $slug = $slugBase;
        $suffix = 2;
        while (Product::withTrashed()->where('slug', $slug)->when($product, fn ($q) => $q->where('id', '!=', $product->id))->exists()) $slug = $slugBase . '-' . $suffix++;

        return [
            'product_category_id' => $data['product_category_id'],
            'product_code' => trim((string) ($data['product_code'] ?? '')) !== ''
                ? trim((string) $data['product_code'])
                : ($product?->product_code ?: $this->uniqueProductCode()),
            'sku' => trim($data['sku']), 'name' => $name, 'slug' => $slug,
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'product_type' => $data['product_type'] ?? null, 'brand' => $data['brand'] ?? null,
            'unit' => $data['unit'] ?? null, 'weight' => $data['weight'] ?? null,
            'weight_unit' => $data['weight_unit'] ?? null,
            'is_digital' => (bool) ($data['is_digital'] ?? false),
            'featured' => (bool) ($data['featured'] ?? false), 'active' => (bool) ($data['active'] ?? false),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function storeCurrency(): Currency
    {
        $currency = Currency::query()->where('code', 'INR')->where('is_active', true)->first();
        if (!$currency) {
            throw ValidationException::withMessages(['price' => 'An active INR currency is required. Please enable INR in the currency settings before saving products.']);
        }
        return $currency;
    }

    private function saveInventory(Product $product, int $quantity): void
    {
        $location = InventoryLocation::query()->where('active', true)->orderByRaw("CASE WHEN location_code = 'STORE-MAIN' THEN 0 ELSE 1 END")->first();
        if (!$location) {
            $location = InventoryLocation::create(['location_code' => 'STORE-MAIN', 'name' => 'Pooja Nilayam Main Store', 'location_type' => 'store', 'country' => 'India', 'active' => true]);
        }
        $inventory = Inventory::query()->firstOrNew(['product_id' => $product->id, 'inventory_location_id' => $location->id]);
        $inventory->quantity_on_hand = $quantity;
        $inventory->quantity_available = max(0, $quantity - (int) $inventory->quantity_reserved);
        $inventory->track_inventory = true;
        $inventory->active = true;
        $inventory->save();
    }

    private function saveImage(?UploadedFile $image): ?string
    {
        if (!$image) return null;
        $directory = $this->frontendPublicPath() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'products';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException('Unable to create the product image directory.');
        }
        $filename = Str::uuid()->toString() . '.' . strtolower($image->extension());
        $image->move($directory, $filename);
        return 'images/products/' . $filename;
    }

    private function removeSavedImage(?string $path): void
    {
        if (!$path || dirname($path) !== 'images/products') return;
        $pathBasename = basename($path);
        if ($pathBasename === '.' || $pathBasename === '..' || $pathBasename === '') return;
        $fullPath = $this->frontendPublicPath() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $pathBasename;
        if (is_file($fullPath)) @unlink($fullPath);
    }

    private function frontendPublicPath(): string
    {
        $configured = config('store.frontend_public_path');
        if (!$configured) return public_path();

        // Relative paths are resolved from the admin Laravel project root.
        $isWindowsAbsolutePath = strlen($configured) >= 3
            && ctype_alpha($configured[0])
            && $configured[1] === ':'
            && ($configured[2] === '\\' || $configured[2] === '/');

        $resolved = str_starts_with($configured, DIRECTORY_SEPARATOR) || $isWindowsAbsolutePath
            ? $configured
            : base_path($configured);

        return rtrim($resolved, DIRECTORY_SEPARATOR);
    }

    private function uniqueProductCode(): string
    {
        do { $code = 'PN-' . Str::upper(Str::random(10)); }
        while (Product::withTrashed()->where('product_code', $code)->exists());
        return $code;
    }
}
