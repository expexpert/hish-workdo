<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .email-wrapper {
            width: 100%;
            padding: 40px 0;
            background: #f5f7fb;
        }

        .email-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .email-header {
            background: #2c7be5;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .email-body {
            padding: 30px;
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }

        .email-footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #888;
            background: #fafafa;
        }
    </style>
</head>

<body>

    <div class="email-wrapper">
        <div class="email-container">

            <div class="email-header">
                {{ $subject }}
            </div>

            <div class="email-body">
                {!! nl2br(e($messageContent)) !!}
            </div>

            <div class="email-footer">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>

        </div>
    </div>

</body>

</html>