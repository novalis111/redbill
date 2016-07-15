@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    @if ($company->company_name)
                        <h1>{{ trans_choice('redbill.companies', 1) }} {{ $company->company_name }}
                            <small>{{ $company->name }}</small>
                        </h1>
                    @else
                        <h1>@lang('redbill.new_company')</h1>
                    @endif
                </div>
            </div>
        </div>

        <form action="{{ url('company/save') }}" method="POST" class="form-horizontal">

            {{ csrf_field() }}

            @foreach (['company_name', 'salutation', 'name', 'street', 'street_number', 'postcode', 'city',
            'country', 'telephone', 'mobile', 'fax', 'email', 'website', 'tax_number', 'iban', 'bic', 'bank_name', 'hourly_rate'] as $field)
                <div class="form-group">
                    <label for="{{ $field }}" class="col-sm-2 control-label">@lang('redbill.' . $field)</label>

                    <div class="col-sm-10">
                        <input id="{{ $field }}" type="text" name="data[{{ $field }}]"
                               value="{{ $company->$field? $company->$field : '' }}"/>
                    </div>
                </div>
            @endforeach

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-success">
                        @if ($company->id)
                            @lang('redbill.save')
                        @else
                            @lang('redbill.create_company')
                        @endif
                    </button>
                    @include('common.bits._backBtn')
                </div>
            </div>

            <input type="hidden" name="company_id" value="{{ $company->id }}">

        </form>

    </div>
@endsection