<!-- REVIEWS
      هذا الملف خاص بعرض التقيمات المستخدمين 
-->
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    // Redirect to the login page
    header('Location: index.php');
    exit();
}

require_once 'conn.php';
include 'header.php';
// Create connection
$conn =  mysqli_connect($hn, $un, $pw, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the review table and join with the users table to get the full_name
$sql = "SELECT r.id_review, r.evaluation, r.comment, u.full_name
        FROM review r
        INNER JOIN user u ON r.id_user = u.id_user";

$result = $conn->query($sql);

// Store the data in an array
$review_data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $review_data[] = $row;
    }
}

// Calculate review percentage
$total_reviews = count($review_data);
$total_evaluation = 0;

foreach ($review_data as $row) {
    $total_evaluation += intval($row['evaluation']);
}

$review_percentage = ($total_reviews > 0) ? round($total_evaluation / ($total_reviews * 5) * 100) : 0;

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Review Data</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .review-percentage {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Review Data</h1>
        <table>
            <tr>
                <th>Review ID</th>
                <th>Evaluation</th>
                <th>Comment</th>
                <th>User</th>
            </tr>
            <?php
            if ($total_reviews > 0) {
                foreach ($review_data as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['id_review'] . "</td>";
                    echo "<td>" . $row['evaluation'] . "</td>";
                    echo "<td>" . $row['comment'] . "</td>";
                    echo "<td>" . $row['full_name'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No reviews found.</td></tr>";
            }
            ?>
        </table>

        <div class="review-percentage">
            <?php
            if ($total_reviews > 0) {
                echo "Review Percentage: $review_percentage%";
            } else {
                echo "No reviews found.";
            }
            ?>
        </div>
    </div>
</body>
</html>
