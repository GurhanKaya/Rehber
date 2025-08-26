<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test E-posta</title>
</head>
<body>
    <h1>Test E-posta</h1>
    <p>{{ $message ?? 'Bu bir test e-postasıdır.' }}</p>
    <p>Gönderim zamanı: {{ now() }}</p>
</body>
</html>
