<?php
session_start();  // Start the session
include('db.php');  // Include the database connection file

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve student details
$name = $_SESSION['name'] ?? 'Student';
$student_id = $_SESSION['user_id']; // Ensure student_id is set

// Extract the first letter of the student's name for profile icon
$first_letter = strtoupper(substr($name, 0, 1));

// Fetch courses from the database
$sql = "SELECT courses.course_id, courses.course_name, courses.description, courses.instructor_id, courses.school_year, users.name AS instructor_name 
        FROM courses 
        JOIN users ON courses.instructor_id = users.user_id";  // Assuming 'user_id' is the correct column name
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NRGLMS Site Home</title>
    <link rel="stylesheet" href="dashboard/db.css">
    <link rel="stylesheet" href="site/site.css">
    <link rel="icon" type="image/png" href="img/log.png">
</head>
<body>
<header class="header">
    <div class="logo-container">
        <div class="logo">
            <img src="img/log.png" alt="LMS Logo">
            <div class="logo-text">
                <span>N</span> R <span>G</span><br>
                <small>Learning Management System</small>
            </div>
        </div>
    </div>
    <div class="icons">
        <div class="profile">
            <div id="profile-icon" class="profile-icon"><?= $first_letter ?></div>
            <div id="dropdown-menu" class="dropdown-menu">
            <ul>
                    <li><a href="sprofile.php">View Profile</a></li>
                    <li><a href="studentdb.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="sidebar">
    <a href="studentdb.php">⏲<span>Dashboard</span></a>
    <a href="site.php">🏠 <span>Site Home</span></a>
    <a href="student_calendar.php">📅 <span>Calendar</span></a>
</div>

<div class="content">
    <div class="header">
        <h1>NRGLMS</h1>
    </div>

    <!-- My Courses Section -->
    <section class="my-courses">
        <h2>Available Courses</h2>
        <div class="courses-grid">
            <?php while ($course = $result->fetch_assoc()): ?>
                <div class="course-card">
                    <div class="course-details">
                        <span cl ass="badge">School Year: <?php echo htmlspecialchars($course['school_year']); ?></span>
                        <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <h1>Instructor: <?php echo htmlspecialchars($course['instructor_name']); ?></h1>
                        <a href="enrollment.php?id=<?php echo htmlspecialchars($course['course_id']); ?>" class="access-button">Access</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <script src="site/site.js"></script> 
</div>

<script src="dashboard/db.js"></script>
</body>
</html>
