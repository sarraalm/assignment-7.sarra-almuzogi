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
if (!isset($result)) {
    $sql = "SELECT * FROM expenses";
    $result = $con->query($sql);

    if (!$result) {
        die("Error: " . $con->error);
    }
}

// Check if data was fetched successfully
$expenses = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : array();
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="keywords" content="expenses, sarra, 315">
    <title>The Bill</title>
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
    </style>
</head>
<body>
    <center>
        <h1>The Expenses</h1>

        <div class="button-container">
        <button onclick="document.location='search.php'">Search</button>

            <button onclick="document.location='add_expenses.php'">Add Expense</button>
        </div>

        <?php if (count($expenses) > 0) : ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Comments</th>
                    <th>Payment Type</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($expenses as $expense) : ?>
                    <tr>
                        <td><?php echo $expense['id_expenses']; ?></td>
                        <!-- Correct the date formatting -->
                        <td><?php echo date('Y-m-d', strtotime($expense['date'])); ?></td>
                        <td><?php echo $expense['id_category']; ?></td>
                        <td><?php echo $expense['name_category']; ?></td>
                        <td><?php echo $expense['comments']; ?></td>
                        <td><?php echo $expense['payment_type']; ?></td>
                        <td>
                            <a href="update_expenses.php?id=<?php echo $expense['id_expenses']; ?>">Edit</a> |
                            <a href="delete_expenses.php?id=<?php echo $expense['id_expenses']; ?>" onclick="return confirm('Are you sure you want to delete?')">Delete</a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else : ?>
            <p class="no-expenses">No expenses found.</p>
        <?php endif; ?>

    </center>
</body>
</html>
