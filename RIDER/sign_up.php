<?php
// Database connection settings
$host = "localhost";
$user = "root";
$pass = "";
$db = "bikebooking";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch existing riders from the bikes table
$riders = [];
$rider_query = "SELECT DISTINCT rider, rider_no FROM bikes";
$result = mysqli_query($conn, $rider_query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $riders[] = $row;
    }
}

// Handle sign-up
if (isset($_POST['sign_up'])) {
    $selected_rider_no = mysqli_real_escape_string($conn, $_POST['rider_no']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($password === $confirm_password) {
        // Retrieve the rider name directly from the POST data
        $rider_name = mysqli_real_escape_string($conn, $_POST['rider_name']);

        $insert_query = "INSERT INTO riders (rider_name, rider_no, username, password) VALUES ('$rider_name', '$selected_rider_no', '$username', '$password')";
        
        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('Sign up successful. You can now log in.'); window.location = 'login.php';</script>";
        } else {
            echo "<script>alert('Error: Could not sign up.');</script>";
        }
    } else {
        echo "<script>alert('Passwords do not match.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Bike Booking System</title>
    <link rel="icon" href="bike.jpg" type="image">
    <link rel="stylesheet" href="style.css">
</head>
<body>  
    <div class="form-container">
        <div class="avatar-container">
            <img src="avarter.png" alt="Avatar" class="avatar">
        </div>
        <div class="welcome-back">
            <h2>Create Account!</h2>
            <p>Please fill in the information below to create an account.</p>
        </div>
        <form method="POST" action="sign_up.php">
            <label for="rider_no">Select Rider:</label>
            <select id="rider_no" name="rider_no" required onchange="updateRiderName()">
                <option value="">-- Select Rider --</option>
                <?php foreach ($riders as $rider): ?>
                    <option value="<?php echo $rider['rider_no']; ?>" data-name="<?php echo htmlspecialchars($rider['rider']); ?>">
                        <?php echo htmlspecialchars($rider['rider']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="hidden" id="rider_name" name="rider_name" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" class="btn" name="sign_up">Sign Up</button>
            <p>Already have an account? <a href="login.php">Click here to login</a>.</p>
        </form>
    </div>

    <script>
        function updateRiderName() {
            const select = document.getElementById('rider_no');
            const selectedOption = select.options[select.selectedIndex];
            const riderNameInput = document.getElementById('rider_name');

            // Update the hidden input with the selected rider's name
            riderNameInput.value = selectedOption.dataset.name;
        }
    </script>
</body>
</html>
