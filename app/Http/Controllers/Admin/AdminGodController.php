<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\God;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminGodController extends Controller
{
    /**
     * Display all deities.
     */
    public function index(Request $request)
    {
        $query = God::withCount('poojas');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $gods = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.gods.index', compact('gods'));
    }

    /**
     * Show create deity form.
     */
    public function create()
    {
        return view('admin.gods.create');
    }

    /**
     * Store a new deity.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:gods,name'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);

        $slug = $this->generateUniqueSlug($validated['name']);

        $godCode = 'GOD-' . strtoupper(substr(str_replace('-', '', $slug), 0, 10));

        $baseCode = $godCode;
        $counter = 1;

        while (God::withTrashed()->where('god_code', $godCode)->exists()) {
            $godCode = substr($baseCode, 0, 20) . '-' . $counter++;
        }

        God::create([
            'god_code' => $godCode,
            'name' => $validated['name'],
            'slug' => $slug,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.gods.index')
            ->with('success', 'Pooja deity created successfully.');
    }

    /**
     * Show edit deity form.
     */
    public function edit(God $god)
    {
        return view('admin.gods.edit', compact('god'));
    }

    /**
     * Update an existing deity.
     */
    public function update(Request $request, God $god)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('gods', 'name')->ignore($god->id),
            ],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);

        $slug = $this->generateUniqueSlug($validated['name'], $god->id);

        $god->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.gods.index')
            ->with('success', 'Pooja deity updated successfully.');
    }

    /**
     * Soft delete a deity if it is not associated with any Pooja.
     */
    public function destroy(God $god)
    {
        if ($god->poojas()->exists()) {
            return redirect()
                ->route('admin.gods.index')
                ->with(
                    'error',
                    'This deity cannot be deleted because it is associated with existing Poojas.'
                );
        }

        $god->delete();

        return redirect()
            ->route('admin.gods.index')
            ->with('success', 'Pooja deity deleted successfully.');
    }

    /**
     * Generate a unique slug.
     */
    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $originalSlug = Str::slug($name);
        $slug = $originalSlug;
        $counter = 1;

        while (
            God::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
}
