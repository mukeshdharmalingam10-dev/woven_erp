<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #667eea;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .credentials {
            background: white;
            padding: 20px;
            border-left: 4px solid #667eea;
            margin: 20px 0;
        }
        .credentials strong {
            color: #667eea;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to {{ config('app.name') }}</h1>
    </div>
    <div class="content">
        <p>Hello {{ $user->name }},</p>
        
        <p>Your account has been created successfully. Below are your login credentials:</p>
        
        <div class="credentials">
            <p><strong>Username (Email):</strong> {{ $user->email }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
        </div>
        
        <p><strong>Important:</strong> Please change your password after your first login for security purposes.</p>
        
        <p>You can access the system using the following link:</p>
        <p style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Login to System</a>
        </p>
        
        <p>If you have any questions or need assistance, please contact your system administrator.</p>
        
        <p>Best regards,<br>{{ config('app.name') }} Team</p>
    </div>
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>

