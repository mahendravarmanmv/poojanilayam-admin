
@extends('admin.layouts.app')

@section('title', 'Edit Tax')

@section('content')

<div class="mb-4">
    <h3 class="mb-1">Edit Tax</h3>
    <p class="text-muted mb-0">Update tax details and module assignments.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <form action="{{ route('admin.taxes.update', $tax->id) }}" method="POST">
            @csrf
            @method('PUT')

            @include('admin.taxes._form', [
                'buttonText' => 'Update Tax'
            ])

        </form>

    </div>
</div>

@endsection
