@extends('layouts.print')

@section('content')
    <div id="header">
         <img id="logo" src="{{URL::asset('/img/logo.png')}}" alt="Logo"/>

        <div class="divider" style="margin-top: .5em"></div>
    </div>

    <div id="footer" class="text-small divider">
        <table style="width: 100%">
            <tr>
                <td width="33%">
                    <table class="address">
                        <tr>
                            <td>{{ $invoice->owner->company_name }}</td>
                        </tr>
                        <tr>
                            <td>{{ $invoice->owner->salutation }} {{ $invoice->owner->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ $invoice->owner->street }} {{ $invoice->owner->street_number }}</td>
                        </tr>
                        <tr>
                            <td>{{ $invoice->owner->postcode }} {{ $invoice->owner->city }}</td>
                        </tr>
                        <tr>
                            <td>{{ $invoice->owner->country }}</td>
                        </tr>
                    </table>
                </td>
                <td width="33%">
                    <table class="address">
                        <tr>
                            <td class="text-right">@lang('redbill.telephone_s'):</td>
                            <td>{{ $invoice->owner->telephone }}</td>
                        </tr>
                        <tr>
                            <td class="text-right">@lang('redbill.fax'):</td>
                            <td>{{ $invoice->owner->fax }}</td>
                        </tr>
                        <tr>
                            <td class="text-right">@lang('redbill.email'):</td>
                            <td>{{ $invoice->owner->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-right">@lang('redbill.website_s'):</td>
                            <td>{{ $invoice->owner->website }}</td>
                        </tr>
                        <tr>
                            <td class="text-right">@lang('redbill.tax_number_s'):</td>
                            <td>{{ $invoice->owner->tax_number }}</td>
                        </tr>
                    </table>
                </td>
                <td width="33%">
                    <table class="address">
                        <tr>
                            <td class="text-right">@lang('redbill.bank_account')</td>
                        </tr>
                        <tr>
                            <td class="text-right">
                                <?php if (config('redbill.bank_owner') == 'person'): ?>
                                {{ $invoice->owner->name }}
                                <?php else: ?>
                                {{ $invoice->owner->company_name }}
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right">IBAN {{ $invoice->owner->iban }}</td>
                        </tr>
                        <tr>
                            <td class="text-right">BIC {{ $invoice->owner->bic }}</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div id="content">
        <div class="row">
            <div id="address-client" class="address floatl width-50">
                <p class="light-text">{{ $invoice->owner->company_name }} &bull;
                    {{ $invoice->owner->street . ' ' . $invoice->owner->street_number }} &bull;
                    {{ $invoice->owner->postcode . ' ' . $invoice->owner->city }}
                </p>
                <strong>{{ $invoice->client->company_name }}</strong><br/>
                {{ $invoice->client->salutation }} {{ $invoice->client->name }}<br/>
                {{ $invoice->client->street }} {{ $invoice->client->street_number }}<br/>
                {{ $invoice->client->postcode }} {{ $invoice->client->city }}<br/>
                {{ $invoice->client->country }}
            </div>

            <div id="dates" class="floatr width-50" style="padding-top: 1em">
                <table class="floatr">
                    <tr>
                        <td class="text-right">@lang('redbill.date_ordered'):</td>
                        <td>{{ formatDate($invoice->date_ordered) }}</td>
                    </tr>
                    <tr>
                        <td class="text-right">@lang('redbill.date_delivered'):</td>
                        <td>{{ formatDate($invoice->date_delivered) }}</td>
                    </tr>
                    <tr>
                        <td class="text-right">@lang('redbill.date_billed'):</td>
                        <td>{{ formatDate($invoice->date_billed) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row pad-top">
            <p><strong>{{ trans_choice('redbill.invoices', 1) . ' ' . $invoice->number }}</strong></p>
        </div>

        <div class="row">
            <table id="invoice-entries">
                <tr>
                    <th class="min-width">@lang('redbill.pos')</th>
                    <th class="text-left min-width">@lang('redbill.date')</th>
                    <th class="text-left">@lang('redbill.title')</th>
                    <th class="text-left min-width">@lang('redbill.amount')</th>
                    <th class="text-right min-width">@lang('redbill.price')</th>
                    <th class="text-right min-width">@lang('redbill.net_sum')</th>
                </tr>
                <?php $n = 1 ?>
                @foreach ($invoice->sortedEntries() as $entry)
                    <tr class="{{ ($n%2) ? 'even' : 'odd' }}">
                        <td class="text-center">{{ $n++ }}</td>
                        <td class="min-width">{{ formatDate($entry->asset->delivery_date) }}</td>
                        <td>{{ $entry->title }}</td>
                        <td class="min-width">{{ formatFloat($entry->amount) . ' ' . trans('redbill.' . $entry->asset->unit) }}</td>
                        <td class="text-right min-width">{{ formatCurrency($entry->price) }}</td>
                        <td class="text-right min-width">{{ formatCurrency($entry->getNet()) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="text-right divider" colspan="5">@lang('redbill.sum') @lang('redbill.net_sum')</td>
                    <td class="text-right divider min-width">{{ formatCurrency($invoice->getNetSum()) }}</td>
                </tr>
                @foreach ($invoice->getTaxSums() as $taxSum)
                    <tr>
                        <td class="text-right" colspan="5">
                            @lang('redbill.vat') ({{ formatTaxRate($taxSum['tax_rate']) }})
                        </td>
                        <td class="text-right min-width">{{ formatCurrency($taxSum['sum']) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="text-right" colspan="5">@lang('redbill.sum') @lang('redbill.gross_sum')</td>
                    <td class="text-right min-width">{{ formatCurrency($invoice->getGrossSum()) }}</td>
                </tr>
            </table>
        </div>

        <div class="row text-small pad-top">
            {!! trans('redbill.invoice_text',
                [
                'sum'           => formatCurrency($invoice->getGrossSum()),
                'days'          => config('redbill.pay_days', 10),
                'number'        => $invoice->number,
                'company_name'  => $invoice->owner->company_name
                ])
            !!}
        </div>
    </div>
@endsection
