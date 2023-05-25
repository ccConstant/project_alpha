<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <script crossorigin="anonymous"></script>
    <head>
        <meta name="user-id" content="{{Auth::user()}}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Alpha software SW01&SW03</title>
    </head>
    <link href="{{mix('css/app.css')}}" rel="stylesheet">
    <body style="background-color: #212529;">
        <div id="app">
            <navbar></navbar>
            <br>
            <router-view style="height: auto; min-height:80vh;"></router-view>
            <br>
        </div>
        <script src="{{mix('js/app.js')}}"></script>
    </body>
    <footer style="height: 10vh; background-color: #212529;">
        <div style="margin-left: 10px; color : #ffffff;">
            <p> SW01 Version : 0.0.0  (last update Apr 2023)</p>
            <p> SW03 Version : 0.0.0  (last update Apr 2023)</p>
        </div>
    </footer>
</html>
