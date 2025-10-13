<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swap Completed</title>
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
        .swap-details {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #eaeaea;
        }
        .swap-arrow {
            text-align: center;
            font-size: 24px;
            color: #2c5aa0;
            margin: 10px 0;
        }
        .currency-box {
            background-color: #ffffff;
            border: 2px solid #eaeaea;
            border-radius: 6px;
            padding: 15px;
            margin: 10px 0;
        }
        .currency-amount {
            font-size: 20px;
            color: #2c5aa0;
            font-weight: bold;
            margin: 5px 0;
        }
        .currency-code {
            font-size: 16px;
            color: #666;
            font-weight: bold;
        }
        .status-completed {
            color: #28a745;
            font-weight: bold;
            font-size: 18px;
        }
        .success-banner {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .transaction-info {
            background-color: #f8f9fa;
            border-left: 4px solid #2c5aa0;
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
        .rate-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 4px;
            padding: 10px;
            margin: 15px 0;
            text-align: center;
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
            .currency-amount {
                font-size: 18px !important;
            }
            .swap-arrow {
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

                            <div class="success-banner">
                                <h3 style="margin: 0; color: #155724;">✅ Swap Completed Successfully!</h3>
                                <p style="margin: 10px 0 0 0;">Your currency exchange has been processed successfully.</p>
                            </div>

                            <div class="swap-details">
                                <!-- From Currency -->
                                <div class="currency-box">
                                    <div style="color: #666; font-size: 14px;">You Sent</div>
                                    <div class="currency-amount">{{ number_format($transaction->from_amount, 8) }}</div>
                                    <div class="currency-code">{{ $transaction->from_currency }}</div>
                                </div>

                                <!-- Arrow -->
                                <div class="swap-arrow">↓</div>

                                <!-- To Currency -->
                                <div class="currency-box">
                                    <div style="color: #666; font-size: 14px;">You Received</div>
                                    <div class="currency-amount">{{ number_format($transaction->to_amount, 8) }}</div>
                                    <div class="currency-code">{{ $transaction->to_currency }}</div>
                                </div>

                                <!-- Exchange Rate -->
                                <div class="rate-info">
                                    <strong>Exchange Rate:</strong>
                                    1 {{ $transaction->from_currency }} =
                                    {{ number_format($transaction->to_amount / $transaction->from_amount, 8) }} {{ $transaction->to_currency }}
                                </div>
                            </div>

                            <div class="transaction-info">
                                <p><strong>Status:</strong> <span class="status-completed">{{ ucfirst($transaction->status) }}</span></p>
                                <p><strong>Transaction ID:</strong> #{{ $transaction->id }}</p>
                                <p><strong>Date:</strong> {{ $transaction->created_at->format('d M Y, h:i A') }}</p>
                                <p><strong>Wallet:</strong> {{ $transaction->wallet_id }}</p>
                            </div>

                            <p>The exchanged funds are now available in your wallet. You can use them for trading, withdrawals, or further exchanges.</p>

                            <p>If you did not initiate this swap or notice any suspicious activity, please contact our support team immediately.</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p>Thank you for choosing <strong>Coafcare</strong> for your crypto exchanges.</p>
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
