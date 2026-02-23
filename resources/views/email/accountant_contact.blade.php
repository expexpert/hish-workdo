<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .header { background: #f4f4f4; padding: 15px; border-bottom: 2px solid #e2e2e2; }
        .customer-box { border: 1px solid #ddd; padding: 10px; margin: 15px 0; background: #fffdf5; }
        .message-body { margin-top: 20px; white-space: pre-line; }
        .footer { font-size: 12px; color: #777; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Document Submission</h2>
    </div>

    <div class="customer-box">
        <strong>Customer Details:</strong><br>
        Name: {{ $data['customer_name'] }}<br>
        Email: {{ $data['customer_email'] }}<br>
        Date: {{ date('Y-m-d H:i') }}
    </div>

    <div class="message-body">
        <strong>Message from Customer:</strong><br>
        {{ $data['message'] }}
    </div>

    @if(isset($data['has_attachment']) && $data['has_attachment'])
        <p style="color: #2d3748;">📎 <strong>An attachment is included with this email.</strong></p>
    @endif

    <div class="footer">
        This email was sent via the Customer Portal API.
    </div>
</body>
</html>