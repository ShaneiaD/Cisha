<?php
session_start();
include('db.php'); // Include the database connection

// Ensure only logged-in students can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve student ID from session
$student_id = $_SESSION['user_id'];

// Fetch student details from the database
$stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// If student data is found, update session variables
if ($student) {
    $_SESSION['name'] = $student['name'];
    $_SESSION['email'] = $student['email'];
}

// Assign variables for use in the HTML
$name = $_SESSION['name'] ?? 'Student';
$email = $_SESSION['email'] ?? 'No Email';
$first_access = $_SESSION['first_access'] ?? 'N/A';
$last_access = $_SESSION['last_access'] ?? 'N/A';

// Extract first letter for profile icon
$first_letter = strtoupper(substr($name, 0, 1));

// Fetch enrolled courses
$stmt = $conn->prepare("SELECT courses.course_id, courses.course_name, courses.description 
                        FROM enrollments 
                        JOIN courses ON enrollments.course_id = courses.course_id 
                        WHERE enrollments.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$enrolled_courses = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="dashboard/db.css">
    <style>
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-card {
            text-align: center;
            padding: 20px;
        }
        .circle {
            width: 60px;
            height: 60px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            margin: auto;
        }
        .tabs {
            display: flex;
            margin-top: 20px;
            justify-content: space-around;
            cursor: pointer;
        }
        .tab-btn {
            padding: 10px;
            background: #ddd;
            border: none;
            cursor: pointer;
            width: 50%;
            text-align: center;
        }
        .tab-btn.active {
            background: #007bff;
            color: white;
        }
        .tab-content {
            display: none;
            padding: 20px;
        }
        .tab-content.active {
            display: block;
        }
        .course-card {
            background: #eef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .view-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .view-btn:hover {
            background: #0056b3;
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
                    <li><a href="sprofile.php">View Profile</a></li>
                    <li><a href="studentdb.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<div class="sidebar">
    <a href="studentdb.php">⏲ <span>Dashboard</span></a> 
    <a href="site.php">🏠 <span>Site Home</span></a>
    <a href="student_calendar.php">📅 <span>Calendar</span></a>
</div>

<div class="content">
    <div class="header">
        <h1>Student Profile</h1>
    </div>

<div class="container">
    <h1>Student Profile</h1>
    <div class="profile-card">
        <div class="circle"><?php echo $first_letter; ?></div>
        <h2><?php echo htmlspecialchars($name); ?></h2>
        <p><?php echo htmlspecialchars($email); ?></p>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="openTab('courses')">Courses</button>
        <button class="tab-btn" onclick="openTab('details')">Details</button>
    </div>

    <div id="courses" class="tab-content active">
        <h3>Enrolled Courses</h3>
        <?php if (empty($enrolled_courses)): ?>
            <p>You are not enrolled in any courses yet.</p>
        <?php else: ?>
            <?php foreach ($enrolled_courses as $course): ?>
                <div class="course-card">
                    <h4><?php echo htmlspecialchars($course['course_name']); ?></h4>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <a href="courses.php?id=<?php echo $course['course_id']; ?>" class="view-btn">View Course</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="details" class="tab-content">
        <h3>User Details</h3>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>First Access:</strong> <?php echo htmlspecialchars($first_access); ?></p>
        <p><strong>Last Access:</strong> <?php echo htmlspecialchars($last_access); ?></p>
    </div>
</div>

<script>
function openTab(tabName) {
    document.querySelectorAll(".tab-content").forEach(tab => {
        tab.classList.remove("active");
    });
    document.getElementById(tabName).classList.add("active");

    document.querySelectorAll(".tab-btn").forEach(btn => {
        btn.classList.remove("active");
    });
    event.currentTarget.classList.add("active");
}
</script>
<script src="dashboard/db.js"></script>

</body>
</html>
