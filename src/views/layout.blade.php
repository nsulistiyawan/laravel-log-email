<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> {{ $title }} </title>
</head>
<body>
<div style="padding: 20px;">
    @foreach($errors as $key => $value)
        <div>
           <strong> {{ strtoupper($key) }} </strong>
        </div>
        <div style="color: orangered">
            {{ print_r($value,true); }}
        </div>
        <br>
    @endforeach
</div>
</body>
</html>