@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h1>@lang('redbill.bulk_insert')</h1>
                </div>
            </div>
        </div>

        <form action="{{ url('asset/bulkSave') }}" method="POST" class="form-horizontal">

            {{ csrf_field() }}

            <div class="form-group">
                <label for="client" class="col-sm-2 control-label">@lang('redbill.client')</label>

                <div class="col-sm-10">
                    <select id="client" name="client_id">
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('client_id') == $company->id? 'selected=selected' : '' }}>
                                {{ $company->company_name . ' / ' . $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="bulktxt" class="col-sm-2 control-label">@lang('redbill.bulk_insert')</label>
                <textarea id="bulktxt" cols="50" rows="15"
                          name="bulktxt">{{ old('bulktxt', (date('Y-m-d') . ' Copy and change me 123 ' . config('redbill.default_hour_price'))) }}</textarea>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-success">
                        @lang('redbill.create')
                    </button>
                    <a href="{{ url('asset') }}" class="btn btn-primary">@lang('redbill.back')</a>
                </div>
            </div>

        </form>

    </div>
@endsection