<?php
    session_start();
    include 'admin/config.php'; // Include your database configuration

    if (!isset($_SESSION['username'])) {
        header("Location: login.php"); // Redirect to login if not logged in
        exit();
    }

    $destination = '';
    $departure = '';
    $bikes = [];

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['destination']) && isset($_GET['departure'])) {
        $destination = mysqli_real_escape_string($conn, $_GET['destination']);
        $departure = mysqli_real_escape_string($conn, $_GET['departure']);

        // Query to fetch available bikes based on destination and departure
        $bikes_query = mysqli_query($conn, "
            SELECT bikes.*, routes.price 
            FROM bikes 
            JOIN routes ON bikes.bike_plate = routes.bike_plate 
            WHERE routes.destination = '$destination' 
            AND routes.departure = '$departure' 
            AND bikes.available = 1
        ");

        if ($bikes_query) {
            while ($row = mysqli_fetch_assoc($bikes_query)) {
                $bikes[] = $row;
            }
        } else {
            echo "<p>Error fetching bikes: " . mysqli_error($conn) . "</p>";
        }
    }
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
            <a href="index.php">BACK</a>
        </nav>
    </header>
    
    <div class="fred">
        <div class="cont">
            <h1>Bike Booking Service</h1>
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
    <div class="bike-list">
                <h2>Available Bikes</h2>
                <?php if ($bikes): ?>
                    <div class="bike-cards">
                        <?php foreach ($bikes as $bike): ?>
                            <div class="bike-card">
                                <img src="admin/uploaded_img/<?php echo htmlspecialchars($bike['bike_image']); ?>" alt="<?php echo htmlspecialchars($bike['bike_plate']); ?>" class="small-img">
                            </div>
                            <h3><?php echo htmlspecialchars($bike['bike_plate']); ?></h3>
                                <p>Rider: <?php echo htmlspecialchars($bike['rider']); ?></p>
                                <p>Rider No: <?php echo htmlspecialchars($bike['rider_no']); ?></p>
                                <p>Price: Ksh <?php echo htmlspecialchars($bike['price']); ?></p>
                                <button onclick="bookBike('<?php echo htmlspecialchars($bike['bike_plate']); ?>')">Book Now</button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No bikes available for this route.</p>
                <?php endif; ?>
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

        function bookBike(bikePlate) {
        const departure = document.getElementById('departure').value;
        const destination = document.getElementById('destination').value;
        
        // Redirect to booking page with bike plate and user details
        window.location.href = `booking_page.php?bike_plate=${encodeURIComponent(bikePlate)}&departure=${encodeURIComponent(departure)}&destination=${encodeURIComponent(destination)}`;
    }
    </script>

</body>
</html>
