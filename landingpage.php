<?php
// Establish database connection
session_start();  // Start the session to access session variables
include('db.php');  // Include the database connection file

// Check connection to the database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // If connection fails, terminate and show error
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/log.png">
    <title>Learning Management System</title>
    <link rel="stylesheet" href="landingpage.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="img/log.png" alt="LMS Logo">LMS
        </div>
        <nav>
            <ul>
                <li><a href="service.php">Services</a></li>
                <li><a href="landingpage.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
            </ul>
        </nav>
    </header>

    <main>
  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1>LEARNING</h1> <h1>MANAGEMENT</h1> <h1>SYSTEM<span class="registered">&reg;</span></h1>
      <p>/ Empower Learning, Anywhere, Anytime. /</p>
      <a href="login.php"><button class="start-btn">Start</button></a>
    </div>

    <div class="feature">
        <h2>Innovative Design </h2>
        <h2>& Practical Solutions</h2>
        <p>From concepts to classroom applications.</p>
    </div>

    <div class="contact-box">
      <h3>Stay in touch</h3>
      <a>https://bicol-u.edu.ph</a>
      <a>nrglms@bicol-u.edu.ph</a>
    </div>

  </section>

  <footer class="footer">
        <p>&copy; 2025 LMS Services. All Rights Reserved.</p>
    </footer>

</main>

</body>
</html>

