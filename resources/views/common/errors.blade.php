@if (count($errors) > 0)
        <!-- Form Error List -->
<div class="container">
    <div class="alert alert-danger">
        <strong>@lang('redbill.error_occured')</strong>

        <br><br>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif