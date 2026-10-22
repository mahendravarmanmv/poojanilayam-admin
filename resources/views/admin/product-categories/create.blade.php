@extends('admin.layouts.app')
@section('title', 'Add Product Category')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="fw-bold mb-0">Add Product Category</h4><a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary btn-sm">Back to Categories</a></div>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST" action="{{ route('admin.product-categories.store') }}">@csrf @include('admin.product-categories._form')<div class="mt-4"><button class="btn btn-primary" type="submit">Save Category</button></div></form></div></div>
@endsection
