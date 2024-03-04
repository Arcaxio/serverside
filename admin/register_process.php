<?php
include '../includes/db_connect.php';
session_start();

// Protect the page 
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php'); // Redirect if not authorized
}

if (isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO staff (username, password, role) VALUES (?, ?, 'manager')");
    if ($stmt->execute([$username, $hashed_password])) {
        echo "Manager registered successfully"; // Replace with a redirect or success message
    } else {
        echo "Error registering manager";
    }
}
