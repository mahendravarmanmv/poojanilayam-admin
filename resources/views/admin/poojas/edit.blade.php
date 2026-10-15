@extends('admin.layouts.app')

@section('title', 'Edit Pooja: ' . $pooja->name)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Pooja Catalog Item</h1>
            <p class="text-muted small mb-0">Modify information, manage tiered pricing packages, and update ceremony media.</p>
        </div>
        <a href="{{ route('admin.poojas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Catalog
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as$error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.poojas.update', $pooja) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Side: Core Details -->
            <div class="col-lg-8">
                <!-- General Info Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">General Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pooja Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $pooja->name) }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as$category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $pooja->category_id) ==$category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Estimated Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $pooja->details->duration_minutes ?? 90) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Short Description</label>
                            <textarea name="short_description" rows="2" class="form-control">{{ old('short_description', $pooja->short_description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Significance & Benefits</label>
                            <textarea name="significance" rows="4" class="form-control">{{ old('significance', $pooja->details->significance ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ritual Procedure (Vidhanam)</label>
                            <textarea name="procedure" rows="4" class="form-control">{{ old('procedure', $pooja->details->procedure ?? '') }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Devotee Instructions</label>
                            <textarea name="instructions" rows="3" class="form-control">{{ old('instructions', $pooja->details->instructions ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Samagri Checklist Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Samagri Requirements List</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-samagri">
                            <i class="bi bi-plus-lg me-1"></i>Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="samagri-container" class="d-flex flex-column gap-2">
                            @php
                                $samagriItems = old('samagri_list',$pooja->details->samagri_list ?? []);
                            @endphp
                            @if(!empty($samagriItems))
                                @foreach($samagriItems as$item)
                                    <div class="input-group samagri-item">
                                        <span class="input-group-text bg-light"><i class="bi bi-dot"></i></span>
                                        <input type="text" name="samagri_list[]" class="form-control" value="{{ $item }}">
                                        <button type="button" class="btn btn-outline-danger remove-samagri"><i class="bi bi-trash"></i></button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group samagri-item">
                                    <span class="input-group-text bg-light"><i class="bi bi-dot"></i></span>
                                    <input type="text" name="samagri_list[]" class="form-control" placeholder="e.g., Turmeric (Pasupu) 100g">
                                    <button type="button" class="btn btn-outline-danger remove-samagri"><i class="bi bi-trash"></i></button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pricing Packages Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Pricing Packages & Tiers <span class="text-danger">*</span></h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-pricing">
                            <i class="bi bi-plus-lg me-1"></i>Add Package Tier
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="pricing-container" class="d-flex flex-column gap-3">
                            @php
                                $pricings = old('pricings',$pooja->pricings->toArray());
                            @endphp
                            @foreach($pricings as $index =>$pricing)
                                <div class="border rounded p-3 bg-light pricing-row">
                                    <input type="hidden" name="pricings[{{ $index }}][id]" value="{{ $pricing['id'] ?? '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold text-secondary">Package Tier #<span class="package-index">{{ $index + 1 }}</span></span>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-pricing"><i class="bi bi-trash"></i></button>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="pricings[{{ $index }}][title]" class="form-control" value="{{ $pricing['title'] ?? '' }}" placeholder="Package Title" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" step="0.01" name="pricings[{{ $index }}][price]" class="form-control" value="{{ $pricing['price'] ?? '' }}" placeholder="Price (₹)" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" step="0.01" name="pricings[{{ $index }}][special_price]" class="form-control" value="{{ $pricing['special_price'] ?? '' }}" placeholder="Discount Price (₹)">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="pricings[{{ $index }}][description]" class="form-control" value="{{ $pricing['description'] ?? '' }}" placeholder="Package description">
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="pricings[{{ $index }}][includes_samagri]" value="1" id="samagri_check_{{ $index }}" {{ !empty($pricing['includes_samagri']) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="samagri_check_{{ $index }}">Includes Samagri</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Status, Flags & Media -->
            <div class="col-lg-4">
                <!-- Publishing Status Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Publication Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', $pooja->status) === 'active' ? 'selected' : '' }}>Active (Visible on Frontend)</option>
                                <option value="inactive" {{ old('status', $pooja->status) === 'inactive' ? 'selected' : '' }}>Inactive (Draft)</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_popular" value="1" id="is_popular" {{ old('is_popular', $pooja->is_popular) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_popular">Mark as Popular Pooja</label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_temple_pooja" value="1" id="is_temple_pooja" {{ old('is_temple_pooja', $pooja->is_temple_pooja) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_temple_pooja">Temple Pooja Offering</label>
                        </div>
                    </div>
                </div>

                <!-- Cover Image Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Primary Image</h6>
                    </div>
                    <div class="card-body text-center">
                        @php
                            $primaryMedia =$pooja->media->firstWhere('is_primary', true);
                        @endphp
                        @if($primaryMedia &&$primaryMedia->file_name)
                            <div class="mb-3">
                                <img src="/images/poojas/{{ $primaryMedia->file_name }}" alt="{{ $pooja->name }}" class="img-fluid rounded border shadow-sm" style="max-height: 180px;">
                            </div>
                        @endif
                        <input type="file" name="primary_image" class="form-control" id="primary_image" accept="image/*">
                        <div class="form-text">Uploading a new image replaces the existing primary image.</div>
                    </div>
                </div>

                <!-- Gallery Images Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Gallery Images</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $galleryItems =$pooja->media->where('is_primary', false);
                        @endphp
                        @if($galleryItems->isNotEmpty())
                            <label class="form-label small fw-semibold text-muted">Current Gallery (Select to remove):</label>
                            <div class="row g-2 mb-3">
                                @foreach($galleryItems as$media)
                                    <div class="col-4 position-relative">
                                        <img src="/images/poojas/{{ $media->file_name }}" alt="Gallery Item" class="img-fluid rounded border object-fit-cover w-100" style="height: 70px;">
                                        <div class="form-check position-absolute top-0 end-0 p-1 me-1">
                                            <input class="form-check-input bg-danger border-danger" type="checkbox" name="delete_media_ids[]" value="{{ $media->id }}" title="Check to delete">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <label class="form-label small fw-semibold text-muted">Upload Additional Images:</label>
                        <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-save me-1"></i>Save Changes
                    </button>
                    <a href="{{ route('admin.poojas.index') }}" class="btn btn-light border">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Dynamic Samagri Items
    const samagriContainer = document.getElementById('samagri-container');
    document.getElementById('btn-add-samagri').addEventListener('click', function () {
        const item = document.createElement('div');
        item.className = 'input-group samagri-item';
        item.innerHTML = `
            <span class="input-group-text bg-light"><i class="bi bi-dot"></i></span>
            <input type="text" name="samagri_list[]" class="form-control" placeholder="e.g., Turmeric (Pasupu) 100g">
            <button type="button" class="btn btn-outline-danger remove-samagri"><i class="bi bi-trash"></i></button>
        `;
        samagriContainer.appendChild(item);
    });

    samagriContainer.addEventListener('click', function (e) {
        if (e.target.closest('.remove-samagri')) {
            const rows = samagriContainer.querySelectorAll('.samagri-item');
            if (rows.length > 1) {
                e.target.closest('.samagri-item').remove();
            } else {
                rows[0].querySelector('input').value = '';
            }
        }
    });

    // 2. Dynamic Pricing Tiers
    const pricingContainer = document.getElementById('pricing-container');
    let pricingIndex = {{ count($pricings) }};

    document.getElementById('btn-add-pricing').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'border rounded p-3 bg-light pricing-row';
        row.innerHTML = `
            <input type="hidden" name="pricings[${pricingIndex}][id]" value="">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold text-secondary">Package Tier #<span class="package-index">${pricingIndex + 1}</span></span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-pricing"><i class="bi bi-trash"></i></button>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <input type="text" name="pricings[${pricingIndex}][title]" class="form-control" placeholder="Package Title" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="pricings[${pricingIndex}][price]" class="form-control" placeholder="Price (₹)" required>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="pricings[${pricingIndex}][special_price]" class="form-control" placeholder="Discount Price (₹)">
                </div>
            </div>
            <div class="mb-2">
                <input type="text" name="pricings[${pricingIndex}][description]" class="form-control" placeholder="Package description">
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="pricings[${pricingIndex}][includes_samagri]" value="1" id="samagri_check_${pricingIndex}">
                <label class="form-check-label small" for="samagri_check_${pricingIndex}">Includes Samagri</label>
            </div>
        `;
        pricingContainer.appendChild(row);
        pricingIndex++;
    });

    pricingContainer.addEventListener('click', function (e) {
        if (e.target.closest('.remove-pricing')) {
            const rows = pricingContainer.querySelectorAll('.pricing-row');
            if (rows.length > 1) {
                e.target.closest('.pricing-row').remove();
                pricingContainer.querySelectorAll('.pricing-row').forEach((r, idx) => {
                    r.querySelector('.package-index').textContent = idx + 1;
                });
            } else {
                alert('At least one pricing package is required.');
            }
        }
    });
});
</script>
@endsection