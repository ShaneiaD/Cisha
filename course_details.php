<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'] ?? 'Instructor';
$instructor_id = $_SESSION['user_id'];
$first_letter = strtoupper(substr($name, 0, 1));

$instructor_id = $_SESSION['user_id'];
$course_id = $_GET['id'] ?? 0;

// Get course details (ensure instructor owns it)
$sql = "SELECT * FROM courses WHERE course_id = ? AND instructor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $course_id, $instructor_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    die("Course not found or you don't have permission to access it.");
}

// Handle adding a topic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["topic_name"])) {
    $topic_name = $_POST["topic_name"];

    // Insert topic into topics table
    $sql = "INSERT INTO topics (course_id, topic_name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $course_id, $topic_name);
    $stmt->execute();
}

// Get topics for this course
$sql = "SELECT * FROM topics WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$topics = $stmt->get_result();

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $upload_dir = "uploads/";

    // Ensure the upload directory exists, if not, create it
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Set permissions
    }

    $file_name = basename($_FILES["file"]["name"]);
    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
    $target_file = $upload_dir . time() . "_" . $file_name;

    // Topic selected by the user
    $topic_id = $_POST['topic_id'];

    // Attempt to move the uploaded file
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // Insert into database
        $sql = "INSERT INTO coursecontent (course_id, file_name, file_path, file_type, topic_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssi", $course_id, $file_name, $target_file, $file_type, $topic_id);
        $stmt->execute();
    } else {
        echo "File upload failed. Please check directory permissions.";
    }
}

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
    <title>Manage Course - <?= htmlspecialchars($course['course_name']) ?></title>
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

        form {
            margin: 20px 0;
        }

        input[type="file"], input[type="text"] {
            padding: 10px;
        }

        button {
            background: #2ecc71;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background: #27ae60;
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
    <a href="student.php?course_id=<?= $course['course_id'] ?>">📚 <span> View Students</span></a>
    <a href="calendar.php">📅 <span>Calendar</span></a>
</div>

<div class="content">
<div class="header">
        <h1>NRGLMS: <?php echo isset($course['course_name']) ? $course['course_name'] : 'Course not found'; ?></h1>
    </div>


    <div class="container">

        <!-- Add Topic Form -->
        <h2>Add New Topic</h2>
        <form action="" method="post">
            <label for="topic_name">Topic Name:</label>
            <input type="text" name="topic_name" required>
            <button type="submit">Add Topic</button>
        </form>

        <!-- Add Quiz Button -->
<h2>Create Quiz</h2>
<form action="quiz.php" method="get">
    <input type="hidden" name="course_id" value="<?= $course_id ?>">
    <button type="submit">Create Quiz</button>
</form>


        <!-- Upload Course Content Form -->
        <h2>Upload Course Content</h2>
        
        <form action="" method="post" enctype="multipart/form-data">
            <label for="topic_id">Select Topic:</label>
            <select name="topic_id" required>
                <?php while ($topic = $topics->fetch_assoc()): ?>
                    <option value="<?= $topic['topic_id'] ?>"><?= htmlspecialchars($topic['topic_name']) ?></option>
                <?php endwhile; ?>
            </select>
            <input type="file" name="file" required>
            <button type="submit">Upload</button>
        </form>

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
        <a href="<?= $row['file_path'] ?>" target="_blank"><?= htmlspecialchars($row['file_name']) ?></a>
    </li>
    <p>___________________________________________________________________________________________________________________________________________</p>
<?php endwhile; ?>
</ul>
<h2>Quizzes</h2>
<?php
// Get quizzes for the current course
$sql = "SELECT * FROM quizzes WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$quizzes = $stmt->get_result();

// Check if there are quizzes created for the course
if ($quizzes->num_rows > 0):
    echo "<ul>";
    while ($quiz = $quizzes->fetch_assoc()):
        // Display each quiz title with a link to take the quiz or view/edit
        echo "<li>";
        echo "<a href='quiz_details.php?quiz_id=" . $quiz['quiz_id'] . "'>" . htmlspecialchars($quiz['quiz_title']) . "</a>";
        echo "</li>";
    endwhile;
    echo "</ul>";
else:
    echo "<p>No quizzes have been created for this course yet.</p>";
endif;
?>
    </div>
    
    
<script src="dashboard/db.js"></script>

</body>
</html>


  