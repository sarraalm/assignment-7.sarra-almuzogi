<!-- REVIEW
يقوم هذا الملف باظهار صفحة التقيم للمستخدم ويتم تعبيئتها من قبل المستخدم بادخال التقيم من 0 الي 5 
وادخال اي تعليق خاص بيه -->

<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
   
    header('Location: index.php');
    exit();
}

require_once 'conn.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data ($_POST)
    $evaluation = $_POST['evaluation'];
    $comment = $_POST['comment'];

    if ($evaluation < 1 || $evaluation > 5) {
        $error_message = "Invalid evaluation value. Please provide a value between 1 and 5.";
    } else {

        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];

            // Prepare a statement to retrieve the id_user from the user table based on the username
            $query = "SELECT id_user FROM user WHERE username = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $iduser = $row['id_user'];

                // Insert the review 
                $query = "INSERT INTO review (id_user, evaluation, comment) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "iis", $iduser, $evaluation, $comment);
                $insert_result = mysqli_stmt_execute($stmt);

                if ($insert_result) {
                    $success_message = "Review submitted successfully!";
                   
                    header_remove();
                } else {
                    $error_message = "Error: " . mysqli_error($con);
                }
            } 
        } 
    }
}

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Form</title>
    <link href="https://fonts.googleapis.com/css?family=Cairo&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Cairo", sans-serif;
            direction: ltr;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 5px;
        }

        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Submit a Review</h2>

    <?php
    if (isset($error_message)) {
        echo '<p style="color: red;">' . $error_message . '</p>';
    }

    if (isset($success_message)) {
        echo '<p style="color: green;">' . $success_message . '</p>';
    }
    ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="evaluation">Evaluation:</label>
        <input type="number" id="evaluation" name="evaluation" min="1" max="5" required><br><br>

        <label for="comment">Comment:</label>
        <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br><br>

        <input type="submit" value="Submit">
    </form>
</div>
</body>
</html>
