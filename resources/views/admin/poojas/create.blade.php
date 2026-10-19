@extends('admin.layouts.app')

@section('title', 'Add New Pooja')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Add New Pooja</h1>
            <p class="text-muted small mb-0">Create ritual offerings, attach pricing tiers, requirements, and media assets.</p>
        </div>
        <a href="{{ route('admin.poojas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Catalog
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i>Please check the errors below:</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="createPoojaForm" action="{{ route('admin.poojas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="base_price" id="hidden_base_price" value="{{ old('base_price', old('pricings.0.price', 0)) }}">
        <input type="hidden" name="category_id" id="hidden_category_id" value="{{ old('category_id', old('pooja_category_id')) }}">

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
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g., Sri Satyanarayana Swamy Pooja" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold mb-0">Category <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#quickCategoryModal">
                                        + Add New Category
                                    </button>
                                </div>
								<select name="pooja_category_id" id="category_select" class="form-select" required>
								<option value="">Select Category</option>

								@foreach ($categories as $category)
    <option value="{{ $category->id }}"
        {{ (old('pooja_category_id') == $category->id || old('category_id') == $category->id) ? 'selected' : '' }}>
        {{ $category->name }}
    </option>
@endforeach
								</select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Estimated Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', 90) }}" placeholder="e.g. 90">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Short Description</label>
                            <textarea name="short_description" rows="2" class="form-control" placeholder="Brief summary for listings...">{{ old('short_description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Description</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="Overview and description...">{{ old('description', old('short_description')) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Significance & Benefits</label>
                            <textarea name="significance" rows="4" class="form-control" placeholder="Spiritual benefits, historical context...">{{ old('significance') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ritual Procedure (Vidhanam)</label>
                            <textarea name="procedure" rows="4" class="form-control" placeholder="Step-by-step ceremony stages...">{{ old('procedure') }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Devotee Instructions</label>
                            <textarea name="instructions" rows="3" class="form-control" placeholder="Fasting instructions, dressing guidelines...">{{ old('instructions') }}</textarea>
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
                            @if(old('samagri_list') && is_array(old('samagri_list')))
                                @foreach(old('samagri_list') as $item)
                                    <div class="input-group samagri-item">
                                        <span class="input-group-text bg-light"><i class="bi bi-dot"></i></span>
                                        <input type="text" name="samagri_list[]" class="form-control" value="{{ $item }}" placeholder="e.g., Turmeric (Pasupu) 100g">
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
                            @if(old('pricings') && is_array(old('pricings')))
                                @foreach (old('pricings') as $index => $pricing)
                                    <div class="border rounded p-3 bg-light pricing-row">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-semibold text-secondary">Package Tier #<span class="package-index">{{ $loop->iteration }}</span></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-pricing"><i class="bi bi-trash"></i></button>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <input type="text" name="pricings[{{ $index }}][title]" class="form-control" value="{{ $pricing['title'] ?? '' }}" placeholder="Package Title (e.g. Standard, Premium)" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" step="0.01" name="pricings[{{ $index }}][price]" class="form-control pricing-input-price" value="{{ $pricing['price'] ?? '' }}" placeholder="Price (₹)" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" step="0.01" name="pricings[{{ $index }}][special_price]" class="form-control" value="{{ $pricing['special_price'] ?? '' }}" placeholder="Discount Price (₹)">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" name="pricings[{{ $index }}][description]" class="form-control" value="{{ $pricing['description'] ?? '' }}" placeholder="Short package feature list">
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pricings[{{ $index }}][includes_samagri]" value="1" id="samagri_check_{{ $index }}" {{ !empty($pricing['includes_samagri']) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="samagri_check_{{ $index }}">Includes Samagri</label>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="border rounded p-3 bg-light pricing-row">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold text-secondary">Package Tier #<span class="package-index">1</span></span>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="pricings[0][title]" class="form-control" value="Standard Package" placeholder="Package Title (e.g. Standard)" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" step="0.01" name="pricings[0][price]" class="form-control pricing-input-price" placeholder="Price (₹)" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" step="0.01" name="pricings[0][special_price]" class="form-control" placeholder="Discount Price (₹)">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="pricings[0][description]" class="form-control" placeholder="Complete ritual by 1 certified purohit">
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="pricings[0][includes_samagri]" value="1" id="samagri_check_0" checked>
                                        <label class="form-check-label small" for="samagri_check_0">Includes Samagri</label>
                                    </div>
                                </div>
                            @endif
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
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Visible on Frontend)</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive (Draft)</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_popular" value="1" id="is_popular" {{ old('is_popular') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_popular">Mark as Popular Pooja</label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_temple_pooja" value="1" id="is_temple_pooja" {{ old('is_temple_pooja') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_temple_pooja">Temple Pooja Offering</label>
                        </div>
                    </div>
                </div>

                <!-- Primary Cover Image Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Cover Image</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" name="primary_image" class="form-control" id="primary_image" accept="image/*">
                            <div class="form-text">Saved to: <code>public_html/public/images/poojas/</code></div>
                        </div>
                        <div id="primary-preview-container" class="d-none text-center">
                            <img id="primary-preview" src="#" alt="Cover Preview" class="img-fluid rounded border shadow-sm" style="max-height: 180px;">
                        </div>
                    </div>
                </div>

                <!-- Gallery Images Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold">Gallery Images</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" name="gallery_images[]" class="form-control" id="gallery_images" accept="image/*" multiple>
                            <div class="form-text">Select multiple images for ritual media gallery.</div>
                        </div>
                        <div id="gallery-preview-container" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i>Publish Pooja
                    </button>
                    <a href="{{ route('admin.poojas.index') }}" class="btn btn-light border">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>

@include('admin.poojas.category-create-popup')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createPoojaForm');
    const categorySelect = document.getElementById('category_select');
    const hiddenCategory = document.getElementById('hidden_category_id');
    const hiddenBasePrice = document.getElementById('hidden_base_price');

    if (categorySelect && hiddenCategory) {
        categorySelect.addEventListener('change', function () {
            hiddenCategory.value = this.value;
        });
    }

    if (form && hiddenBasePrice) {
        form.addEventListener('submit', function () {
            const firstPriceInput = document.querySelector('.pricing-input-price');
            if (firstPriceInput && firstPriceInput.value) {
                hiddenBasePrice.value = firstPriceInput.value;
            }
        });
    }

    // Dynamic Samagri Items
    const samagriContainer = document.getElementById('samagri-container');
    const btnAddSamagri = document.getElementById('btn-add-samagri');

    if (btnAddSamagri && samagriContainer) {
        btnAddSamagri.addEventListener('click', function () {
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
    }

    // Dynamic Pricing Tiers
    const pricingContainer = document.getElementById('pricing-container');
    const btnAddPricing = document.getElementById('btn-add-pricing');
    let pricingIndex = {{ old('pricings') && is_array(old('pricings')) ? count(old('pricings')) : 1 }};

    if (btnAddPricing && pricingContainer) {
        btnAddPricing.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'border rounded p-3 bg-light pricing-row';
            row.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-secondary">Package Tier #<span class="package-index">${pricingIndex + 1}</span></span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-pricing"><i class="bi bi-trash"></i></button>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <input type="text" name="pricings[${pricingIndex}][title]" class="form-control" placeholder="Package Title (e.g. Premium)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="pricings[${pricingIndex}][price]" class="form-control pricing-input-price" placeholder="Price (₹)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="pricings[${pricingIndex}][special_price]" class="form-control" placeholder="Discount Price (₹)">
                    </div>
                </div>
                <div class="mb-2">
                    <input type="text" name="pricings[${pricingIndex}][description]" class="form-control" placeholder="Short package feature list">
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
    }

    // Image Previews
    const primaryInput = document.getElementById('primary_image');
    const primaryPreview = document.getElementById('primary-preview');
    const primaryContainer = document.getElementById('primary-preview-container');

    if (primaryInput && primaryPreview && primaryContainer) {
        primaryInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    primaryPreview.src = e.target.result;
                    primaryContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const galleryInput = document.getElementById('gallery_images');
    const galleryContainer = document.getElementById('gallery-preview-container');

    if (galleryInput && galleryContainer) {
        galleryInput.addEventListener('change', function () {
            galleryContainer.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'rounded border shadow-sm object-fit-cover';
                    img.style.width = '70px';
                    img.style.height = '70px';
                    galleryContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
</script>
@endsection