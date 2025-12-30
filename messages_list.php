<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sohayok | Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #064e3b; --secondary: #10b981; --bg: #f3f4f6; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); margin: 0; color: #1f2937; }

        /* Sidebar & Nav Styles (Ager motoi thakbe) */
        .sidebar { width: 280px; background: var(--primary); height: 100vh; position: fixed; padding: 30px; color: white; display: flex; flex-direction: column; }
        .sidebar h2 { font-size: 24px; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
        .sidebar a { color: #d1d5db; text-decoration: none; padding: 12px 15px; border-radius: 12px; margin-bottom: 8px; display: flex; align-items: center; gap: 12px; transition: 0.3s; }
        .sidebar a.active { background: rgba(255,255,255,0.1); color: white; }
        .bottom-nav { display: none; position: fixed; bottom: 0; width: 100%; background: white; box-shadow: 0 -2px 15px rgba(0,0,0,0.1); z-index: 1000; justify-content: space-around; padding: 12px 0; }
        .bottom-nav a { color: #6b7280; text-decoration: none; font-size: 20px; }
        .bottom-nav a.active { color: var(--primary); }

        .main-content { margin-left: 300px; padding: 40px; max-width: 800px; }
        .inbox-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

        /* Card Styles */
        .conversation-card { background: white; border-radius: 16px; padding: 18px; margin-bottom: 12px; display: flex; align-items: center; gap: 15px; transition: 0.3s; text-decoration: none; color: inherit; border: 1px solid transparent; box-shadow: 0 2px 5px rgba(0,0,0,0.03); }
        .conversation-card:hover { transform: scale(1.01); border-color: var(--secondary); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
        .avatar { width: 50px; height: 50px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--primary); flex-shrink: 0; }
        .msg-info { flex: 1; min-width: 0; }
        .msg-info h4 { margin: 0; font-size: 16px; font-weight: 600; color: #111827; }
        .msg-info p { margin: 4px 0 0; font-size: 13px; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .post-tag { font-size: 11px; background: #f0fdf4; color: #166534; padding: 2px 8px; border-radius: 4px; margin-top: 5px; display: inline-block; }

        @media (max-width: 992px) { .sidebar { display: none; } .bottom-nav { display: flex; } .main-content { margin-left: 0; padding: 20px; padding-bottom: 80px; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-hand-holding-heart"></i> Sohayok</h2>
        <a href="dashboard.php"><i class="fa-solid fa-house"></i> Home Feed</a>
        <a href="messages_list.php" class="active"><i class="fa-solid fa-message"></i> Messages</a>
        <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <div style="margin-top: auto;">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
        <a href="messages_list.php" class="active"><i class="fa-solid fa-message"></i></a>
        <a href="profile.php"><i class="fa-solid fa-user"></i></a>
        <a href="logout.php"><i class="fa-solid fa-power-off"></i></a>
    </nav>

    <div class="main-content">
        <div class="inbox-header">
            <h2 style="margin: 0;">Messages</h2>
            <span style="font-size: 14px; color: #6b7280;">Conversations</span>
        </div>

        <div id="inbox-list">
            <p style="text-align:center;">Loading conversations...</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadInbox() {
            $.ajax({
                url: 'fetch_inbox.php', // Naya file lagbe
                type: 'GET',
                success: function(data) {
                    $('#inbox-list').html(data);
                }
            });
        }

        // Proti 5 second por por check korbe naya message ashlo ki na
        setInterval(loadInbox, 5000); 
        $(document).ready(loadInbox);
    </script>

</body>
</html>