<!DOCTYPE html>
<html>
<head>
    <title>New Document Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background: #007bff; color: white; padding: 10px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; }
        .button { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Document Uploaded via WhatsApp</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>A new document has been received from <strong>{{ $data['customer_name'] }}</strong> via WhatsApp.</p>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Type:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $data['type'] }}</td>
                </tr>
                @if(isset($data['amount']))
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Amount:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $data['amount'] }}</td>
                </tr>
                @endif
                @if(isset($data['month_year']))
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Period:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $data['month_year'] }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Date:</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $data['date'] }}</td>
                </tr>
            </table>

            <p style="margin-top: 20px; text-align: center;">
                <a href="{{ $data['dashboard_url'] }}" class="button">View in Dashboard</a>
            </p>
        </div>
        <div class="footer">
            <p>Sent automatically by {{ config('app.name') }} WhatsApp Bot.</p>
        </div>
    </div>
</body>
</html>
