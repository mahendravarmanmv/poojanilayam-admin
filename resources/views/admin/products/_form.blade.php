@php($editing = isset($product) && $product->exists)
@php($defaultPrice = $editing ? $product->prices->first() : null)
@php($defaultInventory = $editing ? $product->inventories->first() : null)
@php($currentImage = $editing ? $product->images->first() : null)
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold" for="name">Product name <span class="text-danger">*</span></label>
        <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" maxlength="191" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="sku">SKU <span class="text-danger">*</span></label>
        <input id="sku" type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" maxlength="100" required>
        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="product_category_id">Category <span class="text-danger">*</span></label>
        <select id="product_category_id" name="product_category_id" class="form-select @error('product_category_id') is-invalid @enderror" required>
            <option value="">Choose category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('product_category_id', $product->product_category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('product_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($categories->isEmpty())<small class="text-danger">No active product categories exist. Create/activate a product category before adding products.</small>@endif
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="brand">Brand</label>
        <input id="brand" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}" maxlength="191" placeholder="Optional">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="product_type">Product type</label>
        <select id="product_type" name="product_type" class="form-select">
            <option value="">Choose type</option>
            @foreach(['pooja_kit' => 'Pooja kit', 'samagri' => 'Pooja samagri', 'flower' => 'Flowers', 'idol' => 'Idol / Murti', 'prasadam' => 'Prasadam', 'holy_book' => 'Holy book', 'kumkum' => 'Kumkum', 'turmeric' => 'Turmeric', 'diya' => 'Diya / Lamp', 'incense' => 'Incense', 'rudraksha' => 'Rudraksha', 'yantra' => 'Yantra', 'book' => 'Book', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('product_type', $product->product_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="short_description">Short description</label>
        <input id="short_description" name="short_description" class="form-control @error('short_description') is-invalid @enderror" value="{{ old('short_description', $product->short_description) }}" maxlength="500">
        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="description">Full description</label>
        <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="price">Selling price (₹) <span class="text-danger">*</span></label>
        <input id="price" type="number" name="price" step="0.01" min="0" max="999999999999.99" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $defaultPrice?->amount) }}" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="compare_at_amount">MRP / compare price (₹)</label>
        <input id="compare_at_amount" type="number" name="compare_at_amount" step="0.01" min="0" class="form-control @error('compare_at_amount') is-invalid @enderror" value="{{ old('compare_at_amount', $defaultPrice?->compare_at_amount) }}">
        @error('compare_at_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="stock_quantity">Stock quantity <span class="text-danger">*</span></label>
        <input id="stock_quantity" type="number" name="stock_quantity" min="0" step="1" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', $defaultInventory?->quantity_on_hand ?? 0) }}" required>
        @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="sort_order">Display order</label>
        <input id="sort_order" type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $product->sort_order ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="unit">Unit</label>
        <input id="unit" name="unit" class="form-control" value="{{ old('unit', $product->unit) }}" maxlength="50" placeholder="e.g. pack, piece">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="weight">Weight</label>
        <input id="weight" type="number" name="weight" step="0.001" min="0" class="form-control" value="{{ old('weight', $product->weight) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="weight_unit">Weight unit</label>
        <select id="weight_unit" name="weight_unit" class="form-select">
            <option value="">Not specified</option>
            @foreach(['g' => 'Grams (g)', 'kg' => 'Kilograms (kg)', 'ml' => 'Millilitres (ml)', 'l' => 'Litres (l)'] as $value => $label)
                <option value="{{ $value }}" @selected(old('weight_unit', $product->weight_unit) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="image">Product image (JPG, PNG or WebP; max 5 MB)</label>
        <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-control @error('image') is-invalid @enderror">
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($currentImage)<div class="mt-2"><img src="{{ asset($currentImage->file_path) }}" alt="{{ $currentImage->alt_text ?: $product->name }}" style="width:90px;height:90px;object-fit:cover" class="rounded border"><small class="d-block text-muted">Uploading a new image replaces the current primary image.</small></div>@endif
    </div>
    <div class="col-md-6 d-flex flex-wrap gap-4 align-items-center pt-3">
        <div class="form-check form-switch">
            <input type="hidden" name="active" value="0">
            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" @checked(old('active', $product->exists ? $product->active : true))>
            <label class="form-check-label fw-semibold" for="active">Visible on Store</label>
        </div>
        <div class="form-check form-switch">
            <input type="hidden" name="featured" value="0">
            <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" @checked(old('featured', $product->featured ?? false))>
            <label class="form-check-label fw-semibold" for="featured">Featured product</label>
        </div>
        <div class="form-check form-switch">
            <input type="hidden" name="is_digital" value="0">
            <input class="form-check-input" type="checkbox" id="is_digital" name="is_digital" value="1" @checked(old('is_digital', $product->is_digital ?? false))>
            <label class="form-check-label" for="is_digital">Digital product</label>
        </div>
    </div>
</div>
