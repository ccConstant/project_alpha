<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <script crossorigin="anonymous"></script>
    <head>
        <meta name="user-id" content="{{Auth::user()}}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Article and supplier data mngt SW03</title>
    </head>
    <link href="{{mix('css/app.css')}}" rel="stylesheet">
    <body>
        <div id="app">
        <navbar_sw03></navbar_sw03>
        <router-view></router-view>
        </div>
        <script src="{{mix('js/app.js')}}"></script>
    </body>
</html>
