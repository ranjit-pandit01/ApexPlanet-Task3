<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT username, email, role, profile_pic FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
    <p>Role: <strong><?php echo htmlspecialchars($user['role']); ?></strong></p>

    <div>
        <h3>Profile Picture:</h3>
        <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Pic" width="150" style="border-radius: 50%; border: 1px solid #ccc;"><br><br>
        <a href="edit_profile.php">Edit Profile & Upload Picture</a>
    </div>

    <br><hr><br>

    <?php if ($user['role'] == 'Admin'): ?>
        <div style="background: #e0f7fa; padding: 15px; border-left: 5px solid #00acc1;">
            <h3>Admin Panel (CRUD Operations)</h3>
            <p>You have admin privileges. You can manage all registered users here.</p>
            <a href="manage_users.php" style="font-weight: bold; font-size: 18px;">Go to User Management (CRUD)</a>
        </div>
        <br><hr><br>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</body>
</html>
