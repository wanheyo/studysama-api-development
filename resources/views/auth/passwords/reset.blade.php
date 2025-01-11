<!-- resources/views/auth/passwords/reset.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
            <label for="password">New Password</label>
            <input type="password" name="password" required>
        </div>

        <div>
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" name="password_confirmation" required>
        </div>

        <button type="submit">Reset Password</button>
    </form>
</body>
</html>