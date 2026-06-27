<?php
include 'db.php';
session_start(); // Session start karna login track karne ke liye

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepared Statement use karna SQL Injection se bachne ke liye
    $sql = "SELECT id, username, password, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Hash password ko verify karna
            if (password_verify($password, $row['password'])) {
                // Session variables set karna
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                
                echo "<script>alert('Login Successful!'); window.location='dashboard.php';</script>";
            } else {
                echo "<script>alert('Invalid Password!');</script>";
            }
        } else {
            echo "<script>alert('No user found with this email!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login Account</h2>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter Email" required><br><br>
        <input type="password" name="password" placeholder="Enter Password" required><br><br>
        <button type="submit" name="login">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
