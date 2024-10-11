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
if (!isset($_SESSION['rider_no'])) {
    header("Location: login.php");
    exit();
}

$rider_name = mysqli_real_escape_string($conn, $_SESSION['rider_name']);

// Fetch all booked bikes and their details
$query = "
    SELECT bo.*, b.bike_image, r.rider_name, r.rider_no 
    FROM bookings bo 
    JOIN bikes b ON bo.bike_plate = b.bike_plate 
    JOIN riders r ON b.rider_no = r.rider_no
";

$result = mysqli_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
$rider_username = $_SESSION['username'];

// Fetch notifications for the rider, specifically for pending bookings
$notification_query = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM bookings b 
    WHERE b.username = ? AND b.status = 'pending'
");

if (!$notification_query) {
    die("Error preparing statement: " . $conn->error);
}

$notification_query->bind_param("s", $rider_username);
$notification_query->execute();
$result = $notification_query->get_result();

$notification_row = $result->fetch_assoc();
$notification_count = $notification_row['count'];
$notification_query->close();

// Fetch all pending bookings
$bookings_query = mysqli_query($conn, "SELECT * FROM bookings WHERE status = 'pending'");
$bookings = mysqli_fetch_all($bookings_query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bike Booking System</title>
    <link rel="icon" href="bike.jpg" type="image">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="logo">
        <img src="avarter.png" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links"> 
            <a href="rider_notifications.php">Approve (<?php echo $notification_count; ?>)</a> 
            <a href="logout.php">Logout</a> 
        </nav>
    </header>
    <section class="manage">
        <h1>Welcome Rider, <?php echo htmlspecialchars($rider_name); ?>!</h1>
        <h2>All Booked Bikes</h2>
        
        <table align="center">
            <thead>
                <tr>
                    <th>BIKE IMAGE</th>
                    <th>BIKE PLATE</th>
                    <th>DESTINATION</th>
                    <th>DEPARTURE</th>
                    <th>PRICE</th>
                    <th>RIDER</th>
                    <th>RIDER NO</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><img src="admin/uploaded_img/<?php echo htmlspecialchars($row['bike_image']); ?>" class="small-img" alt="Bike Image"></td>
                    <td><?php echo htmlspecialchars($row['bike_plate']); ?></td>
                    <td><?php echo htmlspecialchars($row['destination']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure']); ?></td>
                    <td>Ksh <?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['rider_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['rider_no']); ?></td>
                </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7' class='empty'>No bikes found in bookings.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</body>
</html>
