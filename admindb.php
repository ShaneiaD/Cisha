<?php
session_start();
include('db.php');

// Ensure only instructors can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'] ?? 'Instructor';
$instructor_id = $_SESSION['user_id'];
$first_letter = strtoupper(substr($name, 0, 1));

// Fetch courses added by the instructor from the database
$courses = [];
$sql = "SELECT course_id, course_name, description 
        FROM courses 
        WHERE instructor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

$conn->close();
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


.course-list {
    display: flex;
    flex-wrap: wrap;
}

.course-card {
    background-color: #f8f8f8;
    padding: 15px;
    border-radius: 8px;
    margin: 10px;
    width: 220px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.course-card h3 {
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.course-card p {
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
}

.view-btn {
    background-color: #3b82f6;
    color: white;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}

.view-btn:hover {
    background-color: #ff6600;
}

/* Table Styling */
.progress-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.progress-table th, .progress-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.progress-table th {
    background-color: #f4f7fc;
    color: #333;
}

.progress-bar {
    height: 10px;
    background-color: #ddd;
    border-radius: 5px;
    width: 100%;
    margin-top: 5px;
}

.progress-bar span {
    display: block;
    height: 100%;
    background-color: #3b82f6;
    border-radius: 5px;
}

/* Chart Container */
.chart-container {
    margin-top: 20px;
    width: 100%;
    height: 400px;
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
        <h1>NRGLMS: Dashboard</h1>
    </div>

    <div class="dashboard">
        <h2>Course Overview</h2>
        <?php if (empty($courses)): ?>
            <p>You have not added any courses yet.</p>
        <?php else: ?>
            <div class="course-list">
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <p><?php echo htmlspecialchars($course['description']); ?></p>
                        <a href="cd.php?id=<?php echo $course['course_id']; ?>" class="view-btn">View Course</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="dashboard/db.js"></script>
</body>
</html>
