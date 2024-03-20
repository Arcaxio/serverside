<?php
include '../includes/db_connect.php';
session_start();

// Check if form is submitted, get username and password
if (isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve the user record
    $stmt = $conn->prepare("SELECT * FROM staff WHERE username = ?");
    $stmt->execute([$username]);
    $staff = $stmt->fetch();

    // Verify password (remember, you'll have stored a password hash)
    if ($staff && password_verify($password, $staff['password'])) {
        $_SESSION['staff_id'] = $staff['staff_id'];
        $_SESSION['role'] = $staff['role'];
        $_SESSION['username'] = $staff['username'];
        header('Location: products.php'); // Redirect to staff panel
    } else {
        echo "Incorrect username or password";
    }
}
