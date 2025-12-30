<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$status_msg = "";

// User er current data fetch kora
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_res);

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_name = mysqli_real_escape_string($conn, $_POST['name']);
    $profile_pic = $user['profile_pic']; // Default purano ta thakbe

    // Jodi naya image upload kore
    if (!empty($_FILES['profile_img']['name'])) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["profile_img"]["name"], PATHINFO_EXTENSION);
        $new_img_name = "profile_" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_img_name;

        if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $target_file)) {
            $profile_pic = $new_img_name;
            // Purano chobi (default chara) thakle delete kore deya bhalo
            if ($user['profile_pic'] != 'default_avatar.png' && file_exists("uploads/" . $user['profile_pic'])) {
                unlink("uploads/" . $user['profile_pic']);
            }
        }
    }

    $update_sql = "UPDATE users SET name = '$new_name', profile_pic = '$profile_pic' WHERE id = '$user_id'";
    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['user_name'] = $new_name; // Session update
        $status_msg = "✅ Profile updated successfully!";
        header("Refresh:1"); // 1 sec por refresh jate naya data dekhay
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sohayok | My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #064e3b; --secondary: #10b981; --bg: #f3f4f6; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); margin: 0; display: flex; flex-direction: column; align-items: center; }
        
        /* Header */
        .profile-header { background: var(--primary); color: white; width: 100%; padding: 20px; text-align: center; position: sticky; top: 0; z-index: 100; }
        .profile-header a { color: white; text-decoration: none; position: absolute; left: 20px; top: 25px; font-size: 20px; }

        .container { background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 20px; margin-top: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center; }
        
        /* Profile Image logic */
        .image-container { position: relative; width: 120px; height: 120px; margin: 0 auto 20px; }
        .image-container img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid var(--secondary); }
        .upload-icon { position: absolute; bottom: 0; right: 0; background: var(--secondary); color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid white; }

        .input-group { text-align: left; margin-bottom: 20px; }
        .input-group label { display: block; font-size: 14px; margin-bottom: 5px; color: #666; }
        .input-box { width: 100%; padding: 12px; border: 1.5px solid #ddd; border-radius: 10px; font-family: inherit; font-size: 15px; }

        .btn-update { background: var(--primary); color: white; border: none; padding: 15px; width: 100%; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-update:hover { background: var(--secondary); }

        #file-input { display: none; }
    </style>
</head>
<body>

<div class="profile-header">
    <a href="dashboard.php"><i class="fa-solid fa-arrow-left"></i></a>
    <h2>My Profile</h2>
</div>

<div class="container">
    <p style="color: green; font-weight: 500; margin-bottom: 15px;"><?php echo $status_msg; ?></p>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="image-container">
            <img id="profileDisplay" src="uploads/<?php echo !empty($user['profile_pic']) ? $user['profile_pic'] : 'default_avatar.png'; ?>">
            <label for="file-input" class="upload-icon">
                <i class="fa-solid fa-camera"></i>
            </label>
            <input type="file" name="profile_img" id="file-input" onchange="previewImage(this)" accept="image/*">
        </div>

        <div class="input-group">
            <label>Full Name</label>
            <input type="text" name="name" class="input-box" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="input-group">
            <label>Email Address (Cannot change)</label>
            <input type="text" class="input-box" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background: #f9f9f9; color: #aaa;">
        </div>

        <button type="submit" name="update_profile" class="btn-update">Save Changes</button>
    </form>

    <div style="margin-top: 20px;">
        <a href="logout.php" style="color: #ef4444; text-decoration: none; font-size: 14px;">Logout from Sohayok</a>
    </div>
</div>

<script>
    // Image Preview logic
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileDisplay').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</body>
</html>