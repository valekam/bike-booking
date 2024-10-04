<?php
include 'admin/config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password']; // Confirmation password
    $gender = $_POST['gender'];

    // Check if any field is empty
    if (empty($name) || empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword) || empty($gender)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $checkEmail = "SELECT * FROM users WHERE email = ?";
        $stmtCheck = $conn->prepare($checkEmail);
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            // Insert into users table without hashing the password
            $sql = "INSERT INTO users (name, username, email, phone, password, gender) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $name, $username, $email, $phone, $password, $gender);

            if ($stmt->execute()) {
                $message = "Registration successful! Redirecting to login...";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000); // Redirect after 1 seconds
                </script>";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $stmtCheck->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Frebuddz Bike Booking System</title>
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
            background-color: #4CAF50; /* Green for success */
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: none; /* Hidden by default */
            z-index: 1000;
        }
        .error {
            background-color: #f44336; /* Red for errors */
        }
    </style>
</head>
<body>
    <header class="logo">
        <img src="bike/kawa.png" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">
            <a href="index.php">HOME</a> 
            <a href="login.php">LOGIN</a>
        </nav>
    </header>

    <div class="notification <?php echo !empty($message) ? '' : 'error'; ?>" id="notification">
        <?php echo !empty($message) ? $message : $error; ?>
    </div>

    <div class="signup-modal">
        <button class="close-btn" id="closeBtn">&times;</button>
        <div class="avatar-container">
            <img src="bike/avarter.png" alt="Avatar" class="avatar">
        </div>
        <div class="welcome-back">
            <h2>Sign Up Here!</h2>
            <p>Please fill in the information below to create an account.</p>
        </div>
        <div class="signup-section">
            <form id="signupForm" method="post">
                <input type="text" id="name" name="name" placeholder="Full Name" required>
                <input type="text" id="username" name="username" placeholder="Username" required>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>

                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="" disabled selected>Select your gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>

                <button type="submit" class="btn" name="signup">Sign Up</button>
            </form><br>
            <p>Already have an account? <a href="login.php">Click here to login</a>.</p><br>
        </div>
    </div>

    <script>
        // JavaScript to handle the close button click
        document.getElementById('closeBtn').onclick = function() {
            window.location.href = 'index.php'; // Redirect to index.php
        };

        // Show notification if message or error exists
        <?php if (!empty($message) || !empty($error)): ?>
            document.getElementById('notification').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>
