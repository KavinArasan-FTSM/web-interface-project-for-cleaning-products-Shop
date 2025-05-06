<?php
// Start the session
session_start();

// Session Validation
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cutejoy Cleaning Shop : Home</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
    }
    .welcome-container {
      background-color: #f8f9fa;
      padding: 20px;
      text-align: center;
      border-bottom: 2px solid #e0e0e0;
    }
    .welcome-container h1 {
      font-size: 32px;
      margin: 0;
      color: #343a40;
    }
    .welcome-container h3 {
      font-size: 18px;
      margin: 10px 0 0;
      color: #6c757d;
    }
    .background-container {
      background: url(logo.png) center center no-repeat;
      background-size: cover;
      height: calc(100vh - 80px); /* Adjust height to account for the welcome container */
    }
  </style>
</head>
<body>

  <!-- Include Navigation Bar -->
  <?php include_once 'nav_bar.php'; ?>

  <!-- Welcome Message -->
  <div class="welcome-container">
    <h1>Welcome to Cutejoy Cleaning Shop</h1>
    <h3>
      <?php 
      echo "Hello, " . $_SESSION['username'] . "!"; 
      if ($_SESSION['user_level'] === 'Admin') {
          echo " You are logged in as Admin.";
      } else {
          echo " You are logged in as Normal Staff.";
      }
      ?>
    </h3>
  </div>

  <!-- Background Section -->
  <div class="background-container"></div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

</body>
</html>
