<?php
session_start();  // Start the session
include('db.php');  // Include the database connection file

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure only students can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve student details
$name = $_SESSION['name'] ?? 'Student';
$student_id = $_SESSION['user_id']; // Ensure student_id is set

// Extract the first letter of the student's name for profile icon
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
    <title>NRGLMS Dashboard</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="dashboard/db.css">
    <style>
        .course-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .course-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .course-card h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .course-card p {
            font-size: 14px;
            color: #666;
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

        .notif-badge {
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 4px 8px;
    font-size: 12px;
    position: absolute;
    top: 5px;
    right: 10px;
}

.notifications-dropdown {
    display: none;
    position: absolute;
    top: 40px;
    right: 10px;
    background: white;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    width: 250px;
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
}

.notifications-dropdown.active {
    display: block;
}

.notification-item {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    font-size: 14px;
}

.notification-item:hover {
    background-color: #f8f9fa;
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

</div>

<div class="content">
    <div class="header">
        <h1>NRGLMS: Dashboard</h1>
    </div>

    <div class="dashboard">
        <h2>Course Overview</h2>
        <?php if (empty($enrolled_courses)): ?>
        <p>You are not enrolled in any courses yet.</p>
    <?php else: ?>
        <div class="course-list">
            <?php foreach ($enrolled_courses as $course): ?>
                <div class="course-card">
                    <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <a href="courses.php?id=<?php echo $course['course_id']; ?>" class="view-btn">View Course</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const notifButton = document.getElementById("notifButton");
    const notifDropdown = document.getElementById("notifDropdown");
    const notifCount = document.getElementById("notifCount");

    function fetchNotifications() {
        fetch("get_notifications.php")
            .then(response => response.json())
            .then(data => {
                notifDropdown.innerHTML = "";
                if (data.length > 0) {
                    notifCount.textContent = data.length;
                    notifCount.style.display = "inline-block";
                    data.forEach(notif => {
                        const notifItem = document.createElement("div");
                        notifItem.classList.add("notification-item");
                        notifItem.textContent = notif.message;
                        notifItem.addEventListener("click", function () {
                            markAsRead(notif.notification_id);
                        });
                        notifDropdown.appendChild(notifItem);
                    });
                } else {
                    notifCount.style.display = "none";
                    notifDropdown.innerHTML = "<div class='notification-item'>No new notifications</div>";
                }
            })
            .catch(error => console.error("Error fetching notifications:", error));
    }

    function markAsRead(notificationId) {
        fetch("mark_notification_read.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `notification_id=${notificationId}`
        })
        .then(() => fetchNotifications());
    }

    notifButton.addEventListener("click", function () {
        notifDropdown.classList.toggle("active");
    });

    fetchNotifications();
});
</script>
<script src="dashboard/db.js"></script>

</body>
</html>
