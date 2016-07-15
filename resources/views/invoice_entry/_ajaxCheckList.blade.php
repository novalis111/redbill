<table class="table table-condensed table-hover table-striped">
    <thead>
    <tr>
        <th></th>
        <th>@lang('redbill.delivery_date')</th>
        <th class="text-center">@lang('redbill.amount')</th>
        <th>@lang('redbill.unit')</th>
        <th>@lang('redbill.title')</th>
        <th>@lang('redbill.price')</th>
        <th>@lang('redbill.tax_rate')</th>
        <th class="text-right">@lang('redbill.gross_sum')</th>
        <th class="text-right">@lang('redbill.net_sum')</th>
        <th class="text-right">@lang('redbill.profit_sum')</th>
    </tr>
    </thead>
    @foreach ($invoice->sortedEntries() as $entry)
        <tr>
            <td class="text-center" style="width: 1%">
                @if (!$invoice->isLocked() && !$invoice->viewOnly)
                    <input type="hidden" name="entry_id" value="{{ $entry->id }}"/>
                    <label for="entry-check-{{ $entry->id }}" class="hidden"></label>
                    <input id="entry-check-{{ $entry->id }}" type="checkbox">
                @endif
            </td>
            <td>{{ $entry->asset->delivery_date }}</td>
            <td class="text-center" style="width: 1%">{{ $entry->amount }}</td>
            <td style="width: 1%; white-space: nowrap">@lang('redbill.' . $entry->asset->unit)</td>
            <td>{{ $entry->title }}</td>
            <td class="text-center" style="width: 1%; white-space: nowrap">
                <span>{{ sprintf(config('redbill.currency_format'), $entry->price) }}</span>
            </td>
            <td class="text-center" style="width: 1%; white-space: nowrap">{{ $entry->tax_rate }} <span>%</span></td>
            <td class="text-right">{{ formatCurrency($entry->getGross()) }}</td>
            <td class="text-right">{{ formatCurrency($entry->getNet()) }}</td>
            <td class="text-right">{{ formatCurrency($entry->getProfit()) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="7" class="text-right">@lang('redbill.sum')</td>
        <td class="text-right">{{ formatCurrency($invoice->getGrossSum()) }}</td>
        <td class="text-right">{{ formatCurrency($invoice->getNetSum()) }}</td>
        <td class="text-right">{{ formatCurrency($invoice->getProfitSum()) }}</td>
    </tr>
</table>
