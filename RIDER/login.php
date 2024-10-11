<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db = "bikebooking";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check for username and password
    $query = "SELECT * FROM riders WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['rider_no'] = $row['rider_no'];
        $_SESSION['rider_name'] = $row['rider_name']; // Store rider name in session
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Invalid username or password.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bike Booking System</title>
    <link rel="icon" href="bike.jpg" type="image">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <div class="avatar-container">
            <img src="avarter.png" alt="Avatar" class="avatar">
        </div>
        <div class="welcome-back">
            <h2>Login Here!</h2>
            <p>Please fill in the information below to login to your account.</p>
        </div>
        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn" name="login">Login</button>
            <p>Don't have an account? <a href="sign_up.php">Click here to sign up</a>.</p>
        </form>
        <?php if (isset($_POST['login'])) echo "<p>Username used to login: " . htmlspecialchars($username) . "</p>"; ?>
    </div>
</body>
</html>
