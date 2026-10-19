@extends('admin.layouts.app')

@section('title', 'Edit Pooja: ' . $pooja->name)

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Pooja Catalog Item</h1>
            <p class="text-muted small mb-0">Code: <span class="badge bg-secondary font-monospace">{{ $pooja->pooja_code }}</span></p>
        </div>
        <a href="{{ route('admin.poojas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Catalog
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i>Please fix the following issues:</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="editPoojaForm" action="{{ route('admin.poojas.update', $pooja) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Hidden bridge for backward compatibility with base update validation --}}
        <input type="hidden" name="base_price" id="hidden_base_price" value="{{ old('base_price', optional($pooja->pricings->first())->amount ?? 0) }}">

        <div class="row g-4">
            <!-- Left Side: Core Details -->
            <div class="col-lg-8">
                <!-- General Info Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-1"></i>General Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pooja Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $pooja->name) }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold mb-0">Category <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#quickCategoryModal">
                                        + Quick Add
                                    </button>
                                </div>
                                <select name="pooja_category_id" id="category_select" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('pooja_category_id', $pooja->pooja_category_id) ==$category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Estimated Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control" min="15" step="5" value="{{ old('duration_minutes', $pooja->duration_minutes ?? 60) }}">
                            </div>
                        </div>

                        <!-- Associated Deities (Gods) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Associated Deities / Gods</label>
                            <div class="row g-2 border rounded p-2 bg-light" style="max-height: 140px; overflow-y: auto;">
                                @php
                                    $selectedGodIds = old('god_ids',$pooja->gods->pluck('id')->toArray());
                                @endphp
                                @forelse ($gods as $god)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="god_ids[]" value="{{ $god->id }}" id="god_{{ $god->id }}" {{ in_array($god->id,$selectedGodIds) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="god_{{ $god->id }}">
                                                {{ $god->name }}
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted small">No deities available.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Short Description</label>
                            <textarea name="short_description" rows="2" class="form-control" placeholder="Brief tagline or overview...">{{ old('short_description', $pooja->short_description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Description</label>
                            <textarea name="description" rows="4" class="form-control" placeholder="Complete ritual description...">{{ old('description', $pooja->description ?? $pooja->short_description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Significance & Benefits</label>
                            <textarea name="significance" rows="3" class="form-control" placeholder="Spiritual benefits...">{{ old('significance', optional($pooja->details)->significance ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ritual Procedure (Vidhanam)</label>
                            <textarea name="procedure" rows="3" class="form-control" placeholder="Step-by-step ceremony stages...">{{ old('procedure', optional($pooja->details)->procedure ?? '') }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Devotee Instructions</label>
                            <textarea name="instructions" rows="2" class="form-control" placeholder="Prerequisites, clothing guidelines, fasting...">{{ old('instructions', optional($pooja->details)->instructions ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Samagri Checklist Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-card-checklist me-1"></i>Samagri Requirements List</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-samagri">
                            <i class="bi bi-plus-lg me-1"></i>Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        @php
                            $samagriList = old('samagri_list');
                            if (!is_array($samagriList)) {
                                $samagriList = optional($pooja->details)->samagri_list ?? [];
                            }
                            if (!is_array($samagriList)) {$samagriList = [];
                            }
                        @endphp
                        <div id="samagri-container" class="d-flex flex-column gap-2">
                            @if (count($samagriList) > 0)
                                @foreach ($samagriList as $item)
                                    <div class="input-group samagri-item">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-dot"></i></span>
                                        <input type="text" name="samagri_list[]" class="form-control" value="{{ $item }}" placeholder="e.g., Turmeric (Pasupu) 100g">
                                        <button type="button" class="btn btn-outline-danger remove-samagri"><i class="bi bi-trash"></i></button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group samagri-item">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-dot"></i></span>
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
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-tags me-1"></i>Pricing Packages & Tiers <span class="text-danger">*</span></h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-pricing">
                            <i class="bi bi-plus-lg me-1"></i>Add Package Tier
                        </button>
                    </div>
                    <div class="card-body">
                        @php
                            $pricingItems = old('pricings');
                            if (!is_array($pricingItems)) {$pricingItems = [];
                                foreach ($pooja->pricings as $p) {$pricingItems[] = [
                                        'id'            => $p->id,
                                        'title'         => $p->pricing_type ?? 'Standard Package',
                                        'price'         => $p->amount,
                                        'special_price' => ($p->discount_amount > 0) ? ($p->amount -$p->discount_amount) : '',
                                        'description'   => '',
                                        'includes_samagri' => 1,
                                    ];
                                }
                            }
                        @endphp
                        <div id="pricing-container" class="d-flex flex-column gap-3">
                            @if (count($pricingItems) > 0)
                                @foreach ($pricingItems as $index =>$pricing)
                                    <div class="border rounded p-3 bg-light pricing-row">
                                        <input type="hidden" name="pricings[{{ $index }}][id]" value="{{ $pricing['id'] ?? '' }}">
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
                                            <input type="text" name="pricings[{{ $index }}][description]" class="form-control" value="{{ $pricing['description'] ?? '' }}" placeholder="Optional package description or perks">
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
                                        <input type="text" name="pricings[0][description]" class="form-control" placeholder="Optional package description">
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
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-toggle-on me-1"></i>Publication Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Visibility Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', $pooja->status) === 'active' ? 'selected' : '' }}>Active (Published)</option>
                                <option value="inactive" {{ old('status', $pooja->status) === 'inactive' ? 'selected' : '' }}>Inactive (Draft / Hidden)</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_popular" value="1" id="is_popular" {{ old('is_popular', $pooja->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_popular">Mark as Featured / Popular</label>
                        </div>
                    </div>
                </div>

                <!-- Cover Image Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-image me-1"></i>Primary Image</h6>
                    </div>
                    <div class="card-body text-center">
                        @php
                            $primaryMedia = $pooja->media->firstWhere('is_featured', true) ?? $pooja->media->first();
                        @endphp
                        @if ($primaryMedia &&$primaryMedia->file_path)
                            <div class="mb-3">
                                <img src="/{{ ltrim($primaryMedia->file_path, '/') }}" alt="{{ $pooja->name }}" class="img-fluid rounded border shadow-sm" style="max-height: 180px; object-fit: cover;">
                            </div>
                        @else
                            <div class="p-3 bg-light rounded border mb-3 text-muted small">
                                <i class="bi bi-image fs-2 d-block mb-1"></i>No primary image uploaded.
                            </div>
                        @endif
                        <input type="file" name="primary_image" class="form-control" id="primary_image" accept="image/*">
                        <div class="form-text small">Uploading a new file will replace the current cover image.</div>
                    </div>
                </div>

                <!-- Gallery Images Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-images me-1"></i>Gallery Images</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $galleryMediaList =$pooja->media->where('is_featured', false)->all();
                        @endphp
                        @if (count($galleryMediaList) > 0)
                            <label class="form-label small fw-semibold text-muted">Current Gallery (Select to delete):</label>
                            <div class="row g-2 mb-3">
                                @foreach ($galleryMediaList as $mediaItem)
                                    <div class="col-4 position-relative">
                                        <img src="/{{ ltrim($mediaItem->file_path, '/') }}" alt="Gallery" class="img-fluid rounded border object-fit-cover w-100" style="height: 70px;">
                                        <div class="form-check position-absolute top-0 end-0 p-1 me-1">
                                            <input class="form-check-input bg-danger border-danger" type="checkbox" name="delete_media_ids[]" value="{{ $mediaItem->id }}" title="Check to delete">
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

<!-- Quick Add Category Modal -->
<div class="modal fade" id="quickCategoryModal" tabindex="-1" aria-labelledby="quickCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="quickCategoryModalLabel">Quick Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                    <input type="text" id="new_category_name" class="form-control" placeholder="e.g., Vratams, Homams">
                    <div class="invalid-feedback" id="quickCategoryError"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveQuickCategory">Save Category</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('editPoojaForm');
    const hiddenBasePrice = document.getElementById('hidden_base_price');

    // Sync first tier price with hidden base_price on submission
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
                <span class="input-group-text bg-light text-muted"><i class="bi bi-dot"></i></span>
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
    let pricingIndex = @json(max(count($pricingItems), 1));

    if (btnAddPricing && pricingContainer) {
        btnAddPricing.addEventListener('click', function () {
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
                        <input type="text" name="pricings[${pricingIndex}][title]" class="form-control" placeholder="Package Title (e.g., Premium)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="pricings[${pricingIndex}][price]" class="form-control pricing-input-price" placeholder="Price (₹)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="pricings[${pricingIndex}][special_price]" class="form-control" placeholder="Discount Price (₹)">
                    </div>
                </div>
                <div class="mb-2">
                    <input type="text" name="pricings[${pricingIndex}][description]" class="form-control" placeholder="Optional package description">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="pricings[${pricingIndex}][includes_samagri]" value="1" id="samagri_check_${pricingIndex}" checked>
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

    // Quick Add Category Ajax
    const btnSaveCategory = document.getElementById('btnSaveQuickCategory');
    const inputCategoryName = document.getElementById('new_category_name');
    const categorySelect = document.getElementById('category_select');
    const quickCategoryError = document.getElementById('quickCategoryError');

    if (btnSaveCategory) {
        btnSaveCategory.addEventListener('click', function () {
            const name = inputCategoryName.value.trim();
            if (!name) {
                inputCategoryName.classList.add('is-invalid');
                quickCategoryError.textContent = 'Category name is required.';
                return;
            }

            btnSaveCategory.disabled = true;
            btnSaveCategory.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

            fetch('{{ route("admin.categories.quickStore") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name: name })
            })
            .then(res => res.json())
            .then(data => {
                btnSaveCategory.disabled = false;
                btnSaveCategory.innerHTML = 'Save Category';

                if (data.success && data.category) {
                    const option = new Option(data.category.name, data.category.id, true, true);
                    categorySelect.add(option);
                    inputCategoryName.value = '';
                    inputCategoryName.classList.remove('is-invalid');
                    bootstrap.Modal.getInstance(document.getElementById('quickCategoryModal')).hide();
                } else {
                    inputCategoryName.classList.add('is-invalid');
                    quickCategoryError.textContent = data.message || 'Error creating category.';
                }
            })
            .catch(() => {
                btnSaveCategory.disabled = false;
                btnSaveCategory.innerHTML = 'Save Category';
                inputCategoryName.classList.add('is-invalid');
                quickCategoryError.textContent = 'Server error occurred.';
            });
        });
    }
});
</script>
@endsection