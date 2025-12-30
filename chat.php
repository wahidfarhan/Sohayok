<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$other_id = $_GET['other_id'];
$post_id = $_GET['post_id'];

// Get Other User Info
$user_query = mysqli_query($conn, "SELECT name FROM users WHERE id='$other_id'");
$other_user = mysqli_fetch_assoc($user_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Chat with <?php echo $other_user['name']; ?> | Sohayok</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
    /* Global Reset */
    * { 
        box-sizing: border-box; 
        margin: 0; 
        padding: 0; 
    }
    
    body { 
        font-family: 'Poppins', sans-serif; 
        background: #f0f2f5; 
        height: 100vh; 
        display: flex; 
        flex-direction: column; 
        overflow: hidden; /* Body scroll bondho jate shudhu chat-box scroll hoy */
    }

    /* Header Styling */
    .chat-header { 
        background: #064e3b; 
        color: white; 
        padding: 12px 15px; 
        display: flex; 
        align-items: center; 
        gap: 15px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        z-index: 1002;
        flex-shrink: 0;
    }
    .chat-header a { 
        color: white; 
        text-decoration: none; 
        font-size: 18px; 
        display: flex;
        align-items: center;
    }
    .chat-header .name { 
        font-weight: 500; 
        font-size: 16px; 
    }

    /* Chat Messages Area */
    #chat-box { 
        flex: 1; 
        overflow-y: auto; 
        padding: 15px; 
        display: flex; 
        flex-direction: column; 
        gap: 12px;
        background: #e5ddd5; /* Classic chat background */
        /* --- Latest message visibility fix --- */
        padding-bottom: 110px; 
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
    
    /* Message Bubbles */
    .msg { 
        max-width: 80%; 
        padding: 10px 14px; 
        border-radius: 15px; 
        font-size: 14px; 
        line-height: 1.5; 
        word-wrap: break-word; 
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        position: relative;
    }
    
    .sent { 
        background: #dcf8c6; 
        color: #333; 
        align-self: flex-end; 
        border-bottom-right-radius: 2px; 
    }
    
    .received { 
        background: #ffffff; 
        color: #333; 
        align-self: flex-start; 
        border-bottom-left-radius: 2px; 
    }

    /* Time styling inside bubbles */
    .msg small {
        font-size: 10px;
        margin-top: 4px;
        display: block;
        text-align: right;
        opacity: 0.7;
    }
    .sent small { color: #555; }
    .received small { color: #888; }

    /* --- FIXED INPUT AREA (MOBILE OPTIMIZED) --- */
    .input-area { 
        background: #f0f0f0; 
        padding: 10px 15px; 
        display: flex; 
        gap: 10px; 
        align-items: center; 
        position: fixed; 
        bottom: 0; 
        left: 0;
        width: 100%; 
        z-index: 1001;
        border-top: 1px solid #ccc;
        box-sizing: border-box; /* Padding jate width ke boro na kore */
        padding-bottom: env(safe-area-inset-bottom, 10px); /* iPhone notch support */
    }
    
    .input-area input { 
        flex: 1; 
        padding: 12px 18px; 
        border: 1px solid #ccc; 
        border-radius: 25px; 
        outline: none; 
        font-size: 16px; /* 16px font prevents auto-zoom on mobile */
        background: white;
        min-width: 0; /* Input jate button ke screen er baire na thhele */
    }
    
    .input-area button { 
        background: #10b981; 
        color: white; 
        border: none; 
        width: 46px; 
        height: 46px; 
        border-radius: 50%; 
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        flex-shrink: 0; /* Button jate choto na hoy */
        font-size: 18px;
        transition: background 0.2s;
    }
    
    .input-area button:active { 
        background: #064e3b; 
    }

    /* Scrollbar */
    #chat-box::-webkit-scrollbar { width: 4px; }
    #chat-box::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
</style>
</head>
<body>

<div class="chat-header">
    <a href="messages_list.php"><i class="fa-solid fa-chevron-left"></i></a>
    <div class="name"><?php echo htmlspecialchars($other_user['name']); ?></div>
</div>

<div id="chat-box">
    </div>

<div class="input-area">
    <input type="text" id="messageText" placeholder="Write a message..." autocomplete="off">
    <button id="sendBtn"><i class="fa-solid fa-paper-plane"></i></button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const postId = "<?php echo $post_id; ?>";
    const otherId = "<?php echo $other_id; ?>";
    let firstLoad = true;

    function loadMessages() {
        $.ajax({
            url: 'fetch_messages.php',
            type: 'GET',
            data: { post_id: postId, other_id: otherId },
            success: function(data) {
                const chatBox = $('#chat-box');
                // Check if user is near bottom before updating
                const wasAtBottom = chatBox.scrollTop() + chatBox.innerHeight() >= chatBox[0].scrollHeight - 150;
                
                chatBox.html(data);
                
                // Scroll to bottom on first load OR if user was already at the bottom
                if (firstLoad || wasAtBottom) {
                    chatBox.scrollTop(chatBox[0].scrollHeight);
                    firstLoad = false;
                }
            }
        });
    }

    $('#sendBtn').on('click', function() {
        const msg = $('#messageText').val();
        if (msg.trim() === "") return;

        $.ajax({
            url: 'send_message_ajax.php',
            type: 'POST',
            data: { post_id: postId, receiver_id: otherId, message: msg },
            success: function() {
                $('#messageText').val(''); 
                loadMessages(); 
                // Force scroll to bottom after sending a message
                setTimeout(() => {
                    const chatBox = $('#chat-box');
                    chatBox.scrollTop(chatBox[0].scrollHeight);
                }, 100);
            }
        });
    });

    $('#messageText').keypress(function(e) {
        if(e.which == 13) {
            $('#sendBtn').click();
            $(this).blur(); // Optional: hides keyboard on send for some mobile browsers
            $(this).focus();
        }
    });

    // Mobile Keyboard Fix
    $('#messageText').on('focus', function() {
        setTimeout(() => {
            const chatBox = $('#chat-box');
            chatBox.scrollTop(chatBox[0].scrollHeight);
        }, 300);
    });

    // Refresh every 2 seconds
    setInterval(loadMessages, 2000);
    $(document).ready(loadMessages);
</script>

</body>
</html>