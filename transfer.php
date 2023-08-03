<!-- TRANSFER 
يقوم هذا الملف بالتحويلات من فئة الي فئة اخري  بشرط ان  تكون القيمة المحولة
 ليست اكبر من القيمة المخصصة وليست اقل من الصفر والا ستظهر رسالة خطأ-->

<?php
session_start();

require_once 'conn.php';
include 'header.php';
// Create connection
// Function to fetch categories from the "category" table
function fetchCategories($conn) {
    $sql = "SELECT * FROM category";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    return array();
}

// Establishing connection to the database
$conn =  mysqli_connect($hn, $un, $pw, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from the "users" table using the current session's username
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $sql = "SELECT id_user FROM user WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['id_user'];
    } else {
        die("User not found.");
    }
} else {
    die("User not logged in.");
}

// Process the form data if the form is submitted
if (isset($_POST['category_from'], $_POST['category_to'], $_POST['custom'], $_POST['comment'])) {
    $categoryIdFrom = $_POST['category_from'];
    $categoryIdTo = $_POST['category_to'];
    $customMessage = $_POST['custom'];
    $comment = $_POST['comment'];

    // Check if the custom value is greater or less than the original value
    $sql = "SELECT Custom FROM category WHERE id_category = '$categoryIdFrom'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $originalCustomFrom = $row['Custom'];
        if ($customMessage > $originalCustomFrom || $customMessage < 0) {
            die("Invalid transfer amount. The value should be between 0 and the original custom value.");
        }
    }

    // Update the original custom value in the "category" table for "Category From"
    $sql = "UPDATE category SET Custom = Custom - '$customMessage' WHERE id_category = '$categoryIdFrom'";
    if ($conn->query($sql) !== TRUE) {
        echo "Error updating original custom value for 'Category From': " . $conn->error;
    }

    // Update the custom value in the "category" table for "Category To"
    $sql = "UPDATE category SET Custom = Custom + '$customMessage' WHERE id_category = '$categoryIdTo'";
    if ($conn->query($sql) !== TRUE) {
        echo "Error updating custom value for 'Category To': " . $conn->error;
    }

    // Insert transfer data into the "transfer" table
    $date = date("Y-m-d"); // Current date

    // Prepare the comment for insertion, make sure to escape any special characters to prevent SQL injection
    $escapedComment = $conn->real_escape_string($comment);

    // Prepare the SQL statement
    $sql = "INSERT INTO transfer (id_user, Id_category, Category_from, Category_to, Custom, Comment, Date)
            VALUES ('$userId', '$categoryIdFrom', '$categoryIdFrom', '$categoryIdTo', '$customMessage', '$escapedComment', '$date')";

    // Execute the SQL statement
    if ($conn->query($sql) === TRUE) {
        echo "Transfer successfully sent.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch categories from the "category" table
$categories = fetchCategories($conn);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transfer Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 500px;
            margin: 0 auto;
        }
        h2 {
            margin-top: 20px;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        select, input[type="text"], input[type="submit"] {
            margin-bottom: 10px;
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Transfer Custom from Category</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <label for="category_from">From Category:</label>
        <select name="category_from" id="category_from">
            <!-- Populate this dropdown with categories from the "category" table -->
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category["id_category"]; ?>"><?php echo $category["name_category"]; ?></option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="category_to">To Category:</label>
        <select name="category_to" id="category_to">
            <!-- Populate this dropdown with categories from the "category" table -->
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category["id_category"]; ?>"><?php echo $category["name_category"]; ?></option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="custom">Custom:</label>
        <input type="text" name="custom" id="custom">
        <br>
        <label for="comment">Comment:</label>
<input type="text" name="comment" id="comment">
<br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
