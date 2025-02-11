<?php
session_start();
include('db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the quiz details from the database
if (isset($_GET['quiz_id'])) {
    $quiz_id = (int) $_GET['quiz_id'];
    
    // Get quiz information
    $quiz_sql = "SELECT * FROM quizzes WHERE quiz_id = $quiz_id";
    $quiz_result = $conn->query($quiz_sql);
    
    if ($quiz_result->num_rows > 0) {
        $quiz = $quiz_result->fetch_assoc();
        $quiz_title = htmlspecialchars($quiz['quiz_title']);
        $course_id = $quiz['course_id'];
        
        // Get questions for the quiz
        $questions_sql = "SELECT * FROM quizquestions WHERE quiz_id = $quiz_id";
        $questions_result = $conn->query($questions_sql);
    } else {
        echo "<p class='error'>Quiz not found!</p>";
        exit();
    }
} else {
    echo "<p class='error'>No quiz selected!</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Details</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="dashboard/db.css">
    <style>
        .container {
            width: 60%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        .question-container {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
            background: #f9f9f9;
        }

        .question-container label {
            font-weight: bold;
        }

        .mcq_options input {
            margin-bottom: 5px;
        }

        .answer {
            font-style: italic;
        }

        .back-button {
            margin-top: 20px;
            padding: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Quiz Details</h2>
    <h3><?= $quiz_title ?></h3>

    <div>
        <h4>Questions:</h4>
        <?php
        if ($questions_result->num_rows > 0) {
            while ($question = $questions_result->fetch_assoc()) {
                $question_type = $question['question_type'];
                $question_text = htmlspecialchars($question['question_text']);
                $answer = htmlspecialchars($question['answer']);
                $options = json_decode($question['options']);
                
                echo "<div class='question-container'>";
                echo "<label>Question:</label> <p>$question_text</p>";
                echo "<label>Type:</label> <p>$question_type</p>";

                if ($question_type == 'MCQ') {
                    echo "<label>Options:</label><ul>";
                    foreach ($options as $option) {
                        echo "<li>" . htmlspecialchars($option) . "</li>";
                    }
                    echo "</ul>";
                }

                echo "<label>Answer:</label> <p class='answer'>$answer</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No questions available for this quiz.</p>";
        }
        ?>
    </div>

    <a href="adminsite.php" class="back-button">Back to Dashboard</a>
</div>

</body>
</html>
