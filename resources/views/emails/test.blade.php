<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
    <style>
        div{
            width: 70%;
            margin: auto;
        }
    </style>
</head>
<body>
    <!-- dinamikus adatbeillesztés -->
    <h1>{{ $mailData['title'] }}</h1>
    <p> {{ $mailData['body'] }}</p>
    <div>
        <img src="https://static.scientificamerican.com/sciam/cache/file/F766A67E-A8AA-4C90-A929C9AC67075D4B_source.jpg?w=900" alt="cica">
    </div>
    
</body>
</html>