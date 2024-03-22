<?php
// Include database connection file
include 'db_connect.php';

// Function to insert payment record
function insertPayment($cart_id, $payment_amount, $payment_method) {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO payments (cart_id, payment_amount, payment_method) VALUES (:cart_id, :payment_amount, :payment_method)");
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':payment_amount', $payment_amount);
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
        $stmt = $conn->prepare("SELECT * FROM payments");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            foreach ($result as $row) {
                echo "Payment ID: " . $row["payment_id"]. " - Cart ID: " . $row["cart_id"]. " - Amount: " . $row["payment_amount"]. " - Method: " . $row["payment_method"]. "<br>";
            }
        } else {
            echo "No payments recorded";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to update payment record
function updatePayment($payment_id, $payment_amount, $payment_method) {
    global $conn;
    try {
        $stmt = $conn->prepare("UPDATE payments SET payment_amount=:payment_amount, payment_method=:payment_method WHERE payment_id=:payment_id");
        $stmt->bindParam(':payment_id', $payment_id);
        $stmt->bindParam(':payment_amount', $payment_amount);
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
        $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id=:payment_id");
        $stmt->bindParam(':payment_id', $payment_id);
        $stmt->execute();
        echo "Payment record deleted successfully";
    } catch (PDOException $e) {
        echo "Error deleting record: " . $e->getMessage();
    }
}

// Example usage
// insertPayment(1, 50.00, 'Credit Card');
// displayPayments();
// updatePayment(1, 60.00, 'PayPal');
// deletePayment(1);
?>
