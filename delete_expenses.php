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

// Check if the ID parameter is present in the URL
if (isset($_GET["id"])) {
    $expenseId = $_GET["id"];

    // Use prepared statements to prevent SQL injection
    $stmt = $con->prepare("DELETE FROM expenses WHERE id_expenses = ?");
    $stmt->bind_param("i", $expenseId);
    if ($stmt->execute()) {
        $successMessage = "Expense deleted successfully.";
    } else {
        $errorMessage = "Error deleting expense: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="keywords" content="delete expense, sarra, 315">
    <title>Delete Expense</title>
    <link rel="stylesheet" href="delete_expense.css">
</head>
<body>
    <div class="center">
        <div class="container">
            <h1>Delete Expense</h1>
            <div class="success-message">
                <?php
                if (isset($successMessage)) {
                    echo $successMessage;
                } elseif (isset($errorMessage)) {
                    echo $errorMessage;
                } else {
                    echo "Invalid request.";
                }
                ?>
            </div>
            <div class="button-container">
                <a href="expenses.php">Back to Expenses</a>
            </div>
        </div>
    </div>
</body>
</html>
