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
    $confirmPassword = $_POST['confirm_password'];
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
                    }, 2000);
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
    <link rel="stylesheet" href="sign.css">
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

    <div class="signup-container">
        <div class="slideshow-container">
            <div class="mySlides fade">
                <img src="bike/vaya.png" alt="Vaya" class="slideshow-image">
            </div>
        </div>

        <div class="signup-section">
            <div class="avatar-container">
                <img src="bike/avarter.png" alt="Avatar" class="avatar">
            </div>
            <div class="welcome-back">
                <h2>Sign Up Here!</h2>
                <p>Please fill in the information below to create an account.</p>
            </div>
            <form id="signupForm" method="post">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="" disabled selected>Select your gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>

                <button type="submit" class="btn" name="signup">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Click here to login</a>.</p>
        </div>
    </div>
    <script>
        // Show notification if message or error exists
        <?php if (!empty($message) || !empty($error)): ?>
            document.getElementById('notification').style.display = 'block';
        <?php endif; ?>

        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let i;
            const slides = document.getElementsByClassName("mySlides");
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  
            }
            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}    
            slides[slideIndex - 1].style.display = "block";  
            setTimeout(showSlides, 2000); // Change image every 2 seconds
        }
    </script>
</body>
</html>
