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

// Check if the form is submitted and the data is successfully inserted
$successMessage = "";
if (isset($_POST["submit"])) {
    $id_category = $_POST["id_category"];
    $name_category = $_POST["name_category"];
    $custom = $_POST["custom"];
    $id_user = $_POST["id_user"];
    $id_expenss = $_POST["id_expenss"];

    if ($id_category != "" && $name_category != "" && $custom != "" && $id_user != "" && $id_expenss != "") {
        // Use prepared statements to prevent SQL injection
        $stmt = $con->prepare("INSERT INTO category (id_category, name_category, custom, id_user, id_expenss) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $id_category, $name_category, $custom, $id_user, $id_expenss);
        if ($stmt->execute()) {
            $successMessage = "Category added successfully.";
            $stmt->close();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="add_category.css">
</head>
<body>
    <div class="center">
        <div class="container">
            <h1>Add Category</h1>
            <form action="" method="POST">

                <div class="input-box">
                    <label for="id_category">ID Category</label>
                    <input type="number" placeholder="Enter the value" name="id_category">
                </div>

                <div class="input-box">
                    <label for="name_category">Category Name</label>
                    <input type="text" placeholder="Enter the value" name="name_category">
                </div>

                <div class="input-box">
                    <label for="custom">Custom</label>
                    <input type="text" placeholder="Enter custom value" name="custom">
                </div>

                <div class="input-box">
                    <label for="id_user">USER ID</label>
                    <input type="text" placeholder="USER ID" name="id_user">
                </div>

                <div class="input-box">
                    <label for="id_expenss">Expenses ID</label>
                    <input type="text" placeholder="Expenses ID" name="id_expenss">
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
            }
            ?>
        </div>
    </div>
</body>
</html>
