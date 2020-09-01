<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Import</title>
</head>
<body>
<form action="/import" method="POST">
    {!!  csrf_field() !!}
    <textarea name="data" id="" cols="30" rows="10"></textarea>
    <button type="submit">Submit</button>
</form>
</body>
</html>
