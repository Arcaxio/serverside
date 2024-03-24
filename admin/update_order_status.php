<?php
session_start();
include '../includes/db_connect.php';

// Authentication/Authorization (ensure staff is logged in)

if (isset ($_POST['order_id']) && isset ($_POST['status'])) {
    $orderId = (int) $_POST['order_id'];
    $newStatus = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bindParam(1, $newStatus);
    $stmt->bindParam(2, $orderId);

    if ($stmt->execute()) {
        echo "Order status updated successfully!";
    } else {
        echo "Error updating order status.";
    }
}