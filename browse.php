<?php
session_start();  // Start the session
include('db.php');  // Include the database connection file

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all courses
$result = $conn->query("SELECT * FROM courses");

echo "<h2>Available Courses</h2>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li>{$row['title']} 
        <a href='enroll.php?course_id={$row['id']}'>Enroll</a></li>";
}
echo "</ul>";

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Courses</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="styles.css">
    <script defer src="scripts.js"></script>
</head>
<body>
    <h2>Available Courses</h2>
    <ul id="courseList"></ul>
</body>
</html>
