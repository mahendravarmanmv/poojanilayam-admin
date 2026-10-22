<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::query()
            ->with('parent')
            ->withCount('products')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = trim((string) $request->input('search'));
                $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('category_code', 'like', "%{$term}%"));
            })
            ->orderBy('sort_order')->orderBy('name')->paginate(15)->withQueryString();
        return view('admin.product-categories.index', compact('categories'));
    }

    public function create()
    {
        $category = new ProductCategory();
        $parents = ProductCategory::query()->whereNull('parent_id')->where('active', true)->orderBy('name')->get();
        return view('admin.product-categories.create', compact('category', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCategory($request);
        $data['category_code'] = $data['category_code'] ?: $this->uniqueCode();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['name']);
        ProductCategory::create($data);
        return redirect()->route('admin.product-categories.index')->with('success', 'Product category created.');
    }

    public function edit(ProductCategory $product_category)
    {
        $category = $product_category;
        $parents = ProductCategory::query()->whereNull('parent_id')->where('active', true)->whereKeyNot($category->id)->orderBy('name')->get();
        return view('admin.product-categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, ProductCategory $product_category)
    {
        $category = $product_category;
        $data = $this->validateCategory($request, $category);
        $data['category_code'] = $data['category_code'] ?: $category->category_code;
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['name'], $category->id);
        if (!empty($data['parent_id']) && (int) $data['parent_id'] === (int) $category->id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.'])->withInput();
        }
        $category->update($data);
        return redirect()->route('admin.product-categories.index')->with('success', 'Product category updated.');
    }

    public function destroy(ProductCategory $product_category)
    {
        if ($product_category->products()->exists() || $product_category->children()->exists()) {
            return back()->withErrors(['category' => 'This category contains products or subcategories. Move them first, then archive the category.']);
        }
        $product_category->update(['active' => false]);
        $product_category->delete();
        return redirect()->route('admin.product-categories.index')->with('success', 'Product category archived.');
    }

    private function validateCategory(Request $request, ?ProductCategory $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'category_code' => ['nullable', 'string', 'max:50', Rule::unique('product_categories', 'category_code')->ignore($category?->id)],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('product_categories', 'slug')->ignore($category?->id)],
            'parent_id' => ['nullable', 'integer', Rule::exists('product_categories', 'id')->where('active', true)],
            'description' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'featured' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
        ]);
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'category';
        $slug = $base;
        $i = 2;
        while (ProductCategory::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) $slug = $base . '-' . $i++;
        return $slug;
    }

    private function uniqueCode(): string
    {
        do { $code = 'CAT-' . Str::upper(Str::random(8)); }
        while (ProductCategory::withTrashed()->where('category_code', $code)->exists());
        return $code;
    }
}
