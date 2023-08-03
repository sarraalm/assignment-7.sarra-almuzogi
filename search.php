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

<?php
include("conn.php");
include("header.php");

// Initialize search parameters with empty values
$from_date = $to_date = "";
$result = null; // Initialize $result with null

// Check if the search form is submitted with valid dates
if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
    // Retrieve and sanitize the search parameters
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];

    // Validate the date format (you may add more sophisticated validation if needed)
    if (DateTime::createFromFormat('Y-m-d', $from_date) !== false && DateTime::createFromFormat('Y-m-d', $to_date) !== false) {
        // Use DATE() function to extract the date part and compare with the search dates
        $sql = "SELECT * FROM expenses WHERE DATE(date) BETWEEN ? AND ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ss", $from_date, $to_date);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}

// If the search parameters are not provided or invalid, fetch all expenses
if (!$result) {
    $sql = "SELECT * FROM expenses";
    $result = $con->query($sql);

    if (!$result) {
        die("Error: " . $con->error);
    }
}

// Initialize $expenses as an empty array
$expenses = array();

// Check if data was fetched successfully
if ($result->num_rows > 0) {
    $expenses = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        center {
            padding: 20px;
        }

        h1 {
            color: #333;
        }

        .button-container {
            margin-bottom: 20px;
        }

        .button-container form {
            display: inline-block;
        }

        .button-container label,
        .button-container button {
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #f9f9f9;
        }

        .button-container button {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .button-container button:hover {
            background-color: #45a049;
        }

        .no-expenses {
            text-align: center;
            padding: 20px;
            color: #777;
        }

        /* New styles for restyling the page */
        body {
            background-color: #f8f8f8;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .button-container {
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
        }

        input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .button-container button {
            cursor: pointer;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #0056b3;
        }

        table {
            margin-top: 30px;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .no-expenses {
            padding: 20px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <center>
            <h1>Search</h1>

            <div class="button-container">
                <form action="" method="get">
                    <label for="from_date">From Date:</label>
                    <input type="date" id="from_date" name="from_date" value="<?php echo $from_date; ?>">
                    <label for="to_date">To Date:</label>
                    <input type="date" id="to_date" name="to_date" value="<?php echo $to_date; ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <?php if (count($expenses) > 0) : ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Category Name</th>
                    </tr>
                    <?php foreach ($expenses as $expense) : ?>
                        <tr>
                            <!-- Correct the date formatting -->
                            <td><?php echo date('Y-m-d', strtotime($expense['date'])); ?></td>
                            <td><?php echo $expense['name_category']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else : ?>
                <p class="no-expenses">No expenses found.</p>
            <?php endif; ?>

        </center>
    </div>
</body>
</html>
