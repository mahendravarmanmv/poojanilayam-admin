<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pooja;
use App\Models\PoojaCategory;
use App\Models\God;
use App\Models\PoojaDetail;
use App\Models\PoojaPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminPoojaCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Pooja::with(['category', 'gods']);

        if ($request->filled('category_id')) {
            $query->where('pooja_category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $poojas = $query->latest()->paginate(15)->withQueryString();
        $categories = PoojaCategory::all();

        return view('admin.poojas.index', compact('poojas', 'categories'));
    }

    public function create()
    {
        $categories = PoojaCategory::all();
        $gods = God::all();
        return view('admin.poojas.create', compact('categories', 'gods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pooja_category_id' => 'required|exists:pooja_categories,id',
            'name'              => 'required|string|max:255',
            'short_description' => 'required|string',
            'description'       => 'required|string',
            'base_price'        => 'required|numeric|min:0',
            'duration_minutes'  => 'required|integer|min:15',
            'god_ids'           => 'nullable|array',
            'god_ids.*'         => 'exists:gods,id',
            'is_active'         => 'boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $pooja = Pooja::create([
                'pooja_category_id' => $validated['pooja_category_id'],
                'name'              => $validated['name'],
                'slug'              => Str::slug($validated['name']),
                'short_description' => $validated['short_description'],
                'is_active'         => $validated['is_active'] ?? true,
            ]);

            PoojaDetail::create([
                'pooja_id'         => $pooja->id,
                'description'      => $validated['description'],
                'duration_minutes' => $validated['duration_minutes'],
            ]);

            PoojaPricing::create([
                'pooja_id'   => $pooja->id,
                'price'      => $validated['base_price'],
                'is_default' => true,
            ]);

            if (!empty($validated['god_ids'])) {
                $pooja->gods()->sync($validated['god_ids']);
            }
        });

        return redirect()->route('admin.poojas.index')->with('success', 'Pooja catalog item created.');
    }

    public function edit(Pooja $pooja)
    {
        $pooja->load(['details', 'pricing', 'gods']);
        $categories = PoojaCategory::all();
        $gods = God::all();

        return view('admin.poojas.edit', compact('pooja', 'categories', 'gods'));
    }

    public function update(Request $request, Pooja $pooja)
    {
        $validated = $request->validate([
            'pooja_category_id' => 'required|exists:pooja_categories,id',
            'name'              => 'required|string|max:255',
            'short_description' => 'required|string',
            'description'       => 'required|string',
            'base_price'        => 'required|numeric|min:0',
            'duration_minutes'  => 'required|integer|min:15',
            'god_ids'           => 'nullable|array',
            'god_ids.*'         => 'exists:gods,id',
            'is_active'         => 'boolean',
        ]);

        DB::transaction(function () use ($pooja, $validated) {
            $pooja->update([
                'pooja_category_id' => $validated['pooja_category_id'],
                'name'              => $validated['name'],
                'slug'              => Str::slug($validated['name']),
                'short_description' => $validated['short_description'],
                'is_active'         => $validated['is_active'] ?? true,
            ]);

            $pooja->details()->updateOrCreate(
                ['pooja_id' => $pooja->id],
                [
                    'description'      => $validated['description'],
                    'duration_minutes' => $validated['duration_minutes'],
                ]
            );

            $pooja->pricing()->where('is_default', true)->update([
                'price' => $validated['base_price'],
            ]);

            $pooja->gods()->sync($validated['god_ids'] ?? []);
        });

        return redirect()->route('admin.poojas.index')->with('success', 'Pooja catalog item updated.');
    }

    public function destroy(Pooja $pooja)
    {
        $pooja->delete();
        return redirect()->route('admin.poojas.index')->with('success', 'Pooja catalog item archived.');
    }
}