<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Frebuddz Admin Dashboard</title>
    <link rel="icon" href="sign.png" type="image">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-dashboard">
        <h2>Welcome to Admin Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <p>This is your admin panel where you can manage the bike booking system.</p>
    </div>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Frebuddz Admin</h2>
        </div>
        <ul class="nav">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="add_bikes.php">Add Bikes</a></li>
            <li><a href="add_route.php">Add Route</a></li>
            <li><a href="admin_approve_bookings.php">Admin A</a></li>
            <li><a href="approve_booking.php">Approve</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="dashboard-header">
            <?php
              if(isset($_SESSION['username'])) {
                  $activeUser = $_SESSION['username'];
                  echo "<h1>Welcome, $activeUser</h1>";
              }
            ?>
            <div class="dashboard-actions">
                <button onclick="location.href='logout.php'" class="logout-button">Logout</button>
            </div>
        </div>
        <?php
        require_once 'config.php'; 

        // Fetch total available bikes
        $sqlBikes = "SELECT COUNT(*) AS total_bikes FROM bikes";
        $resultBikes = $conn->query($sqlBikes);
        $totalBikes = $resultBikes->fetch_assoc()['total_bikes'] ?? "Error fetching data";

        $sqlRoutes = "SELECT COUNT(*) AS total_routes FROM routes";
        $resultRoutes = $conn->query($sqlRoutes);
        $totalRoutes = $resultRoutes->fetch_assoc()['total_routes'] ?? "Error fetching data";

        $sqlUsers = "SELECT COUNT(*) AS total_users FROM users";
        $resultUsers = $conn->query($sqlUsers);
        $totalUsers = $resultUsers->fetch_assoc()['total_users'] ?? "Error fetching data";

        
        $conn->close();
        ?>
        <div class="dashboard-stats">
            <div class="stat-item" style="background-color: #f0ad4e;">
                <h2>Available Bikes</h2>
                <h1><?php echo $totalBikes; ?></h1>
            </div>
            <div class="stat-item" style="background-color: #5cb85c;">
                <h2>Available Routes</h2>
                <h1><?php echo $totalRoutes; ?></h1>
            </div>
            <div class="stat-item" style="background-color: red;">
                <h2>Available Users</h2>
                <h1><?php echo $totalUsers; ?></h1>
            </div>
            
        </div>
        <br><br><br><br>  
        <footer>
            <div align="center">
                <span>Copyright © 2024 All Rights Reserved <br> Fred Kamau </span>
            </div>
        </footer>        
    </div>
</body>
</html>
