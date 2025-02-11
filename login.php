<?php
session_start(); // Start a session to store user information across pages
include('db.php');  // Include the database connection file

// Check if the form is submitted using POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $conn->real_escape_string(trim($_POST['password']));

    // Query to check if the username and password match any user in the database
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    // Check if a single matching user record exists
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Set session variables
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Ensure this is 'A' for admins
        $_SESSION['name'] = $user['name']; // Optional: store admin name

        // Redirect based on role
        if ($user['role'] === 'instructor') {
            header("Location: admindb.php");
        } else {
            header("Location: studentdb.php");
        }
        exit();
    } else {
        $error_message = "Invalid username or password.";
    } 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Management System Login</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="login.css"> <!-- Link to the external CSS for styling -->
</head>
<body>
<div class="container">
    <div class="login-container">
        <!-- Display logos -->
        <img src="img/log.png" alt="Logo 1" class="logo">
        <h2>LOGIN</h2> <!-- Login header -->

        <!-- Login form -->
        <form action="login.php" method="POST"> <!-- Form submission to the same page -->
            <!-- Username field -->
            <label for="username">USERNAME:</label>
            <input type="text" id="username" name="username" placeholder="Username">

            <!-- Password field with toggle visibility -->
            <label for="password">PASSWORD:</label>
            <div class="input-container">
                <input type="password" id="password" name="password" placeholder="Password">
                <span id="toggle-password">👁️</span> <!-- Icon for toggling password visibility -->
            </div>

            <!-- Submit button -->
            <button type="submit" class="sign-in">Sign In</button>
        </form>

        <!-- JavaScript to toggle password visibility -->
        <script>
            document.getElementById('toggle-password').addEventListener('click', function () {
                const passwordField = document.getElementById('password'); // Get the password field
                const type = passwordField.type === 'password' ? 'text' : 'password'; // Toggle between 'password' and 'text'
                passwordField.type = type; // Update the input type

                // Toggle the icon (optional)
                this.textContent = type === 'password' ? '👁️' : '🙈'; // Change icon based on visibility
            });
        </script>

        <!-- Display error message if login fails -->
        <?php if (isset($error_message) && !empty($error_message)): ?>
            <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <!-- Signup prompt -->
        <p class="signup-text">
            Don’t have an account? <a href="register.php">Sign up. It’s free!</a> <!-- Link to registration page -->
        </p>
    </div>
</div>
</body>
</html>