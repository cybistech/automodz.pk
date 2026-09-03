@php $category = $category ?? null; @endphp
<div class="space-y-4">
    <div>
        <label class="text-sm text-slate-400">Name</label>
        <input type="text" name="name" value="{{ old('name', $category?->name) }}" required class="input-field mt-1">
    </div>
    <div>
        <label class="text-sm text-slate-400">Description</label>
        <textarea name="description" class="input-field mt-1" rows="3">{{ old('description', $category?->description) }}</textarea>
    </div>
    <div>
        <label class="text-sm text-slate-400">Image</label>
        <input type="file" name="image" accept="image/*" class="mt-1 text-sm text-slate-400">
        @if($category?->image)
            <img src="{{ $category->imageUrl() }}" alt="" class="mt-2 h-20 rounded-lg">
        @endif
    </div>
    <div>
        <label class="text-sm text-slate-400">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="input-field mt-1 w-32">
    </div>
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true)) class="rounded text-orange-500">
        <span class="text-sm">Active</span>
    </label>
</div>
