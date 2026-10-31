<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PujariProfile;
use App\Models\PujariService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminPujariServiceController extends Controller
{
    public function index(PujariProfile $pujari): View
    {
        $services = $pujari->services()
            ->orderBy('sort_order')
            ->orderBy('service_name')
            ->paginate(15);

        return view(
            'admin.verifications.pujaris.services.index',
            compact('pujari', 'services')
        );
    }

    public function create(PujariProfile $pujari): View
    {
        return view(
            'admin.verifications.pujaris.services.create',
            compact('pujari')
        );
    }

    public function store(Request $request, PujariProfile $pujari): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('pujari_services', 'service_name')
                    ->where('pujari_profile_id', $pujari->id),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
			'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $pujari->services()->create([
            'service_name' => $validated['service_name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
			'duration_minutes' => $validated['duration_minutes'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.verifications.pujaris.services.index', $pujari->id)
            ->with('success', 'Priest service added successfully.');
    }

    public function edit(
        PujariProfile $pujari,
        PujariService $service
    ): View {
        $service = $pujari->services()->findOrFail($service->id);

        return view(
            'admin.verifications.pujaris.services.edit',
            compact('pujari', 'service')
        );
    }

    public function update(
        Request $request,
        PujariProfile $pujari,
        PujariService $service
    ): RedirectResponse {
        $service = $pujari->services()->findOrFail($service->id);

        $validated = $request->validate([
            'service_name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('pujari_services', 'service_name')
                    ->where('pujari_profile_id', $pujari->id)
                    ->ignore($service->id),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
			'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $service->update([
            'service_name' => $validated['service_name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
			'duration_minutes' => $validated['duration_minutes'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.verifications.pujaris.services.index', $pujari->id)
            ->with('success', 'Priest service updated successfully.');
    }

    public function destroy(
        PujariProfile $pujari,
        PujariService $service
    ): RedirectResponse {
        $service = $pujari->services()->findOrFail($service->id);

        $service->delete();

        return redirect()
            ->route('admin.verifications.pujaris.services.index', $pujari->id)
            ->with('success', 'Priest service deleted successfully.');
    }

    public function toggleStatus(
        PujariProfile $pujari,
        PujariService $service
    ): RedirectResponse {
        $service = $pujari->services()->findOrFail($service->id);

        $service->update([
            'is_active' => ! $service->is_active,
        ]);

        return back()->with('success', 'Service status updated successfully.');
    }
}
