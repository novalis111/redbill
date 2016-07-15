@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h1>{{ trans_choice('redbill.invoices', 1) }} {{ $invoice->number }}
                        <small>{{ $invoice->title }}</small>
                    </h1>
                </div>
            </div>

            <div class="col-md-12">
                @include('common.bits._backBtn')
                <a href="{{ url('invoice/edit/' . $invoice->id) }}" class="btn btn-default">
                    <i class="fa fa-edit"></i> @lang('redbill.edit')
                </a>
            </div>
        </div>

        <br/>

        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default {{ $invoice->isOverdue()? 'panel-danger' : '' }}">
                    <div class="panel-heading">@lang('redbill.invoice_data')</div>
                    <div class="panel-body">
                        <table class="table-condensed">
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
                            <tr>
                                <td class="text-right">@lang('redbill.date_payed'):</td>
                                <td>{{ $invoice->date_payed == 0? '-' : formatDate($invoice->date_payed) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('redbill.contractor')</div>
                    <div class="panel-body">
                        <address>
                            {{ $invoice->owner->company_name }}<br/>
                            {{ $invoice->owner->name }}<br/>
                            {{ $invoice->owner->street . ' ' . $invoice->owner->street_number }}<br/>
                            {{ $invoice->owner->postcode . ' ' . $invoice->owner->city }}<br/>
                            {{ $invoice->owner->country }}
                        </address>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('redbill.client')</div>
                    <div class="panel-body">
                        <address>
                            {{ $invoice->client->company_name }}<br/>
                            {{ $invoice->client->name }}<br/>
                            {{ $invoice->client->street . ' ' . $invoice->client->street_number }}<br/>
                            {{ $invoice->client->postcode . ' ' . $invoice->client->city }}<br/>
                            {{ $invoice->client->country }}
                        </address>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @lang('redbill.invoice_line_items')
                    </div>

                    <div class="panel-body">
                        @include('invoice_entry._ajaxCheckList')
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                @include('common.bits._backBtn')
                <a href="{{ url('invoice/edit/' . $invoice->id) }}" class="btn btn-default">
                    <i class="fa fa-edit"></i> @lang('redbill.edit')
                </a>
            </div>
        </div>

    </div>
@endsection
