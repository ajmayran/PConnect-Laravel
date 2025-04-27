<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; border-collapse: collapse; background-color: #ffffff; border: 1px solid #e1e1e1;">
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #f39c12;">
                <h1 style="color: #ffffff; margin: 0;">Wait for Account Verification</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="margin-bottom: 15px;">Dear {{$distributor->company_name}},</p>
                <p style="margin-bottom: 15px;">Thank you for registering as a distributor with PConnect! Your account has been successfully created, but requires verification by our admin team before you can access all features.</p>
                
                <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin: 25px 0; border: 1px solid #e1e1e1;">
                    <tr style="background-color: #f8f9fa;">
                        <th style="text-align: left; border-bottom: 1px solid #e1e1e1;">Account Information</th>
                        <th style="text-align: left; border-bottom: 1px solid #e1e1e1;">Details</th>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e1e1e1;">Company Name</td>
                        <td style="border-bottom: 1px solid #e1e1e1;">{{$distributor->company_name}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e1e1e1;">Account Type</td>
                        <td style="border-bottom: 1px solid #e1e1e1;">Distributor</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e1e1e1;">Status</td>
                        <td style="border-bottom: 1px solid #e1e1e1;"><span style="color: #f39c12; font-weight: bold;">Pending Verification</span></td>
                    </tr>
                </table>
                
                <div style="background-color: #fff3cd; border-left: 4px solid #f39c12; padding: 15px; margin: 20px 0;">
                    <p style="margin: 0; color: #856404;"><strong>Note:</strong> We're currently reviewing your application. This typically takes 1-2 business days. You'll receive an email notification once your account has been verified.</p>
                </div>
                
                <p style="margin-bottom: 15px;">If you have any questions or need additional information, please contact our support team.</p>
                
                <p style="margin-top: 25px;">Thank you for your patience!</p>
                <p>Best regards,<br>The PConnect Team</p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f8f9fa; padding: 15px; text-align: center; border-top: 1px solid #e1e1e1;">
                <p style="font-size: 12px; color: #666; margin: 0;">&copy; {{ date('Y') }} PConnect. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>