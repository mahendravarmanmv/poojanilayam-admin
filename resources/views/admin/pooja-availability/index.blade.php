@extends('admin.layouts.app')

@section('title', 'Pooja Availability')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Pooja Availability</h2>
        <p class="text-muted mb-0">
            Manage Muhurtham dates and time slots for Pooja bookings.
        </p>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
	@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
	@endif
	<form method="POST" action="{{ route('admin.pooja-availability.store') }}">
			@csrf
			
		@if ($errors->any())
		<div class="alert alert-danger">
		<ul class="mb-0">
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
		@endif
        <div class="mb-4">
            <label for="pooja_id" class="form-label fw-semibold">
                Select Pooja
            </label>
			
            <select class="form-select" id="pooja_id" name="pooja_id">
                <option value="">-- Select Pooja --</option>

                @foreach($poojas as $pooja)
                    <option value="{{ $pooja->id }}">
                        {{ $pooja->name }}
                    </option>
                @endforeach
            </select>

            <small class="text-muted">
                Select a pooja to manage its booking availability.
            </small>
        </div>
		<hr class="my-4">

<div id="scheduleFormSection" class="d-none">

    <h5 class="mb-3">Add Availability Schedule</h5>

    <div class="row g-3">

        {{-- Schedule Type --}}
        <div class="col-md-4">
            <label class="form-label">Schedule Type</label>
            <select class="form-select" id="schedule_type" name="schedule_type">
                <option value="recurring">Recurring Weekly</option>
                <option value="specific_date">Specific Date</option>
            </select>
        </div>

        {{-- Day of Week --}}
        <div class="col-md-4" id="dayOfWeekSection">
            <label class="form-label">Day of Week</label>
            <select class="form-select" id="day_of_week" name="day_of_week">
                <option value="">Select Day</option>
                <option value="0">Sunday</option>
                <option value="1">Monday</option>
                <option value="2">Tuesday</option>
                <option value="3">Wednesday</option>
                <option value="4">Thursday</option>
                <option value="5">Friday</option>
                <option value="6">Saturday</option>
            </select>
        </div>

        {{-- Specific Date --}}
        <div class="col-md-4 d-none" id="scheduleDateSection">
            <label class="form-label">Schedule Date</label>
            <input type="date" class="form-control" id="schedule_date" name="schedule_date">
        </div>

        {{-- Start Time --}}
        <div class="col-md-4">
            <label class="form-label">Start Time</label>
            <input type="time" class="form-control" id="start_time" name="start_time">
        </div>

        {{-- End Time --}}
        <div class="col-md-4">
            <label class="form-label">End Time</label>
            <input type="time" class="form-control" id="end_time" name="end_time">
        </div>

        {{-- Slot Duration --}}
        <div class="col-md-4">
            <label class="form-label">Slot Duration (Minutes)</label>
            <input type="number"
                   class="form-control"
                   id="slot_duration_minutes" name="slot_duration_minutes"
                   min="1"
                   value="60">
        </div>

        {{-- Capacity --}}
        <div class="col-md-4">
            <label class="form-label">Capacity per Slot</label>
            <input type="number"
                   class="form-control"
                   id="capacity" name="capacity"
                   min="1"
                   value="1">
        </div>

        {{-- Notes --}}
        <div class="col-md-8">
            <label class="form-label">Notes (Optional)</label>
            <input type="text"
                   class="form-control"
                   id="notes" name="notes"
                   placeholder="Enter any additional information">
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary" id="addScheduleBtn">
            <i class="bi bi-plus-circle me-1"></i>
            Add Schedule
        </button>
    </div>
	</form>

</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const poojaDropdown = document.getElementById('pooja_id');
    const scheduleFormSection = document.getElementById('scheduleFormSection');

    const scheduleType = document.getElementById('schedule_type');
    const dayOfWeekSection = document.getElementById('dayOfWeekSection');
    const scheduleDateSection = document.getElementById('scheduleDateSection');

    // Show schedule form when a pooja is selected
    poojaDropdown.addEventListener('change', function () {
        if (this.value) {
            scheduleFormSection.classList.remove('d-none');
        } else {
            scheduleFormSection.classList.add('d-none');
        }
    });

    // Toggle recurring weekly / specific date fields
    scheduleType.addEventListener('change', function () {

        if (this.value === 'recurring') {
            dayOfWeekSection.classList.remove('d-none');
            scheduleDateSection.classList.add('d-none');
        } else {
            dayOfWeekSection.classList.add('d-none');
            scheduleDateSection.classList.remove('d-none');
        }

    });

});
</script>
@endsection