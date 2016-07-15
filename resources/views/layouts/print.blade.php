<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">

    <!-- Invoice Font -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic'
          rel='stylesheet' type='text/css'>

    <!-- Styles Print -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/print.css') }}"/>
</head>
<body id="print-layout">
@yield('content')
</body>
</html>
