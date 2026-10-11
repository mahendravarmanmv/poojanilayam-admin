@extends('admin.layouts.app')
@section('title', 'CMS Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Frontend Hero & Promotional Banners</h4>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $b)
                        <tr>
                            <td class="fw-bold">{{ $b->title }}</td>
                            <td><a href="{{ $b->image_url }}" target="_blank" class="small text-truncate d-inline-block" style="max-width: 180px;">{{ $b->image_url }}</a></td>
                            <td><span class="badge {{ $b->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $b->is_active ? 'Active' : 'Disabled' }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">No banners created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white py-3">
                {{ $banners->links() }}
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Create Banner</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.cms.banners.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Banner Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="E.g., Diwali Festive Pooja Booking">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Image URL / Path</label>
                        <input type="text" name="image_url" class="form-control" required placeholder="https://... or /storage/banners/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Link URL</label>
                        <input type="text" name="link_url" class="form-control" placeholder="/poojas/special-diwali">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="bannerActive" checked>
                        <label class="form-check-label small fw-semibold" for="bannerActive">Display on Homepage</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Banner</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection