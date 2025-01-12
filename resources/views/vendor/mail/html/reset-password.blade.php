<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-sizing: border-box;
        }
        .logo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        h1 {
            color: #7734A3;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 20px;
        }
        a.button {
            display: inline-block;
            background-color: #7734A3;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 16px;
        }
        a.button:hover {
            background-color: #602f84;
        }
        footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/SS_Mobile.png') }}" alt="Logo">
        </div>
        <h1>Reset Your Password</h1>
        <p>Hello,</p>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <a href="{{ $url }}" class="button">Reset Password</a>
        <p>If you did not request a password reset, no further action is required.</p>
        <footer>
            &copy; {{ date('Y') }} StudySama. All rights reserved.
        </footer>
    </div>
</body>
</html>
