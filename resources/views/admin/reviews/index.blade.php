@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <form method="GET" class="flex gap-2">
        <select name="status" class="input-field" onchange="this.form.submit()">
            <option value="">All reviews</option>
            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
            <option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
        </select>
    </form>
</div>

<div class="card mt-6 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="border-b border-slate-700 bg-slate-800/50">
            <tr>
                <th class="px-4 py-3 text-left">Product</th>
                <th class="px-4 py-3 text-left">Reviewer</th>
                <th class="px-4 py-3 text-left">Rating</th>
                <th class="px-4 py-3 text-left">Review</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
                <tr class="border-b border-slate-800 align-top">
                    <td class="px-4 py-3">
                        @if($review->product)
                            <a href="{{ route('products.show', $review->product->slug) }}" class="font-medium text-orange-400 hover:text-orange-300" target="_blank">{{ $review->product->name }}</a>
                        @else
                            <span class="text-slate-500">Deleted product</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <p>{{ $review->author_name }}</p>
                        <p class="text-xs text-slate-500">{{ $review->author_email }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <x-star-rating :rating="$review->rating" />
                    </td>
                    <td class="px-4 py-3 max-w-md">
                        <p class="text-slate-400">{{ Str::limit($review->body, 140) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $review->created_at->format('M d, Y') }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $review->is_approved ? 'bg-green-500/20 text-green-400' : 'bg-slate-700 text-slate-300' }}">
                            {{ $review->is_approved ? 'Approved' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-2">
                            <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="is_approved" value="{{ $review->is_approved ? 0 : 1 }}">
                                <button type="submit" class="rounded-lg border border-slate-700 px-2 py-1 text-xs text-slate-300 hover:border-orange-500/50 hover:text-orange-300">
                                    {{ $review->is_approved ? 'Hide' : 'Approve' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete this review permanently?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-700 text-red-400 hover:border-red-500/50 hover:bg-red-500/10" title="Delete" aria-label="Delete review">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">No reviews yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
@endsection
