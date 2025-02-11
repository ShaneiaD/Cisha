<?php
session_start();
include('db.php');

// Ensure only instructors can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    header("Location: admindb.php");
    exit();
}

// Fetch course details
$course = [];
$sql = "SELECT course_name, description FROM courses WHERE course_id = ? AND instructor_id = ?";
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

// Function to get student count based on status
function getStudentCount($conn, $course_id, $status) {
    $sql = "SELECT COUNT(*) AS count FROM studentprogress WHERE course_id = ? AND status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $course_id, $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Fetch student counts based on relevant progress statuses
$completed_count = getStudentCount($conn, $course_id, 'completed');
$in_progress_count = getStudentCount($conn, $course_id, 'in_progress');
$failed_count = getStudentCount($conn, $course_id, 'failed');

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Progress</title>
    <link rel="stylesheet" href="dashboard/db.css">
    <link rel="icon" type="image/png" href="img/log.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .dashboard {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        .header button:hover {
            background-color: #0056b3;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            margin: 0;
            color: #333;
        }

        .card a {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .card a:hover {
            text-decoration: underline;
        }

        .chart {
            margin-top: 10px;    
        }

        .chart canvas {
            display: block;
            margin: 0 auto;
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
    <a href="admindb.php">⏲ <span>Admin Dashboard</span></a> 
    <a href="adminsite.php">🏠 <span>Site Home</span></a>
</div>

<div class="content">
    <div class="header">
        <h1>Course: <?= htmlspecialchars($course['course_name']) ?></h1>
    </div>

    <div class="dashboard">
        <h2>Students Progress</h2>
        
        <div class="grid">
            <div class="card">
                <h3>Completed/Passed</h3>
                <p><?= $completed_count ?></p>
                <a href="view_students.php?course_id=<?= $course_id ?>&status=completed">View list</a>
            </div>
            <div class="card">
                <h3>In Progress</h3>
                <p><?= $in_progress_count ?></p>
                <a href="view_students.php?course_id=<?= $course_id ?>&status=in_progress">View list</a>
            </div>
            <div class="card">
                <h3>Failed</h3>
                <p><?= $failed_count ?></p>
                <a href="view_students.php?course_id=<?= $course_id ?>&status=failed">View list</a>
            </div>
        </div>

        <div class="chart">
            <canvas id="trainingStatusChart" width="400" height="200"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('trainingStatusChart').getContext('2d');

    // Data values from the database
    const dataValues = [
        <?= $in_progress_count ?>,
        <?= $completed_count ?>,
        <?= $failed_count ?>
    ];

    const backgroundColors = ['#42a5f5', '#66bb6a', '#ef5350'];

    const trainingStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['In Progress', 'Completed/Passed', 'Failed'],
            datasets: [{
                label: 'Training Status',
                data: dataValues,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>
</body>
</html>