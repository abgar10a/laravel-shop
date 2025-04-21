<!DOCTYPE html>
<html>
<head>
    <title>NEW ORDER</title>
</head>
<body>
<h2>Hello {{ $user->name }},</h2>

<p>ARTICLE ID : {{ $data['article_id'] }}</p>
<p>MODEL : {{ $data['model'] }}</p>
<p>COUNT : {{ $data['quantity'] }}</p>
<p>ADDRESS : {{ $data['address'] }}</p>

<p><a href="{{ $data['url'] }}">View Order</a></p>
</body>
</html>
