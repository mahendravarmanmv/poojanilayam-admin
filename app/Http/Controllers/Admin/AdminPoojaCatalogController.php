<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pooja;
use App\Models\PoojaCategory;
use App\Models\God;
use App\Models\PoojaDetail;
use App\Models\PoojaPricing;
use App\Models\PoojaMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AdminPoojaCatalogController extends Controller
{
    protected string $uploadPath;

    public function __construct()
    {
        $this->uploadPath = public_path('images/poojas');
    }

    public function index(Request $request)
    {
        $query = Pooja::with(['category', 'gods', 'primaryMedia', 'pricings']);

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
        $request->validate([
            'name'              => ['required', 'string', 'max:200'],
            'pooja_category_id' => ['required', 'exists:pooja_categories,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'duration_minutes'  => ['nullable', 'integer', 'min:1'],
            'status'            => ['required', 'in:active,inactive'],
            'is_popular'        => ['nullable', 'boolean'],
            'significance'      => ['nullable', 'string'],
            'procedure'         => ['nullable', 'string'],
            'instructions'      => ['nullable', 'string'],
            'samagri_list'      => ['nullable', 'array'],
            'samagri_list.*'    => ['nullable', 'string', 'max:255'],
            'primary_image'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery_images.*'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'pricings'          => ['required', 'array', 'min:1'],
            'pricings.*.title'  => ['required', 'string', 'max:255'],
            'pricings.*.price'  => ['required', 'numeric', 'min:0'],
            'pricings.*.special_price' => ['nullable', 'numeric', 'min:0'],
            'pricings.*.description'   => ['nullable', 'string', 'max:500'],
            'pricings.*.includes_samagri' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($request) {
            $slug = Str::slug($request->input('name'));
            $count = Pooja::withTrashed()->where('slug', 'like', "{$slug}%")->count();
            if ($count > 0) {
                $slug = "{$slug}-" . ($count + 1);
            }

            // Generate unique pooja_code
            $slugClean = strtoupper(substr(str_replace('-', '', $slug), 0, 12));
            $poojaCode = 'PJ-' . ($slugClean ?: 'POOJA') . '-' . rand(1000, 9999);

            while (Pooja::withTrashed()->where('pooja_code', $poojaCode)->exists()) {
                $poojaCode = 'PJ-' . ($slugClean ?: 'POOJA') . '-' . rand(1000, 9999);
            }

            $isActive = $request->input('status') === 'active';

            // 1. Insert into poojas table matching exact schema
            $pooja = Pooja::create([
                'pooja_code'        => $poojaCode,
                'pooja_category_id' => $request->input('pooja_category_id'),
                'name'              => $request->input('name'),
                'slug'              => $slug,
                'short_description' => $request->input('short_description'),
                'description'       => $request->input('description') ?? $request->input('short_description'),
                'duration_minutes'  => $request->input('duration_minutes', 60),
                'status'            => $request->input('status', 'active'),
                'is_featured'       => $request->boolean('is_popular'),
                'is_active'         => $isActive,
                'sort_order'        => 0,
            ]);

            // 2. Insert into pooja_details with schema column detection
            $cleanedSamagri = array_values(array_filter($request->input('samagri_list', [])));
            $detailPayload = [
                'pooja_id'         => $pooja->id,
                'description'      => $request->input('description') ?? $request->input('short_description'),
                'duration_minutes' => $request->input('duration_minutes', 60),
                'significance'     => $request->input('significance'),
                'procedure'        => $request->input('procedure'),
                'instructions'     => $request->input('instructions'),
                'samagri_list'     => !empty($cleanedSamagri) ? $cleanedSamagri : null,
            ];

            $detailColumns = Schema::getColumnListing('pooja_details');
            $detailData = array_intersect_key($detailPayload, array_flip($detailColumns));

            PoojaDetail::create($detailData);

            // 3. Pricing Tiers
            if ($request->has('pricings')) {
                // Ensure a valid currency exists
                $currency = null;
                if (class_exists(\App\Models\Currency::class)) {
                    $currency = \App\Models\Currency::where('code', 'INR')->first() 
                        ?? \App\Models\Currency::first();

                    // If the currencies table is completely empty, create the INR record
                    if (!$currency) {
                        $currencyColumns = Schema::getColumnListing('currencies');
                        $currencyPayload = [
                            'code'      => 'INR',
                            'name'      => 'Indian Rupee',
                            'symbol'    => '₹',
                            'is_active' => 1,
                            'status'    => 'active',
                        ];
                        $currencyData = array_intersect_key($currencyPayload, array_flip($currencyColumns));
                        $currency = \App\Models\Currency::create($currencyData);
                    }
                }

                $currencyId = $currency ? $currency->id : 1;

                foreach ($request->input('pricings') as $index => $pricingData) {
                    $rawPrice = $pricingData['price'] ?? 0;
                    $specialPrice = $pricingData['special_price'] ?? null;
                    
                    $discountAmount = 0.00;
                    if ($specialPrice && $specialPrice < $rawPrice) {
                        $discountAmount = $rawPrice - $specialPrice;
                    }

                    $pricingType = !empty($pricingData['title']) 
                        ? Str::slug($pricingData['title']) 
                        : ($index === 0 ? 'base' : 'tier_' . ($index + 1));

                    PoojaPricing::create([
                        'pooja_id'        => $pooja->id,
                        'currency_id'     => $currencyId,
                        'pricing_type'    => substr($pricingType, 0, 40),
                        'amount'          => $rawPrice,
                        'discount_amount' => $discountAmount,
                        'is_default'      => $index === 0,
                        'is_active'       => 1,
                    ]);
                }
            }

            // 4. Media Uploads
            $this->ensureUploadDirectoryExists();

            if ($request->hasFile('primary_image')) {
                $primaryFile = $request->file('primary_image');
                $filename = 'pooja_' . $pooja->id . '_primary_' . time() . '.' . $primaryFile->getClientOriginalExtension();
                $primaryFile->move($this->uploadPath, $filename);

                PoojaMedia::create([
                    'pooja_id'    => $pooja->id,
                    'media_type'  => 'image',
                    'file_path'   => 'images/poojas/' . $filename,
                    'is_featured' => 1,
                    'sort_order'  => 0,
                    'is_active'   => 1,
                ]);
            }

            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $i => $galleryFile) {
                    $filename = 'pooja_' . $pooja->id . '_gal_' . time() . '_' . $i . '.' . $galleryFile->getClientOriginalExtension();
                    $galleryFile->move($this->uploadPath, $filename);

                    PoojaMedia::create([
                        'pooja_id'    => $pooja->id,
                        'media_type'  => 'image',
                        'file_path'   => 'images/poojas/' . $filename,
                        'is_featured' => 0,
                        'sort_order'  => $i + 1,
                        'is_active'   => 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.poojas.index')
            ->with('success', 'Pooja catalog item created successfully.');
    }

    public function edit(Pooja $pooja)
    {
        $pooja->load(['details', 'pricings', 'media', 'gods']);
        $categories = PoojaCategory::all();
        $gods = God::all();

        return view('admin.poojas.edit', compact('pooja', 'categories', 'gods'));
    }

    public function update(Request $request, Pooja $pooja)
    {
        $request->validate([
            'name'              => ['required', 'string', 'max:200'],
            'pooja_category_id' => ['required', 'exists:pooja_categories,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'duration_minutes'  => ['nullable', 'integer', 'min:1'],
            'status'            => ['required', 'in:active,inactive'],
            'is_popular'        => ['nullable', 'boolean'],
            'significance'      => ['nullable', 'string'],
            'procedure'         => ['nullable', 'string'],
            'instructions'      => ['nullable', 'string'],
            'samagri_list'      => ['nullable', 'array'],
            'samagri_list.*'    => ['nullable', 'string', 'max:255'],
            'god_ids'           => ['nullable', 'array'],
            'god_ids.*'         => ['exists:gods,id'],
            'delete_media_ids'  => ['nullable', 'array'],
            'delete_media_ids.*'=> ['integer'],
            'primary_image'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery_images.*'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'pricings'          => ['required', 'array', 'min:1'],
            'pricings.*.title'  => ['required', 'string', 'max:255'],
            'pricings.*.price'  => ['required', 'numeric', 'min:0'],
            'pricings.*.special_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $pooja) {
            $isActive = $request->input('status') === 'active';

            // 1. Update Core Pooja
            $pooja->update([
                'pooja_category_id' => $request->input('pooja_category_id'),
                'name'              => $request->input('name'),
                'slug'              => Str::slug($request->input('name')),
                'short_description' => $request->input('short_description'),
                'description'       => $request->input('description') ?? $request->input('short_description'),
                'duration_minutes'  => $request->input('duration_minutes', 60),
                'status'            => $request->input('status', 'active'),
                'is_featured'       => $request->boolean('is_popular'),
                'is_active'         => $isActive,
            ]);

            // 2. Update Details
            $cleanedSamagri = array_values(array_filter($request->input('samagri_list', [])));
            $detailPayload = [
                'description'      => $request->input('description') ?? $request->input('short_description'),
                'duration_minutes' => $request->input('duration_minutes', 60),
                'significance'     => $request->input('significance'),
                'procedure'        => $request->input('procedure'),
                'instructions'     => $request->input('instructions'),
                'samagri_list'     => !empty($cleanedSamagri) ? $cleanedSamagri : null,
            ];
            $detailColumns = Schema::getColumnListing('pooja_details');
            $pooja->details()->updateOrCreate(
                ['pooja_id' => $pooja->id],
                array_intersect_key($detailPayload, array_flip($detailColumns))
            );

            // 3. Sync Associated Deities
            $pooja->gods()->sync($request->input('god_ids', []));

            // 4. Update or Recreate Pricing Tiers
            if ($request->has('pricings')) {
                $currencyId = 1;
                if (class_exists(\App\Models\Currency::class)) {
                    $currencyId = \App\Models\Currency::where('code', 'INR')->value('id')
                        ?? \App\Models\Currency::value('id')
                        ?? 1;
                }

                // Delete obsolete tiers and replace with current submitted list
                $pooja->pricings()->delete();

                foreach ($request->input('pricings') as $index => $pricingData) {
                    $rawPrice = $pricingData['price'] ?? 0;
                    $specialPrice = $pricingData['special_price'] ?? null;
                    $discountAmount = 0.00;

                    if ($specialPrice && $specialPrice < $rawPrice) {
                        $discountAmount = $rawPrice - $specialPrice;
                    }

                    $pricingType = !empty($pricingData['title']) 
                        ? Str::slug($pricingData['title']) 
                        : ($index === 0 ? 'base' : 'tier_' . ($index + 1));

                    PoojaPricing::create([
                        'pooja_id'        => $pooja->id,
                        'currency_id'     => $currencyId,
                        'pricing_type'    => substr($pricingType, 0, 40),
                        'amount'          => $rawPrice,
                        'discount_amount' => $discountAmount,
                        'is_default'      => $index === 0,
                        'is_active'       => 1,
                    ]);
                }
            }

            // 5. Handle Media Deletions
            if ($request->filled('delete_media_ids')) {
                $mediaToDelete = PoojaMedia::where('pooja_id', $pooja->id)
                    ->whereIn('id', $request->input('delete_media_ids'))
                    ->get();

                foreach ($mediaToDelete as $media) {
                    $fullPath = public_path($media->file_path);
                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }
                    $media->delete();
                }
            }

            // 6. Handle Primary Cover Upload
            $this->ensureUploadDirectoryExists();

            if ($request->hasFile('primary_image')) {
                // Demote existing featured image
                PoojaMedia::where('pooja_id', $pooja->id)->update(['is_featured' => 0]);

                $primaryFile = $request->file('primary_image');
                $filename = 'pooja_' . $pooja->id . '_primary_' . time() . '.' . $primaryFile->getClientOriginalExtension();
                $primaryFile->move($this->uploadPath, $filename);

                PoojaMedia::create([
                    'pooja_id'    => $pooja->id,
                    'media_type'  => 'image',
                    'file_path'   => 'images/poojas/' . $filename,
                    'is_featured' => 1,
                    'sort_order'  => 0,
                    'is_active'   => 1,
                ]);
            }

            // 7. Handle Additional Gallery Uploads
            if ($request->hasFile('gallery_images')) {
                $maxSort = PoojaMedia::where('pooja_id', $pooja->id)->max('sort_order') ?? 0;
                foreach ($request->file('gallery_images') as $i => $galleryFile) {
                    $filename = 'pooja_' . $pooja->id . '_gal_' . time() . '_' . $i . '.' . $galleryFile->getClientOriginalExtension();
                    $galleryFile->move($this->uploadPath, $filename);

                    PoojaMedia::create([
                        'pooja_id'    => $pooja->id,
                        'media_type'  => 'image',
                        'file_path'   => 'images/poojas/' . $filename,
                        'is_featured' => 0,
                        'sort_order'  => $maxSort + $i + 1,
                        'is_active'   => 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.poojas.index')->with('success', 'Pooja catalog item updated successfully.');
    }

    public function quickStoreCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:pooja_categories,name',
        ]);

        $slug = Str::slug($request->name);
        $codeBase = 'CAT-' . strtoupper(str_replace('-', '_', $slug));
        $categoryCode = substr($codeBase, 0, 30);

        if (PoojaCategory::where('category_code', $categoryCode)->exists()) {
            $categoryCode = substr($categoryCode, 0, 24) . '_' . rand(100, 999);
        }

        $category = PoojaCategory::create([
            'category_code' => $categoryCode,
            'name'          => $request->name,
            'slug'          => $slug,
        ]);

        return response()->json([
            'success'  => true,
            'category' => $category,
        ]);
    }

    public function destroy(Pooja $pooja)
    {
        $pooja->delete();
        return redirect()->route('admin.poojas.index')->with('success', 'Pooja catalog item archived.');
    }

    public function toggleStatus(Pooja $pooja)
    {
        $newActive = !$pooja->is_active;
        $pooja->is_active = $newActive;
        $pooja->status = $newActive ? 'active' : 'inactive';
        $pooja->save();

        return response()->json([
            'success'   => true,
            'status'    => $pooja->status,
            'is_active' => $pooja->is_active,
            'message'   => 'Status updated successfully.',
        ]);
    }

    protected function ensureUploadDirectoryExists(): void
    {
        if (!File::isDirectory($this->uploadPath)) {
            File::makeDirectory($this->uploadPath, 0755, true, true);
        }
    }
}