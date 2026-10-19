<!-- Quick Add Category Modal -->
<div class="modal fade" id="quickCategoryModal" tabindex="-1" aria-labelledby="quickCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickCategoryModalLabel">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="quick_category_name">Category Name <span class="text-danger">*</span></label>
                    <input type="text" id="quick_category_name" class="form-control" placeholder="e.g., Dosha Nivarana Poojas" autocomplete="off">
                    <div id="quick_cat_error" class="text-danger small mt-2 d-none"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveQuickCategoryBtn">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="saveCatSpinner" role="status" aria-hidden="true"></span>
                    <span>Save Category</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const saveBtn = document.getElementById('saveQuickCategoryBtn');
    const input = document.getElementById('quick_category_name');
    const errorBox = document.getElementById('quick_cat_error');
    const spinner = document.getElementById('saveCatSpinner');
    const modalEl = document.getElementById('quickCategoryModal');

    saveBtn.addEventListener('click', function () {
        const name = input.value.trim();

        if (!name) {
            errorBox.textContent = 'Please enter a category name.';
            errorBox.classList.remove('d-none');
            input.focus();
            return;
        }

        saveBtn.disabled = true;
        spinner.classList.remove('d-none');
        errorBox.classList.add('d-none');

        fetch("{{ route('admin.pooja-categories.quick-store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Validation failed'));
            }
            return data;
        })
        .then(data => {
            if (data.success && data.category) {
                const select = document.getElementById('category_select');
                const newOption = new Option(data.category.name, data.category.id, true, true);
                select.add(newOption);

                input.value = '';
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            } else {
                errorBox.textContent = data.message || 'Error saving category.';
                errorBox.classList.remove('d-none');
            }
        })
        .catch(err => {
            errorBox.textContent = err.message || 'Server error. Category may already exist.';
            errorBox.classList.remove('d-none');
        })
        .finally(() => {
            saveBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    });

    // Reset error when user types
    input.addEventListener('input', function() {
        errorBox.classList.add('d-none');
    });
});
</script>