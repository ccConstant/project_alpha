<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <script crossorigin="anonymous"></script>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Alpha</title>
    </head>
    <link href="{{mix('css/app.css')}}" rel="stylesheet">
    <body>
        <div id="app">
            <navbar_alpha></navbar_alpha>
            <router-view></router-view>
        </div>

        @if ($errors->any())
            @foreach($error->all() as $error)
                <div> {{error }} </div>
            @endforeach
        @endif
        <script src="{{mix('js/app.js')}}"></script>
    </body>
</html>