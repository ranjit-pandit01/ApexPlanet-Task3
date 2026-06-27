<?php
include 'db.php'; // Database connection ko include karna

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Password ko secure/encrypt karne ke liye password_hash use karein (Task ki requirement)
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'User'; // Default role

    // Prepared Statement use karna SQL Injection se bachne ke liye
    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password, $role);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Registration Successful!'); window.location='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Create an Account</h2>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit" name="register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
