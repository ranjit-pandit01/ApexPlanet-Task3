<?php
session_start();
include 'db.php';

// Check karein ki user logged in hai aur Admin hai ya nahi
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$check_sql = "SELECT role FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$current_user = mysqli_fetch_assoc($res);

if ($current_user['role'] !== 'Admin') {
    die("Access Denied: Aapke paas admin privileges nahi hain.");
}

// Delete Operation
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $del_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($del_stmt, "i", $delete_id);
    if (mysqli_stmt_execute($del_stmt)) {
        header("Location: manage_users.php?msg=User Deleted Successfully");
        exit();
    }
}

// Saare users ko fetch karne ki query
$sql = "SELECT id, username, email, role FROM users";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management (CRUD)</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: sans-serif; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #00acc1; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; border-radius: 3px; font-size: 14px; }
        .btn-edit { background-color: #4CAF50; }
        .btn-delete { background-color: #f44336; }
    </style>
</head>
<body>
    <h2>Admin Panel - User Management (CRUD)</h2>
    <a href="dashboard.php"><- Back to Dashboard</a> | <a href="register.php" style="font-weight:bold; color:#00acc1;">+ Add New User</a>
    
    <?php if (isset($_GET['msg'])): ?>
        <p style="color: green; font-weight: bold;"><?php echo htmlspecialchars($_GET['msg']); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td>
                    <a href="edit_profile.php?user_id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a> 
                    <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
