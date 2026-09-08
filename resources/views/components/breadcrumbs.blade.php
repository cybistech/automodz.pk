@props(['items' => []])

@if(count($items) > 0)
    <nav class="mb-6 text-sm text-slate-400" aria-label="Breadcrumb">
        @foreach($items as $index => $item)
            @if($index > 0)
                <span class="mx-1">/</span>
            @endif
            @if(! empty($item['url']) && $index < count($items) - 1)
                <a href="{{ $item['url'] }}" class="hover:text-orange-400">{{ $item['label'] }}</a>
            @else
                <span @class(['text-slate-300' => $index === count($items) - 1])>{{ $item['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endif
