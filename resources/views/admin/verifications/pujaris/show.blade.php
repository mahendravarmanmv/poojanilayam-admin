@extends('admin.layouts.app')
@section('title', 'Pujari Dossier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.verifications.pujaris.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Back to Verification List</a>
        <h4 class="fw-bold mb-0">Dossier: {{ $pujari->user->name ?? 'Pujari #'.$pujari->id }}</h4>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2">{{ ucfirst($pujari->verification_status) }}</span>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <!-- Profile & Languages -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Background & Skills</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Experience</label>
                        <div class="fw-bold">{{ $pujari->experience_years ?? 0 }} Years</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Temple Affiliation</label>
                        <div class="fw-bold">{{ $pujari->templeAssignments->first()->temple->name ?? 'Independent Freelance' }}</div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Spoken Languages & Ritual Dialects</label>
                        <div>
                            @forelse($pujari->languages as $lang)
                                <span class="badge bg-light text-dark border me-1">{{ $lang->language_name ?? $lang->name }}</span>
                            @empty
                                <span class="text-muted small">No languages indicated.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
                </div>

        <!-- Priest Availability Management -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-calendar-check me-2 text-primary"></i>
                    Priest Availability Management
                </h6>
            </div>

            <div class="card-body">

                <form action="{{ route('admin.verifications.pujaris.availability.store', $pujari) }}"
                      method="POST">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Availability Type</label>
                            <select name="availability_type" id="availability_type" class="form-select" required>
                                <option value="recurring">Recurring (Weekly)</option>
                                <option value="specific">Specific Date</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="availability_day_field">
                            <label class="form-label small fw-semibold">Day of Week</label>
                            <select name="day_of_week" class="form-select">
                                <option value="">Select Day</option>
                                @foreach([
                                    0 => 'Sunday',
                                    1 => 'Monday',
                                    2 => 'Tuesday',
                                    3 => 'Wednesday',
                                    4 => 'Thursday',
                                    5 => 'Friday',
                                    6 => 'Saturday'
                                ] as $day => $dayName)
                                    <option value="{{ $day }}">{{ $dayName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 d-none" id="availability_date_field">
                            <label class="form-label small fw-semibold">Select Date</label>
                            <input type="date" name="availability_date"
                                   class="form-control"
                                   min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Start Time</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">End Time</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Timezone (Optional)</label>
                            <input type="text" name="timezone" class="form-control"
                                   placeholder="Asia/Kolkata">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Notes (Optional)</label>
                            <textarea name="notes" rows="2" class="form-control"
                                      placeholder="Additional availability details"></textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Add Availability
                            </button>
                        </div>

                    </div>
                                </form>

                <hr class="my-4">

                <h6 class="fw-bold mb-3">Existing Availability Schedule</h6>

                @php
                    $days = [
                        0 => 'Sunday',
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                    ];
                @endphp

                @forelse($pujari->availabilities->sortBy('day_of_week') as $availability)

                    <div class="border rounded p-3 mb-3">

                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">

                            <div>
                                <div class="fw-bold">
                                    @if($availability->availability_type === 'specific')
                                        {{ $availability->availability_date?->format('d M Y') ?? 'Specific Date' }}
                                    @else
                                        {{ $days[$availability->day_of_week] ?? 'Unknown Day' }}
                                        <span class="badge bg-light text-dark border">Weekly</span>
                                    @endif
                                </div>

                                <div class="text-muted small mt-1">
                                    {{ \Carbon\Carbon::parse($availability->start_time)->format('h:i A') }}
                                    -
                                    {{ \Carbon\Carbon::parse($availability->end_time)->format('h:i A') }}
                                </div>

                                <div class="mt-2">
                                    @if($availability->status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @else
                                        <span class="badge bg-secondary">Unavailable</span>
                                    @endif
                                </div>

                                @if($availability->notes)
                                    <div class="small text-muted mt-2">
                                        {{ $availability->notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex gap-2">

                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#editAvailability{{ $availability->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>

                                <form action="{{ route('admin.verifications.pujaris.availability.destroy', [$pujari, $availability]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this availability?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>

                            </div>
                        </div>

                        <!-- Edit Availability Form -->
                        <div class="collapse mt-3" id="editAvailability{{ $availability->id }}">

                            <div class="bg-light rounded p-3">

                                <form action="{{ route('admin.verifications.pujaris.availability.update', [$pujari, $availability]) }}"
                                      method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Availability Type</label>
                                            <select name="availability_type"
                                                    class="form-select edit-availability-type"
                                                    data-id="{{ $availability->id }}"
                                                    required>
                                                <option value="recurring" {{ $availability->availability_type === 'recurring' ? 'selected' : '' }}>Recurring (Weekly)</option>
                                                <option value="specific" {{ $availability->availability_type === 'specific' ? 'selected' : '' }}>Specific Date</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 edit-day-field"
                                             id="edit_day_field_{{ $availability->id }}">
                                            <label class="form-label small fw-semibold">Day of Week</label>
                                            <select name="day_of_week" class="form-select">
                                                @foreach($days as $day => $dayName)
                                                    <option value="{{ $day }}"
                                                        {{ (int) $availability->day_of_week === $day ? 'selected' : '' }}>
                                                        {{ $dayName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 edit-date-field {{ $availability->availability_type === 'specific' ? '' : 'd-none' }}"
                                             id="edit_date_field_{{ $availability->id }}">
                                            <label class="form-label small fw-semibold">Select Date</label>
                                            <input type="date"
                                                   name="availability_date"
                                                   class="form-control"
                                                   value="{{ $availability->availability_date?->format('Y-m-d') }}"
                                                   min="{{ date('Y-m-d') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Start Time</label>
                                            <input type="time" name="start_time" class="form-control"
                                                   value="{{ \Carbon\Carbon::parse($availability->start_time)->format('H:i') }}"
                                                   required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">End Time</label>
                                            <input type="time" name="end_time" class="form-control"
                                                   value="{{ \Carbon\Carbon::parse($availability->end_time)->format('H:i') }}"
                                                   required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="available" {{ $availability->status === 'available' ? 'selected' : '' }}>Available</option>
                                                <option value="unavailable" {{ $availability->status === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Timezone</label>
                                            <input type="text" name="timezone" class="form-control"
                                                   value="{{ $availability->timezone }}"
                                                   placeholder="Asia/Kolkata">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Notes</label>
                                            <textarea name="notes" rows="2" class="form-control">{{ $availability->notes }}</textarea>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-check2 me-1"></i> Update Availability
                                            </button>
                                        </div>

                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>

                @empty
                    <div class="text-center text-muted py-4 border rounded">
                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                        No availability schedule added yet.
                    </div>
                @endforelse

            </div>
        </div>

        <!-- Documents / ID Proofs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Uploaded Verification Documents</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($pujari->documents as $doc)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-bold">{{ $doc->document_type ?? 'Government ID' }}</div>
                                <small class="text-muted">{{ $doc->document_number ?? 'Doc #'.$doc->id }}</small>
                            </div>
                            <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right me-1"></i> View Document
                            </a>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No digital documents uploaded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Decision Form -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-shield-check me-2 text-primary"></i>Verification Decision</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.verifications.pujaris.update-status', $pujari) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Decision</label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending', 'verified', 'rejected', 'suspended'] as $st)
                                <option value="{{ $st }}" {{ $pujari->verification_status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Audit Remarks / Justification</label>
                        <textarea name="remarks" rows="4" class="form-control" placeholder="E.g., Government documents and certificates verified over phone interview." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Submit Decision</button>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('availability_type');
        const dayField = document.getElementById('availability_day_field');
        const dateField = document.getElementById('availability_date_field');

        function toggleAvailabilityFields() {
            if (typeSelect.value === 'specific') {
                dayField.classList.add('d-none');
                dateField.classList.remove('d-none');
            } else {
                dayField.classList.remove('d-none');
                dateField.classList.add('d-none');
            }
        }

        typeSelect.addEventListener('change', toggleAvailabilityFields);
        toggleAvailabilityFields();
    });
	
	        // Toggle fields in existing availability edit forms
        document.querySelectorAll('.edit-availability-type').forEach(function (select) {

            function toggleEditFields() {
                const id = select.dataset.id;
                const dayField = document.getElementById('edit_day_field_' + id);
                const dateField = document.getElementById('edit_date_field_' + id);

                if (select.value === 'specific') {
                    dayField.classList.add('d-none');
                    dateField.classList.remove('d-none');
                } else {
                    dayField.classList.remove('d-none');
                    dateField.classList.add('d-none');
                }
            }

            select.addEventListener('change', toggleEditFields);
            toggleEditFields();
        });
</script>
@endpush

@endsection