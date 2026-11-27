<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PoojaCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminPoojaCategoryController extends Controller
{
    /**
     * Display all Pooja categories.
     */
    public function index(Request $request)
    {
        $query = PoojaCategory::withCount('poojas');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pooja-categories.index', compact('categories'));
    }

    /**
     * Show create category form.
     */
    public function create()
    {
        return view('admin.pooja-categories.create');
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:pooja_categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (PoojaCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $codeBase = 'CAT-' . strtoupper(str_replace('-', '_', $slug));
        $categoryCode = substr($codeBase, 0, 30);

        while (PoojaCategory::where('category_code', $categoryCode)->exists()) {
            $categoryCode = substr($codeBase, 0, 23) . '_' . rand(100, 999);
        }

        PoojaCategory::create([
            'category_code' => $categoryCode,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.pooja-categories.index')
            ->with('success', 'Pooja category created successfully.');
    }

    /**
     * Show edit category form.
     */
    public function edit(PoojaCategory $pooja_category)
    {
        return view('admin.pooja-categories.edit', [
            'category' => $pooja_category
        ]);
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, PoojaCategory $pooja_category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pooja_categories', 'name')->ignore($pooja_category->id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (
            PoojaCategory::where('slug', $slug)
                ->where('id', '!=', $pooja_category->id)
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        $pooja_category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.pooja-categories.index')
            ->with('success', 'Pooja category updated successfully.');
    }

    /**
     * Delete a category if it is not assigned to any Pooja.
     */
    
	public function destroy(PoojaCategory $pooja_category)
	{
		// Check only non-deleted Poojas
		$hasPoojas = $pooja_category->poojas()->exists();

		if ($hasPoojas) {
			return redirect()
				->route('admin.pooja-categories.index')
				->with('error', 'This category cannot be deleted because it is associated with existing Poojas.');
		}

		$pooja_category->delete();

		return redirect()
			->route('admin.pooja-categories.index')
			->with('success', 'Pooja category deleted successfully.');
	}

}
