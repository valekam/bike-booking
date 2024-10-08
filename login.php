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
    <link rel="stylesheet" href="sign.css">
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
                <h2>Login Here!</h2>
                <p>Please enter your username and password to login.</p>
            </div>
            <form id="loginForm" method="post">
                <input type="text" id="username" name="username" placeholder="Username" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                
                <button type="submit" class="btn" name="login">Login</button>
            </form>
            <p>Don't have an account? <a href="sign_up.php">Click here to sign up</a>.</p>
        </div>
    </div>

    <script>
        // JavaScript to handle the slideshow
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

        // Show notification if there is an error
        <?php if (!empty($error)): ?>
            document.getElementById('notification').innerText = '<?php echo $error; ?>';
            document.getElementById('notification').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>
