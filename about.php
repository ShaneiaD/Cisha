<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/log.png">
    <title>LMS About Us</title>
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

        .about-section {
            background: white;
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 800px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .about-section h2 {
            color: #4CAF50;
            margin-bottom: 1rem;
        }

        .about-section p {
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        .about-section .mission, .about-section .vision {
            background-color: #f9f9f9;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1.5rem;
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
            <img src="img/log.png" alt="LMS Logo">LMS Platform
        </div>
        <nav>
            <ul>
                <li><a href="service.php">Services</a></li>
                <li><a href="landingpage.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- About Us Section -->
    <section class="about-section" id="about">
        <h2>About Us</h2>
        <p>
            Welcome to <strong>LMS Platform</strong>, your ultimate solution for online learning and education management. 
            Our mission is to empower learners and educators by providing innovative tools and an engaging platform 
            to achieve their goals in the world of education.
        </p>
        <div class="mission">
            <h3>Our Mission</h3>
            <p>
                To create a user-friendly, accessible, and robust platform that enhances the learning experience for students and 
                simplifies the teaching process for educators worldwide.
            </p>
        </div>
        <div class="vision">
            <h3>Our Vision</h3>
            <p>
                To become a leading global platform for education, bridging gaps in accessibility and making learning a seamless 
                and enjoyable journey for everyone.
            </p>
        </div>
        <?php
        // Example dynamic PHP for achievements
        $years_of_service = 5;
        $users = 10000;
        $courses = 150;

        echo "<p>With over $years_of_service years of service, we have successfully supported more than <strong>$users users</strong> and provided access to <strong>$courses courses</strong>.</p>";
        ?>
    </section>
    
    <footer class="footer">
        <p>&copy; 2025 LMS Services. All Rights Reserved.</p>
    </footer>
</body>
</html>
