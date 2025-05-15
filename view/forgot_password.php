<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="..\..\assests\css\login_style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
</head>
<body>
    <div class="wrapper">
        <form action="..\..\controller\firget_pass_check.php" method="POST">
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" id="email" required >
                <i class='bx bxs-user'></i>
            </div>

             <div class="input-box">
                <input type="password" id="cur_password" name="password" placeholder="Current Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <div class="input-box">
                <input type="password" id="new_password" name="password" placeholder="New Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div>
                <button type="button" id="submit-btn" class="btn">Reset Password</button> 
            </div>
        </form>
    </div>
    
    <script src="..\..\assests\js\forget_pass_Valida.js"></script>
</body>
</html>