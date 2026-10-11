<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\ProductPrice;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'price', 'inventory']);

        if ($request->filled('category_id')) {
            $query->where('product_category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = ProductCategory::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductCategory::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
            'brand_id'            => 'nullable|exists:brands,id',
            'sku'                 => 'required|string|unique:products,sku|max:100',
            'price'               => 'required|numeric|min:0',
            'stock_quantity'      => 'required|integer|min:0',
            'description'         => 'required|string',
            'is_active'           => 'boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'product_category_id' => $validated['product_category_id'],
                'brand_id'            => $validated['brand_id'] ?? null,
                'name'                => $validated['name'],
                'slug'                => Str::slug($validated['name']),
                'sku'                 => $validated['sku'],
                'description'         => $validated['description'],
                'is_active'           => $validated['is_active'] ?? true,
            ]);

            ProductPrice::create([
                'product_id' => $product->id,
                'price'      => $validated['price'],
                'is_default' => true,
            ]);

            Inventory::create([
                'product_id'       => $product->id,
                'quantity_on_hand' => $validated['stock_quantity'],
            ]);
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load(['price', 'inventory']);
        $categories = ProductCategory::all();
        $brands = Brand::all();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
            'brand_id'            => 'nullable|exists:brands,id',
            'sku'                 => 'required|string|max:100|unique:products,sku,' . $product->id,
            'price'               => 'required|numeric|min:0',
            'stock_quantity'      => 'required|integer|min:0',
            'description'         => 'required|string',
            'is_active'           => 'boolean',
        ]);

        DB::transaction(function () use ($product, $validated) {
            $product->update([
                'product_category_id' => $validated['product_category_id'],
                'brand_id'            => $validated['brand_id'] ?? null,
                'name'                => $validated['name'],
                'slug'                => Str::slug($validated['name']),
                'sku'                 => $validated['sku'],
                'description'         => $validated['description'],
                'is_active'           => $validated['is_active'] ?? true,
            ]);

            $product->price()->where('is_default', true)->update([
                'price' => $validated['price'],
            ]);

            $product->inventory()->updateOrCreate(
                ['product_id' => $product->id],
                ['quantity_on_hand' => $validated['stock_quantity']]
            );
        });

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product archived.');
    }
}