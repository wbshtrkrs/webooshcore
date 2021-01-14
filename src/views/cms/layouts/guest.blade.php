<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>@yield('metaTitle') | {!! env('PROJECT_NAME') !!}</title>
    <meta name="description" content="@yield('metaDescription')">
    <meta name="keywords" content="@yield('metaKeywords')">

    <link rel="shortcut icon" href="{!! url('/') !!}/favicon.ico"/>

    @include('cms::layouts.part.css')

    @yield('cssCustom')

</head>
<body>


@yield('content')



@include('cms::layouts.part.js')
@yield('jsCustom')

</body>
</html>