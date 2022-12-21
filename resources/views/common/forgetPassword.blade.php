
<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email-Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <style type="text/css">
        a:hover {text-decoration: underline !important;}
        body{
            background-image: url("https://freshly.luckistore.in/images/api_images/body-bg-image.png");
            background-repeat: no-repeat;
            background-position: center center;
    /* height: 100%; */
            background-size: contain;
        }
       table{
           background-color: transparent;
       }
    </style>
</head>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
    <!--100% body table-->
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
        style="font-family: 'Open Sans', sans-serif;@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); @import url('https://fonts.googleapis.com/css2?family=Inter:wght@700&family=Playfair+Display:ital,wght@0,400;1,700&display=swap');">

            <td>
                <table style="background-color: #f2f3f8; max-width:670px; margin:0 auto;" width="100%";background-image:"https://designmodo.com/demo/emailtemplate/images/header-background.jpg" border="0"
                    align="center" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="max-width:560px;background:#fff; border-radius:8px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr>
                                    <td style="text-align:center;">
                                        <h1 style="max-width:560px;font-family: 'Rubik',sans-serif;font-size:40px;font-weight: 400;color: #FFF;background-color:#03696D;margin: 0 auto;padding: 40px 0;border-radius: 8px;">FRESHLY MEALS</h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    <tr>
                    <tr>
                        <td>
                             <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="max-width:560px;background:#fff; border-radius:8px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr>
                                    <td style="padding:60px 70px;">
                                        <h1 style="margin-bottom:30px;font-size:36px;text-align:left;color:#03696D;font-family: 'Inter', sans-serif;font-weight: 700;">Don’t Fret...<br> We all forget.</h1>
                                        <p style="color:#455056; font-size:15px;line-height:24px; margin:0;text-align: left;">
                                            Somebody requested a new password for the account associated with {{ $data['email'] }}. No changes have been made to your account yet.
                                            <br>
                                            <br>
                                            Please Login with the below given password. We’ll have you up and running in no time.
                                        </p>
                                        <a href="javascript:void(0);"
                                            style="background:#E4EFEE;text-decoration:none !important; font-weight:700; margin-top:20px;margin-bottom: 20px; color:#03696D; font-size:14px;padding:10px 24px;display:block;border-radius:50px;width: 40%;font-family: 'Open Sans'">
                                            {{ $data['password'] }}
                                        </a>
                                        <p style="color:#455056; font-size:15px;line-height:24px; margin:0;text-align: left;">
                                            If you did not make this request then please ignore this email. This automated reset link is only valid for the next 30 minutes.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="max-width:560px;background:#fff; border-radius:8px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr>
                                    <td style="padding:20px 70px;">
                                        <h1 style="margin-bottom:30px;font-size:18px;text-align:center;color:#000;font-family: 'Inter', sans-serif;font-weight: 700;">Contact Us.</h1>
                                        <p style="color:#455056; font-size:15px;line-height:24px; margin:0;text-align:center;">
                                            For any queries feel free to contact us. We will do our best to respond to you within 24 hours (excl. holidays).
                                        </p>
                                        <a href="javascript:void(0)"
                                            style="text-decoration:none !important; font-weight:700; margin-top:20px;margin-bottom: 20px; color:#03696D;font-size:12px;font-family: 'Open Sans';";>hello@freshlymeals.ae
                                        </a>

                                        <h1 style="margin-bottom:30px;font-size:18px;text-align:center;color:#000;font-family: 'Inter', sans-serif;font-weight: 700;margin-top:15px;margin-bottom: 15px">Follow Us.</h1>
                                        <div class="social-buttons-block">
                                            <a class="social-button" href="#" style="margin-right:20px; text-decoration: none;">
                                                <img src="https://freshly.luckistore.in/images/api_images/Instagram.png">
                                            </a>

                                            <a class="social-button" href="#" style="margin-right:20px; text-decoration: none;">
                                                <img src="https://freshly.luckistore.in/images/api_images/Facebook.png">
                                            </a>

                                            <a class="social-button" href="#" style="margin-right:20px; text-decoration: none;">
                                                <img src="https://freshly.luckistore.in/images/api_images/Snapchat.png">
                                            </a>

                                            <a class="social-button" href="#" style="margin-right:20px; text-decoration: none;">
                                                <img src="https://freshly.luckistore.in/images/api_images/YouTube.png">
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    <tr>
                        <tr>
                            <td>
                                <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                    style="max-width:560px; text-align:center;">
                                    <tr>
                                        <td style="padding:20px 0px;">
                                            <h1 style="font-family: 'Rubik',sans-serif;font-size:30px;font-weight: 400;text-align: center;">FRESHLY MEALS</h1>
                                            <p style="color:#455056; font-size:10px;line-height:20px; margin:0;text-align:center;">
                                                You received this message because you're signed up for emails from Freshly Eats Hospitality Services about your program perks. You may unsubscribe or update your email preferences at any time by clicking the link below.
                                            </p>
                                          <div class="btn" style="text-align: center;">
                                            <a href="javascript:void(0);"
                                            style="background:#E4EFEE;text-decoration:none !important; font-weight:700; margin-top:20px;margin-bottom: 20px; color:#03696D;text-transform:uppercase; font-size:12px;padding:5px 20px;display:inline-block;border-radius:50px;font-family: 'Open Sans';text-align: center;">unsubscribe
                                          </a>
                                          </div>
                                          <p style="color:#455056; font-size:10px;line-height:20px; margin:0;text-align:center;">
                                            © 2022 Freshly Eats Hospitality Services
                                          </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        <tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
