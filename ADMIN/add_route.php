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

if (isset($_POST['add_route'])) {
    $bike_plate = mysqli_real_escape_string($conn, $_POST['bike_plate']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $departure = mysqli_real_escape_string($conn, $_POST['departure']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    // Insert route into database
    $insert_route_query = "INSERT INTO routes (bike_plate, destination, departure, price) VALUES ('$bike_plate', '$destination', '$departure', '$price')";

    if (mysqli_query($conn, $insert_route_query)) {
        // Mark the bike as unavailable
        $update_bike_query = "UPDATE bikes SET available = 0 WHERE bike_plate = '$bike_plate'";
        mysqli_query($conn, $update_bike_query);
        
        echo "<script>alert('Route added successfully.'); window.location = 'add_route.php';</script>";
    } else {
        echo "<script>alert('Could not add route.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Route</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .small-img {
            height: 50px; /* Adjust size as needed */
        }
    </style>
</head>
<body>
    <header class="logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">
            <a href="index.php">BACK</a>
        </nav>
    </header>
    
    <div class="form-container">
        <h1>Add New Route</h1>
        <form method="POST" action="add_route.php">
            <label for="bike_plate">Bike Plate:</label>
            <select id="bike_plate" name="bike_plate" required>
                <?php
                // Fetch only available bikes from the database
                $bikes_query = mysqli_query($conn, "SELECT bike_plate, bike_image, rider, rider_no FROM bikes WHERE available = 1");
                while ($row = mysqli_fetch_assoc($bikes_query)) {
                    echo "<option value='{$row['bike_plate']}'>{$row['bike_plate']}</option>";
                }
                ?>
            </select>
            <div id="bikeDetails">
                <p id="bikeImageContainer"></p>
                <p id="riderContainer"></p>
            </div>

            <label for="destination">Destination:</label>
            <input type="text" id="destination" name="destination" required>

            <label for="departure">Departure:</label>
            <input type="text" id="departure" name="departure" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <button type="submit" class="btn" name="add_route">Add Route</button>
        </form>
    </div>
    <section class="manage">
        <h1>Added Routes</h1>
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
                // Fetch added routes along with bike details
                $select_routes = mysqli_query($conn, "
                    SELECT routes.*, bikes.bike_image, bikes.rider, bikes.rider_no
                    FROM routes
                    JOIN bikes ON routes.bike_plate = bikes.bike_plate
                ");

                if ($select_routes) {
                    if (mysqli_num_rows($select_routes) > 0) {
                        while ($row = mysqli_fetch_assoc($select_routes)) {
                            ?>
                            <tr>
                                <td><img src="uploaded_img/<?php echo $row['bike_image']; ?>" class="small-img" alt="Bike Image"></td>
                                <td><?php echo $row['bike_plate']; ?></td>
                                <td><?php echo $row['destination']; ?></td>
                                <td><?php echo $row['departure']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['rider']; ?></td>
                                <td><?php echo $row['rider_no']; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='empty'>No routes added</td></tr>";
                    }
                } else {
                    // Output the error if the query fails
                    echo "<tr><td colspan='7' class='empty'>Error fetching routes: " . mysqli_error($conn) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</body>
</html>
