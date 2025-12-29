<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #0056B3 0%, #0077CC 100%);
            padding: 40px 0;
            text-align: center;
        }

        .logo-text {
            color: #ffffff;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            margin: 0;
        }

        .logo-accent {
            color: #F58220;
        }

        .content {
            padding: 40px;
            text-align: center;
        }

        .title {
            color: #0f172a;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .text {
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .otp-box {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            margin: 0 auto 30px;
            display: inline-block;
        }

        .otp-code {
            color: #0056B3;
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 0;
        }

        .expiry {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 10px;
            font-weight: 500;
        }

        .footer {
            background-color: #f8fafc;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            color: #94a3b8;
            font-size: 11px;
            margin: 0;
        }

        .link {
            color: #0056B3;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- Ensure this image is hosted or use text fallback -->
            <h1 class="logo-text">Finance<span class="logo-accent">Pro</span></h1>
            <p style="color: rgba(255,255,255,0.8); margin: 5px 0 0; font-size: 13px; letter-spacing: 1px; text-transform: uppercase;">SLT Digital Services</p>
        </div>

        <div class="content">
            <h2 class="title">Reset Your Password</h2>
            <p class="text">We received a request to reset access for your Professional ID. Use the secure code below to proceed.</p>

            <div class="otp-box">
                <h1 class="otp-code">{{ $otp }}</h1>
            </div>

            <p class="expiry">This code expires in 15 minutes.</p>
            <p class="text" style="font-size: 13px; margin-bottom: 0;">If you didn't request this, you can safely ignore this email.</p>
        </div>

        <div class="footer">
            <p class="footer-text">&copy; {{ date('Y') }} SLT Digital Services. All rights reserved.</p>
            <p class="footer-text" style="margin-top: 5px;">Secure Account System • <a href="#" class="link">Support</a></p>
        </div>
    </div>
</body>

</html>