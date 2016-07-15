<table class="table table-condensed table-hover table-striped">
    <thead>
    <tr>
        <th></th>
        <th class="text-center">@lang('redbill.amount')</th>
        <th>@lang('redbill.unit')</th>
        <th>@lang('redbill.title')</th>
        <th>@lang('redbill.price')</th>
        <th>@lang('redbill.tax_rate')</th>
    </tr>
    </thead>
    @foreach ($openAssets as $asset)
        <tr>
            <td class="text-center" style="width: 1%">
                <input type="hidden" name="asset_id" value="{{ $asset->id }}"/>
                <label for="asset-check-{{ $asset->id }}" class="hidden"></label>
                <input id="asset-check-{{ $asset->id }}" type="checkbox">
            </td>
            <td class="text-center" style="width: 1%">
                <label for="asset-amount-{{ $asset->id }}" class="hidden"></label>
                <input id="asset-amount-{{ $asset->id }}" class="input-amount" type="text" name="amount"
                       value="{{ $asset->amount }}"/>
            </td>
            <td style="width: 1%; white-space: nowrap">@lang('redbill.' . $asset->unit)</td>
            <td>
                <label for="asset-title-{{ $asset->id }}" class="hidden"></label>
                <input id="asset-title-{{ $asset->id }}" type="text" style="width: 100%" name="title"
                       value="{{ $asset->title }}"/>
            </td>
            <td class="text-center" style="width: 1%; white-space: nowrap">
                <label for="asset-price-{{ $asset->id }}" class="hidden"></label>
                <input id="asset-price-{{ $asset->id }}" class="input-amount" type="text" name="price"
                       value="{{ $asset->client->hourly_rate > 0? (float)$asset->client->hourly_rate : (float)config('redbill.default_hour_price') }}"/>
                <span>{{ sprintf(config('redbill.currency_format'), '') }}</span>
            </td>
            <td class="text-center" style="width: 1%; white-space: nowrap">
                <label for="asset-tax-{{ $asset->id }}" class="hidden"></label>
                <input id="asset-tax-{{ $asset->id }}" class="input-amount" type="text" name="tax_rate"
                       value="{{ config('redbill.default_tax_rate') }}"/>
                <span>%</span>
            </td>
        </tr>
    @endforeach
</table>