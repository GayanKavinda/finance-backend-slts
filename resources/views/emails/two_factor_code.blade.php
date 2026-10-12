<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification Code</title>
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
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #0056B3 0%, #0077CC 100%);
            padding: 32px 0;
            text-align: center;
        }

        .logo-text {
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -1px;
            margin: 0;
        }

        .logo-accent {
            color: #F58220;
        }

        .content {
            padding: 36px 40px;
            color: #334155;
        }

        .otp-box {
            margin: 24px auto;
            text-align: center;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            font-size: 34px;
            font-weight: 700;
            letter-spacing: 10px;
            color: #0f172a;
        }

        .note {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        .footer {
            padding: 20px 40px 28px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <p class="logo-text">SLT <span class="logo-accent">ProcureX</span></p>
        </div>
        <div class="content">
            <h2 style="margin: 0 0 12px; font-size: 20px;">Two-Factor Verification</h2>
            <p class="note">
                A sign-in attempt requires additional verification. Enter the following
                one-time code to complete your login. The code expires in 10 minutes.
            </p>
            <div class="otp-box">{{ $otp }}</div>
            <p class="note">
                If this wasn't you, please secure your account immediately by changing
                your password and reviewing active sessions in Settings.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SLT ProcureX. Internal use only.
        </div>
    </div>
</body>

</html>
