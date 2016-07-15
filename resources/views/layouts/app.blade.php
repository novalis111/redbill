<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Redbill</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"
          integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <!-- Styles jQuery UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css"
          integrity="sha384-UojmfO2c7ldhtzt0/YTNpjAu7TcwOHO4//eR4gYTPIw24QDZVOzu1JNngy0J3yk8" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.structure.min.css"
          integrity="sha384-gbqFFDg9vWXQVTZqOP0zfW8eIWwAI/RROBRFnqxO1VcAMxPDdMLDLQBujLyYUhV3" crossorigin="anonymous">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/cupertino/jquery-ui.min.css"
          integrity="sha384-5Bnr7j4Ly3ql1T2VdsUNFNByiP6sWs3xSkwOa42Ner8WCGf1MV/lE13LyhYXSy3D" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/cupertino/theme.min.css"
          integrity="sha384-M4cFRu2z+QmEiMfUNbtTNUq3W6K1WcyxgbzJon4r/hScBYglZPiwkUzwmg5vXZsG" crossorigin="anonymous">
    <!-- Styles App -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}"/>
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <style>
        body {
            font-family: 'Lato', sans-serif;
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                Redbill
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                @if (Auth::user())
                    <li><a href="{{ url('invoice') }}">{{ trans_choice('redbill.invoices', 2) }}</a></li>
                    <li><a href="{{ url('company') }}">{{ trans_choice('redbill.companies', 2) }}</a></li>
                    <li><a href="{{ url('asset') }}">{{ trans_choice('redbill.assets', 2) }}</a></li>
                @endif
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">@lang('redbill.login')</a></li>
                    <li><a href="{{ url('/register') }}">@lang('redbill.register')</a></li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="{{ url('/logout') }}">
                                    <i class="fa fa-btn fa-sign-out"></i>@lang('redbill.logout')
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

@include('common.flash')
@include('common.errors')
@yield('content')

        <!-- JavaScripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"
        integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>
<!-- Additions -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"
        integrity="sha384-YWP9O4NjmcGo4oEJFXvvYSEzuHIvey+LbXkBNJ1Kd0yfugEZN9NCQNpRYBVC1RvA"
        crossorigin="anonymous"></script>
<script src="{{ asset('js/app.js') }}"></script>
{{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
