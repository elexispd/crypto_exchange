<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Coafcare</title>
    <style type="text/css">
        /* Reset styles for email clients */
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* Main styles */
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #2c5aa0;
            padding: 20px;
            text-align: center;
        }
        .logo {
            max-width: 180px;
            height: auto;
        }
        .content {
            padding: 30px;
        }
        .footer {
            background-color: #f5f7fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666666;
        }
        .welcome-banner {
            background: linear-gradient(135deg, #2c5aa0, #3a6bc0);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
        }
        .feature-box {
            background-color: #f8f9fa;
            border-left: 4px solid #2c5aa0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .step-box {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }
        .step-number {
            background-color: #2c5aa0;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }
        .button {
            display: inline-block;
            background-color: #2c5aa0;
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 15px 0;
        }
        .security-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
        @media only screen and (max-width: 480px) {
            .container {
                width: 100% !important;
                border-radius: 0;
            }
            .content {
                padding: 20px !important;
            }
            .welcome-banner {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f5f5f5">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <!-- Main container -->
                <table role="presentation" class="container" cellspacing="0" cellpadding="0" border="0" width="600">
                    <!-- Header with logo -->
                    <tr>
                        <td class="header">
                           <x-logo class="logo" />
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <div class="welcome-banner">
                                <h1 style="margin: 0 0 10px 0; color: white;">Welcome to Coafcare!</h1>
                                <p style="margin: 0; opacity: 0.9;">We're excited to have you on board</p>
                            </div>

                            <h2 style="color: #2c5aa0; margin-top: 0;">Hello {{ $user->name }},</h2>

                            <p>Thank you for joining Coafcare! Your account has been successfully created and you're now part of our growing community of crypto enthusiasts.</p>

                            <div class="feature-box">
                                <h3 style="color: #2c5aa0; margin-top: 0;">Your Account Details:</h3>
                                <p><strong>Username:</strong> {{ $user->username }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>Member Since:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                                <p><strong>Account Status:</strong> <span style="color: #28a745;">Active</span></p>
                            </div>

                            <h3 style="color: #2c5aa0;">Get Started in 3 Easy Steps:</h3>

                            <div class="step-box">
                                <div style="display: flex; align-items: center;">
                                    <span class="step-number">1</span>
                                    <strong>Secure Your Account</strong>
                                </div>
                                <p style="margin: 10px 0 0 40px;">Enable two-factor authentication for enhanced security.</p>
                            </div>

                            <div class="step-box">
                                <div style="display: flex; align-items: center;">
                                    <span class="step-number">2</span>
                                    <strong>Fund Your Wallet</strong>
                                </div>
                                <p style="margin: 10px 0 0 40px;">Deposit crypto to start trading and investing.</p>
                            </div>

                            <div class="step-box">
                                <div style="display: flex; align-items: center;">
                                    <span class="step-number">3</span>
                                    <strong>Explore Features</strong>
                                </div>
                                <p style="margin: 10px 0 0 40px;">Discover trading, swapping, and investment opportunities.</p>
                            </div>



                            <div class="security-note">
                                <strong>🔒 Security Tip:</strong>
                                <p>Always keep your login credentials secure and never share them with anyone. Coafcare will never ask for your password via email.</p>
                            </div>

                            <p>If you have any questions or need assistance, our support team is here to help you 24/7.</p>

                            <p>Welcome aboard!<br>
                            <strong>The Coafcare Team</strong></p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p>© {{ date('Y') }} Coafcare. All rights reserved.</p>
                            <p style="font-size: 12px; color: #999;">
                                This is an automated welcome message. Please do not reply to this email.<br>
                                If you have any questions, contact us at support@coafcare.online
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
