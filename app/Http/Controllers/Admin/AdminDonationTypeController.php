<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AdminDonationTypeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $query = DonationType::query()
            ->withCount(['campaigns', 'donations'])
            ->orderBy('sort_order')
            ->orderBy('name');

        if (!empty($filters['q'])) {
            $term = trim($filters['q']);
            $query->where(function ($builder) use ($term) {
                $builder->where('name', 'like', "%{$term}%")
                    ->orWhere('type_code', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $donationTypes = $query->paginate(15)->withQueryString();

        return view('admin.donation-types.index', compact('donationTypes'));
    }

    public function create(): View
    {
        return view('admin.donation-types.create', [
            'donationType' => new DonationType(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        $validated['type_code'] = strtoupper(trim($validated['type_code']));
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
		
		$imagePath = $this->saveImage($request->file('image'));

		if ($imagePath) {
		$validated['image_path'] = $imagePath;
		}

		try {
		unset($validated['image']);

		DonationType::create($validated);
		} catch (\Throwable $e) {
		$this->removeSavedImage($imagePath);
		throw $e;
		}

        return redirect()->route('admin.donation-types.index')
            ->with('success', 'Donation type created successfully.');
    }

    public function edit(DonationType $donationType): View
    {
        return view('admin.donation-types.edit', compact('donationType'));
    }

    public function update(Request $request, DonationType $donationType): RedirectResponse
    {
        $validated = $this->validateData($request, $donationType);

        $validated['type_code'] = strtoupper(trim($validated['type_code']));
		$validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);

		$newImagePath = $this->saveImage($request->file('image'));
		$oldImagePath = $donationType->image_path;

		if ($newImagePath) {
		$validated['image_path'] = $newImagePath;
		}

		try {
		unset($validated['image']);

		$donationType->update($validated);
		} catch (\Throwable $e) {
		$this->removeSavedImage($newImagePath);
		throw $e;
		}

		// Remove the previous image only after successful update.
		if ($newImagePath && $oldImagePath) {
		$this->removeSavedImage($oldImagePath);
		}

        return redirect()->route('admin.donation-types.index')
            ->with('success', 'Donation type updated successfully.');
    }

    public function destroy(DonationType $donationType): RedirectResponse
    {
        $campaignCount = $donationType->campaigns()->withTrashed()->count();
        $donationCount = $donationType->donations()->withTrashed()->count();

        if ($campaignCount > 0 || $donationCount > 0) {
            return redirect()->route('admin.donation-types.index')
                ->with('error', 'This donation type is linked to campaigns or donation records. Deactivate it instead to preserve history.');
        }

        $donationType->delete();

        return redirect()->route('admin.donation-types.index')
            ->with('success', 'Donation type deleted successfully.');
    }

    private function validateData(Request $request, ?DonationType $donationType = null): array
    {
        $typeCodeRule = Rule::unique('donation_types', 'type_code');
        $slugRule = Rule::unique('donation_types', 'slug');

        if ($donationType) {
            $typeCodeRule->ignore($donationType->id);
            $slugRule->ignore($donationType->id);
        }

        return $request->validate([
            'type_code' => ['required', 'string', 'max:50', $typeCodeRule],
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', $slugRule],
            'description' => ['nullable', 'string', 'max:5000'],
			'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'sort_order' => ['required', 'integer', 'min:0', 'max:4294967295'],
        ]);
    }
	
	private function saveImage(?UploadedFile $image): ?string
	{
	if (!$image) {
		return null;
	}

	$directory = $this->frontendPublicPath()
		. DIRECTORY_SEPARATOR . 'images'
		. DIRECTORY_SEPARATOR . 'donation-types';

	if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
		throw new \RuntimeException('Unable to create the donation type image directory.');
	}

	$filename = Str::uuid()->toString() . '.' . strtolower($image->extension());

	$image->move($directory, $filename);

	return 'images/donation-types/' . $filename;
	}
	
	private function removeSavedImage(?string $path): void
	{
	if (!$path || dirname($path) !== 'images/donation-types') {
		return;
	}

	$pathBasename = basename($path);

	if ($pathBasename === '.' || $pathBasename === '..' || $pathBasename === '') {
		return;
	}

	$fullPath = $this->frontendPublicPath()
		. DIRECTORY_SEPARATOR . 'images'
		. DIRECTORY_SEPARATOR . 'donation-types'
		. DIRECTORY_SEPARATOR . $pathBasename;

	if (is_file($fullPath)) {
		@unlink($fullPath);
	}
	}
	
	private function frontendPublicPath(): string
	{
	$configured = config('store.frontend_public_path');

	if (!$configured) {
		return public_path();
	}

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
}
