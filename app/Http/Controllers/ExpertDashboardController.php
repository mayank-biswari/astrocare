<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpertAvailability;
use Carbon\Carbon;

class ExpertDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.expert.index');
    }

    public function profile()
    {
        $page = \App\Models\CmsPage::where('created_by', auth()->id())
            ->whereHas('pageType', fn($q) => $q->where('name', 'LIKE', '%Astrologer%'))
            ->with(['product.variants'])
            ->first();
        
        $pageType = \App\Models\CmsPageType::where('name', 'LIKE', '%Astrologer%')->first();
        $languages = \App\Models\Language::getActiveLanguages();
        $currencies = \App\Models\Currency::getActiveCurrencies();
        
        return view('dashboard.expert.profile', compact('page', 'pageType', 'languages', 'currencies'));
    }

    public function updateProfile(Request $request)
    {
        $pageType = \App\Models\CmsPageType::where('name', 'LIKE', '%Astrologer%')->firstOrFail();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $page = \App\Models\CmsPage::where('created_by', auth()->id())
            ->whereHas('pageType', fn($q) => $q->where('name', 'LIKE', '%Astrologer%'))
            ->first();

        $data = [
            'title' => $request->title,
            'slug' => \Str::slug($request->title),
            'body' => $request->body,
            'cms_page_type_id' => $pageType->id,
            'custom_fields' => $request->custom_fields,
            'language_code' => $request->language_code ?? 'en',
            'created_by' => auth()->id(),
            'is_published' => true
        ];

        if ($request->hasFile('image')) {
            if ($page && $page->image) {
                \Storage::disk('public')->delete($page->image);
            }
            $data['image'] = $request->file('image')->store('cms', 'public');
        }

        if ($page) {
            $page->update($data);
        } else {
            $page = \App\Models\CmsPage::create($data);
        }

        if ($request->product) {
            $product = $page->product()->updateOrCreate(
                ['cms_page_id' => $page->id],
                [
                    'price' => $request->product['price'] ?? 0,
                    'sale_price' => $request->product['sale_price'] ?? null,
                    'currency_prices' => $request->product['currency_prices'] ?? null,
                    'sku' => $request->product['sku'] ?? 'EXP-' . auth()->id(),
                    'stock_quantity' => 999,
                    'manage_stock' => false
                ]
            );

            if ($request->variants) {
                $existingVariantIds = [];
                foreach ($request->variants as $variantData) {
                    $variant = $product->variants()->updateOrCreate(
                        ['id' => $variantData['id'] ?? null],
                        [
                            'name' => $variantData['name'],
                            'price' => $variantData['price'],
                            'sale_price' => $variantData['sale_price'] ?? null,
                            'currency_prices' => $variantData['currency_prices'] ?? null,
                            'stock_quantity' => 999,
                            'min_quantity' => $variantData['min_quantity'] ?? 1,
                            'quantity_step' => $variantData['quantity_step'] ?? 1,
                            'quantity_unit' => $variantData['quantity_unit'] ?? 'min',
                            'is_active' => isset($variantData['is_active'])
                        ]
                    );
                    $existingVariantIds[] = $variant->id;
                }
                $product->variants()->whereNotIn('id', $existingVariantIds)->delete();
            }
        }

        return redirect()->route('expert.profile')->with('success', 'Profile updated successfully!');
    }

    public function chats()
    {
        return view('dashboard.expert.chats');
    }

    public function calls()
    {
        return view('dashboard.expert.calls');
    }

    public function availability()
    {
        $dates = [];
        for ($i = 0; $i < 14; $i++) {
            $date = Carbon::today()->addDays($i);
            $availability = ExpertAvailability::where('user_id', auth()->id())
                ->whereDate('date', $date->format('Y-m-d'))
                ->first();
            $dates[] = [
                'date' => $date,
                'is_available' => $availability ? $availability->is_available : false
            ];
        }
        return view('dashboard.expert.availability', compact('dates'));
    }

    public function updateAvailability(Request $request)
    {
        $request->validate(['date' => 'required|date', 'is_available' => 'required|boolean']);
        ExpertAvailability::updateOrCreate(
            ['user_id' => auth()->id(), 'date' => $request->date],
            ['is_available' => $request->is_available]
        );
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate(['status' => 'required|in:online,busy,offline']);
        $page = \App\Models\CmsPage::where('created_by', auth()->id())
            ->whereHas('pageType', fn($q) => $q->where('name', 'LIKE', '%Astrologer%'))
            ->firstOrFail();
        $customFields = $page->custom_fields;
        $customFields['status'] = $request->status;
        $page->update(['custom_fields' => $customFields]);
        return response()->json(['success' => true]);
    }
}
