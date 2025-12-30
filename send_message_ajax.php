<?php
session_start();
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $post_id = $_POST['post_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if(!empty($message)) {
        mysqli_query($conn, "INSERT INTO messages (post_id, sender_id, receiver_id, message_text) 
                             VALUES ('$post_id', '$sender_id', '$receiver_id', '$message')");
    }
}
?>