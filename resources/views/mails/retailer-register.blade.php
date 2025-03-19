<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; border-collapse: collapse; background-color: #ffffff; border: 1px solid #e1e1e1;">
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #4a69bd;">
                <h1 style="color: #ffffff; margin: 0;">Welcome to PConnect!</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="margin-bottom: 15px;">Dear {{$user->name}},</p>
                <p style="margin-bottom: 15px;">Thank you for registering with PConnect! Your account has been successfully created and is ready to use.</p>
                
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
                        <td style="border-bottom: 1px solid #e1e1e1;">Retailer</td>
                    </tr>
                </table>
                
                <p style="margin-bottom: 15px;">You can now log in to your account and start exploring our platform. If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ route('login') }}" style="background-color: #4a69bd; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 4px; font-weight: bold; display: inline-block;">Login Now</a>
                </div>
                
                <p style="margin-top: 25px;">Thank you for choosing PConnect!</p>
                <p>Best regards,<br>The PConnect Team</p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f8f9fa; padding: 15px; text-align: center; border-top: 1px solid #e1e1e1;">
                <p style="font-size: 12px; color: #666; margin: 0;">&copy; {{ date('Y') }} PConnect. All rights reserved.</p>
            </td>
        </tr>
    </table>