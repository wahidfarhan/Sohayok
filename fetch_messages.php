<?php
session_start();
include 'db.php';
$user_id = $_SESSION['user_id'];
$other_id = $_GET['other_id'];
$post_id = $_GET['post_id'];

$sql = "SELECT * FROM messages 
        WHERE post_id = '$post_id' 
        AND ((sender_id = '$user_id' AND receiver_id = '$other_id') 
        OR (sender_id = '$other_id' AND receiver_id = '$user_id'))
        ORDER BY sent_at ASC";
$res = mysqli_query($conn, $sql);

if(mysqli_num_rows($res) > 0) {
    while($msg = mysqli_fetch_assoc($res)) {
        $class = ($msg['sender_id'] == $user_id) ? 'sent' : 'received';
        // Time format kora (e.g., 08:30 PM)
        $time = date('h:i A', strtotime($msg['sent_at']));
        
        echo '<div class="msg '.$class.'">';
        echo htmlspecialchars($msg['message_text']);
        echo '<br><small style="font-size: 10px; opacity: 0.7; display: block; margin-top: 5px; text-align: right;">'.$time.'</small>';
        echo '</div>';
    }
} else {
    echo '<p style="text-align:center; color:#999; font-size:12px; margin-top:20px;">Start your conversation...</p>';
}
?>