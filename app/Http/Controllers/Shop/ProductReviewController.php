<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductReviewController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $product = Product::active()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'author_name' => 'required|string|max:100',
            'author_email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'required|string|min:10|max:2000',
        ]);

        $email = strtolower(trim($data['author_email']));

        $exists = ProductReview::query()
            ->where('product_id', $product->id)
            ->where('author_email', $email)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'author_email' => 'You have already reviewed this product.',
            ]);
        }

        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'author_name' => $data['author_name'],
            'author_email' => $email,
            'rating' => (int) $data['rating'],
            'body' => $data['body'],
            'is_approved' => true,
        ]);

        $product->refreshRatingStats();

        return redirect()
            ->route('products.show', $product->slug)
            ->with('success', 'Thank you! Your review has been published.')
            ->withFragment('reviews');
    }
}
