<?php
session_start();
include 'config.php'; 

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    if ($role === 'admin') {
        // Admin Registration
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Check if an admin already exists
        $admin_check_query = "SELECT * FROM admins LIMIT 1";
        $result = mysqli_query($conn, $admin_check_query);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "An admin already exists. Only one admin can be registered.";
        } else {
            // Insert the new admin into the database
            $insert_query = "INSERT INTO admins (username, name, email, password) VALUES ('$username', '$name', '$email', '$password')";
            if (mysqli_query($conn, $insert_query)) {
                $message = "Admin registered successfully.";
                header("Location: login.php");
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    } elseif ($role === 'rider') {
        // Rider Registration
        $rider_username = mysqli_real_escape_string($conn, $_POST['rider_username']);
        $rider_password = mysqli_real_escape_string($conn, $_POST['rider_password']);

        // Insert the new rider into the database
        $insert_rider_query = "INSERT INTO riders (username, password) VALUES ('$rider_username', '$rider_password')";
        if (mysqli_query($conn, $insert_rider_query)) {
            $message = "Rider registered successfully.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_rider'])) {
        // Get the selected rider
        $selected_rider = $_POST['selected_rider'];
        $rider_username = mysqli_real_escape_string($conn, $_POST['rider_username']);
        $rider_password = $_POST['rider_password'];
        $confirm_password = $_POST['confirm_password'];
    
        // Check if passwords match
        if ($rider_password !== $confirm_password) {
            echo "<script>alert('Passwords do not match.');</script>";
            return;
        }
    
        // Check if the rider already exists
        $query = "SELECT * FROM riders WHERE rider_username = '$rider_username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Rider with this username already exists.');</script>";
        } else {
            // Insert the new rider into the database
            $insert_rider_query = "INSERT INTO riders (rider_username, rider_password, rider) VALUES ('$rider_username', '$rider_password', '$selected_rider')";
            if (mysqli_query($conn, $insert_rider_query)) {
                echo "<script>alert('Rider registered successfully.'); window.location = 'rider_dashboard.php';</script>";
            } else {
                echo "<script>alert('Error registering rider.');</script>";
            }
        }
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Frebuddz Bike Booking System</title>
    <link rel="icon" href="bike/vaya.png" type="image">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="signup-modal">
        <button class="close-btn" id="closeBtn">&times;</button>
        <h2>Sign Up Here!</h2>
        <p>Please fill in the information below to create an account.</p>

        <div class="notification <?php echo !empty($message) ? 'success' : ''; ?> <?php echo !empty($error) ? 'error' : ''; ?>" id="notification">
            <?php echo !empty($message) ? $message : $error; ?>
        </div>

        <form method="post">
            <h3>Select Role</h3>
            <label>
                <input type="checkbox" name="role" value="admin"> Admin
            </label><br>
            <label>
                <input type="checkbox" name="role" value="rider"> Rider
            </label><br>

            <div id="adminFields" style="display:none;">
                <input type="text" name="username" placeholder="Admin Username" required>
                <input type="text" id="name" name="name" placeholder="Admin Full Name" required>
                <input type="email" id="email" name="email" placeholder="Admin Email" required>
                <input type="password" name="password" placeholder="Admin Password" required>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Admin Confirm Password" required>
            </div>

            <div id="riderFields" style="display:none;">
   
            <form id="riderRegistrationForm" method="POST">
            <div id="riderChecklist">
                <h3>Select Your Name:</h3>
                <?php
                // Fetch riders from the bikes table
                $riders_query = mysqli_query($conn, "SELECT DISTINCT rider FROM bikes");
                while ($row = mysqli_fetch_assoc($riders_query)) {
                    echo "<label>
                            <input type='radio' name='selected_rider' value='" . htmlspecialchars($row['rider']) . "' required>
                            " . htmlspecialchars($row['rider']) . "
                        </label><br>";
                }
                ?>
            </div>
                <input type="text" name="rider_username" placeholder="Create Username" required>
                <input type="password" name="rider_password" placeholder="Create Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </form>
        </div>
            <button type="submit">Register</button>
        </form><br>
        <p>Already have an account? <a href="login.php">Click here to login</a>.</p>
    </div>

    <script>
        const adminCheckbox = document.querySelector('input[name="role"][value="admin"]');
        const riderCheckbox = document.querySelector('input[name="role"][value="rider"]');
        const adminFields = document.getElementById('adminFields');
        const riderFields = document.getElementById('riderFields');

        adminCheckbox.addEventListener('change', () => {
            if (adminCheckbox.checked) {
                riderCheckbox.checked = false; // Uncheck rider if admin is checked
                riderFields.style.display = 'none';
                adminFields.style.display = 'block';
            } else {
                adminFields.style.display = 'none';
            }
        });

        riderCheckbox.addEventListener('change', () => {
            if (riderCheckbox.checked) {
                adminCheckbox.checked = false; // Uncheck admin if rider is checked
                adminFields.style.display = 'none';
                riderFields.style.display = 'block';
            } else {
                riderFields.style.display = 'none';
            }
        });

        document.getElementById('closeBtn').onclick = function() {
            window.location.href = 'index.php'; // Redirect to index.php
        };
    </script>
</body>
</html>
