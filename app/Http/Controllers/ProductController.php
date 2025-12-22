<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;

class ProductController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::where('is_active', true)->paginate(12);
        $featuredProducts = \App\Models\Product::where('is_featured', true)->where('is_active', true)->take(4)->get();
        return view('shop.index', compact('products', 'featuredProducts'));
    }

    public function category($category)
    {
        $products = \App\Models\Product::where('category', $category)
            ->where('is_active', true)
            ->paginate(12);
        return view('shop.category', compact('products', 'category'));
    }

    public function show($id, $slug = null)
    {
        $product = \App\Models\Product::findOrFail($id);
        $correctSlug = \Str::slug($product->name);

        // Redirect to correct SEO URL if slug is missing or incorrect
        if ($slug !== $correctSlug) {
            return redirect()->route('product.show', ['id' => $id, 'slug' => $correctSlug], 301);
        }

        return view('shop.product', compact('product'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);

        // Store in session cart
        $cart = session()->get('cart', []);
        $cart[$product->id] = [
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $request->quantity,
            'image' => $product->image
        ];
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        return view('shop.cart', compact('cart'));
    }

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }
        return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
    }

    public function checkout(Request $request)
    {
        $currency = session('currency', \App\Models\Currency::getDefaultCurrency()->code);
        $paymentGateways = \App\Models\PaymentGateway::getActiveGateways($currency);

        if ($request->has('buy_now')) {
            // Direct checkout for single product
            $product = \App\Models\Product::findOrFail($request->product_id);
            $total = $product->price * $request->quantity;
            return view('shop.checkout', compact('product', 'total', 'paymentGateways'))->with('success', 'Proceeding to checkout!');
        }

        // Regular cart checkout
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty!');
        }

        return view('shop.checkout', compact('cart', 'paymentGateways'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'pincode' => 'required|string',
            'payment_gateway' => 'required|exists:payment_gateways,code'
        ]);

        // Calculate total from cart
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Create order
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . time() . '-' . rand(1000, 9999),
            'total_amount' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_gateway,
            'payment_status' => 'pending',
            'shipping_address' => $request->address . ', ' . $request->city . ' - ' . $request->pincode,
            'phone' => $request->phone,
            'items' => json_encode($cart),
        ]);

        // Save address if user consented
        if (auth()->check() && $request->has('save_address')) {
            auth()->user()->update([
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'pincode' => $request->pincode,
            ]);
        }

        // Process payment
        $paymentService = new PaymentService();
        $result = $paymentService->processPayment($order, $request->payment_gateway);

        if ($result['success']) {
            session()->forget('cart');
            return redirect()->route('shop.index')->with('success', $result['message']);
        }

        return redirect()->route('shop.index')->with('error', $result['message']);
    }
}
