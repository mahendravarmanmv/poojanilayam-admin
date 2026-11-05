<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdminTaxController extends Controller
{
    /**
     * Available modules for tax assignment.
     */
    private function availableModules(): array
    {
        return [
            'priests' => 'Priests',
            'poojas' => 'Poojas',
            'donations' => 'Donations',
            'products' => 'Products',
        ];
    }

    /**
     * Display all taxes.
     */
    public function index()
    {
        $taxes = Tax::with('modules')
            ->latest()
            ->paginate(10);

        return view('admin.taxes.index', compact('taxes'));
    }

    /**
     * Show create tax form.
     */
    public function create()
    {
        $modules = $this->availableModules();

        return view('admin.taxes.create', compact('modules'));
    }

    /**
     * Store a new tax.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:taxes,code'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'modules' => ['nullable', 'array'],
            'modules.*' => ['required', Rule::in(array_keys($this->availableModules()))],
        ]);

        DB::transaction(function () use ($validated) {
            $tax = Tax::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'percentage' => $validated['percentage'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            foreach ($validated['modules'] ?? [] as $module) {
                $tax->modules()->create([
                    'module' => $module,
                    'is_enabled' => true,
                ]);
            }
        });

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Tax created successfully.');
    }

    /**
     * Show tax details.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show edit tax form.
     */
    public function edit(string $id)
    {
        $tax = Tax::with('modules')->findOrFail($id);
        $modules = $this->availableModules();

        $assignedModules = $tax->modules
            ->pluck('is_enabled', 'module')
            ->toArray();

        return view('admin.taxes.edit', compact(
            'tax',
            'modules',
            'assignedModules'
        ));
    }

    /**
     * Update tax and module assignments.
     */
    public function update(Request $request, string $id)
    {
        $tax = Tax::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('taxes', 'code')->ignore($tax->id),
            ],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'modules' => ['nullable', 'array'],
            'modules.*' => ['required', Rule::in(array_keys($this->availableModules()))],
            'module_enabled' => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($tax, $validated, $request) {
            $tax->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'percentage' => $validated['percentage'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            $selectedModules = $validated['modules'] ?? [];

            $tax->modules()
                ->whereNotIn('module', $selectedModules ?: [''])
                ->delete();

            foreach ($selectedModules as $module) {
                $tax->modules()->updateOrCreate(
                    ['module' => $module],
                    [
                        'is_enabled' => in_array(
                            $module,
                            $request->input('module_enabled', [])
                        ),
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Tax updated successfully.');
    }

    /**
     * Delete tax.
     */
    public function destroy(string $id)
    {
        $tax = Tax::findOrFail($id);
        $tax->delete();

        return redirect()
            ->route('admin.taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }
}
