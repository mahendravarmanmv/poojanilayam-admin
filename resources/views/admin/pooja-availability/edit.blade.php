
@extends('admin.layouts.app')

@section('title', 'Edit Pooja Availability')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Edit Availability</h2>
        <p class="text-muted mb-0">
            Update the schedule for {{ $schedule->pooja?->name }}.
        </p>
    </div>

    <a href="{{ route('admin.pooja-availability.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pooja-availability.update', $schedule) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Pooja</label>
                    <input type="text" class="form-control"
                           value="{{ $schedule->pooja?->name }}" disabled>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Schedule Type</label>
                    <select name="schedule_type" id="edit_schedule_type" class="form-select" required>
                        <option value="recurring"
                            {{ old('schedule_type', $schedule->schedule_type) === 'recurring' ? 'selected' : '' }}>
                            Recurring Weekly
                        </option>
                        <option value="specific"
                            {{ old('schedule_type', $schedule->schedule_type) === 'specific' ? 'selected' : '' }}>
                            Specific Date
                        </option>
                    </select>
                </div>

                <div class="col-md-6" id="editDaySection">
                    <label class="form-label">Day of Week</label>
                    <select name="day_of_week" id="edit_day_of_week" class="form-select">
                        <option value="">Select Day</option>
                        @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $index => $day)
                            <option value="{{ $index }}"
                                {{ old('day_of_week', $schedule->day_of_week) == $index ? 'selected' : '' }}>
                                {{ $day }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 d-none" id="editDateSection">
                    <label class="form-label">Schedule Date</label>
                    <input type="date" name="schedule_date" id="edit_schedule_date"
                           class="form-control"
                           value="{{ old('schedule_date', $schedule->schedule_date?->format('Y-m-d')) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" class="form-control"
                           value="{{ old('start_time', substr($schedule->start_time, 0, 5)) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time" class="form-control"
                           value="{{ old('end_time', substr($schedule->end_time, 0, 5)) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Slot Duration (Minutes)</label>
                    <input type="number" name="slot_duration_minutes" min="1"
                           class="form-control"
                           value="{{ old('slot_duration_minutes', $schedule->slot_duration_minutes) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Capacity per Slot</label>
                    <input type="number" name="capacity" min="1"
                           class="form-control"
                           value="{{ old('capacity', $schedule->capacity) }}" required>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $schedule->notes) }}</textarea>
                </div>
            </div>

            <div class="alert alert-warning mt-4 mb-0">
                <strong>Note:</strong> A schedule with booking or lock history cannot be edited.
                Deactivate it and create a new schedule instead.
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Update Schedule
                </button>
                <a href="{{ route('admin.pooja-availability.index') }}"
                   class="btn btn-light ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('edit_schedule_type');
    const daySection = document.getElementById('editDaySection');
    const dateSection = document.getElementById('editDateSection');
    const dayField = document.getElementById('edit_day_of_week');
    const dateField = document.getElementById('edit_schedule_date');

    function updateFields() {
        const recurring = type.value === 'recurring';

        daySection.classList.toggle('d-none', !recurring);
        dateSection.classList.toggle('d-none', recurring);

        dayField.required = recurring;
        dayField.disabled = !recurring;

        dateField.required = !recurring;
        dateField.disabled = recurring;
    }

    type.addEventListener('change', updateFields);
    updateFields();
});
</script>
@endsection
