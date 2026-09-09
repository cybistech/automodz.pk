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

    <div class="card p-6 space-y-4 lg:col-span-2">
        <h3 class="font-semibold">Product Images</h3>
        <p class="text-xs text-slate-500">Upload multiple images, drag to reorder, set a main image, or tap ✕ to remove a saved or newly selected image.</p>

        <div id="product-image-manager" class="space-y-4" data-primary="{{ old('primary_image', $product?->primary_image) }}">
            <div id="existing-images" class="flex flex-wrap gap-2">
                @php
                    $existingImages = old('existing_images', $product?->images ?? []);
                    $primaryImage = old('primary_image', $existingImages[0] ?? null);
                @endphp
                @foreach($existingImages as $index => $image)
                    <div class="image-card group relative w-24" draggable="true" data-path="{{ $image }}">
                        <input type="hidden" name="existing_images[]" value="{{ $image }}" class="existing-path">
                        <div class="relative h-24 w-24 overflow-hidden rounded-lg border border-slate-700 bg-slate-950">
                            <img
                                src="{{ ($product ?? null)?->imageUrl($image, true) ?? \App\Support\StorageUrl::public($image) }}"
                                alt="Product image {{ $index + 1 }}"
                                class="h-full w-full object-cover"
                                width="96"
                                height="96"
                                loading="lazy"
                                decoding="async"
                            >
                            <span class="main-badge absolute left-1 top-1 rounded bg-orange-500 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white {{ $primaryImage === $image ? '' : 'hidden' }}">Main</span>
                            <button type="button" class="remove-image absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-black/75 text-xs text-red-300 hover:bg-red-500 hover:text-white" title="Remove" aria-label="Remove image">✕</button>
                            <span class="absolute bottom-1 left-1 cursor-grab text-xs text-slate-300" title="Drag to reorder">⠿</span>
                        </div>
                        <div class="mt-1 flex items-center justify-center gap-0.5">
                            <label class="cursor-pointer rounded px-1 py-0.5 text-[10px] text-slate-400 hover:text-orange-300">
                                <input type="radio" name="primary_image" value="{{ $image }}" class="primary-radio sr-only" @checked($primaryImage === $image)>
                                Main
                            </label>
                            <button type="button" class="move-left rounded px-1 py-0.5 text-[10px] text-slate-400 hover:text-slate-200" title="Move left">←</button>
                            <button type="button" class="move-right rounded px-1 py-0.5 text-[10px] text-slate-400 hover:text-slate-200" title="Move right">→</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="no-images-hint" class="rounded-lg border border-dashed border-slate-700 px-4 py-8 text-center text-sm text-slate-500 {{ count($existingImages) ? 'hidden' : '' }}">
                No images yet. Upload one or more below.
            </div>

            <div>
                <label class="text-sm text-slate-400">Add images</label>
                <input id="new-images-input" type="file" name="images[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple class="mt-1 block w-full text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-orange-500/20 file:px-3 file:py-2 file:text-orange-300">
                <p class="mt-1 text-xs text-slate-500">JPG, PNG, GIF, or WebP up to 8MB each. Auto-compressed to lightweight WebP. First / main image is used on listings.</p>
            </div>

            @error('images')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            @error('images.*')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            @error('existing_images')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
            @error('primary_image')
                <p class="text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
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
    </div>

    <div class="card p-6 space-y-4">
        <h3 class="font-semibold">SEO Settings</h3>
        <div>
            <label class="text-sm text-slate-400">Meta Title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $product?->meta_title) }}" class="input-field mt-1" placeholder="Product title for search engines (60 chars)">
        </div>
        <div>
            <label class="text-sm text-slate-400">Meta Description</label>
            <textarea name="meta_description" class="input-field mt-1" rows="2" placeholder="Description for Google search results (160 chars)">{{ old('meta_description', $product?->meta_description) }}</textarea>
        </div>
        <div>
            <label class="text-sm text-slate-400">Meta Keywords</label>
            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $product?->meta_keywords) }}" class="input-field mt-1" placeholder="keyword1, keyword2, keyword3">
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

