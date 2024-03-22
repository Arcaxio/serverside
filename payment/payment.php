<?php
// Include database connection file
include 'db_connect.php';
session_start();

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract data from the POST request
    $buyerName = $_POST['buyer_name'];
    $buyerEmail = $_POST['buyer_email'];
    $buyerAddress = $_POST['buyer_address'];
    $buyerPhone = $_POST['buyer_phone'];
    $paymentMethod = $_POST['payment_method'];

    // Example: You may also want to validate the input data here

    // Insert payment record into the database
    try {
        $stmt = $conn->prepare("INSERT INTO payment (order_id, payment_datetime, total_payment_amount, payment_method) VALUES (:order_id, NOW(), :total_payment_amount, :payment_method)");
        // Assuming you have the order_id and total_payment_amount available
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':total_payment_amount', $total_payment_amount);
        $stmt->bindParam(':payment_method', $paymentMethod);
        $stmt->execute();

        // Fetch the inserted payment ID
        $payment_id = $conn->lastInsertId();

        // Send a JSON response indicating success and the payment ID
        echo json_encode(array("success" => true, "payment_id" => $payment_id));
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(array("success" => false, "error" => $e->getMessage()));
    }
} else {
    // If the request method is not POST, handle the error
    echo json_encode(array("success" => false, "error" => "Invalid request method"));
}
?>
