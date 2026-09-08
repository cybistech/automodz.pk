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
        <input id="category-image-input" type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-orange-500/20 file:px-3 file:py-2 file:text-orange-300">
        <div id="category-selected-preview" class="mt-2 hidden">
            <div class="relative inline-block">
                <img id="category-selected-image" src="" alt="Selected image" class="h-16 w-16 rounded-lg object-cover" width="64" height="64">
                <button type="button" id="category-clear-selected" class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-black/80 text-[10px] text-red-300 hover:bg-red-500 hover:text-white" title="Remove selected image" aria-label="Remove selected image">✕</button>
            </div>
        </div>
        @if($category?->image)
            <div id="category-existing-image" class="mt-2 flex items-center gap-2">
                <img src="{{ $category->imageUrl() }}" alt="" class="h-16 w-16 rounded-lg object-cover" width="64" height="64">
                <label class="text-xs text-slate-400">
                    <input type="checkbox" name="remove_image" value="1" class="rounded text-orange-500">
                    Remove current image
                </label>
            </div>
        @endif
    </div>
    <script>
    (function () {
        const input = document.getElementById('category-image-input');
        const preview = document.getElementById('category-selected-preview');
        const image = document.getElementById('category-selected-image');
        const clearBtn = document.getElementById('category-clear-selected');
        const existing = document.getElementById('category-existing-image');
        let previewUrl = null;

        if (!input || !preview || !image || !clearBtn) return;

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) {
                clearBtn.click();
                return;
            }

            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = URL.createObjectURL(file);
            image.src = previewUrl;
            preview.classList.remove('hidden');
            existing?.classList.add('hidden');
        });

        clearBtn.addEventListener('click', () => {
            input.value = '';
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = null;
            image.removeAttribute('src');
            preview.classList.add('hidden');
            existing?.classList.remove('hidden');
        });
    })();
    </script>
    <div>
        <label class="text-sm text-slate-400">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="input-field mt-1 w-32">
    </div>
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true)) class="rounded text-orange-500">
        <span class="text-sm">Active</span>
    </label>
</div>
