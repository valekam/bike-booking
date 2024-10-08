<?php
session_start();
include 'admin/config.php'; // Include your database configuration

$bike_plate = '';
$departure = '';
$destination = '';
$bike_details = [];
$user_details = [];

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
if (isset($_GET['bike_plate']) && isset($_GET['departure']) && isset($_GET['destination'])) {
    $bike_plate = mysqli_real_escape_string($conn, $_GET['bike_plate']);
    $departure = mysqli_real_escape_string($conn, $_GET['departure']);
    $destination = mysqli_real_escape_string($conn, $_GET['destination']);

    // Fetch bike details from the database, including all relevant fields
    $bike_query = mysqli_query($conn, "
        SELECT bikes.*, routes.price, routes.departure AS route_departure, routes.destination AS route_destination 
        FROM bikes 
        JOIN routes ON bikes.bike_plate = routes.bike_plate 
        WHERE bikes.bike_plate = '$bike_plate'
    ");

    if ($bike_query) {
        $bike_details = mysqli_fetch_assoc($bike_query);
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get departure and destination from the form
    $departure = mysqli_real_escape_string($conn, $_POST['departure']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);

    // Insert booking details into the bookings table
    $user_name = mysqli_real_escape_string($conn, $user_details['name']);
    $user_email = mysqli_real_escape_string($conn, $user_details['email']);
    $user_phone = mysqli_real_escape_string($conn, $user_details['phone']);
    $price = mysqli_real_escape_string($conn, $bike_details['price']);

    // Insert booking into the bookings table
    $insert_booking_query = "
        INSERT INTO bookings (username, bike_plate, departure, destination, price, status) 
        VALUES ('$username', '$bike_plate', '$departure', '$destination', '$price', 'pending')";

    if (mysqli_query($conn, $insert_booking_query)) {
        // Optionally, remove bike from bikes and routes table if necessary
        mysqli_query($conn, "DELETE FROM bikes WHERE bike_plate = '$bike_plate'");
        mysqli_query($conn, "DELETE FROM routes WHERE bike_plate = '$bike_plate'");

        // Notify user
        echo "<script>
            alert('Booking successful! Your booking is pending admin approval.');
            window.location.href = 'index.php'; // Redirect to homepage or booking history
        </script>";
        exit;
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
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
        h1, h2 {
            color: #333;
        }
        .bike-details, .user-details {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .bike-image {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        form {
            margin-top: 20px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Booking Bike</h1>
    <?php if ($bike_details): ?>
        <div class="bike-details">
            <h2>Bike Details</h2>
            <img src="admin/uploaded_img/<?php echo htmlspecialchars($bike_details['bike_image']); ?>" alt="<?php echo htmlspecialchars($bike_details['bike_plate']); ?>" class="bike-image">
            <p>Bike Plate: <?php echo htmlspecialchars($bike_details['bike_plate']); ?></p>
            <p>Rider: <?php echo htmlspecialchars($bike_details['rider']); ?></p>
            <p>Departure: <?php echo htmlspecialchars($bike_details['route_departure']); ?></p>
            <p>Destination: <?php echo htmlspecialchars($bike_details['route_destination']); ?></p>
            <p>Price: Ksh <?php echo htmlspecialchars($bike_details['price']); ?></p>
        </div>

        <div class="user-details">
            <h2>Your Details</h2>
            <p>Name: <?php echo htmlspecialchars($user_details['name']); ?></p>
            <p>Email: <?php echo htmlspecialchars($user_details['email']); ?></p>
            <p>Phone: <?php echo htmlspecialchars($user_details['phone']); ?></p>

            <h2>Confirm Booking</h2>
            <form method="POST" action="">
                <input type="hidden" name="departure" value="<?php echo htmlspecialchars($departure); ?>">
                <input type="hidden" name="destination" value="<?php echo htmlspecialchars($destination); ?>">
                
                <button type="submit">Confirm Booking</button>
            </form>
        </div>
    <?php else: ?>
        <p>No details available for this bike.</p>
    <?php endif; ?>
</body>
</html>
