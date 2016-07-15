@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <a href="{{ url('asset/create') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> @lang('redbill.new_asset')</a>
                <a href="{{ url('asset/fetchProjects/redmine') }}" class="btn btn-success">
                    <i class="fa fa-cloud-download"></i> @lang('redbill.fetch_projects') Redmine</a>
                <a href="{{ url('asset/bulkInsert') }}" class="btn btn-success">
                    <i class="fa fa-file-text-o"></i> @lang('redbill.bulk_insert')</a>
            </div>
        </div>

        <br/>

        @foreach ($groups as $group)
            @if ($group->count())
                @include('asset._list', ['assets' => $group])
            @endif
        @endforeach

    </div>
@endsection
