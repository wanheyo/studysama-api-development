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
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="password"] {
            width: calc(100% - 20px); /* Ensure input aligns perfectly */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 35px;
            cursor: pointer;
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

    <script>
        document.getElementById('resetForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (response.ok) {
                    window.location.href = '{{ route('password.success') }}';
                } else {
                    // handle validation errors
                    if (result.errors) {
                        document.getElementById('passwordError').textContent = result.errors.password?.[0] || '';
                        document.getElementById('passwordConfirmationError').textContent = result.errors.password_confirmation?.[0] || '';
                    } else {
                        alert(result.message);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    </script>
</body>
</html>
