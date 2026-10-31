
@extends('admin.layouts.app')

@section('title', 'Manage Priest Availability')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Priest Availability Management</h4>
        <p class="text-muted mb-0">
            Manage availability for {{ $pujari->user->name ?? 'Priest' }}
        </p>
    </div>

    <a href="{{ route('admin.verifications.pujaris.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Priests
    </a>
</div>

{{-- Add Availability --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Add Availability</h5>
    </div>

    <div class="card-body">
        <form method="POST"
              action="{{ route('admin.verifications.pujaris.availability.store', $pujari->id) }}">
            @csrf

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Availability Type *</label>
                    <select name="availability_type"
                            id="availabilityType"
                            class="form-select"
                            required>
                        <option value="specific">Specific Date</option>
                        <option value="recurring">Recurring Weekly</option>
                    </select>
                </div>

                <div class="col-md-4" id="specificDateField">
                    <label class="form-label">Select Date *</label>
                    <input type="date"
                           name="availability_date"
                           class="form-control"
                           min="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-4 d-none" id="recurringDayField">
                    <label class="form-label">Select Day *</label>
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
                        ] as $day => $label)
                            <option value="{{ $day }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Start Time *</label>
                    <input type="time" name="start_time"
                           class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">End Time *</label>
                    <input type="time" name="end_time"
                           class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Timezone</label>
                    <input type="text" name="timezone"
                           class="form-control"
                           value="Asia/Kolkata">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control"
                              rows="2"
                              placeholder="Optional notes"></textarea>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Save Availability
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Existing Availability --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Existing Availability</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Type</th>
                    <th>Date / Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Timezone</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($availabilities as $availability)
                    <tr>
                        <td>
                            {{ ucfirst($availability->availability_type) }}
                        </td>

                        <td>
                            @if($availability->availability_type === 'specific')
                                {{ $availability->availability_date }}
                            @else
                                {{ [
                                    'Sunday', 'Monday', 'Tuesday',
                                    'Wednesday', 'Thursday', 'Friday', 'Saturday'
                                ][$availability->day_of_week] ?? 'N/A' }}
                            @endif
                        </td>

                        <td>{{ \Carbon\Carbon::parse($availability->start_time)->format('h:i A') }}</td>
                        <td>{{ \Carbon\Carbon::parse($availability->end_time)->format('h:i A') }}</td>

                        <td>
                            <span class="badge {{ $availability->status === 'available' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($availability->status) }}
                            </span>
                        </td>

                        <td>{{ $availability->timezone }}</td>
                        <td>{{ $availability->notes ?: '-' }}</td>

                        <td>
                            <form method="POST"
                                  action="{{ route('admin.verifications.pujaris.availability.destroy', [$pujari->id, $availability->id]) }}"
                                  onsubmit="return confirm('Delete this availability?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No availability added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('availabilityType');
    const dateField = document.getElementById('specificDateField');
    const dayField = document.getElementById('recurringDayField');

    const dateInput = dateField.querySelector('input');
    const dayInput = dayField.querySelector('select');

    function toggleAvailabilityFields() {
        const isSpecific = type.value === 'specific';

        dateField.classList.toggle('d-none', !isSpecific);
        dayField.classList.toggle('d-none', isSpecific);

        dateInput.required = isSpecific;
        dayInput.required = !isSpecific;
    }

    type.addEventListener('change', toggleAvailabilityFields);
    toggleAvailabilityFields();
});
</script>
@endpush
