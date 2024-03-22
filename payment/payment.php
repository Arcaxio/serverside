<?php
// Include database connection file
include 'db_connect.php';

// Function to insert payment record
function insertPayment($order_id, $total_payment_amount, $payment_datetime, $payment_method) {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO payment (order_id, total_payment_amount, payment_datetime, payment_method) VALUES (:order_id, :total_payment_amount, :payment_datetime, :payment_method)");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':total_payment_amount', $total_payment_amount);
        $stmt->bindParam(':payment_datetime', $payment_datetime);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->execute();
        echo "Payment recorded successfully";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to display payment records
function displayPayments() {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT * FROM payment");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to update payment record
function updatePayment($payment_id, $total_payment_amount, $payment_method) {
    global $conn;
    try {
        $stmt = $conn->prepare("UPDATE payment SET total_payment_amount=:total_payment_amount, payment_method=:payment_method WHERE payment_id=:payment_id");
        $stmt->bindParam(':payment_id', $payment_id);
        $stmt->bindParam(':total_payment_amount', $total_payment_amount);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->execute();
        echo "Payment record updated successfully";
    } catch (PDOException $e) {
        echo "Error updating record: " . $e->getMessage();
    }
}

// Function to delete payment record
function deletePayment($payment_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("DELETE FROM payment WHERE payment_id=:payment_id");
        $stmt->bindParam(':payment_id', $payment_id);
        $stmt->execute();
        echo "Payment record deleted successfully";
    } catch (PDOException $e) {
        echo "Error deleting record: " . $e->getMessage();
    }
}


// Example usage
// insertPayment(1, 50.00, 'Credit Card');
// displayPayment();
// updatePayment(1, 60.00, 'PayPal');
// deletePayment(1);
?>
