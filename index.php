<?php
include 'db.php';
session_start();

// Jodi user agei login thake, tahole dashboard-e niye jabe
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Database theke latest kisu post fetch kora public view-r jonno
$sql = "SELECT posts.*, users.name, users.profile_pic 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC LIMIT 6";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sohayok | Together We Can</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #064e3b;
            --secondary: #10b981;
            --bg: #f8fafc;
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; color: #1e293b; }

        /* Navbar */
        nav {
            background: white; padding: 15px 5%; display: flex; justify-content: space-between;
            align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000;
        }
        .logo { font-size: 24px; font-weight: 700; color: var(--primary); text-decoration: none; }
        .nav-btns a { text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; transition: 0.3s; }
        .login-btn { color: var(--primary); margin-right: 10px; }
        .signup-btn { background: var(--primary); color: white; }
        .signup-btn:hover { background: var(--secondary); }

        /* Hero Section */
        .hero {
            text-align: center; padding: 80px 10%; background: linear-gradient(rgba(6, 78, 59, 0.9), rgba(6, 78, 59, 0.8)), url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80');
            background-size: cover; background-position: center; color: white;
        }
        .hero h1 { font-size: 42px; margin-bottom: 20px; }
        .hero p { font-size: 18px; max-width: 700px; margin: 0 auto 30px; opacity: 0.9; }

        /* Feed Preview */
        .container { padding: 50px 10%; }
        .section-title { text-align: center; margin-bottom: 40px; }
        .feed-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }

        .post-card {
            background: white; border-radius: 16px; padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eef2f6;
        }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; color: white; text-transform: uppercase; }
        .Blood { background: #ef4444; } .Food { background: #10b981; } .Books { background: #0ea5e9; }
        
        .user-info { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
        .user-info img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; }
        
        .help-now-btn {
            display: block; text-align: center; background: #f1f5f9; color: var(--primary);
            text-decoration: none; padding: 12px; border-radius: 10px; font-weight: 600; margin-top: 15px;
        }
        .help-now-btn:hover { background: var(--primary); color: white; }

        @media (max-width: 768px) {
            .hero h1 { font-size: 32px; }
            .nav-btns a { padding: 8px 15px; font-size: 14px; }
        }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo"><i class="fa-solid fa-hand-holding-heart"></i> Sohayok</a>
    <div class="nav-btns">
        <a href="login.php" class="login-btn">Login</a>
        <a href="register.php" class="signup-btn">Create Account</a>
    </div>
</nav>

<section class="hero">
    <h1>Bridging Hearts, Building Community</h1>
    <p>Sohayok is a peer-to-peer platform where individuals can request and offer help. Join our community to make a difference today.</p>
    <a href="register.php" class="signup-btn" style="padding: 15px 40px; font-size: 18px; border-radius: 30px;">Get Started for Free</a>
</section>

<div class="container">
    <div class="section-title">
        <h2>Recent Help Requests</h2>
        <p style="color: #64748b;">See how you can assist people in your community</p>
    </div>

    <div class="feed-grid">
        <?php while ($row = mysqli_fetch_assoc($result)) { 
            $p_pic = !empty($row['profile_pic']) ? $row['profile_pic'] : 'default_avatar.png';
        ?>
            <div class="post-card">
                <div class="user-info">
                    <img src="uploads/<?php echo $p_pic; ?>" alt="user">
                    <div>
                        <span style="font-size: 14px; font-weight: 600;"><?php echo htmlspecialchars($row['name']); ?></span><br>
                        <small style="color: #64748b;"><?php echo date('M d', strtotime($row['created_at'])); ?></small>
                    </div>
                </div>
                <span class="badge <?php echo $row['category']; ?>"><?php echo $row['category']; ?></span>
                <h3 style="margin: 10px 0; font-size: 18px;"><?php echo htmlspecialchars($row['title']); ?></h3>
                <p style="font-size: 14px; color: #475569; line-height: 1.5;">
                    <?php echo substr(htmlspecialchars($row['description']), 0, 100) . '...'; ?>
                </p>
                <a href="login.php" class="help-now-btn">Login to Help</a>
            </div>
        <?php } ?>
    </div>
</div>

<footer style="text-align: center; padding: 40px; color: #64748b; font-size: 14px;">
    &copy; 2025 Sohayok | Built with ❤️ for Social Good.
</footer>

</body>
</html>