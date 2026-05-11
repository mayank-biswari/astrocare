<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'code' => 'required|string|max:50|unique:coupons',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];

        $messages = [
            'code.unique' => 'This coupon code already exists.',
            'end_date.after_or_equal' => 'The end date must be equal to or after the start date.',
        ];

        $request->validate($rules, $messages);

        // Additional validation: percentage must be between 1 and 100
        if ($request->discount_type === 'percentage' && ($request->discount_value < 1 || $request->discount_value > 100)) {
            return back()->withErrors(['discount_value' => 'Percentage discount value must be between 1 and 100.'])->withInput();
        }

        Coupon::create([
            'code' => $request->code,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.coupons')->with('success', 'Coupon created successfully!');
    }

    public function edit(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, int $id)
    {
        $coupon = Coupon::findOrFail($id);

        $rules = [
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];

        $messages = [
            'end_date.after_or_equal' => 'The end date must be equal to or after the start date.',
        ];

        $request->validate($rules, $messages);

        // Additional validation: percentage must be between 1 and 100
        if ($request->discount_type === 'percentage' && ($request->discount_value < 1 || $request->discount_value > 100)) {
            return back()->withErrors(['discount_value' => 'Percentage discount value must be between 1 and 100.'])->withInput();
        }

        $coupon->update([
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.coupons')->with('success', 'Coupon updated successfully!');
    }

    public function destroy(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('success', 'Coupon deleted successfully!');
    }

    public function toggleStatus(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['is_active' => !$coupon->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $coupon->is_active,
            'message' => 'Coupon status updated successfully!',
        ]);
    }
}
