<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Services</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <style>
        /* CSS Code */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }

        header .logo {
            display: flex;
            align-items: center;
            color:#fff;
            font-weight: bold;
            font-size: 36px;
            text-shadow: 4px 4px 5px rgba(0, 0, 0, 0.4);
        }

        header .logo img {
            height: 80px; /* Adjust the logo size */
            margin-right: 10px;
        }

        .navbar {
            background-color: #4CAF50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            margin: 0;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar ul li {
            margin: 0 1rem;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .navbar ul li a:hover {
            color: #194ba1; 
        }

        .service {
            background: white;
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 800px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .service h2 {
            color: #4CAF50;
        }

        .footer {
            text-align: center;
            background-color: #333;
            color: white;
            padding: 1rem 0;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <header class="navbar">
    <div class="logo">
            <img src="img/log.png" alt="LMS Logo">LMS Services
        </div>
        <nav>
            <ul>
                <li><a href="service.php">Services</a></li>
                <li><a href="landingpage.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
            </ul>
        </nav>
    </header>
    
    <section id="user-management" class="service">
        <h2>User Management</h2>
        <p>Manage users, including registration, login, and roles (students, instructors, administrators).</p>
        <?php
            // Example PHP code for user management
            echo "<p><strong>Example Feature:</strong> User Registration and Login.</p>";
        ?>
    </section>
    
    <section id="course-management" class="service">
        <h2>Course Management</h2>
        <p>Create and organize courses, upload content, and set deadlines.</p>
        <?php
            // Example PHP for displaying available courses
            $courses = ["Web Development", "Data Science", "Graphic Design"];
            echo "<ul>";
            foreach ($courses as $course) {
                echo "<li>$course</li>";
            }
            echo "</ul>";
        ?>
    </section>
    
    <section id="content-delivery" class="service">
        <h2>Content Delivery</h2>
        <p>Provide access to course materials like videos, documents, and interactive quizzes.</p>
        <?php
            // Example PHP for content count
            $content_count = 12;
            echo "<p>Total available content: $content_count items.</p>";
        ?>
    </section>
    
    <section id="communication-tools" class="service">
        <h2>Communication Tools</h2>
        <p>Facilitate discussions and interactions through forums, chat, and messaging.</p>
        <?php
            // Example PHP for messaging service
            echo "<p>Message service status: <strong>Active</strong></p>";
        ?>
    </section>
    
    <footer class="footer">
        <p>&copy; 2025 LMS Services. All Rights Reserved.</p>
    </footer>
</body>
</html>
