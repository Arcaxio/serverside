<?php
include 'includes/db_connect.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>
</head>

<body>
    <div class="container">
        <h1>Registration Form</h1>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize user input (important for security)
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            // Validate input to prevent obvious errors (add more validation if needed)
            if (empty ($username) || empty ($email) || empty ($password)) {
                echo "All fields are required.";
            } else {
                // Prepare a SQL statement
                $stmt = $conn->prepare("INSERT INTO customers (username, email, password) VALUES (?, ?, ?)");
                // Hash the password before storing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(1, $username);
                $stmt->bindParam(2, $email);
                $stmt->bindParam(3, $hashed_password);

                if ($stmt->execute()) {
                    // Start a session
                    $secondsInWeek = 60 * 60 * 24 * 7;
                    // Set cookie lifetime and start the session
                    session_set_cookie_params($secondsInWeek);
                    session_start();

                    // Set session variable to indicate logged-in state 
                    $_SESSION['username'] = $username;

                    // Redirect to index.php
                    header("Location: index.php");
                    exit;
                } else {
                    echo "Error during registration. Please try again.";
                }
            }
        }
        ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" name="submit" value="Register">
        </form>
    </div>
</body>

</html>