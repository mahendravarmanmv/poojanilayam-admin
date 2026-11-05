
@php
    $selectedModules = old(
        'modules',
        isset($tax) ? $tax->modules->pluck('module')->toArray() : []
    );

    $enabledModules = old(
        'module_enabled',
        isset($tax)
            ? $tax->modules->where('is_enabled', true)->pluck('module')->toArray()
            : $selectedModules
    );
@endphp

<div class="row">

    <div class="col-md-6 mb-3">
        <label class="form-label">Tax Name <span class="text-danger">*</span></label>
        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name', $tax->name ?? '') }}"
               placeholder="Example: GST"
               required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Tax Code <span class="text-danger">*</span></label>
        <input type="text"
               name="code"
               class="form-control"
               value="{{ old('code', $tax->code ?? '') }}"
               placeholder="Example: GST18"
               required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Tax Percentage (%) <span class="text-danger">*</span></label>
        <input type="number"
               name="percentage"
               class="form-control"
               value="{{ old('percentage', $tax->percentage ?? '') }}"
               min="0"
               max="100"
               step="0.01"
               placeholder="Example: 18"
               required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Tax Status <span class="text-danger">*</span></label>
        <select name="is_active" class="form-select" required>
            <option value="1" {{ (string) old('is_active', isset($tax) ? (int) $tax->is_active : 1) === '1' ? 'selected' : '' }}>
                Active
            </option>
            <option value="0" {{ (string) old('is_active', isset($tax) ? (int) $tax->is_active : 1) === '0' ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description"
                  class="form-control"
                  rows="3"
                  placeholder="Enter tax description">{{ old('description', $tax->description ?? '') }}</textarea>
    </div>

</div>

<hr class="my-4">

<h5 class="mb-2">Module Assignments</h5>
<p class="text-muted small">
    Select the modules where this tax should be available.
    You can enable or disable it separately for each selected module.
</p>

<div class="row">

    @foreach($modules as $key => $label)
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="border rounded p-3 h-100">

                <div class="form-check mb-2">
                    <input type="checkbox"
                           name="modules[]"
                           value="{{ $key }}"
                           id="module_{{ $key }}"
                           class="form-check-input module-checkbox"
                           {{ in_array($key, $selectedModules) ? 'checked' : '' }}>

                    <label class="form-check-label fw-semibold"
                           for="module_{{ $key }}">
                        {{ $label }}
                    </label>
                </div>

                <div class="form-check module-enable-wrapper"
                     id="enable_wrapper_{{ $key }}"
                     style="{{ in_array($key, $selectedModules) ? '' : 'display:none;' }}">

                    <input type="checkbox"
                           name="module_enabled[]"
                           value="{{ $key }}"
                           id="enable_{{ $key }}"
                           class="form-check-input"
                           {{ in_array($key, $enabledModules) ? 'checked' : '' }}>

                    <label class="form-check-label small"
                           for="enable_{{ $key }}">
                        Enable tax for this module
                    </label>
                </div>

            </div>
        </div>
    @endforeach

</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i> {{ $buttonText }}
    </button>

    <a href="{{ route('admin.taxes.index') }}" class="btn btn-outline-secondary">
        Cancel
    </a>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.module-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const module = this.value;
            const wrapper = document.getElementById('enable_wrapper_' + module);
            const enableCheckbox = document.getElementById('enable_' + module);

            if (this.checked) {
                wrapper.style.display = '';
                enableCheckbox.checked = true;
            } else {
                wrapper.style.display = 'none';
                enableCheckbox.checked = false;
            }
        });
    });
});
</script>
@endpush
