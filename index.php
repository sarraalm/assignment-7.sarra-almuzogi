<?php
session_start();
require_once 'conn.php'; // Include the file with your database connection code

// Validate input data
if (isset($_POST['submit'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = validate_input($_POST['username']);
        $password = validate_input($_POST['password']);

        // Prepare and bind the SQL statement to prevent SQL injection attacks
        $stmt = $con->prepare("SELECT * FROM user WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            // User exists, verify the password
            $row = $result->fetch_assoc();
            // For a production environment, use password_hash() to hash the passwords and store them in the database.
            // For now, we are comparing plain text passwords for demonstration purposes.
            if ($password == $row['password']) {
                // Password is correct, set session data for the authenticated user
                $_SESSION['username'] = $row['username'];
                header('Location: expenses.php'); // Replace 'dashboard.php' with the page you want to redirect to after login
                exit();
            } else {
                // Invalid credentials, show error message
                showError("Invalid Credentials!");
            }
        } else {
            // Invalid credentials, show error message
            showError("Invalid Credentials!");
        }

        $stmt->close();
    } else {
        // Form data not submitted, redirect to login page
        header('Location: login.php');
        exit();
    }
}

// Function to validate user input and prevent common attacks
function validate_input($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $input;
}

// Function to show error message
function showError($message)
{
    echo '<script>alert("' . $message . '")</script>';
    echo '<script>window.location.href="login.php"</script>';
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="keywords" content="login, sarra, 315">
    <title>Login</title>
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

        form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 400px;
            width: 100%;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .input-box {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .button-container {
            text-align: center;
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
    <div class="center">
        <form method="post">
            <h1>Login</h1>
            <div class="input-box">
                <label for="username">Username:</label>
                <input type="text" placeholder="Enter username" name="username" required>
            </div>
            <div class="input-box">
                <label for="password">Password:</label>
                <input type="password" placeholder="Enter the password" name="password" required>
            </div>
            <div class="button-container">
                <button type="submit" name="submit">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
