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

// Check if the form is submitted and the data is successfully updated
$updateMessage = "";
if (isset($_POST["update"])) {
    $expenseId = $_POST["id_expenses"];
    $date = $_POST["date"];
    $id_category = $_POST["id_category"];
    $name_category = $_POST["name_category"];
    $comments = $_POST["comments"];
    $payment_type = $_POST["payment_type"];

    // Use prepared statements to prevent SQL injection
    $stmt = $con->prepare("UPDATE expenses SET date = ?, id_category = ?, name_category = ?, comments = ?, payment_type = ? WHERE id_expenses = ?");
    $stmt->bind_param("sssssi", $date, $id_category, $name_category, $comments, $payment_type, $expenseId);
    if ($stmt->execute()) {
        $updateMessage = "Expense updated successfully.";
        $stmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch the expense data for editing
$expense = array(); // Initialize $expense as an empty array
if (isset($_GET["id"])) {
    $expenseId = $_GET["id"];
    $stmt = $con->prepare("SELECT * FROM expenses WHERE id_expenses = ?");
    $stmt->bind_param("i", $expenseId);
    $stmt->execute();
    $result = $stmt->get_result();
    $expense = $result->fetch_assoc();
    $stmt->close();

    // Convert the date from the database to the correct format (YYYY-MM-DDTHH:MM)
    $expense['date'] = date("Y-m-d\TH:i", strtotime($expense['date']));
} else {
    // Redirect to expenses.php if no id is selected
    header("Location: expenses.php");
    exit;
}

// ...

?>


<!DOCTYPE html>
<html>
<head>
    <meta name="keywords" content="edit expenses, sarra, 315">
    <title>Edit Expense</title>
    <link rel="stylesheet" href="update_expenses.css">
</head>
<body>
    <div class="center">
        <div class="container">
            <h1>Edit Expense</h1>
            <form action="" method="POST">
                <div class="input-box">
                    <label for="date">Date</label>
                    <input type="datetime-local" name="date" value="<?php echo $expense['date']; ?>">
                </div>

                <div class="input-box">
                    <label for="id_category">ID Category</label>
                    <input type="number" name="id_category" value="<?php echo $expense['id_category']; ?>">
                </div>

                <div class="input-box">
                    <label for="name_category">Category Name</label>
                    <input type="text" name="name_category" value="<?php echo $expense['name_category']; ?>">
                </div>

                <div class="input-box">
                    <label for="comments">Comments</label>
                    <input type="text" name="comments" value="<?php echo $expense['comments']; ?>">
                </div>

                <div class="payment-type">
                    <label for="payment_type">Payment Type</label>
                    <label for="bank_card">Bank Card</label>
                    <input type="radio" name="payment_type" value="Bank Card" <?php if ($expense['payment_type'] == 'Bank Card') echo 'checked'; ?>>
                    <label for="monetary">Monetary</label>
                    <input type="radio" name="payment_type" value="Monetary" <?php if ($expense['payment_type'] == 'Monetary') echo 'checked'; ?>>
                    <label for="check">Check</label>
                    <input type="radio" name="payment_type" value="Check" <?php if ($expense['payment_type'] == 'Check') echo 'checked'; ?>>
                </div>

                <div class="button-container">
                    <input type="hidden" name="id_expenses" value="<?php echo $expense['id_expenses']; ?>">
                    <input type="submit" name="update" value="Update">
                </div>
            </form>
        </div>
        <!-- Update message will be shown here -->
        <div class="update-message">
            <?php
            if (!empty($updateMessage)) {
                echo $updateMessage;
                // Redirect to expenses.php after 3 seconds
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "expenses.php";
                    }, 3000); // 3 seconds
                </script>';
            
            }
            ?>
        </div>
    </div>
</body>
</html>