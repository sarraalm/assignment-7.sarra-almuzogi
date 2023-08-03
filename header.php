<?php
// Check if the session is not already started before starting it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="keywords" content="expenses, sarra, 315">
    <title>Expenses Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        /* Style for the navbar */
        .navbar {
            background-color: #007bff; /* Blue color */
            text-align: center;
            padding: 10px 0;
            position: fixed;
            top: 0;
            width: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Add a subtle shadow */
        }

        /* Style for the navbar links */
        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 0 5px;
            transition: background-color 0.2s ease;
            font-size: 16px;
            display: inline-block;
        }

        /* Style for the navbar links on hover */
        .navbar a:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        /* Add padding to the content to avoid overlapping with the navbar */
        .content {
            padding-top: 60px; /* Adjust this value as needed */
            margin: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <?php
        // Check if the user is logged in
        if (isset($_SESSION['username'])) {
            ?>
            <!-- If logged in, display other links except "Login" -->
            <a href="add_category.php" style="background-color: #0056b3;">Add category</a>
            <a href="reviews.php">Reviews</a>
            <a href="review.php">New Review</a>
            <a href="expenses.php">Expenses</a>
            <a href="transfer.php">Transfer</a>
            <a href="transfers.php">Transfers</a>
            <a href="logout.php">Logout</a>
            <?php
        } else {
            // If not logged in, display "Login" link
            ?>
            <a href="login.php">Login</a>
            <?php
        }
        ?>
    </div>

    <!-- Add some content below the navbar to see the padding effect -->
    <div class="content">
        <?php
        // Check if the user is logged in
        if (isset($_SESSION['username'])) {
            echo '<h1>Welcome, ' . $_SESSION['username'] . '!</h1>';
        }
        ?>
    </div>
</body>
</html>