(function () {
    const manager = document.getElementById('product-image-manager');
    if (!manager) return;

    const list = document.getElementById('existing-images');
    const emptyHint = document.getElementById('no-images-hint');
    const fileInput = document.getElementById('new-images-input');
    const form = fileInput?.closest('form');
    let dragCard = null;
    let selectedFiles = [];
    let previewUrls = new Map();
    let syncingFiles = false;

    function cards() {
        return Array.from(list.querySelectorAll('.image-card'));
    }

    function cardPrimaryValue(card) {
        if (card.dataset.path) {
            return card.dataset.path;
        }

        if (card.dataset.pendingIndex !== undefined) {
            return `new:${card.dataset.pendingIndex}`;
        }

        return null;
    }

    function refreshEmptyState() {
        emptyHint.classList.toggle('hidden', cards().length > 0);
    }

    function refreshMainBadges() {
        const selected = manager.querySelector('.primary-radio:checked');

        cards().forEach((card) => {
            const value = cardPrimaryValue(card);
            const isMain = selected && selected.value === value;
            card.querySelector('.main-badge')?.classList.toggle('hidden', !isMain);
            const radio = card.querySelector('.primary-radio');
            if (radio) {
                radio.checked = !!isMain;
            }
        });

        if (!selected && cards().length) {
            const first = cards()[0].querySelector('.primary-radio');
            if (first) {
                first.checked = true;
                cards()[0].querySelector('.main-badge')?.classList.remove('hidden');
            }
        }
    }

    function moveCard(card, direction) {
        const siblings = cards();
        const index = siblings.indexOf(card);
        const target = siblings[index + direction];
        if (!target) return;

        if (direction < 0) {
            list.insertBefore(card, target);
        } else {
            list.insertBefore(target, card);
        }

        refreshMainBadges();
    }

    function revokePreviewUrls() {
        previewUrls.forEach((url) => URL.revokeObjectURL(url));
        previewUrls.clear();
    }

    function syncSelectedFiles() {
        if (!fileInput) return;

        const dataTransfer = new DataTransfer();
        selectedFiles.forEach((file) => dataTransfer.items.add(file));
        syncingFiles = true;
        fileInput.files = dataTransfer.files;
        syncingFiles = false;
    }

    function removePendingCards() {
        cards()
            .filter((card) => card.dataset.pendingIndex !== undefined)
            .forEach((card) => card.remove());
    }

    function rebuildPendingCards() {
        removePendingCards();
        revokePreviewUrls();

        selectedFiles.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            previewUrls.set(index, url);

            const card = document.createElement('div');
            card.className = 'image-card group relative w-24';
            card.draggable = true;
            card.dataset.pendingIndex = String(index);

            card.innerHTML = `
                <div class="relative h-24 w-24 overflow-hidden rounded-lg border border-orange-500/50 bg-slate-950">
                    <img src="${url}" alt="${file.name.replace(/"/g, '&quot;')}" class="h-full w-full object-cover" width="96" height="96">
                    <span class="main-badge absolute left-1 top-1 rounded bg-orange-500 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white hidden">Main</span>
                    <span class="absolute bottom-1 left-1 rounded bg-orange-500/80 px-1 py-0.5 text-[8px] font-semibold uppercase text-white">New</span>
                    <button type="button" class="remove-image absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-black/75 text-xs text-red-300 hover:bg-red-500 hover:text-white" title="Remove" aria-label="Remove image">✕</button>
                    <span class="absolute bottom-1 right-1 cursor-grab text-xs text-slate-300" title="Drag to reorder">⠿</span>
                </div>
                <div class="mt-1 flex items-center justify-center gap-0.5">
                    <label class="cursor-pointer rounded px-1 py-0.5 text-[10px] text-slate-400 hover:text-orange-300">
                        <input type="radio" name="primary_image" value="new:${index}" class="primary-radio sr-only">
                        Main
                    </label>
                    <button type="button" class="move-left rounded px-1 py-0.5 text-[10px] text-slate-400 hover:text-slate-200" title="Move left">←</button>
                    <button type="button" class="move-right rounded px-1 py-0.5 text-[10px] text-slate-400 hover:text-slate-200" title="Move right">→</button>
                </div>
                <p class="mt-0.5 truncate text-center text-[10px] text-slate-500">${file.name.replace(/</g, '&lt;')}</p>
            `;

            list.appendChild(card);
        });

        refreshEmptyState();
        refreshMainBadges();
    }

    list.addEventListener('click', (event) => {
        const card = event.target.closest('.image-card');
        if (!card) return;

        if (event.target.closest('.remove-image')) {
            const wasChecked = card.querySelector('.primary-radio')?.checked;

            if (card.dataset.pendingIndex !== undefined) {
                selectedFiles.splice(parseInt(card.dataset.pendingIndex, 10), 1);
                syncSelectedFiles();
                rebuildPendingCards();
            } else {
                card.remove();
            }

            if (wasChecked) {
                refreshMainBadges();
            }

            refreshEmptyState();
            return;
        }

        if (event.target.closest('.move-left')) {
            moveCard(card, -1);
            return;
        }

        if (event.target.closest('.move-right')) {
            moveCard(card, 1);
            return;
        }
    });

    list.addEventListener('change', (event) => {
        if (event.target.classList.contains('primary-radio')) {
            refreshMainBadges();
        }
    });

    list.addEventListener('dragstart', (event) => {
        dragCard = event.target.closest('.image-card');
        if (!dragCard) return;
        dragCard.classList.add('opacity-60');
        event.dataTransfer.effectAllowed = 'move';
    });

    list.addEventListener('dragend', () => {
        dragCard?.classList.remove('opacity-60');
        dragCard = null;
        refreshMainBadges();
    });

    list.addEventListener('dragover', (event) => {
        event.preventDefault();
        const over = event.target.closest('.image-card');
        if (!dragCard || !over || over === dragCard) return;
        const rect = over.getBoundingClientRect();
        const before = (event.clientX - rect.left) < rect.width / 2;
        list.insertBefore(dragCard, before ? over : over.nextSibling);
    });

    fileInput?.addEventListener('change', () => {
        if (syncingFiles) return;

        const incoming = Array.from(fileInput.files || []).filter((file) => file.size > 0);
        if (!incoming.length) return;

        selectedFiles = [...selectedFiles, ...incoming];
        fileInput.value = '';
        syncSelectedFiles();
        rebuildPendingCards();
    });

    form?.addEventListener('submit', () => {
        syncSelectedFiles();
    });

    refreshEmptyState();
    refreshMainBadges();
})();
</script>
