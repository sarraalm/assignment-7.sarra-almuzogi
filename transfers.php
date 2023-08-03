
<!DOCTYPE html>
<!-- TRANSFERS 
يقوم هذا النلف بعض جميع التحويلات التي قام بيها الممستخدم -->

<html>
<head>
    <title>Transfer History</title>
    <style>
        body {
            font-family: "Cairo", sans-serif;
            background-color: #f0f0f0;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:hover {
            background-color: #e5e5e5;
        }

        .center {
            text-align: center;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <table>
        <tr>
        <h1>Transfer History</h1>

            <th>User Name</th>
            <th>Category From</th>
            <th>Category To</th>
            <th>Amount</th>
            <th>Comment</th>
            <th>Date</th>
        </tr>

        <?php
        session_start();

        include'header.php';
        require_once 'conn.php';

        $conn =  mysqli_connect($hn, $un, $pw, $db);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch transfer data from the "transfer" table with user's full name and category names
        $sql = "SELECT transfer.id_user, user.full_name, category_from.name_category AS Category_from, category_to.name_category AS Category_to, transfer.Custom, transfer.Comment, transfer.Date
                FROM transfer
                JOIN user ON transfer.id_user = user.id_user
                JOIN category AS category_from ON transfer.Category_from = category_from.id_category
                JOIN category AS category_to ON transfer.Category_to = category_to.id_category";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['full_name'] . "</td>";
                echo "<td>" . $row['Category_from'] . "</td>";
                echo "<td>" . $row['Category_to'] . "</td>";
                echo "<td>" . $row['Custom'] . "</td>";
                echo "<td>" . $row['Comment'] . "</td>";
                echo "<td>" . $row['Date'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' class='no-data'>No transfer data found.</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </table>
</body>
</html>
