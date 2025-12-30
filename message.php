<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$post_id = $_GET['post_id'];
$receiver_id = $_GET['receiver_id'];

// Check koro jodi agei kotha hoye thake, tahole direct chat-e niye jabe
$check_chat = mysqli_query($conn, "SELECT id FROM messages WHERE post_id='$post_id' AND 
    ((sender_id='$sender_id' AND receiver_id='$receiver_id') OR (sender_id='$receiver_id' AND receiver_id='$sender_id')) LIMIT 1");

if (mysqli_num_rows($check_chat) > 0) {
    header("Location: chat.php?post_id=$post_id&other_id=$receiver_id");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $msg_text = mysqli_real_escape_string($conn, $_POST['message']);
    
    $sql = "INSERT INTO messages (post_id, sender_id, receiver_id, message_text) 
            VALUES ('$post_id', '$sender_id', '$receiver_id', '$msg_text')";
    
    if (mysqli_query($conn, $sql)) {
        // Message pathanor por ekhon direct Chat page-e niye jabe
        header("Location: chat.php?post_id=$post_id&other_id=$receiver_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sohayok | Start Helping</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #eef2f3; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .msg-box { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 90%; max-width: 450px; text-align: center; }
        textarea { width: 100%; padding: 15px; border: 2px solid #e2e8f0; border-radius: 10px; margin: 15px 0; box-sizing: border-box; font-family: inherit; resize: none; }
        .send-btn { background: #10b981; color: white; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; width: 100%; font-weight: 600; font-size: 16px; }
        .cancel-link { display: block; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="msg-box">
        <i class="fa-solid fa-hand-holding-heart" style="font-size: 40px; color: #10b981; margin-bottom: 10px;"></i>
        <h3>Send a Help Offer</h3>
        <p style="color: #64748b; font-size: 14px;">Let them know how you can assist with this request.</p>
        
        <form method="POST">
            <textarea name="message" rows="4" placeholder="Hi! I have the books you need. I can drop them off tomorrow..." required></textarea>
            <button type="submit" class="send-btn"><i class="fa-solid fa-paper-plane"></i> Send First Message</button>
            <a href="dashboard.php" class="cancel-link">Maybe later</a>
        </form>
    </div>
</body>
</html>