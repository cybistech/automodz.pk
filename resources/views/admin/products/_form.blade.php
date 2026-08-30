@php $product = $product ?? null; @endphp
<div class="grid gap-6 lg:grid-cols-2">
    <div class="card p-6 space-y-4">
        <h3 class="font-semibold">Basic Information</h3>
        <div>
            <label class="text-sm text-slate-400">Name *</label>
            <input type="text" name="name" value="{{ old('name', $product?->name) }}" required class="input-field mt-1">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm text-slate-400">SKU *</label>
                <input type="text" name="sku" value="{{ old('sku', $product?->sku ?? '') }}" required class="input-field mt-1">
            </div>
            <div>
                <label class="text-sm text-slate-400">Category *</label>
                <select name="category_id" required class="input-field mt-1">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="text-sm text-slate-400">Brand</label>
            <input type="text" name="brand" value="{{ old('brand', $product?->brand ?? '') }}" class="input-field mt-1">
        </div>
        <div>
            <label class="text-sm text-slate-400">Short Description</label>
            <textarea name="short_description" class="input-field mt-1" rows="2">{{ old('short_description', $product?->short_description ?? '') }}</textarea>
        </div>
        <div>
            <label class="text-sm text-slate-400">Full Description</label>
            <textarea name="description" class="input-field mt-1" rows="4">{{ old('description', $product?->description ?? '') }}</textarea>
        </div>
    </div>

    <div class="card p-6 space-y-4">
        <h3 class="font-semibold">Pricing & Inventory</h3>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm text-slate-400">Price (PKR) *</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $product?->price ?? '') }}" required class="input-field mt-1">
            </div>
            <div>
                <label class="text-sm text-slate-400">Sale Price</label>
                <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product?->sale_price ?? '') }}" class="input-field mt-1">
            </div>
            <div>
                <label class="text-sm text-slate-400">Stock *</label>
                <input type="number" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" required class="input-field mt-1">
            </div>
            <div>
                <label class="text-sm text-slate-400">Condition *</label>
                <select name="condition" class="input-field mt-1">
                    @foreach(['new', 'used', 'refurbished'] as $cond)
                        <option value="{{ $cond }}" @selected(old('condition', $product?->condition ?? 'new') === $cond)>{{ ucfirst($cond) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm text-slate-400">Part Number</label>
                <input type="text" name="part_number" value="{{ old('part_number', $product?->part_number ?? '') }}" class="input-field mt-1">
            </div>
            <div>
                <label class="text-sm text-slate-400">Warranty</label>
                <input type="text" name="warranty" value="{{ old('warranty', $product?->warranty ?? '') }}" class="input-field mt-1">
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm text-slate-400">Vehicle Make</label>
                <input type="text" name="vehicle_make" value="{{ old('vehicle_make', $product?->vehicle_make ?? '') }}" class="input-field mt-1">
            </div>
            <div>
                <label class="text-sm text-slate-400">Vehicle Model</label>
                <input type="text" name="vehicle_model" value="{{ old('vehicle_model', $product?->vehicle_model ?? '') }}" class="input-field mt-1">
            </div>
            <div>
                <label class="text-sm text-slate-400">Year From</label>
                <input type="text" name="vehicle_year_from" value="{{ old('vehicle_year_from', $product?->vehicle_year_from ?? '') }}" class="input-field mt-1">
            </div>
            <div>
                <label class="text-sm text-slate-400">Year To</label>
                <input type="text" name="vehicle_year_to" value="{{ old('vehicle_year_to', $product?->vehicle_year_to ?? '') }}" class="input-field mt-1">
            </div>
        </div>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product?->is_featured ?? false)) class="rounded text-orange-500"><span class="text-sm">Featured Product</span></label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true)) class="rounded text-orange-500"><span class="text-sm">Active</span></label>
    </div>

    <div class="card p-6 space-y-4">
        <h3 class="font-semibold">Media</h3>
        <div>
            <label class="text-sm text-slate-400">Product Images</label>
            <input type="file" name="images[]" accept="image/*" multiple class="mt-1 text-sm text-slate-400">
            @if(isset($product) && $product?->images)
                <div class="mt-2 flex gap-2">
                    @foreach($product?->images as $image)
                        <img src="{{ asset('storage/'.$image) }}" class="h-16 rounded object-cover">
                    @endforeach
                </div>
            @endif
        </div>
        <div>
            <label class="text-sm text-slate-400">Video URL (YouTube/Vimeo)</label>
            <input type="url" name="video_url" value="{{ old('video_url', $product?->video_url ?? '') }}" class="input-field mt-1" placeholder="https://youtube.com/watch?v=...">
        </div>
        <div>
            <label class="text-sm text-slate-400">Or Upload Video (MP4, max 50MB)</label>
            <input type="file" name="video_file" accept="video/mp4,video/webm,video/quicktime" class="mt-1 text-sm text-slate-400">
            @if(isset($product) && $product?->video_path)
                <p class="mt-1 text-xs text-green-400">Video uploaded</p>
            @endif
        </div>
    </div>

    <div class="card p-6 space-y-4">
        <h3 class="font-semibold">Specifications</h3>
        <div id="specs-container" class="space-y-2">
            @php $specs = old('spec_keys') ? array_combine(old('spec_keys'), old('spec_values')) : ($product?->specifications ?? ['Material' => '', 'Dimensions' => '']); @endphp
            @foreach($specs as $key => $value)
                <div class="flex gap-2">
                    <input type="text" name="spec_keys[]" value="{{ $key }}" placeholder="Key" class="input-field">
                    <input type="text" name="spec_values[]" value="{{ $value }}" placeholder="Value" class="input-field">
                </div>
            @endforeach
        </div>
        <button type="button" onclick="addSpec()" class="btn-secondary text-xs">+ Add Specification</button>
    </div>
</div>

<script>
function addSpec() {
    const container = document.getElementById('specs-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = '<input type="text" name="spec_keys[]" placeholder="Key" class="input-field"><input type="text" name="spec_values[]" placeholder="Value" class="input-field">';
    container.appendChild(div);
}
</script>
