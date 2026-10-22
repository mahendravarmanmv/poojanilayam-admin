@extends('admin.layouts.app')
@section('title', 'Edit Product Category')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="fw-bold mb-0">Edit Category: {{ $category->name }}</h4><a href="{{ route('admin.product-categories.index') }}" class="btn btn-outline-secondary btn-sm">Back to Categories</a></div>
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form method="POST" action="{{ route('admin.product-categories.update', $category) }}">@csrf @method('PUT') @include('admin.product-categories._form')<div class="mt-4"><button class="btn btn-primary" type="submit">Update Category</button></div></form></div></div>
@endsection
