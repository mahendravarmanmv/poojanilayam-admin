<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pooja;
use App\Models\PoojaCategory;
use App\Models\PoojaDetail;
use App\Models\PoojaPricing;
use App\Models\PoojaMedia;
use App\Http\Requests\Admin\StorePoojaRequest;
use App\Http\Requests\Admin\UpdatePoojaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminPoojaCatalogController extends Controller
{
    protected string $uploadPath;

    public function __construct()
    {
        // Destination mapped directly to the shared public path
        $this->uploadPath = base_path('../public/images/poojas');
    }

    public function index(Request $request)
    {
        $categories = PoojaCategory::orderBy('name')->get();

        $query = Pooja::with(['category', 'pricings', 'primaryMedia'])
            ->withCount('pricings');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('filter_type')) {
            if ($request->input('filter_type') === 'popular') {
                $query->where('is_popular', true);
            } elseif ($request->input('filter_type') === 'temple') {
                $query->where('is_temple_pooja', true);
            }
        }

        $poojas = $query->latest('id')->paginate(15)->withQueryString();

        return view('admin.poojas.index', compact('poojas', 'categories'));
    }

    public function create()
    {
        $categories = PoojaCategory::where('status', 'active')->orderBy('name')->get();
        return view('admin.poojas.create', compact('categories'));
    }

    public function store(StorePoojaRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            $slug = Str::slug($validated['name']);
            $count = Pooja::where('slug', 'like', "{$slug}%")->count();
            if ($count > 0) {
                $slug = "{$slug}-" . ($count + 1);
            }

            // 1. Create Base Pooja
            $pooja = Pooja::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'short_description' => $validated['short_description'] ?? null,
                'status' => $validated['status'],
                'is_popular' => $request->boolean('is_popular'),
                'is_temple_pooja' => $request->boolean('is_temple_pooja'),
            ]);

            // 2. Create Pooja Details
            $cleanedSamagri = array_values(array_filter($validated['samagri_list'] ?? []));
            PoojaDetail::create([
                'pooja_id' => $pooja->id,
                'significance' => $validated['significance'] ?? null,
                'procedure' => $validated['procedure'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'duration_minutes' => $validated['duration_minutes'] ?? null,
                'samagri_list' => !empty($cleanedSamagri) ? $cleanedSamagri : null,
            ]);

            // 3. Create Pooja Pricing Tiers
            foreach ($validated['pricings'] as $index => $pricingData) {
                PoojaPricing::create([
                    'pooja_id' => $pooja->id,
                    'title' => $pricingData['title'],
                    'price' => $pricingData['price'],
                    'special_price' => $pricingData['special_price'] ?? null,
                    'description' => $pricingData['description'] ?? null,
                    'includes_samagri' => isset($pricingData['includes_samagri']),
                    'is_default' => $index === 0,
                ]);
            }

            // 4. Handle Media Uploads to shared path
            $this->ensureUploadDirectoryExists();

            if ($request->hasFile('primary_image')) {
                $primaryFile = $request->file('primary_image');
                $filename = 'pooja_' . $pooja->id . '_primary_' . time() . '.' . $primaryFile->getClientOriginalExtension();
                $primaryFile->move($this->uploadPath, $filename);

                PoojaMedia::create([
                    'pooja_id' => $pooja->id,
                    'file_name' => $filename,
                    'file_path' => 'images/poojas/' . $filename,
                    'media_type' => 'image',
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $i => $galleryFile) {
                    $filename = 'pooja_' . $pooja->id . '_gal_' . time() . '_' . $i . '.' . $galleryFile->getClientOriginalExtension();
                    $galleryFile->move($this->uploadPath, $filename);

                    PoojaMedia::create([
                        'pooja_id' => $pooja->id,
                        'file_name' => $filename,
                        'file_path' => 'images/poojas/' . $filename,
                        'media_type' => 'image',
                        'is_primary' => false,
                        'sort_order' => $i + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.poojas.index')
            ->with('success', 'Pooja catalog item created successfully.');
    }

    public function edit(Pooja $pooja)
    {
        $pooja->load(['category', 'details', 'pricings', 'media']);
        $categories = PoojaCategory::where('status', 'active')->orderBy('name')->get();

        return view('admin.poojas.edit', compact('pooja', 'categories'));
    }

    public function update(UpdatePoojaRequest $request, Pooja $pooja)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $pooja) {
            // 1. Update Core Attributes
            $pooja->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'short_description' => $validated['short_description'] ?? null,
                'status' => $validated['status'],
                'is_popular' => $request->boolean('is_popular'),
                'is_temple_pooja' => $request->boolean('is_temple_pooja'),
            ]);

            // 2. Update Details
            $cleanedSamagri = array_values(array_filter($validated['samagri_list'] ?? []));
            $pooja->details()->updateOrCreate(
                ['pooja_id' => $pooja->id],
                [
                    'significance' => $validated['significance'] ?? null,
                    'procedure' => $validated['procedure'] ?? null,
                    'instructions' => $validated['instructions'] ?? null,
                    'duration_minutes' => $validated['duration_minutes'] ?? null,
                    'samagri_list' => !empty($cleanedSamagri) ? $cleanedSamagri : null,
                ]
            );

            // 3. Sync Pricing
            $currentPricingIds = [];
            foreach ($validated['pricings'] as $index => $pricingData) {
                if (!empty($pricingData['id'])) {
                    $pricing = PoojaPricing::where('pooja_id', $pooja->id)->where('id', $pricingData['id'])->first();
                    if ($pricing) {
                        $pricing->update([
                            'title' => $pricingData['title'],
                            'price' => $pricingData['price'],
                            'special_price' => $pricingData['special_price'] ?? null,
                            'description' => $pricingData['description'] ?? null,
                            'includes_samagri' => isset($pricingData['includes_samagri']),
                            'is_default' => $index === 0,
                        ]);
                        $currentPricingIds[] = $pricing->id;
                    }
                } else {
                    $newPricing = PoojaPricing::create([
                        'pooja_id' => $pooja->id,
                        'title' => $pricingData['title'],
                        'price' => $pricingData['price'],
                        'special_price' => $pricingData['special_price'] ?? null,
                        'description' => $pricingData['description'] ?? null,
                        'includes_samagri' => isset($pricingData['includes_samagri']),
                        'is_default' => $index === 0,
                    ]);
                    $currentPricingIds[] = $newPricing->id;
                }
            }

            // Remove any dropped pricing rows
            $pooja->pricings()->whereNotIn('id', $currentPricingIds)->delete();

            // 4. Handle Deletion of flagged gallery media
            if (!empty($validated['delete_media_ids'])) {
                $mediaItemsToDelete = PoojaMedia::where('pooja_id', $pooja->id)
                    ->whereIn('id', $validated['delete_media_ids'])
                    ->get();

                foreach ($mediaItemsToDelete as $mediaItem) {
                    $fullPath = $this->uploadPath . '/' . $mediaItem->file_name;
                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }
                    $mediaItem->delete();
                }
            }

            // 5. Handle Uploads
            $this->ensureUploadDirectoryExists();

            if ($request->hasFile('primary_image')) {
                // Delete previous primary image
                $oldPrimary = PoojaMedia::where('pooja_id', $pooja->id)->where('is_primary', true)->first();
                if ($oldPrimary) {
                    $oldPath = $this->uploadPath . '/' . $oldPrimary->file_name;
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                    $oldPrimary->delete();
                }

                $primaryFile = $request->file('primary_image');
                $filename = 'pooja_' . $pooja->id . '_primary_' . time() . '.' . $primaryFile->getClientOriginalExtension();
                $primaryFile->move($this->uploadPath, $filename);

                PoojaMedia::create([
                    'pooja_id' => $pooja->id,
                    'file_name' => $filename,
                    'file_path' => 'images/poojas/' . $filename,
                    'media_type' => 'image',
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            if ($request->hasFile('gallery_images')) {
                $maxSortOrder = PoojaMedia::where('pooja_id', $pooja->id)->max('sort_order') ?? 0;
                foreach ($request->file('gallery_images') as $i => $galleryFile) {
                    $filename = 'pooja_' . $pooja->id . '_gal_' . time() . '_' . $i . '.' . $galleryFile->getClientOriginalExtension();
                    $galleryFile->move($this->uploadPath, $filename);

                    PoojaMedia::create([
                        'pooja_id' => $pooja->id,
                        'file_name' => $filename,
                        'file_path' => 'images/poojas/' . $filename,
                        'media_type' => 'image',
                        'is_primary' => false,
                        'sort_order' => $maxSortOrder + $i + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.poojas.index')
            ->with('success', 'Pooja catalog item updated successfully.');
    }

    public function destroy(Pooja $pooja)
    {
        DB::transaction(function () use ($pooja) {
            // Delete all associated files from disk
            $mediaItems = $pooja->media;
            foreach ($mediaItems as $media) {
                $fullPath = $this->uploadPath . '/' . $media->file_name;
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }

            $pooja->media()->delete();
            $pooja->pricings()->delete();
            $pooja->details()->delete();
            $pooja->delete();
        });

        return redirect()->route('admin.poojas.index')
            ->with('success', 'Pooja and all associated assets deleted successfully.');
    }

    public function toggleStatus(Pooja $pooja)
    {
        $pooja->status = $pooja->status === 'active' ? 'inactive' : 'active';
        $pooja->save();

        return response()->json([
            'success' => true,
            'status' => $pooja->status,
            'message' => 'Status updated successfully.',
        ]);
    }

    protected function ensureUploadDirectoryExists(): void
    {
        if (!File::isDirectory($this->uploadPath)) {
            File::makeDirectory($this->uploadPath, 0755, true, true);
        }
    }
}