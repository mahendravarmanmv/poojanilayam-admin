
<div class="mb-3">
    <label for="type_code" class="form-label">Type Code</label>
    <input
        type="text"
        name="type_code"
        id="type_code"
        class="form-control @error('type_code') is-invalid @enderror"
        value="{{ old('type_code', $donationType->type_code ?? '') }}"
        required
    >
    @error('type_code')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="name" class="form-label">Donation Type Name</label>
    <input
        type="text"
        name="name"
        id="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $donationType->name ?? '') }}"
        required
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="slug" class="form-label">Slug (Optional)</label>
    <input
        type="text"
        name="slug"
        id="slug"
        class="form-control @error('slug') is-invalid @enderror"
        value="{{ old('slug', $donationType->slug ?? '') }}"
    >
    @error('slug')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea
        name="description"
        id="description"
        class="form-control @error('description') is-invalid @enderror"
        rows="4"
    >{{ old('description', $donationType->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select name="status" id="status" class="form-select" required>
        <option value="active"
            @selected(old('status', $donationType->status ?? 'active') === 'active')>
            Active
        </option>
        <option value="inactive"
            @selected(old('status', $donationType->status ?? 'active') === 'inactive')>
            Inactive
        </option>
    </select>
</div>

<div class="mb-3">
    <label for="sort_order" class="form-label">Display Order</label>
    <input
        type="number"
        name="sort_order"
        id="sort_order"
        class="form-control @error('sort_order') is-invalid @enderror"
        value="{{ old('sort_order', $donationType->sort_order ?? 0) }}"
        min="0"
        required
    >
    @error('sort_order')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        {{ $submitLabel ?? 'Save Donation Type' }}
    </button>

    <a href="{{ route('admin.donation-types.index') }}"
       class="btn btn-outline-secondary">
        Cancel
    </a>
</div>
