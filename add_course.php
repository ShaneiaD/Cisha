<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "learning_ms");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Check if user is logged in as an instructor
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$instructor_id = $_SESSION['user_id']; // Get the logged-in instructor's user_id

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $conn->real_escape_string($_POST['course_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $school_year = $conn->real_escape_string($_POST['school_year']);
    $course_code = $conn->real_escape_string($_POST['course_code']);

    if (!empty($course_name) && !empty($description) && !empty($school_year) && !empty($course_code)) {
        // Insert course and associate it with the instructor
        $sql = "INSERT INTO courses (course_name, description, school_year, course_code, instructor_id) 
                VALUES ('$course_name', '$description', '$school_year', '$course_code', '$instructor_id')";

        if ($conn->query($sql) === TRUE) {
            $message = "<p style='color: green;'>Course added successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error: " . $conn->error . "</p>";
        }
    } else {
        $message = "<p style='color: red;'>All fields are required!</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/log.png">
    <title>Add Course</title>
    <style>
        /* Basic reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    color: #333;
    padding: 20px;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
    color: #2c3e50;
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    font-size: 16px;
    color: #555;
    margin-bottom: 5px;
}

input[type="text"],
textarea {
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 100%;
}

textarea {
    resize: vertical;
    min-height: 120px;
}

button {
    background-color: #3498db;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #2980b9;
}

a {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #3498db;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Message styling */
p {
    text-align: center;
    font-weight: bold;
}

p[style*="color: green"] {
    color: green;
}

p[style*="color: red"] {
    color: red;
}

</style>
</head>
<body>
    <div class="container">
        <h2>Add a New Course</h2>
        <?php echo $message; ?>
        <form action="add_course.php" method="POST">
            <label for="course_name">Course Name:</label>
            <input type="text" name="course_name" required>

            <label for="description">Course Description:</label>
            <textarea name="description" required></textarea>

            <label for="school_year">School Year:</label>
            <input type="text" name="school_year" required>

            <label for="course_code">Course Code:</label>
            <input type="text" name="course_code" required>

            <button type="submit">Add Course</button>
        </form>
        <br>
        <a href="admindb.php">Back to Dashboard</a>
    </div>
</body>
</html>
