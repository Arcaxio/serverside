<?php
session_start();
include 'includes/db_connect.php';

if (isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];


    $update_stmt = $conn->prepare("UPDATE orders SET order_status = 'cancelled' WHERE order_id = ?");
    $update_stmt->bindParam(1, $order_id);

    if ($update_stmt->execute()) {
        // If the query was successful, redirect back to the orders page or display a success message
        header("Location: orders.php"); // Redirect back to the orders page
        exit(); // Stop further execution
    } else {
        // If an error occurred, display an error message
        echo "Failed to cancel the order.";
    }
} else {
    // If the order_id is not set, display an error message
    echo "Order ID is missing.";
}
?>