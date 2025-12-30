<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Handle Post Delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Security check: Only post owner can delete
    $check_sql = "SELECT image_path FROM posts WHERE id='$delete_id' AND user_id='$user_id'";
    $res = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if(!empty($row['image_path'])) unlink("uploads/".$row['image_path']); 
        mysqli_query($conn, "DELETE FROM posts WHERE id='$delete_id'");
        header("Location: dashboard.php");
        exit();
    }
}

// Handle New Help Post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_help'])) {
    $category = $_POST['category'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $image_name = ""; 
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);
    }
    $sql = "INSERT INTO posts (user_id, category, title, description, image_path) VALUES ('$user_id', '$category', '$title', '$desc', '$image_name')";
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php");
        exit(); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sohayok | Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #064e3b;
            --secondary: #10b981;
            --accent: #f59e0b;
            --bg: #f3f4f6;
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); margin: 0; color: #1f2937; }

        /* --- UPDATED PROFESSIONAL SIDEBAR --- */
        .sidebar {
            width: 280px; 
            background: var(--primary); 
            height: 100vh; 
            position: fixed;
            padding: 40px 25px; 
            color: white; 
            display: flex; 
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }
        .sidebar h2 { 
            font-size: 26px; 
            font-weight: 600; 
            margin-bottom: 50px; 
            display: flex; 
            align-items: center; 
            gap: 15px;
            letter-spacing: 1px;
        }
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .sidebar a { 
            color: rgba(255,255,255,0.7); 
            text-decoration: none; 
            padding: 14px 20px; 
            border-radius: 12px;
            display: flex; 
            align-items: center; 
            gap: 15px; 
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .sidebar a i {
            font-size: 18px;
            width: 25px;
            text-align: center;
        }
        .sidebar a:hover { 
            background: rgba(255,255,255,0.1); 
            color: white; 
            transform: translateX(5px);
        }
        .sidebar a.active { 
            background: var(--secondary); 
            color: white; 
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }

        /* --- Mobile Navigation --- */
        .bottom-nav {
            display: none; position: fixed; bottom: 0; width: 100%; background: white;
            box-shadow: 0 -2px 15px rgba(0,0,0,0.1); z-index: 1000; justify-content: space-around; padding: 10px 0;
        }
        .bottom-nav a { color: #6b7280; text-decoration: none; font-size: 20px; display: flex; flex-direction: column; align-items: center; }
        .bottom-nav a.active { color: var(--primary); }

        /* --- Main Content --- */
        .main-content { margin-left: 280px; padding: 40px; max-width: 1000px; }

        /* --- Post Cards --- */
        .post-card {
            background: white; border-radius: 20px; padding: 20px; margin-bottom: 25px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: 0.3s; border: 1px solid #f3f4f6;
        }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }

        .badge { padding: 4px 12px; border-radius: 99px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: white; }
        .Money { background: #ef4444; } .Food { background: #10b981; } .Assignment { background: #3b82f6; }
        .Dress { background: #8b5cf6; } .Books { background: #0ea5e9; } .Blood { background: #dc2626; animation: pulse 2s infinite; }
        
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.6; } 100% { opacity: 1; } }

        .post-image { width: 100%; max-height: 450px; object-fit: cover; border-radius: 15px; margin: 15px 0; border: 1px solid #eee; }

        .user-avatar {
            width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--secondary);
        }

        /* --- Inputs --- */
        .input-box { width: 100%; background: #f9fafb; border: 1.5px solid #e5e7eb; padding: 12px 16px; border-radius: 12px; margin-bottom: 12px; font-family: inherit; }
        .input-box:focus { outline: none; border-color: var(--secondary); background: white; }

        .btn-submit { background: var(--primary); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 600; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: #043a2c; }

        .help-btn { background: var(--secondary); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 500; }

        /* --- Responsive Queries --- */
        @media (max-width: 992px) {
            .sidebar { display: none; }
            .bottom-nav { display: flex; }
            .main-content { margin-left: 0; padding: 20px; padding-bottom: 80px; }
        }

        .filter-pill {
            background: white; border: 1px solid #e5e7eb; padding: 8px 18px; border-radius: 20px;
            cursor: pointer; font-size: 14px; white-space: nowrap; transition: 0.3s; margin-right: 8px;
        }
        .filter-pill.active { background: var(--primary); color: white; border-color: var(--primary); }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-hand-holding-heart"></i> Sohayok</h2>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Home Feed</a>
            <a href="messages_list.php"><i class="fa-solid fa-message"></i> Messages</a>
            <a href="profile.php"><i class="fa-solid fa-user"></i> My Profile</a>
        </div>
        <div class="sidebar-footer">
    <a href="logout.php" style="color: #f87171;" onclick="return confirm('Are you sure you want to logout?')">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</div>
    </div>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i></a>
        <a href="messages_list.php"><i class="fa-solid fa-message"></i></a>
        <a href="#" onclick="togglePostForm()"><i class="fa-solid fa-circle-plus" style="font-size: 30px; color: var(--secondary);"></i></a>
        <a href="profile.php"><i class="fa-solid fa-user"></i></a>
        <a href="logout.php"><i class="fa-solid fa-power-off"></i></a>
    </nav>

    <div class="main-content">
        <div class="post-card" id="postFormContainer" style="border-top: 4px solid var(--secondary);">
            <h3 style="margin-top: 0;"><i class="fa-solid fa-pen-to-square"></i> Create Help Request</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="title" class="input-box" placeholder="Title (e.g., Need O+ Blood)" required>
                <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                    <select name="category" class="input-box" style="margin-bottom:0; flex: 1;">
                        <option value="Money">Money</option>
                        <option value="Food">Food</option>
                        <option value="Assignment">Assignment</option>
                        <option value="Dress">Dress</option>
                        <option value="Books">Books</option>
                        <option value="Blood">Blood</option>
                    </select>
                    <input type="file" name="image" id="imgUpload" hidden accept="image/*">
                    <button type="button" onclick="document.getElementById('imgUpload').click()" class="input-box" style="margin-bottom:0; width: auto; background: #fff; cursor: pointer;">
                        <i class="fa-solid fa-image"></i> Image
                    </button>
                </div>
                <textarea name="description" class="input-box" rows="2" placeholder="Explain your situation..." required></textarea>
                <button type="submit" name="post_help" class="btn-submit">Post to Sohayok</button>
            </form>
        </div>

        <div style="display: flex; overflow-x: auto; padding-bottom: 15px; margin-bottom: 10px; scrollbar-width: none;">
            <div class="filter-pill active" onclick="filterCat('all')">All</div>
            <div class="filter-pill" onclick="filterCat('Blood')">Blood</div>
            <div class="filter-pill" onclick="filterCat('Food')">Food</div>
            <div class="filter-pill" onclick="filterCat('Books')">Books</div>
            <div class="filter-pill" onclick="filterCat('Assignment')">Assignment</div>
            <div class="filter-pill" onclick="filterCat('Money')">Money</div>
        </div>

        <div id="postsContainer">
            <?php
            $sql = "SELECT posts.*, users.name, users.profile_pic FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $p_pic = !empty($row['profile_pic']) ? $row['profile_pic'] : 'default_avatar.png';
                ?>
                <div class="post-card help-post" data-category="<?php echo $row['category']; ?>">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="uploads/<?php echo $p_pic; ?>" class="user-avatar" alt="User">
                            <div>
                                <h4 style="margin: 0; font-size: 15px;"><?php echo htmlspecialchars($row['name']); ?></h4>
                                <span class="badge <?php echo $row['category']; ?>"><?php echo $row['category']; ?></span>
                            </div>
                        </div>
                        <small style="color: #9ca3af;"><?php echo date('M d, g:i a', strtotime($row['created_at'])); ?></small>
                    </div>

                    <h3 style="margin: 0 0 10px 0; font-size: 18px; color: var(--primary);"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p style="color: #4b5563; line-height: 1.6; font-size: 14px;"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                    
                    <?php if (!empty($row['image_path'])) { ?>
                        <img src="uploads/<?php echo $row['image_path']; ?>" class="post-image" alt="Help Request Image">
                    <?php } ?>

                    <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f3f4f6; gap: 10px;">
                        <?php if ($row['user_id'] != $user_id) { ?>
                            <a href="message.php?post_id=<?php echo $row['id']; ?>&receiver_id=<?php echo $row['user_id']; ?>" class="help-btn">
                                <i class="fa-solid fa-paper-plane"></i> Help Now
                            </a>
                        <?php } else { ?>
                            <a href="dashboard.php?delete_id=<?php echo $row['id']; ?>" class="help-btn" style="background:#fee2e2; color:#ef4444;" onclick="return confirm('Delete this help request?')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        function filterCat(cat) {
            const posts = document.querySelectorAll('.help-post');
            const pills = document.querySelectorAll('.filter-pill');
            
            pills.forEach(p => p.classList.remove('active'));
            event.currentTarget.classList.add('active');

            posts.forEach(post => {
                if (cat === 'all' || post.dataset.category === cat) {
                    post.style.display = 'block';
                } else {
                    post.style.display = 'none';
                }
            });
        }

        function togglePostForm() {
            const form = document.getElementById('postFormContainer');
            if (form.style.display === "none") {
                form.style.display = "block";
                window.scrollTo(0, 0);
            } else {
                form.style.display = "none";
            }
        }

        if(window.innerWidth < 992) {
            document.getElementById('postFormContainer').style.display = "none";
        }
    </script>
</body>
</html>