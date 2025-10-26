<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($type === 'initial') Investment Started
        @elseif($type === 'interest') Interest Received
        @else Investment Completed
        @endif - Coafcare
    </title>
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
        .investment-banner {
            @if($type === 'initial')
            background: linear-gradient(135deg, #2c5aa0, #3a6bc0);
            @elseif($type === 'interest')
            background: linear-gradient(135deg, #28a745, #34ce57);
            @else
            background: linear-gradient(135deg, #6f42c1, #8c68cd);
            @endif
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
        }
        .investment-details {
            background-color: #f8f9fa;
            border-left: 4px solid #2c5aa0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .amount-highlight {
            font-size: 24px;
            color: #2c5aa0;
            font-weight: bold;
            margin: 10px 0;
        }
        .interest-amount {
            font-size: 28px;
            color: #28a745;
            font-weight: bold;
            margin: 15px 0;
        }
        .payout-box {
            background-color: #d4edda;
            border: 2px solid #c3e6cb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .next-steps {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
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
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            @if($type === 'initial')
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
            @elseif($type === 'interest')
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            @else
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            @endif
        }
        @media only screen and (max-width: 480px) {
            .container {
                width: 100% !important;
                border-radius: 0;
            }
            .content {
                padding: 20px !important;
            }
            .amount-highlight, .interest-amount {
                font-size: 20px !important;
            }
            .investment-banner {
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
                           <x-logo />
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <!-- Dynamic Banner based on type -->
                            <div class="investment-banner">
                                @if($type === 'initial')
                                <h1 style="margin: 0 0 10px 0; color: white;">🎯 Investment Started!</h1>
                                <p style="margin: 0; opacity: 0.9;">Your investment journey has begun</p>
                                @elseif($type === 'interest')
                                <h1 style="margin: 0 0 10px 0; color: white;">💰 Interest Earned!</h1>
                                <p style="margin: 0; opacity: 0.9;">Your investment is growing</p>
                                @else
                                <h1 style="margin: 0 0 10px 0; color: white;">🏆 Investment Completed!</h1>
                                <p style="margin: 0; opacity: 0.9;">Your investment cycle has ended</p>
                                @endif
                            </div>

                            <h2 style="color: #2c5aa0; margin-top: 0;">Hello {{ $user->name }},</h2>

                            <!-- Dynamic Message based on type -->
                            @if($type === 'initial')
                            <p>Great news! Your investment has been successfully activated and is now earning returns.</p>
                            @elseif($type === 'interest')
                            <p>Congratulations! Your investment has generated interest and your earnings have been credited.</p>
                            @else
                            <p>Your investment cycle has been successfully completed. Your funds are now available.</p>
                            @endif

                            <!-- Investment Details -->
                            <div class="investment-details">
                                <h3 style="color: #2c5aa0; margin-top: 0;">Investment Details</h3>
                                <p><strong>Investment ID:</strong> #{{ $investment->id }}</p>
                                <p><strong>Network:</strong> {{ strtoupper($investment->network) }}</p>
                                <p><strong>Initial Amount:</strong>
                                    <span class="amount-highlight">{{ number_format($investment->amount, 8) }} {{ strtoupper($investment->network) }}</span>
                                </p>
                                <p><strong>Status:</strong>
                                    <span class="status-badge">
                                        @if($type === 'initial') Active
                                        @elseif($type === 'interest') Earning
                                        @else Completed
                                        @endif
                                    </span>
                                </p>
                                <p><strong>Date:</strong> {{ $investment->created_at->format('M d, Y h:i A') }}</p>

                                @if($investment->investmentPlan)
                                <p><strong>Plan:</strong> {{ $investment->investmentPlan->name }}</p>
                                @endif
                            </div>

                            <!-- Interest/Payout Information -->
                            @if($type === 'interest' && $interestAmount)
                            <div class="payout-box">
                                <h3 style="color: #155724; margin-top: 0;">Interest Earned</h3>
                                <div class="interest-amount">
                                    +{{ number_format($interestAmount, 8) }} {{ strtoupper($investment->network) }}
                                </div>
                                <p>This interest has been automatically added to your investment balance.</p>
                            </div>
                            @endif

                            @if($type === 'completed' && $totalPayout)
                            <div class="payout-box">
                                <h3 style="color: #155724; margin-top: 0;">Total Payout</h3>
                                <div class="interest-amount">
                                    {{ number_format($totalPayout, 8) }} {{ strtoupper($investment->network) }}
                                </div>
                                <p>Your initial investment plus all earned interest has been returned to your wallet.</p>
                            </div>
                            @endif

                            <!-- Next Steps -->
                            <div class="next-steps">
                                <h4 style="color: #2c5aa0; margin-top: 0;">
                                    @if($type === 'initial') What's Next?
                                    @elseif($type === 'interest') Keep Growing
                                    @else What's Next?
                                    @endif
                                </h4>

                                @if($type === 'initial')
                                <p>Your investment is now active and will start earning returns according to your selected plan. You'll receive regular updates on your earnings.</p>
                                @elseif($type === 'interest')
                                <p>Your investment continues to earn returns. Consider reinvesting your earnings for compound growth.</p>
                                @else
                                <p>Your investment cycle has ended successfully. You can now withdraw your funds or start a new investment.</p>
                                @endif
                            </div>

                            <p>If you have any questions about your investment, our support team is always ready to assist you.</p>

                            <p>Happy Investing!<br>
                            <strong>The Coafcare Team</strong></p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p>© {{ date('Y') }} Coafcare. All rights reserved.</p>
                            <p style="font-size: 12px; color: #999;">
                                This is an automated investment notification. Please do not reply to this email.<br>
                                For support, contact us at support@coafcare.online
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
