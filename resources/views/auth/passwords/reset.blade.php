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
            padding: 20px;
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
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            position: relative;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="password"] {
            width: calc(100% - 40px); /* Adjust width for toggle button */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .toggle-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            position: relative;
        }
        .toggle-password-btn {
            margin-left: 10px;
            background: none;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 6px 10px;
            cursor: pointer;
            font-size: 0.9em;
            color: #7734A3;
        }
        .toggle-password-btn:hover {
            background-color: #f2f2f2;
            color: #602f84;
        }
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
        button {
            background-color: #7734A3;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
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
                <div class="toggle-wrapper">
                    <input type="password" name="password" id="password" required>
                    <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password', this)">Show</button>
                </div>
                <div class="error-message" id="passwordError"></div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <div class="toggle-wrapper">
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                    <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password_confirmation', this)">Show</button>
                </div>
                <div class="error-message" id="passwordConfirmationError"></div>
            </div>

            <button type="submit">Reset Password</button>
        </form>
    </div>
    <footer>
        &copy; {{ date('Y') }} StudySama. All rights reserved.
    </footer>

    <script>
        function togglePasswordVisibility(id, button) {
            const input = document.getElementById(id);
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            button.textContent = isPassword ? 'Hide' : 'Show';
        }
    </script>
</body>
</html>
