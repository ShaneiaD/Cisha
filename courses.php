<?php
session_start();
include('db.php');

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'] ?? 'Student';
$user_id = $_SESSION['user_id'];
$first_letter = strtoupper(substr($name, 0, 1));

$role = $_SESSION['role'] ?? 'student'; // Assuming 'role' is stored in the session

$course_id = $_GET['id'] ?? 0;

// Get course details (ensure student is enrolled in the course)
$sql = "SELECT * FROM courses WHERE course_id = ? AND EXISTS (SELECT * FROM enrollments WHERE course_id = ? AND student_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $course_id, $course_id, $user_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    die("Course not found or you are not enrolled in this course.");
}

// Fetch course content (topics and files) for student
$sql_content = "SELECT * FROM coursecontent WHERE course_id = ?";
$stmt_content = $conn->prepare($sql_content);
$stmt_content->bind_param("i", $course_id);
$stmt_content->execute();
$content = $stmt_content->get_result();

// Get course content for each topic
$sql = "SELECT c.file_name, c.file_path, c.file_type, t.topic_name 
        FROM coursecontent c
        JOIN topics t ON c.topic_id = t.topic_id
        WHERE c.course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$content = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course - <?= htmlspecialchars($course['course_name']) ?></title>
    <link rel="stylesheet" href="dashboard/db.css">
    <link rel="icon" type="image/png" href="img/log.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container {
            margin-top: 20px;
            width: 95%;
            padding: 30px;
            background: #f4f4f4;
            border-radius: 5px;
        }

        h2 {
            color: #333;
            text-align: center;
            padding: 10px;
            background: #ecf0f1;
            border-radius: 5px;
        }

        .course-content-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 20px;
            padding: 10px;
            background: #ecf0f1;
            border-radius: 5px;
        }

        .topic-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #34495e;
            margin-top: 20px;
            padding: 10px;
            background: #ecf0f1;
            border-radius: 5px;
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
    <a href="studentdb.php">⏲ <span>Student Dashboard</span></a>
    <a href="site.php">🏠 <span>Site Home</span></a>
    <a href="student_calendar.php">📅 <span>Calendar</span></a>
</div>

<div class="content">
    <div class="header">
        <h1>NRGLMS: <?= htmlspecialchars($course['course_name']) ?></h1>
    </div>

    <div class="container">
        <!-- Display Course Content by Topic -->
        <p>_____________________________________________________________________________________________________________________________________________________</p>
        <h2 class="course-content-title">========Course Content========</h2>
        <p>_____________________________________________________________________________________________________________________________________________________</p>
        <?php
        $current_topic = '';
        while ($row = $content->fetch_assoc()):
            if ($current_topic !== $row['topic_name']):
                if ($current_topic !== '') echo "</ul>";
                $current_topic = $row['topic_name'];
                echo "<h3 class='topic-title'>" . htmlspecialchars($row['topic_name']) . "</h3><ul>";
            endif;
        ?>
        <li>
            <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank"><?= htmlspecialchars($row['file_name']) ?></a>
        </li>
        <p>___________________________________________________________________________________________________________________________________________</p>
        <?php endwhile; ?>
        </ul>

        <!-- Below Course Content -->
<p>_____________________________________________________________________________________________________________________________________________________</p>
<h2 class="course-content-title">========Quizzes========</h2>
<p>_____________________________________________________________________________________________________________________________________________________</p>

<?php
// Fetch quizzes for the specific course
$sql_quizzes = "SELECT * FROM quizzes WHERE course_id = ?";
$stmt_quizzes = $conn->prepare($sql_quizzes);
$stmt_quizzes->bind_param("i", $course_id);
$stmt_quizzes->execute();
$quizzes = $stmt_quizzes->get_result();

// Check if there are quizzes for this course
if ($quizzes->num_rows > 0) {
    while ($quiz = $quizzes->fetch_assoc()):
        ?>
        <div class="quiz-container">
            <!-- Wrap the quiz title in a link so it becomes clickable -->
            <h3 class="quiz-title">
            <a href="take_quiz.php?quiz_id=<?= $quiz['quiz_id'] ?>" class="quiz-title"><?= htmlspecialchars($quiz['quiz_title']) ?></a>
            </h3>
        </div>
        <p>___________________________________________________________________________________________________________________________________________</p>
        <?php
    endwhile;
} else {
    echo "<p>No quizzes available for this course.</p>";
}
?>


    </div>
</div>  

<script src="dashboard/db.js"></script>

</body>
</html>
