
<?php
// Step 1: Include necessary files and start the session
include 'includes/db_connect.php';
session_start();

// Retrieve payment method from checkout.php if available
if (isset($_POST['payment_method'])) {
    $paymentMethod = $_POST['payment_method'];
} else {
    // Retrieve payment method from the database if available
    // Replace this with your database retrieval logic
    $paymentMethod = ''; // Fetch payment method from the database here
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payment Gateway</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .loader {
            display: none;
        }

        .payment-success {
            display: none;
        }

        #paymentMethod {
            font-size: 20px;
            text-align: center;
        }

        /* Center the content */
        .center-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div class="center-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                <div class="text-center loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                        <p class="mt-3">Processing <strong id="loadingPaymentMethod"></strong> payment...</p>
                    </div>
                    <div class="text-center payment-success">
                        <h2>Payment Complete</h2>
                        <p class="mt-3">Your <strong id="successPaymentMethod"></strong> payment was successful!</p>
                        <button class="btn btn-primary" onclick="redirectToIndex()">Continue</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Retrieve payment method passed from checkout.php
        var paymentMethod = "<?php echo isset($_GET['payment_method']) ? htmlspecialchars($_GET['payment_method']) : ''; ?>";
        // Set payment method text for loading message
        $('#loadingPaymentMethod').text(paymentMethod);
        // Set payment method text for success message
        $('#successPaymentMethod').text(paymentMethod);

        // Display loading spinner
        $('.loader').show();

        // Simulate payment completion after 2 seconds
        setTimeout(function() {
            $('.loader').hide();
            $('.payment-success').show();
        }, 2000);

        function redirectToIndex() {
            window.location.href = 'orders.php?success=true"';
        }
    </script>
</body>

</html>
