@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="mb-4">
        <a href="{{ route('admin.donation-types.index') }}" class="text-decoration-none small">&larr; Back to donation types</a>
        <h1 class="h3 mt-2 mb-1">Edit Donation Type</h1>
        <p class="text-muted mb-0">Update this purpose's details and availability.</p>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('admin.donation-types.update', $donationType) }}">
    @csrf
    @method('PUT')

    @include('admin.donation-types._form', [
        'submitLabel' => 'Save Changes'
    ])
</form>
</div>
@endsection
