<?php
session_start();
include('db.php');


$name = $_SESSION['name'] ?? 'Instructor';
$instructor_id = $_SESSION['user_id'];
$first_letter = strtoupper(substr($name, 0, 1));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NRGLMS Dashboard</title>
    <link rel="stylesheet" href="dashboard/db.css">
    <link rel="icon" type="image/png" href="img/log.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
/* Content */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    padding: 15px;
    border-radius: 5px;
}

header button {
    background: #2ecc71;
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 5px;
}

header button:hover {
    background: #27ae60;
}

/* Courses Section */
h2 {
    color: #2c3e50;
}

#course-list {
    list-style: none;
    padding: 0;
}

#course-list li {
    background: white;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

#course-list li a {
    text-decoration: none;
    color: #2c3e50;
    font-weight: bold;
}

#course-list li a:hover {
    color: #3498db;
}

</style>
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
                <li><a href="iprofile.php">View Profile</a></li>
                    <li><a href="admindb.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="sidebar">
    <a href="admindb.php">⏲ <span>Admin Dashboard</span></a>
    <a href="adminsite.php">🏠 <span>Site Home</span></a>
    <a href="calendar.php">📅 <span>Calendar</span></a>
</div>

<div class="content">
    <div class="header">
        <h1>NRGLMS</h1>
    </div>

    <div class="content">
        <header>
            <button onclick="location.href='add_course.php'"> + Add New Course</button>
        </header>
        <section>
            <h2>Your Courses</h2>
            <ul id="course-list">
            <?php
// Get the instructor ID from the session
$instructor_id = $_SESSION['user_id'] ?? 0; // Default to 0 if not logged in

$sql = "SELECT course_id, course_name FROM courses WHERE instructor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<li><a href='course_details.php?id=" . $row["course_id"] . "'>" . $row["course_name"] . "</a></li>";
    }
} else {
    echo "<li>No courses found.</li>";
}

$stmt->close();
$conn->close();
?>

            </ul>
        </section>
    </div>

    
<script src="dashboard/db.js"></script>
</body>
</html>
