@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-10">
                <ul role="tablist" class="nav nav-pills">
                    <li class="active">
                        <a href="{{ url('invoice/span/' . date('Y-m-01') . '/' . date('Y-m-t')) }}">{{ invoiceSpan() }}</a>
                    </li>
                    @foreach($spanOptions as $label => $spans)
                        <li class="dropdown">
                            <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown"
                               class="dropdown-toggle" href="#" id="drop{{ $label }}">
                                {{ is_numeric($label)? $label : trans("redbill.$label") }} <span class="caret"></span>
                            </a>
                            <ul aria-labelledby="drop{{ $label }}" class="dropdown-menu" id="menu{{ $label }}">
                                @foreach ($spans as $spanVals)
                                    <li>
                                        <a href="{{ url('invoice/span', ['from' => $spanVals['from'], 'to' => $spanVals['to']]) }}">{{ $spanVals['label'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-2 text-right">
                <a href="{{ url('invoice/create') }}" class="btn btn-success">
                    <i class="fa fa-btn fa-plus"></i>@lang('redbill.new_invoice')
                </a>
            </div>
        </div>

        <br/>

        <div class="row">
            <div class="col-md-12">
                @if (count($invoices['open']))
                    @include('invoice._list', ['title' => 'open', 'invoices' => $invoices['open'], 'sums' => $sums['open']])
                @endif
                @if (count($invoices['payed']))
                    @include('invoice._list', ['title' => 'payed', 'invoices' => $invoices['payed'], 'sums' => $sums['payed']])
                @endif
                @if (count($invoices['cancelled']))
                    @include('invoice._list', ['title' => 'cancelled', 'invoices' => $invoices['cancelled'], 'sums' => $sums['cancelled']])
                @endif
                @if (!count($invoices['open']) && !count($invoices['payed']) && !count($invoices['cancelled']))
                    <div class="jumbotron text-center">
                        <h3>@lang('redbill.no_invoices')</h3>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
