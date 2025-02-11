<?php
session_start();
include('db.php');

// Ensure only instructors can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['course_id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$course_id || !$status) {
    header("Location: admindb.php");
    exit();
}

// Fetch course details to display on the page
$course = [];
$sql = "SELECT course_name FROM courses WHERE course_id = ? AND instructor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $course_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $course = $result->fetch_assoc();
} else {
    header("Location: admindb.php");
    exit();
}

// Fetch students based on course_id and status
$students = [];
$sql = "SELECT users.name, studentprogress.score
        FROM users
        JOIN studentprogress ON users.user_id = studentprogress.student_id
        WHERE studentprogress.course_id = ? AND studentprogress.status = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $course_id, $status); // Assuming status is a string (e.g., 'completed', 'in_progress')
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student List</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="dashboard/db.css">
    <style>
        /* Student Table */
.progress-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: white;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.progress-table th, .progress-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.progress-table th {
    background-color: #2c3e50;
    color: white;
}

.progress-table tr:hover {
    background-color: #f1f1f1;
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
            <div id="profile-icon" class="profile-icon"><?= strtoupper(substr($_SESSION['name'], 0, 1)) ?></div>
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
    <a href="cd.php">⏲ <span>Student Progress</span></a>
    <a href="adminsite.php">🏠 <span>Site Home</span></a>
</div>

<div class="content">
    <div class="header">
        <h1>Students in Course: <?= htmlspecialchars($course['course_name']) ?></h1>
        <h2>Status: <?= htmlspecialchars(ucfirst($status)) ?></h2>
    </div>

    <div class="dashboard">
        <table class="progress-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td><?= htmlspecialchars($student['score']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No students found for this status.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="dashboard/db.js"></script>
</body>
</html>
