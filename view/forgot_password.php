<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/login_style.css">
    <style>
        .message-area {
            margin-bottom: 15px;
            min-height: 20px;
            text-align: center;
        }
        .password-requirements {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        .success-message {
            color: green;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Change Password</h1>
        <div id="message-area" class="message-area"></div>
        <form id="resetForm" method="POST" autocomplete="off">
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-box">
                <input type="password" id="recent_password" name="recent_password" placeholder="Enter current password" required>
            </div>
            <div class="input-box">
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                <div class="password-requirements">
                    Password must be at least 5 characters long, containing at least one uppercase letter, one lowercase letter, and one number.
                </div>
            </div>
            <div class="input-box">
                <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Confirm new password" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
        <div class="register-link">
            <p><a href="login.php">Back to Login</a></p>
        </div>
    </div>

    <script>
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../controller/reset_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageArea = document.getElementById('message-area');
                messageArea.textContent = data.message;
                messageArea.className = 'message-area ' + (data.status === 'success' ? 'success-message' : 'error-message');
                
                if (data.status === 'success') {
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('message-area').textContent = 'An error occurred. Please try again.';
                document.getElementById('message-area').className = 'message-area error-message';
            });
        });
    </script>
</body>
</html>