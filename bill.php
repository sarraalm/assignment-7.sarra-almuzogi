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

// Fetch expenses data from the database
$sql = "SELECT * FROM expenss";
$result = $con->query($sql);

if (!$result) {
    die("Error: " . $con->error);
}

// Check if data was fetched successfully
if ($result->num_rows > 0) {
    $expenses = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $expenses = array(); // If no data, initialize the variable as an empty array
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="keywords" content="expenses, sarra, 315">
    <title>The Bill</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        table {
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <br><br>
    <center>
        <h1>The Bill</h1>
        <br><br>
        <br><br>

        <table>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>ID USER</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Phone</th>
                <th>Payment Type</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($expenses as $expense) : ?>
                <tr>
                    <td><?php echo $expense['id_bill']; ?></td>
                    <td><?php echo $expense['date']; ?></td>
                    <td><?php echo $expense['id_user']; ?></td>
                    <td><?php echo $expense['name_proudect']; ?></td>
                    <td><?php echo $expense['price']; ?></td>
                    <td><?php echo $expense['qunitety']; ?></td>
                    <td><?php echo $expense['total']; ?></td>
                    <td><?php echo $expense['phone']; ?></td>
                    <td><?php echo $expense['payment type']; ?></td>
                    <td>
                        <a href="edit_expense.php?id=<?php echo $expense['id_bill']; ?>">Edit</a> |
                        <a href="delete_expense.php?id=<?php echo $expense['id_bill']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="button-container">
            <button onclick="document.location='add expenses.php'">Add Expense</button>
        </div>
    </center>
</body>
</html>
