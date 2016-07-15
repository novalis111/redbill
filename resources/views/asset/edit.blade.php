@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    @if ($asset->title)
                        <h1>{{ trans_choice('redbill.assets', 1) }} {{ $asset->title }}
                            <small>{{ $asset->client->company_name }}</small>
                        </h1>
                    @else
                        <h1>@lang('redbill.new_asset')</h1>
                    @endif
                </div>
            </div>
        </div>

        <form action="{{ url('asset/save') }}" method="POST" class="form-horizontal">

            {{ csrf_field() }}

            <div class="form-group">
                <label for="client" class="col-sm-2 control-label">@lang('redbill.client')</label>

                <div class="col-sm-10">
                    <select id="client" name="data[client_id]">
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ $asset->client_id == $company->id || $companyId == $company->id? 'selected="selected"' : ''}}>
                                {{ $company->company_name . ' / ' . $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="amount" class="col-sm-2 control-label">@lang('redbill.amount')</label>

                <div class="col-sm-10">
                    <input id="amount" type="text" name="data[amount]"
                           value="{{ $asset->amount? $asset->amount : '' }}"/>
                    <label for="unit" class="col-sm-2 control-label hidden">@lang('redbill.unit')</label>

                    <select id="unit" name="data[unit]">
                        @foreach($asset->getUnits() as $unit)
                            <option value="{{ $unit }}" {{ isset($asset->unit) && $asset->unit == $unit? 'selected="selected"' : ''}}>
                                @lang('redbill.' . $unit)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">@lang('redbill.title')</label>

                <div class="col-sm-10">
                    <input id="title" type="text" name="data[title]"
                           value="{{ $asset->title? $asset->title : '' }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="delivery_date" class="col-sm-2 control-label">@lang('redbill.delivery_date')</label>

                <div class="col-sm-10">
                    <input id="delivery_date" type="text" name="data[delivery_date]" class="datepicker"
                           value="{{ $asset->delivery_date? $asset->delivery_date : date('Y-m-d') }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="comment" class="col-sm-2 control-label">@lang('redbill.comment')</label>
                <textarea id="comment" name="data[comment]">{{ $asset->comment }}</textarea>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-success">
                        @if ($asset->id)
                            @lang('redbill.save')
                        @else
                            @lang('redbill.create')
                        @endif
                    </button>
                    @if ($companyId)
                        <button type="submit" class="btn btn-success" name="btn_continue" value="1">
                            @lang('redbill.create_continue')</button>
                    @endif
                    <a href="{{ url('asset#client' . $companyId) }}" class="btn btn-primary">@lang('redbill.back')</a>
                    <button class="btn btn-danger" type="submit" name="delete"
                            value="1">@lang('redbill.delete_asset')</button>
                </div>
            </div>

            <input type="hidden" name="asset_id" value="{{ $asset->id }}"/>

        </form>

    </div>
@endsection