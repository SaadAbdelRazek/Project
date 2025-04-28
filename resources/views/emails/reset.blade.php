<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }
        h1 {
            color: #4b4b4b;
            font-size: 24px;
        }
        p {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
        }
        .btn {
            background-color: #08053B;
            color: #ffffff;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .footer {
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Hi, {{ $name }}!</h1>
    <p>We received a request to reset your password. Click the button below to reset it:</p>

    <a href="{{ $resetLink }}" class="btn">Reset Password</a>

    <p>If you didn’t request a password reset, please ignore this email.</p>

    <div class="footer">
        <p>This email was sent to you because we received a password reset request for your account.</p>
        <p>&copy; 2025 Esaltare. All rights reserved.</p>
    </div>
</div>
</body>
</html>
