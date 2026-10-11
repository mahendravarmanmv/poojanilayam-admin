@extends('admin.layouts.app')
@section('title', 'Add New Pooja')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Create Catalog Item</h4>
    <a href="{{ route('admin.poojas.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Catalog</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.poojas.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Pooja Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="pooja_category_id" class="form-select" required>
                        <option value="">-- Choose Category --</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ old('pooja_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Base Price (₹)</label>
                    <input type="number" step="0.01" name="base_price" class="form-control" value="{{ old('base_price') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Duration (Minutes)</label>
                    <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', 60) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Associated Gods / Deities</label>
                    <select name="god_ids[]" class="form-select" multiple>
                        @foreach($gods as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple deities.</small>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Short Summary</label>
                    <textarea name="short_description" rows="2" class="form-control" required>{{ old('short_description') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Full Detailed Procedure Description</label>
                    <textarea name="description" rows="5" class="form-control" required>{{ old('description') }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                        <label class="form-check-label fw-semibold" for="isActive">Publish directly to client app</label>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Save Pooja Item</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection