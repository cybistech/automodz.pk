@php
    use App\Support\UploadLimits;

    $existingImages = old('existing_images', $product?->images ?? []);
    $primaryImage = old('primary_image', $existingImages[0] ?? null);
    $maxUploadMb = UploadLimits::effectiveMaxUploadMb();
    $maxUploadBytes = UploadLimits::effectiveMaxUploadKb() * 1024;
@endphp

<div class="card p-6 space-y-4 lg:col-span-2">
    <h3 class="font-semibold">Product Images</h3>
    <p class="text-xs text-slate-500">
        Upload JPG, PNG, GIF, or WebP (max {{ $maxUploadMb }}MB each). Images are auto-compressed to WebP.
        Drag to reorder, set a main image, or remove with ✕.
    </p>

    <div
        id="product-image-manager"
        class="space-y-4"
        data-primary="{{ $primaryImage }}"
        data-max-bytes="{{ $maxUploadBytes }}"
        data-max-mb="{{ $maxUploadMb }}"
    >
        {{-- Saved images --}}
        <div id="existing-images" class="flex flex-wrap gap-2">
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
            No images yet. Drop files below or click to browse.
        </div>

        {{-- Drop zone + file input --}}
        <div
            id="image-drop-zone"
            class="relative rounded-lg border-2 border-dashed border-slate-600 bg-slate-900/50 px-4 py-6 text-center transition-colors hover:border-orange-500/50 hover:bg-slate-900"
        >
            <input
                id="new-images-input"
                type="file"
                name="images[]"
                accept="image/jpeg,image/png,image/gif,image/webp"
                multiple
                class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
            >
            <p class="text-sm text-slate-300">
                <span class="font-medium text-orange-400">Click to browse</span> or drag images here
            </p>
            <p class="mt-1 text-xs text-slate-500">Up to 20 images, {{ $maxUploadMb }}MB each (total request limit: {{ \App\Support\UploadLimits::humanPostMax() }})</p>
        </div>

        {{-- New upload previews --}}
        <div id="new-images-preview" class="flex flex-wrap gap-2"></div>

        {{-- Client-side validation errors --}}
        <div id="image-client-errors" class="hidden rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300"></div>

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

<script>
(function () {
    const manager = document.getElementById('product-image-manager');
    if (!manager) return;

    const list = document.getElementById('existing-images');
    const emptyHint = document.getElementById('no-images-hint');
    const dropZone = document.getElementById('image-drop-zone');
    const fileInput = document.getElementById('new-images-input');
    const preview = document.getElementById('new-images-preview');
    const clientErrors = document.getElementById('image-client-errors');
    const form = manager.closest('form');

    const MAX_BYTES = parseInt(manager.dataset.maxBytes, 10) || (2 * 1024 * 1024);
    const MAX_MB = parseFloat(manager.dataset.maxMb) || 2;
    const MAX_FILES = 20;
    const ACCEPTED = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    let dragCard = null;
    let pendingFiles = [];
    let previewUrls = [];

    function existingCount() {
        return list.querySelectorAll('.image-card').length;
    }

    function totalCount() {
        return existingCount() + pendingFiles.length;
    }

    function cards() {
        return Array.from(list.querySelectorAll('.image-card'));
    }

    function refreshEmptyState() {
        emptyHint.classList.toggle('hidden', totalCount() > 0);
    }

    function refreshMainBadges() {
        const selected = manager.querySelector('.primary-radio:checked');
        cards().forEach((card) => {
            const path = card.dataset.path;
            const isMain = selected && selected.value === path;
            card.querySelector('.main-badge')?.classList.toggle('hidden', !isMain);
            const radio = card.querySelector('.primary-radio');
            if (radio) radio.checked = !!isMain;
        });

        if (!selected && cards().length) {
            const first = cards()[0].querySelector('.primary-radio');
            if (first) {
                first.checked = true;
                cards()[0].querySelector('.main-badge')?.classList.remove('hidden');
            }
        }
    }

    function showClientError(messages) {
        if (!messages.length) {
            clientErrors.classList.add('hidden');
            clientErrors.innerHTML = '';
            return;
        }
        clientErrors.innerHTML = messages.map((m) => `<p>${m}</p>`).join('');
        clientErrors.classList.remove('hidden');
    }

    function validateFile(file) {
        const errors = [];
        if (!ACCEPTED.includes(file.type)) {
            errors.push(`"${file.name}" is not a supported image type.`);
        }
        if (file.size > MAX_BYTES) {
            errors.push(`"${file.name}" is too large (${formatSize(file.size)}). Max is ${MAX_MB}MB.`);
        }
        return errors;
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function revokePreviewUrls() {
        previewUrls.forEach((url) => URL.revokeObjectURL(url));
        previewUrls = [];
    }

    function applyFilesToInput() {
        if (!fileInput || typeof DataTransfer === 'undefined') return;

        const dt = new DataTransfer();
        pendingFiles.forEach((file) => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function renderNewPreviews() {
        revokePreviewUrls();
        preview.innerHTML = '';

        pendingFiles.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            previewUrls.push(url);

            const item = document.createElement('div');
            item.className = 'relative w-24';
            item.dataset.index = String(index);

            const wrap = document.createElement('div');
            wrap.className = 'relative h-24 w-24 overflow-hidden rounded-lg border border-orange-500/40 bg-slate-950';

            const badge = document.createElement('span');
            badge.className = 'absolute left-1 top-1 rounded bg-blue-500 px-1.5 py-0.5 text-[9px] font-bold uppercase text-white';
            badge.textContent = 'New';

            const img = document.createElement('img');
            img.src = url;
            img.alt = file.name;
            img.className = 'h-full w-full object-cover';
            img.width = 96;
            img.height = 96;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-black/75 text-xs text-red-300 hover:bg-red-500 hover:text-white';
            removeBtn.title = 'Remove';
            removeBtn.setAttribute('aria-label', 'Remove selected image');
            removeBtn.textContent = '✕';
            removeBtn.addEventListener('click', () => {
                pendingFiles.splice(index, 1);
                applyFilesToInput();
                renderNewPreviews();
                refreshEmptyState();
            });

            const name = document.createElement('p');
            name.className = 'mt-1 truncate text-[10px] text-slate-500';
            name.textContent = file.name + ' (' + formatSize(file.size) + ')';

            wrap.appendChild(img);
            wrap.appendChild(badge);
            wrap.appendChild(removeBtn);
            item.appendChild(wrap);
            item.appendChild(name);
            preview.appendChild(item);
        });
    }

    function addFiles(incoming) {
        const errors = [];
        const existingNames = new Set(pendingFiles.map((f) => f.name + f.size));

        incoming.forEach((file) => {
            errors.push(...validateFile(file));

            const key = file.name + file.size;
            if (existingNames.has(key)) return;

            if (totalCount() >= MAX_FILES) {
                errors.push(`Maximum ${MAX_FILES} images allowed.`);
                return;
            }

            pendingFiles.push(file);
            existingNames.add(key);
        });

        showClientError([...new Set(errors)]);
        applyFilesToInput();
        renderNewPreviews();
        refreshEmptyState();
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

    list.addEventListener('click', (event) => {
        const card = event.target.closest('.image-card');
        if (!card) return;

        if (event.target.closest('.remove-image')) {
            const wasChecked = card.querySelector('.primary-radio')?.checked;
            card.remove();
            if (wasChecked) refreshMainBadges();
            refreshEmptyState();
            return;
        }

        if (event.target.closest('.move-left')) {
            moveCard(card, -1);
            return;
        }

        if (event.target.closest('.move-right')) {
            moveCard(card, 1);
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
        const incoming = Array.from(fileInput.files || []);
        if (!incoming.length) return;
        addFiles(incoming);
        fileInput.value = '';
    });

    dropZone?.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('border-orange-500', 'bg-slate-900');
    });

    dropZone?.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-orange-500', 'bg-slate-900');
    });

    dropZone?.addEventListener('drop', (event) => {
        event.preventDefault();
        dropZone.classList.remove('border-orange-500', 'bg-slate-900');
        const incoming = Array.from(event.dataTransfer?.files || []).filter((f) => f.type.startsWith('image/'));
        if (incoming.length) addFiles(incoming);
    });

    form?.addEventListener('submit', (event) => {
        applyFilesToInput();

        const errors = [];
        pendingFiles.forEach((file) => errors.push(...validateFile(file)));

        if (errors.length) {
            event.preventDefault();
            showClientError([...new Set(errors)]);
            clientErrors.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return;
        }

        let attempted = form.querySelector('input[name="images_attempted"]');
        if (pendingFiles.length) {
            if (!attempted) {
                attempted = document.createElement('input');
                attempted.type = 'hidden';
                attempted.name = 'images_attempted';
                form.appendChild(attempted);
            }
            attempted.value = String(pendingFiles.length);
        } else if (attempted) {
            attempted.remove();
        }

        showClientError([]);
    });

    refreshEmptyState();
    refreshMainBadges();
})();
</script>
