<div class="panel panel-default">
    <div class="panel-heading">
        {{ trans("redbill.x_$title", ['count' => count($invoices)]) . ' ' . trans_choice('redbill.invoices', $invoices) }}
    </div>

    <div class="panel-body">
        <table class="table table-striped table-hover invoice-table">

            <!-- Table Headings -->
            <thead>
            <tr>
                <th><!-- actions --></th>
                @if ($title == 'open')
                    <th>@lang('redbill.created')</th>
                @else
                    <th>{{ trans_choice('redbill.payed', 1) }}</th>
                @endif
                <th>@lang('redbill.number')</th>
                <th>@lang('redbill.client')</th>
                <th class="text-right">@lang('redbill.gross_sum')</th>
                <th class="text-right">@lang('redbill.net_sum')</th>
                <th class="text-right">@lang('redbill.profit_sum')</th>
            </tr>
            </thead>

            <!-- Table Body -->
            <tbody>
            @foreach ($invoices as $invoice)
                <tr class="{{ getInvoiceRowClasses($invoice) }}">
                    <td class="table-text text-center">
                        <div class="">
                            <div class="btn-group btn-group-xs">
                                <a class="btn btn-xs btn-default" href="{{ url('invoice/view/' . $invoice->id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a class="btn btn-xs btn-default" href="{{ url('invoice/getPdf/' . $invoice->id) }}">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                                <a class="btn btn-xs btn-default" href="{{ url('invoice/edit/' . $invoice->id) }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                @if ($invoice->status == \Redbill\Invoice::STATUS_OPEN)
                                    <a class="btn btn-xs btn-default"
                                       href="{{ url('invoice/set_payed/'.$invoice->id) }}">
                                        <i class="fa fa-dollar"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="table-text">
                        @if ($title == 'open')
                            <div>{{ formatDate($invoice->date_billed) }}</div>
                        @else
                            <div>{{ $invoice->date_payed == 0? '-' : formatDate($invoice->date_payed) }}</div>
                        @endif
                    </td>
                    <td class="table-text">
                        <div>{{ $invoice->number }}</div>
                    </td>
                    <td class="table-text">
                        <div>{{ $invoice->client->company_name }}</div>
                    </td>
                    <td class="table-text text-right">
                        <div>{{ formatCurrency($invoice->getGrossSum()) }}</div>
                    </td>
                    <td class="table-text text-right">
                        <div>{{ formatCurrency($invoice->getNetSum()) }}</div>
                    </td>
                    <td class="table-text text-right">
                        <div>{{ formatCurrency($invoice->getProfitSum()) }}</div>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-right">@lang('redbill.sum')</td>
                <td class="text-right">{{ formatCurrency($sums['gross']) }}</td>
                <td class="text-right">{{ formatCurrency($sums['net']) }}</td>
                <td class="text-right">{{ formatCurrency($sums['profit']) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>