<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f8f8f8 0%, #fff7e6 100%);
            margin: 0;
            padding: 0;
        }
        .container {
            background: #fff;
            max-width: 600px;
            margin: 40px auto;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(230, 103, 34, 0.08);
            padding: 40px 32px;
        }
        .brand {
            text-align: center;
            margin-bottom: 20px;
        }
        .brand h1 {
            color: #e62222;
            font-size: 2rem;
            margin: 0;
        }
        .otp-box {
            background: #fff7e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-top: 24px;
        }
        .otp-code {
            font-size: 2rem;
            color: #e67e22;
            letter-spacing: 4px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #888;
            text-align: center;
        }
        .highlight {
            color: #e67e22;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <h1>Crispy King</h1>
        </div>

        <h2 style="text-align:center;color:#e62222;">Password Reset Verification</h2>
        <p style="font-size:1.1rem;text-align:center;">
            You requested to reset your password.<br>
            Please use the OTP code below to verify your email.
        </p>

        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <p style="font-size:0.95rem;text-align:center;margin-top:20px;color:#555;">
            This code will expire in <span class="highlight">5 minutes</span>.
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} Crispy King. This is an automated message. Please do not reply.<br>
            <span style="color:#e67e22;">Thank you for choosing Crispy King!</span>
        </div>
    </div>
</body>
</html>
