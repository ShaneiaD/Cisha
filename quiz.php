<?php
session_start();
include('db.php');

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$name = $_SESSION['name'] ?? 'Instructor';
$instructor_id = $_SESSION['user_id'];
$first_letter = strtoupper(substr($name, 0, 1));


// Check if course_id is passed through URL, if not check session
if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
    $_SESSION['course_id'] = $_GET['course_id']; // Store in session if it's from URL
}

// Now fetch course_id from session if set, otherwise default to null
$course_id = $_SESSION['course_id'] ?? null;

// If course_id is still not set, show error
if (!$course_id) {
    echo "<p class='error'>Course ID is not set. Please provide a valid course ID.</p>";
    exit();
}

// Handle form submission for quiz creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quiz_title = isset($_POST["quiz_title"]) ? $conn->real_escape_string($_POST["quiz_title"]) : '';
    
    if ($quiz_title !== '') {
        $num_questions = isset($_POST["num_questions"]) ? (int) $_POST["num_questions"] : 0;

        if ($num_questions > 0) {
            $insert_quiz_sql = "INSERT INTO quizzes (quiz_title, course_id) VALUES ('$quiz_title', '$course_id')";
            if ($conn->query($insert_quiz_sql)) {
                $quiz_id = $conn->insert_id;
                for ($i = 0; $i < $num_questions; $i++) {
                    $question_type = $_POST["question_type"][$i];
                    $questionText = $conn->real_escape_string($_POST["question_text"][$i]);
                    $answer = $conn->real_escape_string($_POST["answer"][$i]);
                    $options = isset($_POST["options"][$i]) ? json_encode($_POST["options"][$i]) : null;

                    $sql = "INSERT INTO quizquestions (quiz_id, question_type, question_text, options, answer) 
                            VALUES ('$quiz_id', '$question_type', '$questionText', '$options', '$answer')";

                    if (!$conn->query($sql)) {
                        echo "<p class='error'>Error: " . $conn->error . "</p>";
                    }
                }
                echo "<p class='success'>Quiz created successfully!</p>";
            } else {
                echo "<p class='error'>Error creating quiz: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='error'>Please specify the number of questions.</p>";
        }
    } else {
        echo "<p class='error'>Please provide a quiz title.</p>";
    }
}

// Fetch saved quizzes
$quiz_result = $conn->query("SELECT * FROM quizzes WHERE course_id = $course_id");
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

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input, textarea, select {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        button {
            margin-top: 15px;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background: #218838;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background: #eee;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        .question-container {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
            background: #f9f9f9;
        }

        .mcq_options input {
            margin-bottom: 5px;
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
    <a href="adminsite.php">🏠 <span>Site Home</span></a>
</div>

<div class="content">
    <div class="header">
        <h1>NRGLMS: Dashboard</h1>
    </div>

    <div class="container">
        <h2>Create a Quiz</h2>

        <form method="POST" action="">
    <label>Quiz Title:</label>
    <input type="text" name="quiz_title" id="quiz_title" required>

    <label>Number of Questions:</label>
    <input type="number" id="num_questions" name="num_questions" min="1" value="1">

    <button type="button" onclick="generateQuestions()">Set Questions</button>

    <input type="hidden" name="num_questions" id="hidden_num_questions" value="1">
    <div id="questions_container">
        <!-- Questions will be generated here -->
    </div>

    <button type="submit">Create Quiz</button>
</form>


        <h2>Saved Quizzes</h2>
        <ul>
            <?php
           while ($quiz = $quiz_result->fetch_assoc()) {
               $quiz_id = $quiz["quiz_id"];
               $quiz_title = htmlspecialchars($quiz["quiz_title"]);
               echo "<li><a href='quiz_details.php?quiz_id=$quiz_id'>$quiz_title</a></li>";
           }
           
            ?>
        </ul>
    </div>

    <script>
        function generateQuestions() {
            let numQuestions = document.getElementById("num_questions").value;
            let container = document.getElementById("questions_container");
            container.innerHTML = ""; // Clear previous questions
            document.getElementById("hidden_num_questions").value = numQuestions;

            for (let i = 0; i < numQuestions; i++) {
                let questionHTML = ` 
                    <div class="question-container">
                        <label>Question Type:</label>
                        <select name="question_type[]" class="question_type" required>
                            <option value="MCQ">Multiple Choice Question</option>
                            <option value="TrueFalse">True/False</option>
                            <option value="Short_Answer">Short Answer</option>
                        </select>

                        <label>Question:</label>
                        <textarea name="question_text[]" required></textarea>

                        <div class="mcq_options">
                            <label>Number of Options for MCQ:</label>
                            <input type="number" name="num_options[${i}]" value="4" min="2" max="10" oninput="updateOptions(${i})">
                            <div class="options_container" id="options_container_${i}">
                                <label>Options:</label>
                                <input type="text" name="options[${i}][]" placeholder="Option 1">
                                <input type="text" name="options[${i}][]" placeholder="Option 2">
                                <input type="text" name="options[${i}][]" placeholder="Option 3">
                                <input type="text" name="options[${i}][]" placeholder="Option 4">
                            </div>
                        </div>

                        <label>Answer:</label>
                        <input type="text" name="answer[]" required>
                    </div>
                `;
                container.innerHTML += questionHTML;
            }

            updateOptionsDisplay();
        }

        function updateOptionsDisplay() {
            let selectElements = document.querySelectorAll(".question_type");
            selectElements.forEach((select, index) => {
                select.addEventListener("change", function () {
                    let optionsContainer = document.querySelectorAll(".mcq_options")[index];
                    let optionsFieldContainer = document.getElementById(`options_container_${index}`);
                    if (this.value === "MCQ") {
                        optionsContainer.style.display = "block";
                        updateOptions(index); // Update the number of options if MCQ is selected
                    } else if (this.value === "TrueFalse") {
                        optionsFieldContainer.innerHTML = `
                            <label>Options:</label>
                            <input type="text" name="options[${index}][]" placeholder="True" required>
                            <input type="text" name="options[${index}][]" placeholder="False" required>
                        `;
                        optionsContainer.style.display = "block";
                    } else {
                        optionsContainer.style.display = "none";
                    }
                });
            });
        }

        function updateOptions(index) {
            let numOptions = document.querySelector(`input[name="num_options[${index}]"]`).value;
            let optionsContainer = document.getElementById(`options_container_${index}`);
            optionsContainer.innerHTML = '';
            for (let i = 0; i < numOptions; i++) {
                optionsContainer.innerHTML += `<input type="text" name="options[${index}][]" placeholder="Option ${i + 1}" required>`;
            }
        }

        document.getElementById("num_questions").addEventListener("change", generateQuestions);
    </script>
    <script src="dashboard/db.js"></script>
</body>
</html>
