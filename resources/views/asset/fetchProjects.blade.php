@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('asset/saveProjectsToClients') }}" method="post">
            {{ csrf_field() }}
            <table class="table">
                @foreach ($projects as $project)
                @if ($project['project']->level > 0 && count($project['entries']) == 0)
                        <!-- Skip project {{ $project['project']->name }} with no entries -->
                @else
                    <tr class="{{ $project['project']->level == 0? 'info' : '' }}"
                        style="background-color: {{ $project['project']->level == 0? 'inherit' : '#' . str_repeat(dechex(255 - 10*$project['project']->level), 3) }}">
                        <td>{{ str_repeat('&nbsp;', $project['project']->level*10) . $project['project']->name }}</td>
                        <td>
                            <label for="company-{{ $project['project']->id }}"></label>
                            <select id="company-{{ $project['project']->id }}"
                                    name="projectToClient[{{ $project['project']->id }}]">
                                <option value="none">@lang('redbill.none')</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" {{ \Redbill\ProjectToClient::doesBelongTo($interfaceToken, $company->id, $project['project']->id)? 'selected=selected' : '' }}>{{ $company->company_name . ' / ' . $company->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @endif
                @if (count($project['entries']) > 0)
                    <tr class="{{ $project['project']->level == 0? 'info' : '' }}"
                        style="background-color: {{ $project['project']->level == 0? 'inherit' : '#' . str_repeat(dechex(255 - 10 * $project['project']->level), 3) }}">
                        <td colspan="2">
                            <table class="table table-condensed table-borderless" style="background-color: inherit">
                                <tr>
                                    <th>@lang('redbill.delivery_date')</th>
                                    <th class="text-center">@lang('redbill.amount')</th>
                                    <th>@lang('redbill.title')</th>
                                </tr>
                                @foreach ($project['entries'] as $entry)
                                    <tr>
                                        <td style="width: 1%">{{ $entry->spent_on }}</td>
                                        <td class="text-center" style="width: 1%">{{ $entry->amount }}</td>
                                        <td>{{ $entry->title }}</td>
                                        <td>
                                            <input type="hidden"
                                                   name="entries[{{ $project['project']->id }}][{{ $entry->entryId }}][spent_on]"
                                                   value="{{ $entry->spent_on }}">
                                            <input type="hidden"
                                                   name="entries[{{ $project['project']->id }}][{{ $entry->entryId }}][amount]"
                                                   value="{{ $entry->amount }}">
                                            <input type="hidden"
                                                   name="entries[{{ $project['project']->id }}][{{ $entry->entryId }}][title]"
                                                   value="{{ $entry->title }}">
                                            <input type="hidden"
                                                   name="entries[{{ $project['project']->id }}][{{ $entry->entryId }}][comment]"
                                                   value="{{ $entry->comment }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                @endif
                @endforeach
            </table>
            <input type="hidden" name="interfaceToken" value="{{ $interfaceToken }}"/>
            <button type="submit">@lang('redbill.save')</button>
        </form>

    </div>
@endsection
