<?php
include 'includes/db_connect.php';
session_start();

$userId = null;
if (isset ($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE username = ?");
    $stmt->bindParam(1, $username);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $userId = $stmt->fetch()['customer_id'];
    }
} else {
    // Handle the case when there is no 'username' in session (Optional)
    $username = null;  // Set a default, or perform other actions if needed
}

// Fetch cart items
$cartItems = [];
$subtotal = 0;

if ($userId !== null) {
    $stmt = $conn->prepare("SELECT cart.cart_id, cart.product_id, cart.quantity, products.product_name, products.price 
                            FROM cart 
                            JOIN products ON cart.product_id = products.product_id 
                            WHERE cart.user_id = ?");
    $stmt->bindParam(1, $userId);
    $stmt->execute();
    $cartItems = $stmt->fetchAll();

    // Calculate subtotal
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}

// Generate random payment ID
$paymentId = mt_rand(100000, 999999);

// Current date and time
$paymentDatetime = date("Y-m-d H:i:s");

// Fetch buyer information from customer table
if ($userId !== null) {
    $stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->bindParam(1, $userId);
    $stmt->execute();
    $customer = $stmt->fetchAll();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_payment'])) {
    $address = trim($_POST['buyer_address']);
    $phone = trim($_POST['buyer_phone_number']);
    $buyerName = trim($_POST['buyer_name']);
    // Calculate total payment amount
    $totalPaymentAmount = $subtotal + 10.00 + $salesTaxes; // Subtotal + Shipping Fee + Sales Taxes
    
    // Validate address, phone, and buyer name
    if (empty($buyerName) || empty($address) || empty($phone)) {
        echo '<script>alert("Please fill in all the required fields.");</script>';
    } elseif (!isValidPhoneNumber($phone)) {
        echo '<script>alert("Please provide a valid phone number.");</script>';
    } else {
        // Proceed with payment confirmation
        // Update customer information
        $stmt = $conn->prepare("UPDATE customers SET name = ?, address = ?, phone_number = ? WHERE customer_id = ?");
        $stmt->bindParam(1, $buyerName);
        $stmt->bindParam(2, $address);
        $stmt->bindParam(3, $phone);
        $stmt->bindParam(4, $userId);

        // Insert payment details into payment table
        $paymentMethod = $_POST['payment_method']; // Assuming payment method is selected in the form
        // Fetch the cart_id associated with the user
        $productId = isset($cartItems[0]['product_id']) ? $cartItems[0]['product_id'] : null;
        if ($productId) {
            // Assuming $subtotal holds the correct subtotal value
            $stmt = $conn->prepare("INSERT INTO payment (payment_id, product_id, user_id, payment_datetime, payment_method, total_payment_amount) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $paymentId);
            $stmt->bindParam(2, $productId);
            $stmt->bindParam(3, $userId);
            $stmt->bindParam(4, $paymentDatetime);
            $stmt->bindParam(5, $paymentMethod);
            $stmt->bindParam(6, $totalPaymentAmount);
            $stmt->execute();

            // Redirect to index.php with success parameter
            header("Location: index.php?success=true");
            exit();
        } else {
            echo "Error: No cart items found for the user.";
        }
    }
}

function isValidPhoneNumber($phone) {
    // Validate phone number format (assuming 11-digit phone number)
    $phonePattern = '/^\d{11}$/'; // Assumes 11-digit phone number
    return preg_match($phonePattern, $phone);
}

// Delete cart items after successful payment
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    // Delete cart items associated with the user
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bindParam(1, $userId);
    if ($stmt->execute()) {
        echo "Cart items deleted successfully."; // Debugging statement
    } else {
        echo "Error deleting cart items: " . $stmt->errorInfo()[2]; // Output any errors
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payment Details</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</head>

<body>
<header class="header sticky-top py-3 bg-black">
        <nav class="container d-flex justify-content-between align-items-center">
            <div class="text-light" onclick="location.href='index.php';" style="cursor: pointer;">
                <i class="bi bi-app-indicator fs-3 me-3"></i>
                <span class="ms-2 fs-3">Boom Inc</span>
            </div>
            <ul class="nav justify-content-end gap-1">
                <li>
                    <span class="nav-link text-light fw-medium">
                        <?php if (isset ($username)) {
                            echo "Welcome, " . $username . "!";
                        } ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="products.php">Products</a>
                </li>
                <?php if (isset ($_SESSION['username'])) { ?>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="orders.php">Order</a>
                    </li>
                    <li class="nav-logging">
                        <a class="nav-link fw-medium" href="customer/logout.php">Logout</a>
                    </li>
                <?php } else { ?>
                    <li class="nav-logging">
                        <a class="nav-link fw-medium" href="customer/login.php">Login</a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <!-- Main content -->
        <div class="bg-white p-4">
            <div class="p-3 border rounded rounded-3">
                <div class="card-body">
                    <!-- Payment Details Section -->
                    <h3 class="card-title">Payment Details</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_id">Payment ID:</label>
                                <input type="text" class="form-control" id="payment_id" value="<?php echo $paymentId; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_datetime">Payment Date & Time:</label>
                                <input type="text" class="form-control" id="payment_datetime" value="<?php echo $paymentDatetime; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <br>

                    <!-- Selected Order Products Section -->
                    <div class="table-responsive">
                        <h5 class="card-title">Selected Order Products</h5>
                        <table class="table">
                            <!-- Table headers -->
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Product Name</th>
                                    <th>Price (RM)</th>
                                    <th>Order Amount</th>
                                    <th>Total Price (RM)</th>
                                </tr>
                            </thead>
                            <!-- Table body for selected products -->
                            <tbody id="selected_products">
                                <?php
                                $totalPrice = 0;
                                foreach ($cartItems as $index => $item) {
                                    $no = $index + 1;
                                    $productName = $item['product_name'];
                                    $price = $item['price'];
                                    $orderAmount = $item['quantity'];
                                    $totalPrice += $price * $orderAmount;
                                ?>
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $productName; ?></td>
                                        <td>RM <?php echo number_format($price, 2); ?></td>
                                        <td><?php echo $orderAmount; ?></td>
                                        <td>RM <?php echo number_format($price * $orderAmount, 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <form id="confirm_payment_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <!-- Buyer Information Form -->
                        <h3 class="card-title mt-4">Buyer Information</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="buyer_name">Full Name:</label>
                                    <input type="text" class="form-control" id="buyer_name" name="buyer_name" value="<?php echo isset($_POST['buyer_name']) ? $_POST['buyer_name'] : $customer[0]['name']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="buyer_email">Email:</label>
                                    <input type="email" class="form-control" id="buyer_email" value="<?php echo isset($customer[0]['email']) ? $customer[0]['email'] : '-NA-'; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="buyer_address">Address:</label>
                                    <input type="text" class="form-control" name="buyer_address" value="<?php echo isset($_POST['buyer_address']) ? $_POST['buyer_address'] : $customer[0]['address']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="buyer_phone_number">Phone Number:</label>
                                    <input type="tel" class="form-control" name="buyer_phone_number" value="<?php echo isset($_POST['buyer_phone_number']) ? $_POST['buyer_phone_number'] : $customer[0]['phone_number']; ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Section -->
                        <h4 class="card-title mt-4">Payment Method</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_method">Select Payment Method:</label>
                                    <select class="form-control" id="payment_method" name="payment_method">
                                        <option value="Debit/Credit Card">Debit/Credit Card</option>
                                        <option value="PayPal">PayPal</option>
                                        <option value="E-Wallet">E-Wallet</option>
                                        <!-- Add more payment methods if needed -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Total Payment Amount Section -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>Subtotal (RM):</td>
                                            <td><?php echo number_format($subtotal, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Shipping Fee (RM):</td>
                                            <td>10.00</td>
                                        </tr>
                                        <?php
                                        // Calculate sales taxes (6% of subtotal)
                                        $salesTaxes = ($subtotal + 10.00) * 0.06;
                                        ?>
                                        <tr>
                                            <td>Sales Taxes (6%) (RM):</td>
                                            <td><?php echo number_format($salesTaxes, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 20px;"><strong>Total Payment Amount (RM):</strong></td>
                                            <?php
                                            // Calculate total payment amount
                                            $totalPaymentAmount = $subtotal + 10.00 + $salesTaxes; // Subtotal + Shipping Fee + Sales Taxes
                                            ?>
                                            <td style="font-size: 20px;"><strong><?php echo number_format($totalPaymentAmount, 2); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Confirm Payment Button -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-md-6 text-center">
                                <button type="submit" class="btn btn-primary" name="confirm_payment">Confirm Payment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

    <footer class="bg-body-secondary">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-6">
                    © 2024 Boom Inc. All rights reserved
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <i class="bi bi-facebook fs-3 px-3"></i>
                    <i class="bi bi-twitter fs-3 px-3"></i>
                    <i class="bi bi-instagram fs-3 px-3"></i>
                    <i class="bi bi-whatsapp fs-3 px-3"></i>
                </div>
            </div>
        </div>
    </footer>
</html>
