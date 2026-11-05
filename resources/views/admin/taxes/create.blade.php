
@extends('admin.layouts.app')

@section('title', 'Add Tax')

@section('content')

<div class="mb-4">
    <h3 class="mb-1">Add New Tax</h3>
    <p class="text-muted mb-0">Create a tax and assign it to applicable modules.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <form action="{{ route('admin.taxes.store') }}" method="POST">
            @csrf

            @include('admin.taxes._form', [
                'buttonText' => 'Save Tax'
            ])

        </form>

    </div>
</div>

@endsection
