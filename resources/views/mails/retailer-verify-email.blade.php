<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Retailer Account</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #10b981;
            padding: 30px 20px;
            text-align: center;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            background-color: #ffffff;
        }
        .content h2 {
            color: #111827;
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .content p {
            margin-bottom: 20px;
            color: #4b5563;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 25px 0;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #059669;
        }
        .verification-link {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            word-break: break-all;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 30px;
        }
        .verification-link a {
            color: #10b981;
            text-decoration: none;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
            gap: 10px;
        }
        .social-icon {
            display: inline-block;
            width: 32px;
            height: 32px;
            background-color: #e5e7eb;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('img/Pconnect Logo.png') }}" alt="PConnect Logo" class="logo">
            <h1>Verify Your Email Address</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $user->first_name }}!</h2>
            
            <p>Thank you for registering as a retailer with PConnect! Before you can start using our platform features, we need to verify your email address.</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verify My Email</a>
            </div>
            
            <p>If the button above doesn't work, you can copy and paste this URL into your browser:</p>
            <div class="verification-link">
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>
            
            <p>After verification, you'll be able to:</p>
            <ul>
                <li>Browse products from various distributors</li>
                <li>Place orders with preferred suppliers</li>
                <li>Track your shipments and manage your inventory</li>
                <li>Access exclusive deals and promotions</li>
            </ul>
            
            <p>If you did not create an account with PConnect, please disregard this email.</p>
            
            <p>We're excited to have you join our network of retailers!</p>
            
            <p>Best regards,<br>The PConnect Team</p>
        </div>
        <div class="footer">
            <div class="social-icons">
                <a href="#" class="social-icon">f</a>
                <a href="#" class="social-icon">in</a>
                <a href="#" class="social-icon">t</a>
            </div>
            <p>© {{ date('Y') }} PConnect. All rights reserved.</p>
            {{-- <p>123 Business Street, Metro Manila, Philippines</p> --}}
        </div>
    </div>
</body>
</html>