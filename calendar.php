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

// Fetch instructor's courses
$sql = "SELECT course_id, course_name FROM courses WHERE instructor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="dashboard/db.css">
    <style>
        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-left: 1000px;    
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .scheduled-event {
            margin-top: 5px;
            background: #007bff;
            color: white;
            font-size: 12px;
            padding: 3px;
            border-radius: 3px;
        }

        .modal-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        .modal-content h3 {
            font-size: 18px;
            margin-bottom: 15px;
        }

        .modal-content label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .modal-content select,
        .modal-content input,
        .modal-content textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .modal-content button:hover {
            background: #218838;
        }

.calendar-container {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    padding: 90px;
    width: 800px;
    margin-top: 30px;
    margin-left: 200px;
    justify-content: center;
    align-items: center;
    max-width: 100%; /* Ensure it doesn't stretch out of the viewport on smaller screens */
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.nav-button {
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.nav-button:hover {
    background-color: #0056b3;
}

#monthName {
    font-size: 22px;
    color: #333;
}

.calendar-body {
    display: flex;
    flex-direction: column;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
}

.day-name {
    font-weight: bold;
    color: #555;
    padding: 5px 0;
}

.calendar-days-list {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
}

.day {
    width: 35px;
    height: 35px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f8f9fa;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.day:hover {
    background-color: #007bff;
    color: white;
}

.scheduled-day {
    background-color: #ffcc00 !important; /* Highlight color */
    font-weight: bold;
    border-radius: 6px;
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
        <h1>Calendar</h1>
        <button id="open-schedule-modal" class="btn-primary">+ Add Schedule</button>
    <div id="calendar-root"></div>
    </div>

<!-- Modal for Adding Schedule -->
<div id="addScheduleModal" class="modal-background" style="display: none;">
    <div class="modal-content">
        <h3>Add Schedule</h3>

        <label for="course">Course</label>
        <select id="course">
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['course_id']; ?>"><?= $course['course_name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="scheduleDate">Date</label>
        <input type="date" id="scheduleDate">

        <label for="scheduleTime">Time</label>
        <input type="time" id="scheduleTime">

        <label for="meetingDetails">Meeting Details</label>
        <textarea id="meetingDetails" rows="3" placeholder="Enter meeting details..."></textarea>

        <button id="save-schedule">Create Schedule</button>
        <button id="close-modal-btn">Cancel</button>
    </div>
</div>

<div class="calendar-container">
        <div class="calendar-header">
            <button id="prevMonth" class="nav-button">‹</button>
            <h2 id="monthName"></h2>
            <button id="nextMonth" class="nav-button">›</button>
        </div>
        <div class="calendar-body">
            <div class="calendar-days">
                <div class="day-name">Sun</div>
                <div class="day-name">Mon</div>
                <div class="day-name">Tue</div>
                <div class="day-name">Wed</div>
                <div class="day-name">Thu</div>
                <div class="day-name">Fri</div>
                <div class="day-name">Sat</div>
            </div>
            <div id="calendarDays" class="calendar-days-list"></div>
        </div>
    </div>

<script>
    
    const prevMonthButton = document.getElementById('prevMonth');
const nextMonthButton = document.getElementById('nextMonth');
const monthName = document.getElementById('monthName');
const calendarDays = document.getElementById('calendarDays');

let currentDate = new Date();
let schedules = {}; // Object to store schedules (date as key)

function renderCalendar(date) {
    const month = date.getMonth();
    const year = date.getFullYear();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    calendarDays.innerHTML = '';

    const options = { year: 'numeric', month: 'long' };
    monthName.textContent = date.toLocaleDateString('en-US', options);

    // Render empty days for alignment
    for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        calendarDays.appendChild(emptyDay);
    }

    // Render the days of the month
    for (let i = 1; i <= daysInMonth; i++) {
        const day = document.createElement('div');
        day.classList.add('day');
        day.textContent = i;

        const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;

        // Highlight days with schedules
        if (schedules[dateKey]) {
            day.classList.add('scheduled-day'); // Add special class for styling
        }

        // Show schedule details when clicked
        day.addEventListener('click', () => {
            if (schedules[dateKey]) {
                alert(`Schedule for ${dateKey}:\n${schedules[dateKey]}`);
            } else {
                alert(`No schedule on ${dateKey}`);
            }
        });

        calendarDays.appendChild(day);
    }
}

// Event listeners for navigation
prevMonthButton.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
});

nextMonthButton.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
});

// Initial render
renderCalendar(currentDate);

// Modal Functionality
document.addEventListener("DOMContentLoaded", function () {
    const openModalBtn = document.getElementById("open-schedule-modal");
    const closeModalBtn = document.getElementById("close-modal-btn");
    const modal = document.getElementById("addScheduleModal");
    const saveScheduleBtn = document.getElementById("save-schedule");

    // Open modal
    openModalBtn.addEventListener("click", function () {
        modal.style.display = "flex";
    });

    // Close modal
    closeModalBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Close modal when clicking outside
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Save schedule
    saveScheduleBtn.addEventListener("click", function () {
        const course = document.getElementById("course").value;
        const date = document.getElementById("scheduleDate").value;
        const time = document.getElementById("scheduleTime").value;
        const details = document.getElementById("meetingDetails").value;

        if (!course || !date || !time || !details) {
            alert("All fields are required.");
            return;
        }

        const scheduleText = `Course: ${course}\nTime: ${time}\nDetails: ${details}`;
        schedules[date] = scheduleText;

        alert(`Schedule added:\n${scheduleText}`);

        modal.style.display = "none";
        renderCalendar(currentDate); // Refresh calendar to update color
    });
});

document.getElementById("save-schedule").addEventListener("click", function () {
    const course = document.getElementById("course").value;
    const date = document.getElementById("scheduleDate").value;
    const time = document.getElementById("scheduleTime").value;
    const details = document.getElementById("meetingDetails").value;

    if (!course || !date || !time || !details) {
        alert("All fields are required.");
        return;
    }

    fetch("save_schedule.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `course_id=${course}&date=${date}&time=${time}&details=${details}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (!schedules[date]) {
                schedules[date] = [];
            }
            schedules[date].push(`Time: ${time}\nDetails: ${details}`);
            renderCalendar(currentDate);
            document.getElementById("addScheduleModal").style.display = "none";
            alert("Schedule saved successfully!");
        } else {
            alert("Failed to save schedule.");
        }
    })
    .catch(error => console.error("Error saving schedule:", error));
});

function loadSchedules() {
    fetch("get_schedules.php")
        .then(response => response.json())
        .then(data => {
            schedules = data;
            renderCalendar(currentDate);
        })
        .catch(error => console.error("Error loading schedules:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    loadSchedules();
});


</script>
  <script src="dashboard/db.js"></script>

</body>
</html>
