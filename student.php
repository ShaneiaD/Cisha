<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    die("Session error: User not logged in.");
}

$instructor_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) && is_numeric($_GET['course_id']) ? (int) $_GET['course_id'] : 0;

if ($course_id === 0) {
    die("Invalid course ID.");
}


$sql = "SELECT * FROM courses WHERE course_id = ? AND instructor_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error: " . $conn->error);
}
$stmt->bind_param("ii", $course_id, $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

// Debugging: Check course query result
if (!$course) {
    die("Course not found or permission denied. Debug Info: course_id=$course_id, instructor_id=$instructor_id");
}

$name = $_SESSION['name'] ?? 'Instructor';
$instructor_id = $_SESSION['user_id'];
$first_letter = strtoupper(substr($name, 0, 1));


// Fetch instructor details
$sql = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$instructor = $stmt->get_result()->fetch_assoc();

// Fetch students enrolled in the course
$sql = "SELECT u.user_id, u.name, u.email 
        FROM users u
        JOIN enrollments e ON u.user_id = e.student_id
        WHERE e.course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Enrolled in <?= htmlspecialchars($course['course_name']) ?></title>
    <link rel="stylesheet" href="dashboard/db.css">
    <link rel="icon" type="image/png" href="img/log.png">
    <style>
        /* Reset some default styles */
/* Reset some default styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f7f7f7;
    color: #333;
    line-height: 1.6;
    margin: 0;
}

.container {
    width: 80%;
    margin: 30px auto;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 20px;
    color: #333;
}

.instructor-info {
    background: #f4f4f4;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.instructor-info h3 {
    margin-bottom: 10px;
    color: #555;
}

.instructor-info p {
    color: #777;
    text-align: left; /* Align the email to the left */
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 12px;
    text-align: left;
    color: #555;
}

th {
    background-color: #4CAF50;
    color: white;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #f1f1f1;
}

table td {
    word-wrap: break-word;
    max-width: 250px;
}

p {
    text-align: center;
    font-size: 1.1rem;
    color: #888;
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
    <!-- <a href="student.php">📚 <span>Students</span></a> -->
    <a href="student.php?course_id=<?= $course['course_id'] ?>">📚 <span> View Students</span></a>
    <a href="student_calendar">📅 <span>Calendar</span></a>
</div>

<div class="content">
<div class="header">
        <h1>NRGLMS: <?php echo isset($course['course_name']) ? $course['course_name'] : 'Course not found'; ?></h1>
    </div>

    <div class="container">
        <h2>Enrolled Students</h2>
        <div class="instructor-info">
            <h3>Instructor: <?= htmlspecialchars($instructor['name']) ?></h3>
            <p>Email: <?= htmlspecialchars($instructor['email']) ?></p>
        </div>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['user_id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php if ($result->num_rows == 0) echo "<p>No students are enrolled in this course.</p>"; ?>
    </div>
<script src="dashboard/db.js"></script>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
