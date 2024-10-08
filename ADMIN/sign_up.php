<?php
include 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$error = '';

// Admin registration logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if any field is empty
    if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        // Check if an admin already exists
        $checkAdmin = "SELECT * FROM admins WHERE is_admin = 1";
        $resultCheck = $conn->query($checkAdmin);

        if ($resultCheck->num_rows > 0) {
            $error = "An admin account already exists!";
        } else {
            // Insert into users table with is_admin set to 1
            $sql = "INSERT INTO admins (name, username, email, password, is_admin) VALUES (?, ?, ?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $username, $email, $password);

            if ($stmt->execute()) {
                $message = "Admin registration successful! You can now log in.";
                header("Location: login.php");
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Frebuddz Bike Booking System</title>
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
                <h2>Admin Sign Up Here!</h2>
                <p>Please fill in the information below to create an admin account.</p>
            </div>
            <form id="signupForm" method="post">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit" class="btn" name="signup">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Click here to login</a>.</p>
            <div class="notification <?php echo !empty($message) ? '' : 'error'; ?>" id="notification">
                <?php echo !empty($message) ? $message : $error; ?>
            </div>
        </div>
    </div>
</body>
</html>

