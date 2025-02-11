<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $course_id = $_POST["course_id"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $details = $_POST["details"];

    // Fetch student emails from the database based on the course
    $stmt = $conn->prepare("SELECT users.email FROM users 
                            JOIN enrollments ON users.id = enrollments.student_id 
                            WHERE enrollments.course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $emails = [];

    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }

    // Insert the schedule into the database
    $stmt = $conn->prepare("INSERT INTO classschedule (course_id, date, time, meeting_details) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $course_id, $date, $time, $meeting_details);
    $stmt->execute();

    // Send email notifications
    foreach ($emails as $email) {
        sendEmailNotification($email, $course_id, $date, $time, $meeting_details);
    }

    echo json_encode(["success" => true]);
}

// Function to send email notification
function sendEmailNotification($email, $course_id, $date, $time, $meeting_details) {
    $subject = "New Schedule Added for Your Course";
    $message = "
        <html>
        <head>
            <title>New Schedule Notification</title>
        </head>
        <body>
            <h2>New Schedule Notification</h2>
            <p>A new schedule has been added for your course (Course ID: $course_id).</p>
            <p><strong>Date:</strong> $date</p>
            <p><strong>Time:</strong> $time</p>
            <p><strong>Details:</strong> $meeting_details</p>
            <p>Please check your calendar for more information.</p>
        </body>
        </html>
    ";
    
    // Set headers for HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@example.com"; // Change to your email

    // Send the email
    mail($email, $subject, $message, $headers);
}
?>
