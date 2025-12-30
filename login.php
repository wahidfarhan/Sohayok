<?php
session_start();
include 'db.php';
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            header("Location: dashboard.php");
        } else {
            $msg = "❌ Invalid Password!";
        }
    } else {
        $msg = "❌ No account found with this email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sohayok | Welcome Back</title>
    <style>
        body {
            margin: 0; padding: 0;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
                        url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover; background-position: center;
            height: 100vh; display: flex; justify-content: center; align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .glass-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px; border-radius: 20px;
            width: 350px; color: white; text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        input {
            width: 100%; padding: 12px; margin: 10px 0;
            background: rgba(255, 255, 255, 0.2);
            border: none; border-radius: 8px;
            color: white; outline: none; box-sizing: border-box;
        }
        button {
            width: 100%; padding: 12px; margin-top: 15px;
            background: #10b981; border: none; border-radius: 8px;
            color: white; font-weight: bold; cursor: pointer;
        }
        button:hover { background: #059669; }
        a { color: #10b981; text-decoration: none; font-weight: bold; }
        .error { color: #ffa3a3; font-size: 13px; margin-bottom: 10px; }
        /* Sob input field-er placeholder color change korar jonno */
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
    <h2>Welcome Back</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        
        <div style="position: relative;">
            <input type="password" name="password" id="password" placeholder="Enter Password" required>
            <i class="fa-solid fa-eye" id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #fff;"></i>
        </div>

        <button type="submit">Login Now</button>
    </form>
</div>
<script>
    const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

togglePassword.addEventListener('click', function (e) {
    // পাসওয়ার্ড টাইপ পরিবর্তন করা (password থেকে text এবং উল্টোটা)
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    // আইকন পরিবর্তন করা (চোখ খোলা বনাম চোখ বন্ধ)
    this.classList.toggle('fa-eye-slash');
});
</script>
</body>
</html>