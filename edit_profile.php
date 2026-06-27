<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// यूजर का पुराना डेटा निकालना
$sql = "SELECT username, email, profile_pic FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $profile_pic_name = $user['profile_pic']; // डिफ़ॉल्ट पुरानी वाली पिक्चर

    // फाइल अपलोड और वैलिडेशन चेक करना
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $file_name = $_FILES['profile_pic']['name'];
        $file_size = $_FILES['profile_pic']['size'];
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = array("jpg", "jpeg", "png");

        // 1. Type Validation (सिर्फ jpg, jpeg, png)
        if (!in_array($ext, $allowed_ext)) {
            $error = "Only JPG, JPEG, and PNG files are allowed!";
        }
        // 2. Size Validation (अधिकतम 2MB = 2097152 bytes)
        unset($allowed_ext);
        if ($file_size > 2097152) {
            $error = "File size must be less than 2MB!";
        }

        if (empty($error)) {
            // नया यूनिक नाम देना ताकि फाइलें आपस में ओवरराइट न हों
            $profile_pic_name = "user_" . $user_id . "_" . time() . "." . $ext;
            move_uploaded_file($file_tmp, "uploads/" . $profile_pic_name);
        }
    }

    // अगर कोई एरर नहीं है तो डेटाबेस अपडेट करें
    if (empty($error)) {
        $update_sql = "UPDATE users SET username = ?, email = ?, profile_pic = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sssi", $username, $email, $profile_pic_name, $user_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            $_SESSION['username'] = $username; // सेशन अपडेट करें
            $success = "Profile updated successfully!";
            // अपडेटेड डेटा को दोबारा लोड करना
            $user['username'] = $username;
            $user['email'] = $email;
            $user['profile_pic'] = $profile_pic_name;
        } else {
            $error = "Something went wrong while updating database.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile Information</h2>
    
    <?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if(!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

        <label>Current Profile Picture:</label><br>
        <img src="uploads/<?php echo $user['profile_pic']; ?>" width="100" style="border: 1px solid #ccc;"><br><br>

        <label>Upload New Profile Picture (Max 2MB, JPG/PNG only):</label><br>
        <input type="file" name="profile_pic"><br><br>

        <button type="submit" name="update_profile">Save Changes</button>
    </form>
    
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
