<?php
session_start();
include('db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the quiz details
if (isset($_GET['quiz_id'])) {
    $quiz_id = (int) $_GET['quiz_id'];

    // Check if the student has already attempted this quiz
    $attempt_check_sql = "SELECT * FROM quiz_attempts WHERE student_id = $user_id AND quiz_id = $quiz_id";
    $attempt_check_result = $conn->query($attempt_check_sql);

    if ($attempt_check_result->num_rows > 0) {
        echo "<div class='result-container'>";
        echo "<h1>You have already taken this quiz.</h1>";
        echo "<h3>Your response has been recorded.</h3>";
        echo '<a href="studentdb.php"><button class="back-button">Go Back to Courses</button></a>';
        echo "</div>";
        exit();
    }

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $total_questions = 0;
    $correct_answers = [];

    foreach ($_POST as $question_id => $student_answer) {
        $question_id = (int) $question_id;

        $answer_sql = "SELECT question_text, answer FROM quizquestions WHERE question_id = $question_id";
        $answer_result = $conn->query($answer_sql);

        if ($answer_result->num_rows > 0) {
            $row = $answer_result->fetch_assoc();
            $correct_answer = $row['answer'];
            $question_text = htmlspecialchars($row['question_text']);

            if ($student_answer == $correct_answer) {
                $score++;
            }

            $correct_answers[] = [
                'question' => $question_text,
                'your_answer' => htmlspecialchars($student_answer),
                'correct_answer' => htmlspecialchars($correct_answer)
            ];
        }
        $total_questions++;
    }

    // Save quiz attempt to the database
    $insert_attempt = "INSERT INTO quiz_attempts (student_id, quiz_id, score, attempt_date, status) 
                       VALUES ($user_id, $quiz_id, $score, NOW(), 'completed')";
    $conn->query($insert_attempt);

    $check_progress_sql = "SELECT * FROM studentprogress WHERE student_id = $user_id AND course_id = $course_id";
    $check_progress_result = $conn->query($check_progress_sql);
    
    if ($check_progress_result->num_rows > 0) {
        // Update existing record
        $update_progress = "UPDATE studentprogress SET score = $score, status = 'completed', date_updated = NOW() 
                            WHERE student_id = $user_id AND course_id = $course_id";
        $conn->query($update_progress);
    } else {
        // Insert new record
        $insert_progress = "INSERT INTO studentprogress (student_id, course_id, score, status, date_updated) 
                            VALUES ($user_id, $course_id, $score, 'completed', NOW())";
        $conn->query($insert_progress);
    }
    

    // Show results
    echo "<div class='result-container'>";
    echo "<h1>You have taken the quiz for the course: " . htmlspecialchars($quiz['quiz_title']) . "</h1>";
    echo "<h3>Your response has been recorded.</h3>";
    echo "<h2>Your Score: $score / $total_questions</h2>";

    echo "<h3>Correct Answers:</h3>";
    echo "<ul class='answer-list'>";
    foreach ($correct_answers as $answer) {
        echo "<li><strong>" . $answer['question'] . "</strong><br>";
        echo "Your Answer: <span class='your-answer'>" . $answer['your_answer'] . "</span><br>";
        echo "Correct Answer: <span class='correct-answer'>" . $answer['correct_answer'] . "</span></li>";
    }
    echo "</ul>";

    echo '<a href="studentdb.php"><button class="back-button">Go Back to Courses</button></a>';
    echo "</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <style>

.result-container {
    width: 60%;
    margin: auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

h1, h2, h3 {
    color: #333;
}

.answer-list {
    list-style-type: none;
    padding: 0;
    text-align: left;
}

.answer-list li {
    background: #f9f9f9;
    padding: 10px;
    margin-top: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.your-answer {
    color: #dc3545;
    font-weight: bold;
}

.correct-answer {
    color: #28a745;
    font-weight: bold;
}

.back-button {
    margin-top: 20px;
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.back-button:hover {
    background: #0056b3;
}

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
        .submit-button {
            margin-top: 20px;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-button:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Take Quiz</h2>
    <h3><?= $quiz_title ?></h3>

    <form method="POST">
        <?php
        if ($questions_result->num_rows > 0) {
            while ($question = $questions_result->fetch_assoc()) {
                $question_id = $question['question_id'];
                $question_type = $question['question_type'];
                $question_text = htmlspecialchars($question['question_text']);
                $options = json_decode($question['options']);
                
                echo "<div class='question-container'>";
                echo "<p><strong>$question_text</strong></p>";
                
                if ($question_type == 'MCQ') {
                    foreach ($options as $option) {
                        echo "<label><input type='radio' name='$question_id' value='" . htmlspecialchars($option) . "' required> " . htmlspecialchars($option) . "</label><br>";
                    }
                } else {
                    echo "<input type='text' name='$question_id' required>";
                }
                echo "</div>";
            }
        } else {
            echo "<p>No questions available for this quiz.</p>";
        }
        ?>

        <button type="submit" class="submit-button">Submit Quiz</button>
    </form>
</div>

</body>
</html>
