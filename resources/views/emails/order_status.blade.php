<!DOCTYPE html>
<html>
<head>
    <title>Order status update</title>
</head>
<body>
<h2>Hello {{ $user->name }},</h2>
<p>Order status for article No {{ $data['article_id'] }} updated to : {{ $data['order_status'] }}</p>
<p><a href="{{ $data['url'] }}">View Order</a></p>
<p>Thank you for shopping with us!</p>
</body>
</html>
