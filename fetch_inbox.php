<?php
session_start();
include 'db.php';
$user_id = $_SESSION['user_id'];

$sql = "SELECT DISTINCT 
            CASE WHEN sender_id = '$user_id' THEN receiver_id ELSE sender_id END AS other_user_id,
            users.name, 
            users.profile_pic,
            posts.title, 
            messages.post_id,
            (SELECT message_text FROM messages m2 
             WHERE (m2.sender_id = messages.sender_id AND m2.receiver_id = messages.receiver_id) 
             OR (m2.sender_id = messages.receiver_id AND m2.receiver_id = messages.sender_id) 
             ORDER BY m2.sent_at DESC LIMIT 1) as last_msg
        FROM messages 
        JOIN users ON (CASE WHEN sender_id = '$user_id' THEN receiver_id ELSE sender_id END) = users.id 
        JOIN posts ON messages.post_id = posts.id
        WHERE messages.sender_id = '$user_id' OR messages.receiver_id = '$user_id'
        GROUP BY other_user_id, messages.post_id
        ORDER BY (SELECT sent_at FROM messages m3 
                  WHERE (m3.sender_id = messages.sender_id AND m3.receiver_id = messages.receiver_id) 
                  OR (m3.sender_id = messages.receiver_id AND m3.receiver_id = messages.sender_id) 
                  ORDER BY m3.sent_at DESC LIMIT 1) DESC";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) { 
        $img = !empty($row['profile_pic']) ? $row['profile_pic'] : 'default_avatar.png';
        ?>
        <a href="chat.php?post_id=<?php echo $row['post_id']; ?>&other_id=<?php echo $row['other_user_id']; ?>" class="conversation-card">
            <div class="avatar">
                <img src="uploads/<?php echo $img; ?>" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
            </div>
            <div class="msg-info">
                <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                <span class="post-tag">Post: <?php echo htmlspecialchars($row['title']); ?></span>
                <p><?php echo htmlspecialchars($row['last_msg']); ?></p>
            </div>
            <div style="color: #9ca3af;">
                <i class="fa-solid fa-chevron-right"></i>
            </div>
        </a>
    <?php }
} else {
    echo "
    <div style='text-align:center; padding: 50px 20px;'>
        <i class='fa-solid fa-comment-slash' style='font-size: 50px; color: #d1d5db; margin-bottom: 15px;'></i>
        <p style='color: #9ca3af;'>No conversations found yet.</p>
    </div>";
}
?>