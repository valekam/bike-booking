<?php
session_start();
include 'admin/config.php'; // Include your database configuration

$bike_plate = '';
$bike_details = [];
$user_details = [];
$booking_pending = false; // Flag to check if booking is pending

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You need to be logged in to book a bike.</p>";
    exit;
}

$username = $_SESSION['username'];

// Fetch user details from the database
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
if ($user_query) {
    $user_details = mysqli_fetch_assoc($user_query);
} else {
    echo "<p>Error fetching user details: " . mysqli_error($conn) . "</p>";
}

// Check if the bike_plate is set in the URL
if (isset($_GET['bike_plate'])) {
    $bike_plate = mysqli_real_escape_string($conn, $_GET['bike_plate']);

    // Fetch bike details from the database
    $bike_query = mysqli_query($conn, "
        SELECT bikes.*, routes.price, routes.departure AS route_departure, routes.destination AS route_destination 
        FROM bikes 
        JOIN routes ON bikes.bike_plate = routes.bike_plate 
        WHERE bikes.bike_plate = '$bike_plate'
    ");

    if ($bike_query && mysqli_num_rows($bike_query) > 0) {
        $bike_details = mysqli_fetch_assoc($bike_query);
    } else {
        echo "<p>No details available for this bike.</p>";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form was submitted
    if (isset($_POST['confirm_booking'])) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO bookings (username, bike_plate, departure, destination, price, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        
        // Check if the preparation was successful
        if ($stmt === false) {
            echo "<p>Error preparing statement: " . htmlspecialchars($conn->error) . "</p>";
            exit;
        }

        // Bind parameters
        $stmt->bind_param("sssss", $username, $bike_plate, $bike_details['route_departure'], $bike_details['route_destination'], $bike_details['price']);

        // Execute the statement
        if ($stmt->execute()) {
            // Notify the rider (you can implement your own notification logic here)
            $rider_username = $bike_details['rider']; // Assume rider is stored in bike_details
            $notification_query = $conn->prepare("INSERT INTO notifications (recipient_username, message) VALUES (?, ?)");
            
            // Check if the notification preparation was successful
            if ($notification_query === false) {
                echo "<p>Error preparing notification statement: " . htmlspecialchars($conn->error) . "</p>";
                exit;
            }

            $message = "Booking request from $username for bike $bike_plate is pending approval.";
            $notification_query->bind_param("ss", $rider_username, $message);
            $notification_query->execute();
            $notification_query->close();

            $booking_pending = true; // Set booking pending flag
            echo "<script>alert('Your booking request is pending approval! The rider will be notified.');
            window.location.href = 'booked_page.php';</script>"; 
        } else {
            echo "<p>Error executing statement: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS -->
    <style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
    }

    h1 {
        text-align: center;
        color: #333;
    }

    .container {
        display: flex;
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
        gap: 20px;
    }

    .bike-details, .user-details {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        padding: 15px;
        flex: 1;
    }

    .bike-image {
        max-width: 150px; /* Set a smaller width for the bike image */
        width: 100%; /* Make it responsive within the set width */
        height: auto; /* Maintain aspect ratio */
        border-radius: 4px;
        margin-bottom: 10px; /* Space below the image */
    }

    p {
        color: #555;
        line-height: 1.4; /* Adjust line height for compactness */
        font-size: 14px; /* Smaller font size for details */
    }

    button {
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 8px 12px; /* Adjust padding for a smaller button */
        cursor: pointer;
        font-size: 14px; /* Smaller font size */
        transition: background-color 0.3s ease;
        display: block;
        margin: 20px auto 0; /* Margin for spacing */
    }

    button:hover {
        background-color: #218838;
    }

    @media (max-width: 600px) {
        .container {
            flex-direction: column; /* Stack vertically on small screens */
        }

        .bike-details, .user-details {
            width: 100%; /* Full width for smaller screens */
        }
    }
    </style>
</head>
<body>
    <header class="logo">
        <img src="bike/kawa.png" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">
            <a href="search.php">Back</a> 
        </nav>
    </header>
    <h1>Booking Bike</h1>
    <?php if ($bike_details): ?>
        <div class="container">
            <div class="bike-details">
                <h2>Bike Details</h2>
                <img src="admin/uploaded_img/<?php echo htmlspecialchars($bike_details['bike_image']); ?>" alt="<?php echo htmlspecialchars($bike_details['bike_plate']); ?>" class="bike-image">
                <p>Bike Plate: <?php echo htmlspecialchars($bike_details['bike_plate']); ?></p>
                <p>Rider: <?php echo htmlspecialchars($bike_details['rider']); ?></p>
                <p>Rider Number: <?php echo htmlspecialchars($bike_details['rider_no']); ?></p>
                <p>Departure: <?php echo htmlspecialchars($bike_details['route_departure']); ?></p>
                <p>Destination: <?php echo htmlspecialchars($bike_details['route_destination']); ?></p>
                <p>Price: Ksh <?php echo htmlspecialchars($bike_details['price']); ?></p>
            </div>

            <div class="user-details">
                <h2>Your Details</h2>
                <p>Name: <?php echo htmlspecialchars($user_details['name']); ?></p>
                <p>Email: <?php echo htmlspecialchars($user_details['email']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($user_details['phone']); ?></p>
                <form method="POST" action="">
                    <button type="submit" name="confirm_booking">Request Booking</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p>No details available for this bike.</p>
    <?php endif; ?>
</body>
</html>
