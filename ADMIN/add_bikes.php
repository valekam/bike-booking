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

if (isset($_POST['add_bike'])) {
    $bike_plate = mysqli_real_escape_string($conn, $_POST['bike_plate']);
    $owner = mysqli_real_escape_string($conn, $_POST['owner']);
    $owner_no = mysqli_real_escape_string($conn, $_POST['owner_no']);
    $rider = mysqli_real_escape_string($conn, $_POST['rider']);
    $rider_no = mysqli_real_escape_string($conn, $_POST['rider_no']);
    $bike_image = $_FILES['bike_image']['name'];
    $bike_image_tmp_name = $_FILES['bike_image']['tmp_name'];
    $bike_image_folder = 'uploaded_img/' . $bike_image;

    $duplicate_check_query = mysqli_query($conn, "SELECT * FROM bikes WHERE bike_plate = '$bike_plate'");
    if (mysqli_num_rows($duplicate_check_query) > 0) {
        echo "<script>alert('A bike with the same plate exists.'); window.location.href='add_bikes.php';</script>";
    } else {
        // Set the bike as available (1)
        $insert_bike_query = "INSERT INTO bikes (bike_plate, owner, owner_no, rider, rider_no, bike_image, available)
        VALUES ('$bike_plate', '$owner', '$owner_no', '$rider', '$rider_no', '$bike_image', 1)";

        if (mysqli_query($conn, $insert_bike_query)) {
            move_uploaded_file($bike_image_tmp_name, $bike_image_folder);
            echo "<script>alert('Bike added successfully.'); window.location = 'add_bikes.php';</script>";
        } else {
            echo "<script>alert('Could not add bike.');</script>";
        }
    }
}

// Booking logic should set availability to 0
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_bike'])) {
    $bike_plate = mysqli_real_escape_string($conn, $_POST['bike_plate']);
    // Other booking details here...

    // Update availability to not available (0)
    $stmt = $conn->prepare("UPDATE bikes SET available = 0 WHERE bike_plate = ?");
    $stmt->bind_param("s", $bike_plate);
    $stmt->execute();
}

// Fetch added bikes with availability status
$select_products = mysqli_query($conn, "SELECT * FROM `bikes`");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frebuddz Bike Booking System</title>
    <link rel="icon" href="bike.jpg" type="image">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header class="logo">
        <img src="bike.jpg" alt="Bike Logo" class="bike-logo">
        <div class="logo-title">SECURE <span>BIKE BOOKING SYSTEM</span></div>
        <nav class="nav-links">
            <a href="index.php">BACK</a>
        </nav>
    </header>
    
    <div class="form-container">
        <h1>Add New Bike</h1>
        <form method="POST" action="add_bikes.php" enctype="multipart/form-data">
            <label for="bike_plate">Bike Plate:</label>
            <input type="text" id="bike_plate" name="bike_plate" required>

            <label for="owner">Owner:</label>
            <input type="text" id="owner" name="owner" required>

            <label for="owner_no">Owner No:</label>
            <input type="number" id="owner_no" name="owner_no" required>
            
            <label for="rider">Rider:</label>
            <input type="text" id="rider" name="rider" required>

            <label for="rider_no">Rider No:</label>
            <input type="number" id="rider_no" name="rider_no" required>

            <label for="bike_image">Bike Image:</label>
            <input type="file" id="bike_image" name="bike_image" required>

            <button type="submit" class="btn" name="add_bike">Add Bike</button>
        </form>
    </div>
    <section class="manage">
        <h1>Added Bikes</h1>
        <table align="center">
            <thead>
                <tr>
                    <th>BIKE IMAGE</th>
                    <th>BIKE PLATE</th>
                    <th>OWNER</th>
                    <th>OWNER NO</th>
                    <th>RIDER</th>
                    <th>RIDER NO</th>
                    <th>AVAILABLE</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$select_products) {
                    echo "Error: " . mysqli_error($conn);
                } elseif (mysqli_num_rows($select_products) > 0) {
                    while ($row = mysqli_fetch_assoc($select_products)) {
                        $availability = $row['available'] ? 'Yes' : 'No';
                ?>
                <tr>
                    <td><img src="uploaded_img/<?php echo $row['bike_image']; ?>" height="100" alt=""></td>
                    <td><?php echo $row['bike_plate']; ?></td>
                    <td><?php echo $row['owner']; ?></td>
                    <td><?php echo $row['owner_no']; ?></td>
                    <td><?php echo $row['rider']; ?></td>
                    <td><?php echo $row['rider_no']; ?></td>
                    <td><?php echo $availability; ?></td>
                </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7' class='empty'>No bike added</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</body>
</html>
