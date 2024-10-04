<?php
session_start();
include 'config.php'; // Include your database configuration

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // No hashing

    if ($role === 'admin') {
        // Fetch the admin from the database
        $query = "SELECT * FROM admins WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            // Directly compare the password (not secure)
            if ($password === $admin['password']) {
                $_SESSION['is_admin'] = true;
                $_SESSION['username'] = $username;
                header("Location: admin_dashboard.php"); // Redirect to admin dashboard
                exit;
            } else {
                echo "<p>Invalid password.</p>";
            }
        } else {
            echo "<p>No admin found with that username.</p>";
        }
    } elseif ($role === 'rider') {
        // Fetch the rider from the database
        $query = "SELECT * FROM riders WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $rider = mysqli_fetch_assoc($result);
            // Directly compare the password (not secure)
            if ($password === $rider['password']) {
                $_SESSION['is_rider'] = true;
                $_SESSION['username'] = $username;
                header("Location: rider_dashboard.php"); // Redirect to rider dashboard
                exit;
            } else {
                echo "<p>Invalid password.</p>";
            }
        } else {
            echo "<p>No rider found with that username.</p>";
        }
    } else {
        echo "<p>Please select a role to login.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Admin Login</title>
    
</head>
<body>
    <div class="signup-modal">
        <button class="close-btn" id="closeBtn">&times;</button>
        <h2>Login Here!</h2>
        <p>Please enter your username and password to login.</p>

        <div class="notification" id="notification">
            <?php if (!empty($error)) echo "<p>$error</p>"; ?>
        </div>

        <form id="loginForm" method="post">
            <h3>Select Role</h3>
            <label>
                <input type="radio" name="role" value="admin" required> Admin
            </label><br>
            <label>
                <input type="radio" name="role" value="rider"> Rider
            </label><br>
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
                
            <button type="submit" class="btn" name="login">Login</button>
        </form><br>
        <p>Don't have an account? <a href="sign_up.php">Click here to sign up</a>.</p><br>
    </div>

    <script>
        // JavaScript to handle the close button click
        document.getElementById('closeBtn').onclick = function() {
            window.location.href = 'index.php'; // Redirect to index.php
        };
    </script>
</body>
</html>
