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

// Ensure the rider is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch pending booking count for the logged-in user
$username = $_SESSION['username'];
$notification_query = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE username = ? AND status = 'pending'");
$notification_query->bind_param("s", $username);
$notification_query->execute();
$notification_query->bind_result($notification_count);
$notification_query->fetch();
$notification_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frebuddz Bike Booking System</title>
    <link rel="icon" href="bike/vaya.png" type="image">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="logo">
        <img src="bike/kawa.png" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">
            <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="booked_page.php">My Bookings (<?php echo $notification_count; ?>)</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </header>
    <div class="fred">
        <div class="cont">
            <h1>Bike Booking Service</h1>
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>Thank you for logging in. You can now enter the destination and the departure to book your bike and enjoy your ride!</p>
            <form id="route-form" action="bikes.php" method="GET">
                <label for="destination">Destination:</label>
                <input type="text" id="destination" name="destination" required>
                
                <label for="departure">Departure:</label>
                <input type="text" id="departure" name="departure" required>

                <button type="submit">Search Bike</button>
            </form>
            <div class="map">
                <iframe
                    id="map-frame"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345091645!2d36.7062373153159!3d-0.7498397992051434!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f3c8f1f8f87f7%3A0x1e34f4ee48298d69!2sMurang%27a%20County%2C%20Kenya!5e0!3m2!1sen!2sus!4v1632281722330!5m2!1sen!2sus"
                    width="300" height="300" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>
    </div>

    <script>
        function updateMap(event) {
            event.preventDefault(); // Prevent form submission
            const departure = document.getElementById('departure').value;
            const destination = document.getElementById('destination').value;

            // Encode the locations for the URL
            const encodedDeparture = encodeURIComponent(departure);
            const encodedDestination = encodeURIComponent(destination);

            // Create the new map URL
            const mapSrc = `https://www.google.com/maps/embed/v1/directions?key=YOUR_API_KEY&origin=${encodedDeparture}&destination=${encodedDestination}&maptype=roadmap`;

            // Update the iframe's source
            document.getElementById('map-frame').src = mapSrc;
        }
    </script>
</body>
</html>
