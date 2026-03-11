<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f6f9;
            font-family: Arial, Helvetica, sans-serif;
        }

        .wrapper {
            width: 100%;
            padding: 40px 0;
            background: #f4f6f9;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .header {
            background: #2c7be5;
            color: #fff;
            padding: 18px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }

        .notification-box {
            background: #f8fafc;
            border-left: 4px solid #2c7be5;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: #2c7be5;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            padding: 16px;
            font-size: 12px;
            color: #888;
            background: #fafafa;
            border-top: 1px solid #eee;
        }
    </style>
</head>

<body>

<div class="wrapper">
    <div class="container">

        <div class="header">
            New Notification
        </div>

        <div class="content">

            <p>Hello,</p>

            <p>You have received a new notification from your accountant through <strong>{{ config('app.name') }}</strong>.</p>

            <div class="notification-box">
                {!! nl2br(e($messageContent)) !!}
            </div>

            <p>
                You can also view this notification directly in your account dashboard.
            </p>

        </div>

        <div class="footer">
            This email was sent as a notification from {{ config('app.name') }}.<br>
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>

    </div>
</div>

</body>

</html>