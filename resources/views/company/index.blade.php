@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-right">
                <a href="{{ url('company/create') }}" class="btn btn-success">
                    <i class="fa fa-btn fa-plus"></i>@lang('redbill.new_company')
                </a>
            </div>
        </div>

        <br/>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ trans_choice("redbill.x_companies", count($companies)) }}
                    </div>

                    <div class="panel-body">
                        <table class="table table-striped table-hover invoice-table">

                            <!-- Table Headings -->
                            <thead>
                            <tr>
                                <th><!-- actions --></th>
                                <th>@lang('redbill.company_name')</th>
                                <th>@lang('redbill.name')</th>
                                <th>@lang('redbill.email')</th>
                                <th>@lang('redbill.website')</th>
                            </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody>
                            @foreach ($companies as $company)
                                <tr>
                                    <td class="table-text text-center">
                                        <div class="">
                                            <div class="btn-group btn-group-xs">
                                                <a class="btn btn-xs btn-default"
                                                   href="{{ url('company/edit/'.$company->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-text">
                                        <div>{{ $company->company_name }}</div>
                                    </td>
                                    <td class="table-text">
                                        <div>{{ $company->name }}</div>
                                    </td>
                                    <td class="table-text">
                                        <div><a href="mailto:{{ $company->email }}">{{ $company->email }}</a></div>
                                    </td>
                                    <td class="table-text">
                                        <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
