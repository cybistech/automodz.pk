<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Support\ShopCache;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductReview::with(['product:id,name,slug', 'user:id,name'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        $reviews = $query->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, ProductReview $review)
    {
        $approved = $request->boolean('is_approved');

        $review->update(['is_approved' => $approved]);
        $review->product?->refreshRatingStats();
        ShopCache::flush();

        return back()->with('success', $approved ? 'Review approved.' : 'Review hidden.');
    }

    public function destroy(ProductReview $review)
    {
        $product = $review->product;
        $review->delete();
        $product?->refreshRatingStats();
        ShopCache::flush();

        return back()->with('success', 'Review deleted.');
    }
}
