<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email-Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">
    <style type="text/css">
        a:hover {
            text-decoration: underline !important;
        }
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
            <table style="background-color: #f2f3f8; max-width:670px; margin:0 auto;" width="100%"
                ;background-image:"https://designmodo.com/demo/emailtemplate/images/header-background.jpg" border="0"
                align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                            style="max-width:560px;background:#fff; border-radius:8px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                            <tr>
                                <td style="text-align:center;">
                                    <h1
                                        style="max-width:560px;font-family: 'Rubik',sans-serif;font-size:40px;font-weight: 400;color: #FFF;background-color:#03696D;margin: 0 auto;padding: 40px 0;border-radius: 8px;">
                                        FRESHLY MEALS</h1>
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
                            <h1
                                style="margin-bottom:30px;font-size:36px;text-align:left;color:#03696D;font-family: 'Inter', sans-serif;font-weight: 700;">
                                Yay! <br> You’re back.</h1>
                            <p style="color:#455056; font-size:15px;line-height:24px; margin:0;text-align: left;">
                                Glad you’re back!
                                <br>
                                <br>
                                Your request to restart your meal plan has been received and is being processed.
                                <br>
                                <br>

                            </p>

                    <tr>
                    <tr>
                        <td>
                            <h1
                                style="margin-bottom:20px;font-size:18px;text-align:center;color:#000;font-family: 'Inter', sans-serif;font-weight: 700;">
                                Your Plan</h1>
                        </td>
                    </tr>

                    <td>
                        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                            style="max-width:420px;background:#fff; border-radius:8px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);padding: 20px;">
                            <tr>
                                <td>
                                    <img src="{{"https://freshly.luckistore.in/images/api_images"}}/{{ $data->image }}">
                                </td>
                                <td>
                                    <h1
                                        style="margin-bottom:0px;font-size:16px;text-align:left;color:#000;font-family: 'Inter', sans-serif;font-weight: 700;">
                                        {{ $data->meal_name }} </h1>
                                    <ul style="padding-left: 0;">
                                        <li
                                            style="list-style: none; text-align: left;font-size:14px;margin-bottom: 5px;">
                                            {{ $data->weeks }} Weeks</li>
                                        <li
                                            style="list-style: none; text-align: left;font-size:14px;margin-bottom: 5px">
                                            {{ $data->days }} Days Per Week</li>
                                        <li
                                            style="list-style: none; text-align: left;font-size:14px;margin-bottom: 5px">
                                            {{ $data->meals }} Meals Per Day</li>
                                        <li
                                            style="list-style: none; text-align: left;font-size:14px;margin-bottom: 5px">
                                            {{ $data->snacks }} Snacks Per Day</li>
                                        <li
                                            style="list-style: none; text-align: left;font-size:14px;margin-bottom: 5px">
                                            Start Date {{ $data->start_date_order }}</li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </td>
                    </tr>



                    <tr>
                    <tr>
                        <td>
                            <h1
                                style="margin-bottom:20px;font-size:18px;text-align:center;color:#000;font-family: 'Inter', sans-serif;font-weight: 700;">
                                Order Details</h1>
                        </td>
                    </tr>

                    <td>
                        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                            style="max-width:420px;background:#fff; border-radius:8px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);padding: 20px;">
                            <tr>
                                <th style="text-align: left;font-size: 14px;">Name</th>
                                <td style="text-align: right;font-size: 12px;line-height: 20px;">{{ $data->name }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;font-size: 14px;">Order Number</th>
                                <td style="text-align: right;font-size: 12px;line-height: 20px;">{{ $data->order_number }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;font-size: 14px;">Order Date</th>
                                <td style="text-align: right;font-size: 12px;line-height: 20px;">{{ $data->order_date }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;font-size: 14px;">Email</th>
                                <td style="text-align: right;font-size: 12px;line-height: 20px;">{{ $data->email }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;font-size: 14px;">Contact</th>
                                <td style="text-align: right;font-size: 12px;line-height: 20px;">{{ $data->mobile_number }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;font-size: 14px;">Delivery</th>
                                <td style="text-align: right;font-size: 12px;line-height: 20px;">{{ $data->location_tag }}</td>
                            </tr>

                        </table>
                    </td>
                    </tr>
                    {{-- <tr>
                        <td style="padding:0px 70px;display:flex;justify-content: space-between;">
                            <a href="javascript:void(0);"
                                style="background:#E4EFEE;text-decoration:none !important; font-weight:700; margin-top:20px;margin-bottom: 20px; color:#03696D;text-transform:uppercase; font-size:14px;padding:10px 24px;display:block;border-radius:50px;width: 20%;font-family: 'Open Sans'">OK
                            </a>

                            <a href="javascript:void(0);"
                                style="background:#E4EFEE;text-decoration:none !important; font-weight:700; margin-top:20px;margin-bottom: 20px; color:#03696D;text-transform:uppercase; font-size:14px;padding:10px 24px;display:block;border-radius:50px;width: 20%;font-family: 'Open Sans'">SUPPORT
                            </a>
                        </td>

                    </tr> --}}
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
    </tr>
    <tr>
        <td>
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                style="max-width:560px; text-align:center;">
                <tr>
                    <td style="padding:20px 0px;">
                        <h1 style="font-family: 'Rubik',sans-serif;font-size:30px;font-weight: 400;text-align: center;">
                            FRESHLY MEALS</h1>
                        <p style="color:#455056; font-size:10px;line-height:20px; margin:0;text-align:center;">
                            You received this message because you're signed up for emails from Freshly Eats Hospitality
                            Services about your program perks. You may unsubscribe or update your email preferences at
                            any time by clicking the link below.
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
