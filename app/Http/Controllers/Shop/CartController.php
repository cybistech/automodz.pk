<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        return view('shop.cart', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'nullable|integer|min:1|max:99']);

        if (! $product->is_active || ! $product->isInStock()) {
            return back()->with('error', 'This product is currently unavailable.');
        }

        $quantity = (int) $request->get('quantity', 1);
        $this->cart->add($product->id, $quantity);

        return back()->with('success', 'Added to cart successfully.');
    }

    public function update(Request $request, int $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:99']);
        $this->cart->update($productId, (int) $request->quantity);

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function remove(int $productId)
    {
        $this->cart->remove($productId);

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }
}
