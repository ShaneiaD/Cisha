<?php
session_start();  // Start the session
include('db.php');  // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}
$user_id = $_SESSION['user_id'];

// Get course ID from URL
$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    die("Invalid course ID.");
}

// Fetch Course Details
$stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

// Fetch Instructor Name
$stmt = $conn->prepare("SELECT users.name FROM users JOIN courses ON users.user_id = courses.instructor_id WHERE courses.course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$instructor = $stmt->get_result()->fetch_assoc();

// Check if User is Enrolled
$enrolled = false;
$stmt = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$enrolled = $stmt->get_result()->num_rows > 0;

// Handle Enrollment with Code Verification
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['course_code'])) {
    $entered_code = trim($_POST['course_code']); // Trim input

    // Fetch correct course code from database
    $stmt = $conn->prepare("SELECT course_code FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course_data = $result->fetch_assoc();

    if ($course_data) {
        $correct_code = trim($course_data['course_code']); // Trim stored code

        // Compare codes (case-insensitive)
        if (strcasecmp($entered_code, $correct_code) === 0) {
            if (!$enrolled) {
                // Enroll user
                $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ii", $user_id, $course_id);
                    $stmt->execute();
                    $_SESSION['success_message'] = "✅ You have successfully enrolled!";
                }
            }
        } else {
            $_SESSION['error_message'] = "❌ Your code is invalid. Please try again."; // Store error message
        }
    } else {
        $_SESSION['error_message'] = "❌ Course not found.";
    }
    
    // Refresh page to update UI
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $course_id);
    exit();
}

// Retrieve student details
$name = $_SESSION['name'] ?? 'Student';
$student_id = $_SESSION['user_id']; // Ensure student_id is set

// Extract the first letter of the student's name for profile icon
$first_letter = strtoupper(substr($name, 0, 1));


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($course['course_name']) ? $course['course_name'] : 'Course not found'; ?> - Enrollment</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="dashboard/db.css">
    <link rel="stylesheet" href="site/site.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
        .enroll-btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .enroll-btn:hover {
            background: #0056b3;
        }
        .enrolled-message {
            color: green;
            font-weight: bold;
        }
        .back-btn {
            display: inline-block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .back-btn:hover {
            text-decoration: underline;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 40%;
            text-align: center;
        }
        .modal input {
            width: 80%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .modal .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
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
    <a href="studentdb.php">⏲<span>Dashboard</span></a>
    <a href="site.php">🏠 <span>Site Home</span></a>
    <a href="student_calendar.php">📅 <span>Calendar</span></a>
</div>

<div class="content">
    <div class="header">
        <h1><?php echo isset($course['course_name']) ? $course['course_name'] : 'Course not found'; ?> - Enrollment</h1>
    </div>

    <div class="container">
    <h1><?php echo isset($course['course_name']) ? $course['course_name'] : 'Course not found'; ?></h1>
    <p><strong>Instructor:</strong> <?php echo isset($instructor['name']) ? $instructor['name'] : 'Instructor not found'; ?></p>

    <div class="course-description">
        <p><?php echo isset($course['description']) ? $course['description'] : 'Description not available'; ?></p>
    </div>

    <?php if (!$user_id): ?>
        <p style="color:red;">⚠️ You must log in to enroll.</p>
    <?php elseif (!$enrolled): ?>
        <button onclick="openModal()" class="enroll-btn">Enroll Now</button>
    <?php else: ?>
        <p class="success">✅ You are already enrolled in this course.</p>
    <?php endif; ?>

    <a href="site.php" class="back-btn">Back to Courses</a>

</div>

<!-- Enrollment Code Modal -->
<div id="enrollModal" class="modal" style="<?php echo isset($_SESSION['error_message']) ? 'display: block;' : 'display: none;'; ?>">
    <div class="modal-content">
        <h2>Enter Enrollment Code</h2>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="course_code" placeholder="Enter course code" required>
            <br>
            <button type="submit" class="enroll-btn">Submit</button>
            <button type="button" class="close-btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>





<script>
    function openModal() {
    document.getElementById("enrollModal").style.display = "block";
}

function closeModal() {
    document.getElementById("enrollModal").style.display = "none";
}
</script>

<script src="dashboard/db.js"></script>
</body>
</html>
