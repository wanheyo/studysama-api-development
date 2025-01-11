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
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
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
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            text-align: left;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
        button {
            background-color: #7734A3;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
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
        <h1>Reset Password</h1>
        <form method="POST" action="{{ route('password.update') }}" id="resetForm">
            @csrf
            <input type="hidden" name="token" value="{{ request()->query('token') }}">
            <input type="hidden" name="email" value="{{ request()->query('email') }}">

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" required>
                <div class="error-message" id="passwordError"></div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
                <div class="error-message" id="passwordConfirmationError"></div>
            </div>

            <button type="submit">Reset Password</button>
        </form>
    </div>
    <footer>
        &copy; {{ date('Y') }} StudySama. All rights reserved.
    </footer>
</body>
</html>
