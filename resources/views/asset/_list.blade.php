@if (count($assets) == 0)
    @stop
@endif
<a name="client{{ $assets->first()->client->id }}"><!-- jump --></a>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading toggle-panel cursor-pointer clearfix">
                <div class="col-md-9">
                    {{ $assets->first()->client->company_name . ' - ' . trans_choice("redbill.x_assets", count($assets)) }}
                </div>
                <div class="col-md-3 text-right">
                    {{ trans('redbill.net_sum') }}
                    &nbsp;{{ formatCurrency($assets->sum('amount') * $assets->first()->client->getHourlyRate()) }}
                    &nbsp;/&nbsp;
                    {{ trans('redbill.profit_sum') }}
                    &nbsp;{{ formatCurrency($assets->sum('amount') * $assets->first()->client->getHourlyRate() / (1 + (float)config('redbill.income_tax_rate') / 100)) }}
                </div>
            </div>

            <div class="panel-body hidden">
                <table class="table table-striped table-hover asset-table">

                    <!-- Table Headings -->
                    <thead>
                    <tr>
                        <th><!-- actions --></th>
                        <th>@lang('redbill.amount')</th>
                        <th>@lang('redbill.title')</th>
                        <th>@lang('redbill.delivery_date')</th>
                    </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                    @foreach ($assets as $asset)
                        <tr>
                            <td class="table-text text-center">
                                <div class="">
                                    <div class="btn-group btn-group-xs">
                                        @if ($asset->foreign_id == 0)
                                            <a class="btn btn-xs btn-default"
                                               href="{{ url('asset/edit/'.$asset->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="table-text">
                                <div>{{ formatFloat($asset->amount) }} {{ trans('redbill.'.$asset->unit) }}</div>
                            </td>
                            <td class="table-text">
                                <div>{{ $asset->title }}</div>
                            </td>
                            <td class="table-text">
                                <div>{{ $asset->delivery_date }}</div>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="text-right">@lang('redbill.sum')</td>
                        <td colspan="3">{{ formatFloat($assets->sum('amount')) }}</td>
                    </tr>
                    </tbody>
                </table>
                <a href="{{ url('asset/create/' . $assets->first()->client->id) }}" class="btn btn-success">
                    <i class="fa fa-btn fa-plus"></i>@lang('redbill.new_asset')
                </a>
                <a href="{{ url('invoice/create/' . $assets->first()->client->id) }}" class="btn btn-success">
                    <i class="fa fa-btn fa-plus"></i>@lang('redbill.new_invoice')
                </a>
            </div>
        </div>
    </div>
</div>