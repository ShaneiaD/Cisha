<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Do not hash the password
    $role = $_POST['role'];
    $email = $_POST['email'];

    // Validate role
    if (!in_array($role, ['student', 'instructor'])) {
        die("Invalid role selected.");
    }

    // Prepare and execute SQL query
    $stmt = $conn->prepare("INSERT INTO users (name, username, password, role, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $username, $password, $role, $email);

    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create an Account</title>
  <link rel="icon" type="image/png" href="img/log.png">
  <link rel="stylesheet" href="register.css"> <!-- Link to external CSS for styling -->
</head>
<body>

  <div class="register-container">
      <!-- Display logos at the top -->
      <img src="img/log.png" alt="Logo 1" class="logo">

      <!-- Registration form -->
      <form class="account-form" method="POST"> <!-- Use POST method for form submission -->
          <h1>Create an Account</h1> <!-- Form heading -->

          <!-- Input field for full name -->
          <h5>FULL NAME<input type="text" name="name" placeholder="Full Name" required></h5>

          <!-- Input field for username -->
          <h5>USERNAME<input type="text" name="username" placeholder="Username" required></h5>

          <!-- Input field for email -->
          <h5>EMAIL<input type="email" name="email" placeholder="Email" required></h5>

          <!-- Input field for password -->
          <h5>PASSWORD<input type="password" id="password" name="password" placeholder="Password" required></h5>

          <!-- Selection field for role -->
          <h5>ROLE</h5>
          <div class="input-container">
              <select id="role" name="role" class="form-input">
                  <option value="instructor">Instructor</option>
                  <option value="student">Student</option>
              </select>
          </div>
        
          <!-- Submit button -->
          <button type="submit" class="create-account">Create Account</button>

          <!-- Link to login page if the user already has an account -->
          <p>Already have an account? <a href="login.php">Log in</a></p>
          
          <!-- Button to navigate back to the homepage -->
          <button class="back-button"><a href="landingpage.php">← Back to Home</a></button>
      </form>

  </div>
</body>
</html>
