<?php
include 'db.php';
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkEmail = "SELECT email FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
        $msg = "❌ Email already exists!";
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if (mysqli_query($conn, $sql)) {
            $msg = "✅ Registration Successful! <a href='login.php' style='color:#fff;'>Login here</a>";
        } else {
            $msg = "❌ Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sohayok | Join Us</title>
    <style>
        body {
            margin: 0; padding: 0;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex; justify-content: center; align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .glass-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px;
            border-radius: 20px;
            width: 380px;
            color: white;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            text-align: center;
        }
        h2 { margin-bottom: 10px; font-size: 28px; }
        p { font-size: 14px; opacity: 0.9; }
        input {
            width: 100%; padding: 12px; margin: 10px 0;
            background: rgba(255, 255, 255, 0.2);
            border: none; border-radius: 8px;
            color: white; outline: none; box-sizing: border-box;
        }
        input::placeholder { color: #ddd; }
        button {
            width: 100%; padding: 12px; margin-top: 15px;
            background: #10b981; border: none; border-radius: 8px;
            color: white; font-weight: bold; cursor: pointer;
            transition: 0.3s; font-size: 16px;
        }
        button:hover { background: #059669; transform: translateY(-2px); }
        .msg { font-size: 13px; margin-bottom: 10px; }
        a { color: #10b981; text-decoration: none; font-weight: bold; }
        input::placeholder {
    color: #ffffff; /* Ekhane tomar pochondo moto color code boshao */
    opacity: 0.8;   /* Placeholder ektu jhapsha rakhar jonno (optional) */
}

/* Specific kono field-er (jemon shudhu email) placeholder change korte chaile */
input[type="email"]::placeholder {
    color: #ffffff;
}
    </style>
</head>
<body>
    <div class="glass-box">
        <h2>Sohayok</h2>
        <p>Together we can make a difference</p>
        <div class="msg"><?php echo $msg; ?></div>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Create Password" required>
            <button type="submit">Start Helping</button>
        </form>
        <p style="margin-top:20px;">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>