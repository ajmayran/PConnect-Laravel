<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; border-collapse: collapse; background-color: #ffffff; border: 1px solid #e1e1e1;">
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #2ecc71;">
                <h1 style="color: #ffffff; margin: 0;">Your Account Has Been Verified!</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="margin-bottom: 15px;">Dear {{$user->name}},</p>
                <p style="margin-bottom: 15px;">Great news! Your distributor account has been successfully verified by our admin team. You now have full access to all PConnect distributor features.</p>
                
                <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse: collapse; margin: 25px 0; border: 1px solid #e1e1e1;">
                    <tr style="background-color: #f8f9fa;">
                        <th style="text-align: left; border-bottom: 1px solid #e1e1e1;">Account Information</th>
                        <th style="text-align: left; border-bottom: 1px solid #e1e1e1;">Details</th>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e1e1e1;">Username</td>
                        <td style="border-bottom: 1px solid #e1e1e1;">{{$user->email}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e1e1e1;">Registration Date</td>
                        <td style="border-bottom: 1px solid #e1e1e1;">{{$user->created_at->format('F j, Y')}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e1e1e1;">Account Type</td>
                        <td style="border-bottom: 1px solid #e1e1e1;">Distributor</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e1e1e1;">Status</td>
                        <td style="border-bottom: 1px solid #e1e1e1;"><span style="color: #2ecc71; font-weight: bold;">Verified</span></td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e1e1e1;">Verification Date</td>
                        <td style="border-bottom: 1px solid #e1e1e1;">{{now()->format('F j, Y')}}</td>
                    </tr>
                </table>
                
                <div style="background-color: #d4edda; border-left: 4px solid #2ecc71; padding: 15px; margin: 20px 0;">
                    <p style="margin: 0; color: #155724;"><strong>Next Steps:</strong> Log in to your account to complete your profile, upload your product catalog, and start connecting with retailers.</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('login') }}" style="background-color: #2ecc71; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 4px; font-weight: bold; display: inline-block;">Login to Your Account</a>
                </div>
                
                <p style="margin-top: 25px;">Welcome to the PConnect distributor network!</p>
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