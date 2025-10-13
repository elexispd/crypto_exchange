<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Approved</title>
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
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        .amount-highlight {
            font-size: 24px;
            color: #2c5aa0;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-approved {
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
        .button {
            display: inline-block;
            background-color: #28a745;
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

                            <div class="success-banner">
                                <h3 style="margin: 0; color: #155724;">🎉 Withdrawal Approved!</h3>
                                <p style="margin: 10px 0 0 0;">Your withdrawal request has been approved and processed successfully.</p>
                            </div>

                            <div class="amount-highlight">
                                ${{ number_format($transaction->amount, 2) }} {{ strtoupper($transaction->currency) }}
                            </div>

                            <div class="transaction-details">
                                <p><strong>Status:</strong> <span class="status-approved">{{ ucfirst($transaction->status) }}</span></p>
                                <p><strong>Transaction ID:</strong> #{{ $transaction->id }}</p>
                                <p><strong>Approval Date:</strong> {{ now()->format('d M Y, h:i A') }}</p>
                                <p><strong>To Address:</strong> {{ $transaction->to_address }}</p>
                                @if($transaction->narrative)
                                <p><strong>Narrative:</strong> {{ $transaction->narrative }}</p>
                                @endif
                                <p><strong>Transaction Fee:</strong> ${{ number_format($transaction->fee, 2) }}</p>
                            </div>

                            <p><strong>Next Steps:</strong> The funds have been sent to your specified address. Please allow some time for the transaction to be confirmed on the blockchain.</p>


                            <p>If you have any questions about this transaction, please contact our support team.</p>
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
