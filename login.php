<?php
    session_start();
    include 'admin/config.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $message = '';
    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare SQL statement
        $sql = "SELECT username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Check if password matches (no hashing)
            if ($password === $user['password']) {
                // Store username in session
                $_SESSION['username'] = $user['username'];
                header("Location: search.php"); // Redirect to search page
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "Username not found!";
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
    <title>Login - Frebuddz Bike Booking System</title>
    <link rel="icon" href="bike/vaya.png" type="image">
    <link rel="stylesheet" href="style.css">
    <style>
        .close-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            color: #000;
            background: none;
            border: none;
            cursor: pointer;
        }
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #f44336; /* Red for errors */
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: none; /* Hidden by default */
            z-index: 1000;
        }
    </style>
</head>
<body>
    <header class="logo">
        <img src="bike/kawa.png" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">
            <a href="index.php">HOME</a>
            <a href="sign_up.php">SIGN UP</a>
        </nav>
    </header>

    <div class="notification" id="notification">
        <?php echo $error; ?>
    </div>

    <div class="signup-modal">
        <button class="close-btn" id="closeBtn">&times;</button>
        <div class="avatar-container">
            <img src="bike/avarter.png" alt="Avatar" class="avatar">
        </div>
        <div class="welcome-back">
            <h2>Login Here!</h2>
            <p>Please enter your username and password to login.</p>
        </div>
        <div class="signup-section">
            <form id="loginForm" method="post">
                <input type="text" id="username" name="username" placeholder="Username" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                
                <button type="submit" class="btn" name="login">Login</button>
            </form><br>
            <p>Don't have an account? <a href="sign_up.php">Click here to sign up</a>.</p><br>
        </div>
    </div>

    <script>
        // JavaScript to handle the close button click
        document.getElementById('closeBtn').onclick = function() {
            window.location.href = 'index.php'; // Redirect to index.php
        };

        // Show notification if there is an error
        <?php if (!empty($error)): ?>
            document.getElementById('notification').innerText = '<?php echo $error; ?>';
            document.getElementById('notification').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>
