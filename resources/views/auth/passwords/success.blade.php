<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Successful</title>
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
        .logo {
            margin: 0 auto 20px;
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
            font-size: 1.2em;
            color: #333;
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
        <h1>Password Reset Successful</h1>
        <p>You can close this tab and return to the StudySama app to log in with your new password.</p>
        <footer>
            &copy; {{ date('Y') }} StudySama. All rights reserved.
        </footer>
    </div>
</body>
</html>