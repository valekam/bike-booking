<?php
session_start();
include 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$error = '';

// Admin login logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare SQL statement
    $sql = "SELECT username, password FROM admins WHERE username = ? AND is_admin = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Check if password matches
        if ($password === $user['password']) {
            // Store username in session
            $_SESSION['username'] = $user['username'];
            header("Location: index.php"); // Redirect to admin dashboard
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Username not found or not an admin!";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Frebuddz Bike Booking System</title>
    <link rel="icon" href="bike/vaya.png" type="image">
    <link rel="stylesheet" href="sign.css">
</head>
<body>
    <div class="signup-container">
    <button class="close-btn" onclick="window.location.href='logout.php'">&times;</button>
        <div class="signup-section">
            <div class="avatar-container">
                <img src="bike.jpg" alt="Avatar" class="avatar">
            </div>
            <div class="welcome-back">
                <h2>Admin Login Here!</h2>
                <p>Please enter your credentials to log in.</p>
            </div>
            <form id="loginForm" method="post">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <div>
                    <input type="checkbox" id="is_admin" name="is_admin" checked>
                    <label for="is_admin">Admin</label>
                </div>

                <button type="submit" class="btn" name="login">Login</button>
            </form>
            <p>Don't have an account? <a href="sign_up.php">Click here to register</a>.</p>
            <div class="notification <?php echo !empty($message) ? '' : 'error'; ?>" id="notification">
                <?php echo !empty($message) ? $message : $error; ?>
            </div>
        </div>
    </div>
</body>
</html>
