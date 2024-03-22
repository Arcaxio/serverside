<?php
include 'includes/db_connect.php'; // Include your database connection script

if (isset ($_SESSION['username'])) {
    // User is already logged in, redirect to index.php
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <div class="container">
        <h1>Login</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Prepared statement for security
            $stmt = $conn->prepare("SELECT customer_id, username, password FROM customers WHERE username = ?");
            $stmt->bindParam(1, $username);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();

                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Start a session
                    $secondsInWeek = 60 * 60 * 24 * 7;
                    // Set cookie lifetime and start the session
                    session_set_cookie_params($secondsInWeek);
                    session_start();
                    $_SESSION['username'] = $user['username'];
                    header("Location: index.php");
                    exit;
                } else {
                    echo "Incorrect username or password.";
                }
            } else {
                echo "Incorrect username or password.";
            }
        }
        ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" name="submit" value="Login">
        </form>
    </div>
</body>

</html>