
@extends('admin.layouts.app')

@section('title', 'Pooja Availability')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Pooja Availability</h2>
        <p class="text-muted mb-0">Manage Pooja booking schedules and time slots.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="row g-3 mb-4">

    <div class="col-md-4 col-xl">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <small class="text-muted">Active Schedules</small>
                <h3 class="mb-0 mt-2">{{ $activeSchedulesCount }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <small class="text-muted">Generated Slots</small>
                <h3 class="mb-0 mt-2">{{ $slotSummary['total'] }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <small class="text-muted">Slots Marked Available</small>
                <h3 class="mb-0 mt-2 text-success">{{ $slotSummary['available'] }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <small class="text-muted">Fully Booked Slots</small>
                <h3 class="mb-0 mt-2 text-warning">{{ $slotSummary['fully_booked'] }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <small class="text-muted">Blocked Slots</small>
                <h3 class="mb-0 mt-2 text-danger">{{ $slotSummary['blocked'] }}</h3>
            </div>
        </div>
    </div>

</div>


{{-- Add Availability --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Add Availability Schedule</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.pooja-availability.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Select Pooja <span class="text-danger">*</span></label>
                    <select class="form-select" name="pooja_id" required>
                        <option value="">-- Select Pooja --</option>
                        @foreach($poojas as $pooja)
                            <option value="{{ $pooja->id }}"
                                {{ old('pooja_id') == $pooja->id ? 'selected' : '' }}>
                                {{ $pooja->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Schedule Type <span class="text-danger">*</span></label>
                    <select class="form-select" id="schedule_type" name="schedule_type" required>
                        <option value="recurring" {{ old('schedule_type', 'recurring') === 'recurring' ? 'selected' : '' }}>
                            Recurring Weekly
                        </option>
                        <option value="specific_date" {{ old('schedule_type') === 'specific_date' ? 'selected' : '' }}>
                            Specific Date
                        </option>
                        <option value="date_range" {{ old('schedule_type') === 'date_range' ? 'selected' : '' }}>
                            Date Range
                        </option>
                        <option value="multiple_dates" {{ old('schedule_type') === 'multiple_dates' ? 'selected' : '' }}>
                            Multiple Dates
                        </option>
                    </select>
                </div>

                <div class="col-md-4" id="dayOfWeekSection">
                    <label class="form-label">Day of Week <span class="text-danger">*</span></label>
                    <select class="form-select" name="day_of_week" id="day_of_week">
                        <option value="">Select Day</option>
                        @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $index => $day)
                            <option value="{{ $index }}" {{ old('day_of_week') == $index ? 'selected' : '' }}>
                                {{ $day }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-none" id="scheduleDateSection">
                    <label class="form-label">Schedule Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="schedule_date"
                           id="schedule_date" value="{{ old('schedule_date') }}">
                </div>

                <div class="col-md-4 d-none" id="rangeStartSection">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="range_start_date"
                           id="range_start_date" value="{{ old('range_start_date') }}">
                </div>

                <div class="col-md-4 d-none" id="rangeEndSection">
                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="range_end_date"
                           id="range_end_date" value="{{ old('range_end_date') }}">
                </div>

                <div class="col-12 d-none" id="multipleDatesSection">
                    <label class="form-label">Select Dates <span class="text-danger">*</span></label>
                    <div id="multipleDatesContainer">
                        @foreach(old('schedule_dates', ['']) as $date)
                            <div class="row g-2 mb-2 date-input-row">
                                <div class="col-md-4">
                                    <input type="date" class="form-control"
                                           name="schedule_dates[]" value="{{ $date }}">
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-outline-danger remove-date">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addDateBtn">
                        <i class="bi bi-plus-circle me-1"></i>Add Another Date
                    </button>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" name="start_time"
                           value="{{ old('start_time') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" name="end_time"
                           value="{{ old('end_time') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Slot Duration (Minutes) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="slot_duration_minutes"
                           min="1" value="{{ old('slot_duration_minutes', 60) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Capacity per Slot <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="capacity"
                           min="1" value="{{ old('capacity', 1) }}" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Add Availability
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Existing Schedules --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Availability Schedules</h5>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.pooja-availability.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <select name="pooja_id" class="form-select">
                    <option value="">All Poojas</option>
                    @foreach($poojas as $pooja)
                        <option value="{{ $pooja->id }}"
                            {{ request('pooja_id') == $pooja->id ? 'selected' : '' }}>
                            {{ $pooja->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.pooja-availability.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Pooja</th>
                        <th>Schedule</th>
                        <th>Date / Day</th>
                        <th>Time</th>
                        <th>Duration</th>
                        <th>Capacity</th>
                        <th>Generated Slots</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->id }}</td>
                            <td>{{ $schedule->pooja?->name ?? '—' }}</td>
                            <td>
                                {{ $schedule->schedule_type === 'recurring' ? 'Recurring Weekly' : 'Specific Date' }}
                            </td>
                            <td>
                                @if($schedule->schedule_type === 'recurring')
                                    {{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$schedule->day_of_week] ?? '—' }}
                                @else
                                    {{ $schedule->schedule_date?->format('d M Y') ?? '—' }}
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                                -
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                            </td>
                            <td>{{ $schedule->slot_duration_minutes }} min</td>
                            <td>{{ $schedule->capacity }}</td>
                            <td>{{ $schedule->booking_slots_count }}</td>
                            <td>
                                @if($schedule->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
							<td class="text-center">
							<div class="d-inline-flex gap-1">

							{{-- Edit --}}
							<a href="{{ route('admin.pooja-availability.edit', $schedule) }}"
							class="btn btn-sm btn-outline-primary"
							title="Edit">
							<i class="bi bi-pencil-square"></i>
							</a>

							{{-- Activate / Deactivate --}}
							<form method="POST"
							action="{{ route('admin.pooja-availability.toggle-status', $schedule) }}">
							@csrf
							@method('PATCH')

							<button type="submit"
							class="btn btn-sm btn-outline-warning"
							title="{{ $schedule->status === 'active' ? 'Deactivate' : 'Activate' }}">
							<i class="bi bi-power"></i>
							</button>
							</form>

							{{-- Delete --}}
							<form method="POST"
							action="{{ route('admin.pooja-availability.destroy', $schedule) }}"
							onsubmit="return confirm('Remove this schedule from availability? Existing booking records will be retained.')">
							@csrf
							@method('DELETE')

							<button type="submit"
							class="btn btn-sm btn-outline-danger"
							title="Remove">
							<i class="bi bi-trash"></i>
							</button>
							</form>

							{{-- View Generated Slots --}}
							<a href="{{ route('admin.pooja-availability.slots', $schedule) }}"
							class="btn btn-sm btn-outline-info"
							title="View Generated Slots">
							<i class="bi bi-calendar-week"></i>
							</a>

							</div>
							</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                No availability schedules found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $schedules->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('schedule_type');

    const sections = {
        recurring: document.getElementById('dayOfWeekSection'),
        specific: document.getElementById('scheduleDateSection'),
        rangeStart: document.getElementById('rangeStartSection'),
        rangeEnd: document.getElementById('rangeEndSection'),
        multiple: document.getElementById('multipleDatesSection')
    };

    const fields = {
        day: document.getElementById('day_of_week'),
        date: document.getElementById('schedule_date'),
        rangeStart: document.getElementById('range_start_date'),
        rangeEnd: document.getElementById('range_end_date')
    };

    function toggleField(element, field, visible) {
        element.classList.toggle('d-none', !visible);
        if (field) {
            field.required = visible;
            field.disabled = !visible;
        }
    }

    function updateScheduleFields() {
        const value = type.value;

        toggleField(sections.recurring, fields.day, value === 'recurring');
        toggleField(sections.specific, fields.date, value === 'specific_date');
        toggleField(sections.rangeStart, fields.rangeStart, value === 'date_range');
        toggleField(sections.rangeEnd, fields.rangeEnd, value === 'date_range');

        sections.multiple.classList.toggle('d-none', value !== 'multiple_dates');

        document.querySelectorAll('#multipleDatesContainer input').forEach(input => {
            input.required = value === 'multiple_dates';
            input.disabled = value !== 'multiple_dates';
        });
    }

    type.addEventListener('change', updateScheduleFields);
    updateScheduleFields();

    document.getElementById('addDateBtn').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 date-input-row';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="date" class="form-control" name="schedule_dates[]" required>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-outline-danger remove-date">Remove</button>
            </div>
        `;
        document.getElementById('multipleDatesContainer').appendChild(row);
    });

    document.getElementById('multipleDatesContainer').addEventListener('click', function (event) {
        if (event.target.closest('.remove-date')) {
            const rows = this.querySelectorAll('.date-input-row');
            if (rows.length > 1) {
                event.target.closest('.date-input-row').remove();
            }
        }
    });
});
</script>
@endsection
