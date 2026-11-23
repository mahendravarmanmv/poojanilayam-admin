<!-- Quick Add Category Modal -->
<div class="modal fade" id="quickGodModal" tabindex="-1" aria-labelledby="quickGodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickGodModalLabel">Create New Deity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="quick_god_name">Deity Name <span class="text-danger">*</span></label>
                    <input type="text" id="quick_god_name" class="form-control" placeholder="e.g., Lord Ganesha" autocomplete="off">
					<div id="quick_god_error" class="text-danger small mt-2 d-none"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="saveQuickGodBtn">
				<span class="spinner-border spinner-border-sm me-1 d-none" id="saveGodSpinner" role="status" aria-hidden="true"></span>
				<span>Save Deity</span>
				</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const saveBtn = document.getElementById('saveQuickGodBtn');
	const input = document.getElementById('quick_god_name');
	const errorBox = document.getElementById('quick_god_error');
	const spinner = document.getElementById('saveGodSpinner');
	const modalEl = document.getElementById('quickGodModal');

    saveBtn.addEventListener('click', function () {
        const name = input.value.trim();

        if (!name) {
            errorBox.textContent = 'Please enter a deity name.';
            errorBox.classList.remove('d-none');
            input.focus();
            return;
        }

        saveBtn.disabled = true;
        spinner.classList.remove('d-none');
        errorBox.classList.add('d-none');

        fetch("{{ route('admin.gods.quickStore') }}", {
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
    if (data.success && data.god) {

        const container = document.getElementById('gods-checkbox-container');

        // Remove "No deities available" message if present
        const emptyMessage = container.querySelector('.text-muted');
        if (emptyMessage) {
            emptyMessage.remove();
        }

        // Create new deity checkbox
        const wrapper = document.createElement('div');
        wrapper.className = 'col-md-4 col-sm-6';

        const formCheck = document.createElement('div');
        formCheck.className = 'form-check';

        const checkbox = document.createElement('input');
        checkbox.className = 'form-check-input';
        checkbox.type = 'checkbox';
        checkbox.name = 'god_ids[]';
        checkbox.value = data.god.id;
        checkbox.id = 'god_' + data.god.id;
        checkbox.checked = true;

        const label = document.createElement('label');
        label.className = 'form-check-label small';
        label.htmlFor = checkbox.id;
        label.textContent = data.god.name;

        formCheck.appendChild(checkbox);
        formCheck.appendChild(label);
        wrapper.appendChild(formCheck);

        container.appendChild(wrapper);

        // Clear input
        input.value = '';

        // Close modal
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

    } else {
        errorBox.textContent = data.message || 'Error saving deity.';
        errorBox.classList.remove('d-none');
    }
	})
        .catch(err => {
            errorBox.textContent = err.message || 'Server error. Deity may already exist or an error occurred.';
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