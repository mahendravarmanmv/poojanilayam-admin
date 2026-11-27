
@extends('admin.layouts.app')

@section('title', 'Generated Booking Slots')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Generated Booking Slots</h2>
        <p class="text-muted mb-0">
            Pooja: <strong>{{ $schedule->pooja?->name ?? '—' }}</strong>
        </p>
    </div>

    <a href="{{ route('admin.pooja-availability.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Schedules
    </a>
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

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted d-block">Schedule Type</small>
                <strong>
                    {{ $schedule->schedule_type === 'recurring' ? 'Recurring Weekly' : 'Specific Date' }}
                </strong>
            </div>

            <div class="col-md-3">
                <small class="text-muted d-block">Schedule Time</small>
                <strong>
                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                    -
                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                </strong>
            </div>

            <div class="col-md-3">
                <small class="text-muted d-block">Slot Duration</small>
                <strong>{{ $schedule->slot_duration_minutes }} minutes</strong>
            </div>

            <div class="col-md-3">
                <small class="text-muted d-block">Capacity per Slot</small>
                <strong>{{ $schedule->capacity }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Booking Slots</h5>
    </div>

    <div class="card-body">

        <form method="GET"
              action="{{ route('admin.pooja-availability.slots', $schedule) }}"
              class="row g-3 mb-4">

            <div class="col-md-3">
                <label class="form-label">Slot Date</label>
                <input type="date"
                       name="date"
                       class="form-control"
                       value="{{ request('date') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Slot Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>
                        Available
                    </option>
                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>
                        Blocked
                    </option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('admin.pooja-availability.slots', $schedule) }}"
                   class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Capacity</th>
                        <th>Booked</th>
                        <th>Active Locks</th>
                        <th>Remaining</th>
                        <th>Status</th>
						<th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($slots as $slot)
                        @php
                            $remaining = max(
                                0,
                                $slot->capacity
                                - $slot->booked_count
                                - $slot->active_locks_count
                            );
                        @endphp

                        <tr>
                            <td>{{ $slots->firstItem() + $loop->index }}</td>
                            <td>{{ $slot->slot_date->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</td>
                            <td>{{ $slot->capacity }}</td>
                            <td>{{ $slot->booked_count }}</td>
                            <td>{{ $slot->active_locks_count }}</td>
                            <td>
                                <strong>{{ $remaining }}</strong>
                            </td>
                            <td>
                                @if($slot->status === 'blocked')
                                    <span class="badge bg-danger">Blocked</span>
                                @elseif($remaining === 0)
                                    <span class="badge bg-warning text-dark">Fully Booked</span>
                                @else
                                    <span class="badge bg-success">Available</span>
                                @endif
                            </td>
							<td class="text-center">
							@if($slot->status === 'blocked')
							<form method="POST"
							action="{{ route('admin.pooja-availability.slots.unblock', [
							'schedule' => $schedule->id,
							'slot' => $slot->id
							]) }}"
							onsubmit="return confirm('Unblock this slot?')">
							@csrf
							@method('PATCH')

							<button type="submit" class="btn btn-sm btn-outline-success">
							<i class="bi bi-unlock me-1"></i>Unblock
							</button>
							</form>
							@elseif($slot->booked_count == 0 && $slot->bookings_count == 0 && $slot->active_locks_count == 0)
							<form method="POST"
							action="{{ route('admin.pooja-availability.slots.block', [
							'schedule' => $schedule->id,
							'slot' => $slot->id
							]) }}"
							onsubmit="return confirm('Block this slot? Customers will not be able to book it.')">
							@csrf
							@method('PATCH')

							<button type="submit" class="btn btn-sm btn-outline-danger">
							<i class="bi bi-lock me-1"></i>Block
							</button>
							</form>
							@else
							<button type="button"
							class="btn btn-sm btn-outline-secondary"
							disabled
							title="This slot has bookings or active locks">
							<i class="bi bi-lock-fill me-1"></i>Cannot Block
							</button>
							@endif
							</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                No generated slots found for this schedule.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $slots->links() }}
        </div>
    </div>
</div>

@endsection
