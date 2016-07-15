@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    @if ($invoice->title)
                        <h1>{{ trans_choice('redbill.invoices', 1) }} {{ $invoice->number }}
                            <small>{{ $invoice->title }}</small>
                        </h1>
                    @else
                        <h1>@lang('redbill.new_invoice')</h1>
                    @endif
                </div>
            </div>
        </div>

        <form action="{{ url('invoice/save') }}" method="POST" class="form-horizontal">

            {{ csrf_field() }}

            <div class="form-group">
                <label for="contractor" class="col-sm-2 control-label">@lang('redbill.contractor')</label>

                <div class="col-sm-10">
                    <select id="contractor"
                            name="data[owner_id]" {{ $invoice->id || $invoice->isLocked()? 'disabled=disabled' : '' }}>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ $invoice->owner_id == $company->id? 'selected=selected' : ''}}>
                                {{ $company->company_name . ' / ' . $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="client" class="col-sm-2 control-label">@lang('redbill.client')</label>

                <div class="col-sm-10">
                    <select id="client"
                            name="data[client_id]" {{ $invoice->id || $invoice->isLocked()? 'disabled=disabled' : '' }}>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ $invoice->client_id == $company->id? 'selected=selected' : ''}}>
                                {{ $company->company_name . ' / ' . $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">@lang('redbill.title')</label>

                <div class="col-sm-10">
                    <input id="title" type="text" name="data[title]"
                           {{ $invoice->isLocked()? 'disabled="disabled"' : '' }}
                           value="{{ $invoice->title? $invoice->title : trans(config('redbill.invoice_title')) }}"/>
                </div>
            </div>

            <!-- Dates -->
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-2">
                    <label for="date_ordered" class="control-label">@lang('redbill.date_ordered')</label>
                    <input id="date_ordered" type="text" name="data[date_ordered]" class="datepicker"
                           {{ $invoice->isLocked()? 'disabled="disabled"' : '' }}
                           value="{{ $invoice->date_ordered > 0? $invoice->date_ordered : date('Y-m-d') }}"/>
                </div>
                <div class="col-sm-3">
                    <label for="date_delivered" class="control-label">@lang('redbill.date_delivered')</label>
                    <input id="date_delivered" type="text" name="data[date_delivered]" class="datepicker"
                           {{ $invoice->isLocked()? 'disabled="disabled"' : '' }}
                           value="{{ $invoice->date_delivered > 0? $invoice->date_delivered : date('Y-m-d') }}"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-2">
                    <label for="date_billed" class="control-label">@lang('redbill.date_billed')</label>
                    <input id="date_billed" type="text" name="data[date_billed]" class="datepicker"
                           {{ $invoice->isLocked()? 'disabled="disabled"' : '' }}
                           value="{{ $invoice->date_billed > 0? $invoice->date_billed : date('Y-m-d') }}"/>
                </div>
                <div class="col-sm-3">
                    <label for="date_payed" class="control-label">@lang('redbill.date_payed')</label>
                    <input id="date_payed" type="text" name="data[date_payed]"
                           {{ $invoice->isLocked()? 'disabled="disabled"' : '' }}
                           class="datepicker" value="{{ $invoice->date_payed > 0? $invoice->date_payed : '' }}"/>
                </div>
            </div>

            @if (isset($invoice->id))
                <div class="form-group">
                    <label for="invoiceEntries" class="col-sm-2 control-label">
                        {{ trans_choice('redbill.entries', 2) }}
                        @if (!$invoice->isLocked() && !$invoice->viewOnly)
                            <br/>
                            <div class="btn-group-vertical">
                                <button id="btnRemoveEntriesFromInvoice" type="button" role="button"
                                        title="@lang('redbill.remove_checked')" class="btn btn-info">
                                    <i class="fa fa-caret-square-o-down"></i>
                                </button>
                                <button type="button" role="button" title="@lang('redbill.reverse_checked')"
                                        class="btn btn-info reverseSelection">
                                    <i class="fa fa-check-square"></i>
                                </button>
                            </div>
                        @endif
                    </label>

                    <div class="col-sm-10">
                        <div id="invoiceEntries"><span>@lang('redbill.none')</span></div>
                    </div>
                </div>

                @if (!$invoice->isLocked())
                    <div class="form-group">
                        <label for="assetCheckList" class="col-sm-2 control-label">@lang('redbill.open_assets')
                            <br/>

                            <div class="btn-group-vertical">
                                <button id="btnAddAssetsToInvoice" type="button" role="button"
                                        title="@lang('redbill.add_checked')" class="btn btn-info">
                                    <i class="fa fa-caret-square-o-up"></i>
                                </button>
                                <button type="button" role="button" title="@lang('redbill.reverse_checked')"
                                        class="btn btn-info reverseSelection">
                                    <i class="fa fa-check-square"></i>
                                </button>
                            </div>
                        </label>

                        <div class="col-sm-10">
                            <div id="assetCheckList"><span>@lang('redbill.none')</span></div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-8">
                    <a href="{{ url('/invoice') }}" class="btn btn-primary">@lang('redbill.back')</a>
                    @if (!$invoice->isLocked())
                        @if ($invoice->id)
                            <button type="submit" class="btn btn-success">@lang('redbill.save')</button>
                        @else
                            <button type="submit" class="btn btn-success">@lang('redbill.continue')</button>
                        @endif
                    @else
                        <a href="{{ url('/invoice/reopen/' . $invoice->id) }}" class="btn btn-danger">
                            @lang('redbill.reopen_invoice')</a>
                    @endif
                </div>
                <div class="col-sm-2 text-right">
                    @if ($invoice->id && $invoice->status != \Redbill\Invoice::STATUS_CANCELLED)
                        <button type="submit" class="btn btn-danger"
                                name="cancel" value="1">@lang('redbill.cancel_order')</button>
                    @endif
                </div>
            </div>

            <input id="invoice_id" type="hidden" name="invoice_id" value="{{ $invoice->id }}">

        </form>

    </div>
@endsection