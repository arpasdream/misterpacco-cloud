<!DOCTYPE html>
<html lang="it-IT">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ $content['subject'] ?? 'Newsletter' }}</title>
</head>
<body>
{!! $content['body'] !!}

<div style="margin-top:20px; color:#fff; background-color:#19BCBF; padding:10px; font-size:16px; text-align:center;">
    <a href="{{ url('/unsubscribe?email='.$content['email']) }}"
       style="color:#fff; text-decoration:none; font-weight:bold;">
        CANCELLATI QUI
    </a>
</div>
</body>
</html>
