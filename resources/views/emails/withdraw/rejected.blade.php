<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Rejected</title>
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
        .transaction-details {
            background-color: #f9f9f9;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        .amount-highlight {
            font-size: 24px;
            color: #2c5aa0;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
            font-size: 18px;
        }
        .rejection-banner {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .refund-notice {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
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
        @media only screen and (max-width: 480px) {
            .container {
                width: 100% !important;
                border-radius: 0;
            }
            .content {
                padding: 20px !important;
            }
            .amount-highlight {
                font-size: 20px !important;
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
                            <x-logo />
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <h2 style="margin-top: 0; color: #2c5aa0;">Hello {{ $user->name }},</h2>

                            <div class="rejection-banner">
                                <h3 style="margin: 0; color: #721c24;">⚠️ Withdrawal Rejected</h3>
                                <p style="margin: 10px 0 0 0;">Your withdrawal request has been rejected.</p>
                            </div>

                            <div class="amount-highlight">
                                ${{ number_format($transaction->amount, 2) }} {{ strtoupper($transaction->currency) }}
                            </div>

                            <div class="refund-notice">
                                <p><strong>Good News:</strong> The total amount of <strong>${{ number_format($transaction->amount + $transaction->fee, 2) }}</strong> including the transaction fee has been refunded to your wallet.</p>
                            </div>

                            <div class="transaction-details">
                                <p><strong>Status:</strong> <span class="status-rejected">{{ ucfirst($transaction->status) }}</span></p>
                                <p><strong>Transaction ID:</strong> #{{ $transaction->id }}</p>
                                <p><strong>Rejection Date:</strong> {{ now()->format('d M Y, h:i A') }}</p>
                                <p><strong>To Address:</strong> {{ $transaction->to_address }}</p>
                                @if($transaction->narrative)
                                <p><strong>Narrative:</strong> {{ $transaction->narrative }}</p>
                                @endif
                            </div>

                            <p><strong>Possible Reasons for Rejection:</strong></p>
                            <ul>
                                <li>Invalid destination address</li>
                                <li>Security verification required</li>
                                <li>Insufficient documentation</li>
                                <li>Suspicious activity detected</li>
                            </ul>

                            <p>For more specific information about why your withdrawal was rejected, please contact our support team.</p>

                            <div style="text-align: center; color:white;">
                                <a href="mailto:support@coafcare.org" class="btn btn-primary">Contact Support</a>
                            </div>

                            <p>You may submit a new withdrawal request once the issue has been resolved.</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p>Thank you for choosing <strong>Coafcare</strong>.</p>
                            <p>© {{ date('Y') }} Coafcare. All rights reserved.</p>
                            <p style="font-size: 12px; color: #999;">
                                This is an automated message, please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
