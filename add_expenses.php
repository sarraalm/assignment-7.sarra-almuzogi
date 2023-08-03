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

// Initialize the successMessage variable
$successMessage = "";

try {
    // Check if the form is submitted and the data is successfully inserted
    if (isset($_POST["submit"])) {
        $date = $_POST["date"];
        $id_category = $_POST["id_category"];
        $name_category = $_POST["name_category"];
        $comments = $_POST["comment"];

        // Check if the "payment_type" index exists in $_POST and is not empty
        if (isset($_POST["payment_type"]) && $_POST["payment_type"] !== "") {
            $payment_type = $_POST["payment_type"];

            // Use prepared statements to prevent SQL injection
            $stmt = $con->prepare("INSERT INTO expenses (date, id_category, name_category, comments, payment_type) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $date, $id_category, $name_category, $comments, $payment_type);

            if ($stmt->execute()) {
                $successMessage = "Expense added successfully.";
                $stmt->close();
            } else {
                throw new Exception("Error executing the database query.");
            }
        } else {
            throw new Exception("Payment type is not selected.");
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta name="keywords" content="add expenses, sarra, 315">
    <title>Add Expense</title>
    <link rel="stylesheet" href="add_expenses.css">
</head>
<body>
    <div class="center">
        <div class="container">
            <h1>Add Expense</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="input-box">
                    <label for="date">Date</label>
                    <input type="datetime-local" placeholder="Enter date" name="date">
                </div>

                <div class="input-box">
                    <label for="id_category">ID Category</label>
                    <input type="number" placeholder="Enter the value" name="id_category">
                </div>

                <div class="input-box">
                    <label for="name_category">Category Name</label>
                    <input type="text" placeholder="Enter the value" name="name_category">
                </div>

                <div class="input-box">
                    <label for="comment">Comments</label>
                    <input type="text" placeholder="Enter the comment" name="comment">
                </div>

                <div class="payment-type">
                    <label for="payment_type">Payment Type</label>
                    <label for="bank_card">Bank Card</label>
                    <input type="radio" name="payment_type" id="bank_card" value="Bank Card">
                    <label for="monetary">Monetary</label>
                    <input type="radio" name="payment_type" id="monetary" value="Monetary">
                    <label for="check">Check</label>
                    <input type="radio" name="payment_type" id="check" value="Check">
                </div>

                <div class="button-container">
                    <input type="submit" name="submit" value="Add">
                </div>
            </form>
        </div>
        <!-- Success message will be shown here -->
        <div class="success-message">
            <?php
            if (!empty($successMessage)) {
                echo $successMessage;
// Redirect to expenses.php after 3 seconds
sleep(3); // Wait for 3 seconds
header("Location: expenses.php");
exit;}
            ?>
        </div>

    </div>
</body>
</html>