@php
    $product = $page->product ?? null;
    $hasProduct = $product !== null;
    $currentCurrency = session('currency', \App\Models\Currency::getDefaultCurrency()->code);

    if ($product && $product->currency_prices && is_array($product->currency_prices) && isset($product->currency_prices[$currentCurrency])) {
        $currencyPrice = $product->currency_prices[$currentCurrency];
        $price = $currencyPrice['price'] ?? $product->price;
        $salePrice = $currencyPrice['sale_price'] ?? null;
    } else {
        $price = $product->price ?? 0;
        $salePrice = $product->sale_price ?? null;
    }

    $sku = $product->sku ?? '';
    $inStock = $product ? $product->isInStock() : false;
    $minQuantity = $product->min_quantity ?? 1;
    $quantityUnit = $product->quantity_unit ?? 'item';
@endphp

@if($hasProduct)
<div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-md p-6 text-white">
    <h3 class="text-xl font-bold mb-4">{{ __('messages.pricing') }}</h3>
    <div class="space-y-3">
        @if($product->variants && $product->variants->where('is_active', true)->count() > 0)
            <div class="mb-4">
                <label class="block mb-2 font-semibold">{{ __('messages.select-option') }}:</label>
                <div class="space-y-2" id="variantOptions">
                    @foreach($product->variants->where('is_active', true) as $index => $variant)
                    @php
                        $vCurrentCurrency = session('currency', \App\Models\Currency::getDefaultCurrency()->code);
                        if ($variant->currency_prices && is_array($variant->currency_prices) && isset($variant->currency_prices[$vCurrentCurrency])) {
                            $vCurrencyPrice = $variant->currency_prices[$vCurrentCurrency];
                            $vPrice = $vCurrencyPrice['price'] ?? $variant->price;
                            $vSalePrice = $vCurrencyPrice['sale_price'] ?? null;
                        } else {
                            $vPrice = $variant->price;
                            $vSalePrice = $variant->sale_price;
                        }
                        $vEffectivePrice = $vSalePrice ?? $vPrice;
                    @endphp
                    <label class="flex items-center justify-between p-3 bg-white/10 rounded-lg cursor-pointer hover:bg-white/20 transition" onclick="selectVariant({{ $variant->id }}, '{{ $variant->name }}', {{ $vEffectivePrice }}, {{ $variant->min_quantity ?? 1 }}, {{ $variant->quantity_step ?? 1 }}, '{{ $variant->quantity_unit ?? 'item' }}')">
                        <div class="flex items-center">
                            <input type="radio" name="variant" value="{{ $variant->id }}" class="mr-3" {{ $index === 0 ? 'checked' : '' }}>
                            <span class="font-semibold">{{ $variant->name }}</span>
                        </div>
                        <div class="text-right">
                            @if($vSalePrice)
                                <div class="text-sm line-through opacity-75">{{ currencySymbol() }}{{ number_format($vPrice, 2) }}</div>
                                <div class="text-lg font-bold">{{ currencySymbol() }}{{ number_format($vSalePrice, 2) }}</div>
                            @else
                                <div class="text-lg font-bold">{{ currencySymbol() }}{{ number_format($vPrice, 2) }}</div>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-white/30">
                <form action="{{ route('cart.add') }}" method="POST" id="variantForm">
                    @csrf
                    <input type="hidden" name="product_type" value="cms_page_variant">
                    <input type="hidden" name="product_id" value="{{ $page->id }}">
                    <input type="hidden" name="variant_id" id="selectedVariantId" value="{{ $product->variants->where('is_active', true)->first()->id }}">
                    <input type="hidden" name="quantity" id="selectedQuantity" value="{{ $product->variants->where('is_active', true)->first()->min_quantity ?? 1 }}">
                    <button type="submit" class="w-full bg-white text-orange-600 hover:bg-gray-100 px-6 py-3 rounded-lg font-semibold transition">
                        <span id="bookNowText">{{ __('messages.book-now-text') }} ({{ __('messages.minimum-sort') }}: {{ $product->variants->where('is_active', true)->first()->min_quantity ?? 1 }} {{ $product->variants->where('is_active', true)->first()->quantity_unit ?? 'item' }})</span>
                    </button>
                </form>
            </div>
        @else
            @if($salePrice)
            <div class="flex justify-between items-center">
                <span>{{ __('messages.regular-price') }}</span>
                <span class="text-lg line-through opacity-75">{{ currencySymbol() }}{{ number_format($price, 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-semibold">{{ __('messages.sale-price') }}</span>
                <span class="text-3xl font-bold">{{ currencySymbol() }}{{ number_format($salePrice, 2) }}</span>
            </div>
            @else
            <div class="flex justify-between items-center">
                <span class="font-semibold">{{ __('messages.price') }}</span>
                <span class="text-3xl font-bold">{{ currencySymbol() }}{{ number_format($price, 2) }}</span>
            </div>
            @endif

            @if($inStock)
            <div class="pt-3 border-t border-white/30">
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_type" value="cms_page">
                    <input type="hidden" name="product_id" value="{{ $page->id }}">
                    <input type="hidden" name="quantity" value="{{ $minQuantity }}">
                    <button type="submit" class="w-full bg-white text-orange-600 hover:bg-gray-100 px-6 py-3 rounded-lg font-semibold transition">
                        {{ __('messages.book-now-text') }} ({{ __('messages.minimum-sort') }}: {{ $minQuantity }} {{ $quantityUnit }})
                    </button>
                </form>
            </div>
            @else
            <div class="pt-3 border-t border-white/30">
                <button class="w-full bg-gray-400 text-white px-6 py-3 rounded-lg font-semibold cursor-not-allowed" disabled>
                    {{ __('messages.out_of_stock') }}
                </button>
            </div>
            @endif
        @endif

        @if($sku)
        <div class="text-sm opacity-75 text-center">
            {{ __('messages.sku') }}: {{ $sku }}
        </div>
        @endif
    </div>
</div>

<script>
function selectVariant(variantId, variantName, price, minQty, step, unit) {
    document.getElementById('selectedVariantId').value = variantId;
    document.getElementById('selectedQuantity').value = minQty;
    document.getElementById('bookNowText').textContent = `{{ __('messages.book-now-text') }} ({{ __('messages.minimum-sort') }}: ${minQty} ${unit})`;
}
</script>
@endif
