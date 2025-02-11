<?php
session_start();
include('db.php');

$instructor_id = $_SESSION['user_id'];
$sql = "SELECT date, time, meeting_details FROM schedules WHERE instructor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['date'];
    if (!isset($schedules[$date])) {
        $schedules[$date] = [];
    }
    $schedules[$date][] = "Time: " . $row['time'] . "\nDetails: " . $row['details'];
}

echo json_encode($schedules);
$stmt->close();
$conn->close();
?>
