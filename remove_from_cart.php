<?php
session_start();
include 'includes/db_connect.php';

if (isset($_GET['cart_id'])) {
    $cartId = (int)$_GET['cart_id'];

    // Ensure the user is authorized to remove this item
    // ... Add logic to verify item belongs to the current user

    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
    $stmt->bindParam(1, $cartId);
    if ($stmt->execute()) {
        header("Location: cart.php?success=1"); // Redirect with success
    } else {
        header("Location: cart.php?error=1"); // Redirect with error
    }
}
?>