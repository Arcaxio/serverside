<?php
session_start(); // Start the session

include 'includes/db_connect.php';

if (isset ($_POST['product_id']) && isset ($_POST['quantity'])) {
    $productId = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];

    // If the user is logged in, get their user_id
    if (isset ($_SESSION['username'])) {
        $username = $_SESSION['username'];

        // Fetch customer_id 
        $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE username = ?");
        $stmt->bindParam(1, $username);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch();
            $userId = $row['customer_id'];
        } else {
            // Handle the case where a customer is not found with this username 
            // (this might indicate an error or data inconsistency)
        }
    } else {
        $userId = null; // Handle guest users (optional)
    }

    // 1. Check if the product is already in the cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE product_id = ? AND user_id = ?");
    $stmt->bindParam(1, $productId);
    $stmt->bindParam(2, $userId);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Product exists, update quantity
        $cartRow = $stmt->fetch();
        $newQuantity = $cartRow['quantity'] + $quantity;

        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE product_id = ? AND user_id = ?");
        $stmt->bindParam(1, $newQuantity);
        $stmt->bindParam(2, $productId);
        $stmt->bindParam(3, $userId);
    } else {
        // Product not in cart, insert new row
        $stmt = $conn->prepare("INSERT INTO cart (product_id, user_id, quantity) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $productId);
        $stmt->bindParam(2, $userId);
        $stmt->bindParam(3, $quantity);
    }

    if ($stmt->execute()) {
        // Success! 
        header("Location: product_details.php?id=" . $productId . "&success=1"); // Redirect with success indicator
    } else {
        header("Location: product_details.php?id=" . $productId . "&error=1"); // Redirect with error indicator
    }
} else {
    // Handle missing product_id or quantity
}
