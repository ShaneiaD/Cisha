<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        p {
            font-size: 18px;
            color: #555;
        }

        strong {
            color: #222;
        }

        hr {
            border: 0;
            height: 1px;
            background: #ccc;
            margin: 20px 0;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .question {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        session_start();
        include('db.php');

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header("Location: login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $quiz_id = $_GET['quiz_id'] ?? 0;

        if ($quiz_id == 0) {
            die("Quiz ID is missing or invalid.");
        }

        $sql_score = "SELECT score FROM quiz_scores WHERE user_id = ? AND quiz_id = ?";
        $stmt_score = $conn->prepare($sql_score);
        $stmt_score->bind_param("ii", $user_id, $quiz_id);
        $stmt_score->execute();
        $result_score = $stmt_score->get_result();

        if ($result_score->num_rows === 0) {
            die("Score not found for this quiz.");
        }

        $score = $result_score->fetch_assoc()['score'];

        $sql_quiz = "SELECT * FROM quizzes WHERE quiz_id = ?";
        $stmt_quiz = $conn->prepare($sql_quiz);
        $stmt_quiz->bind_param("i", $quiz_id);
        $stmt_quiz->execute();
        $quiz = $stmt_quiz->get_result()->fetch_assoc();

        if (!$quiz) {
            die("Quiz not found for quiz_id: " . $quiz_id);
        }

        $sql_questions = "SELECT * FROM quizquestions WHERE quiz_id = ?";
        $stmt_questions = $conn->prepare($sql_questions);
        $stmt_questions->bind_param("i", $quiz_id);
        $stmt_questions->execute();
        $questions = $stmt_questions->get_result();

        $sql_answers = "SELECT * FROM quiz_scores WHERE user_id = ? AND quiz_id = ?";
        $stmt_answers = $conn->prepare($sql_answers);
        $stmt_answers->bind_param("ii", $user_id, $quiz_id);
        $stmt_answers->execute();
        $answers_result = $stmt_answers->get_result()->fetch_assoc();

        echo "<h1>Quiz Results for: " . htmlspecialchars($quiz['quiz_title']) . "</h1>";
        echo "<p><strong>Your Score: </strong>" . $score . " / " . $questions->num_rows . "</p>";
        echo "<h2>Review Your Answers:</h2>";

        while ($question = $questions->fetch_assoc()) {
            $correct_answer = $question['answer'];
            $user_answer_data = json_decode($answers_result['answer'], true);
            $user_answer = null;

            if (isset($user_answer_data['question_id']) && $user_answer_data['question_id'] == $question['question_id']) {
                $user_answer = $user_answer_data['answer'];
            }

            echo "<div class='question'>";
            echo "<p><strong>Question: </strong>" . htmlspecialchars($question['question_text']) . "</p>";
            echo "<p><strong>Your Answer: </strong>" . htmlspecialchars($user_answer) . "</p>";
            echo "<p><strong>Correct Answer: </strong>" . htmlspecialchars($correct_answer) . "</p>";
            echo "</div>";
        }
        ?>
        <div style="margin-top: 20px;">
    <a href="studentdb.php">
        <button>Go Back to Courses</button>
    </a>
</div>


    </div>
</body>
</html>
