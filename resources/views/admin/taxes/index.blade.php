
@extends('admin.layouts.app')

@section('title', 'Tax Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Tax Management</h3>
        <p class="text-muted mb-0">Manage taxes and module-wise assignments.</p>
    </div>

    <a href="{{ route('admin.taxes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add New Tax
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tax Name</th>
                        <th>Code</th>
                        <th>Percentage</th>
                        <th>Assigned Modules</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($taxes as $tax)
                        <tr>
                            <td>{{ $taxes->firstItem() + $loop->index }}</td>

                            <td>
                                <strong>{{ $tax->name }}</strong>
                                @if($tax->description)
                                    <div class="small text-muted">
                                        {{ $tax->description }}
                                    </div>
                                @endif
                            </td>

                            <td>{{ $tax->code }}</td>

                            <td>{{ number_format((float) $tax->percentage, 2) }}%</td>

                            <td>
                                @forelse($tax->modules as $module)
                                    <span class="badge {{ $module->is_enabled ? 'bg-success' : 'bg-secondary' }} me-1 mb-1">
                                        {{ ucfirst($module->module) }}
                                        {{ $module->is_enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                @empty
                                    <span class="text-muted small">No modules assigned</span>
                                @endforelse
                            </td>

                            <td>
                                @if($tax->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <a href="{{ route('admin.taxes.edit', $tax->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <form action="{{ route('admin.taxes.destroy', $tax->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this tax?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No taxes configured yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $taxes->links() }}
        </div>

    </div>
</div>

@endsection
