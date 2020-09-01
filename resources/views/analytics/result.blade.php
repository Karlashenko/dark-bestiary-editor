<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/css/app.css">

    <title>Analytics</title>
</head>
<body>
<div class="container-fluid" style="padding: 25px 50px;">
    <div class="row">
        <div class="col-md-4">
            <h3>Skills</h3>
            <table class="table">
                @foreach($data['skills'] as $info)
                    <tr>
                        <td><img src="/resources/{{$info['skill']->icon}}.png" style="width: 32px;"></td>
                        <td>{{ $info['skill']->nameI18n->en }}</td>
                        <td>{{ $info['count'] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="col-md-4">
            <h3>Talents</h3>
            <table class="table">
                @foreach($data['talents'] as $info)
                    <tr>
                        <td><img src="/resources/{{$info['talent']->icon}}.png" style="width: 32px;"></td>
                        <td>{{ $info['talent']->nameI18n->en }}</td>
                        <td>{{ $info['count'] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="col-md-4">
            <h3>Relics</h3>
            <table class="table">
                @foreach($data['relics'] as $info)
                    <tr>
                        <td><img src="/resources/{{$info['relic']->icon}}.png" style="width: 32px;"></td>
                        <td>{{ $info['relic']->nameI18n->en }}</td>
                        <td>{{ $info['count'] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
</body>
</html>
