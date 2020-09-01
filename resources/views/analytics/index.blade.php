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
<div class="container" style="padding: 50px;">
    <form action="/analytics" method="POST">
        {!!  csrf_field() !!}

        <div class="form-group">
            <textarea class="form-control" name="data" style="width: 100%; height: 400px;"></textarea>
        </div>

        <button type="submit" class="btn btn-wide btn-primary">Submit</button>
    </form>
</div>
</body>
</html>
