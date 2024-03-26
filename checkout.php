<?php
    // Step 1: Include necessary files and start the session
    include 'includes/db_connect.php';
    session_start();

    // Step 2: Retrieve the user ID if the username is set in the session
    $userId = null;
    if (isset ($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bindParam(1, $username);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $userId = $stmt->fetch()['user_id'];
        }
    } else {
        // Handle the case when there is no 'username' in session (Optional)
        $username = null;  // Set a default, or perform other actions if needed
    }

    // Step 3: Generate a random payment ID and get the current date and time
    $paymentId = mt_rand(100000, 999999);
    $paymentDatetime = date("Y-m-d H:i:s");

    // Step 4: Fetch cart items and calculate subtotal if the user ID is not null
    $cartItems = [];
    $subtotal = 0;
    $salesTaxes = 0;

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


    // Step 5: Fetch user'semail from the users table if the user ID is not null
    if ($userId !== null) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bindParam(1, $userId);
        $stmt->execute();
        $users = $stmt->fetchAll();
    }


    // Step 6: Parse the JSON data to get states and cities
    // Include the JSON file
    $citiesJson = file_get_contents('cities.json');
    $citiesData = json_decode($citiesJson, true);

    // Fetch states from the JSON data
    $states = array_keys($citiesData);

    // Step 7: Define a function to validate phone numbers
    function isValidPhoneNumber($phoneNumber) {
        // Validate phone number format (assuming 10 or 11-digit phone number)
        $phoneNumberPattern = '/^\d{10,11}$/'; // Accepts 10 or 11-digit phone number
        return preg_match($phoneNumberPattern, $phoneNumber);
    }

    // Step 8: Process form submission and handle payment confirmation
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_payment'])) {
        $buyerName = trim($_POST['buyer_name']);
        $address = trim($_POST['buyer_address']);
        $phoneNumber = trim($_POST['buyer_phone_number']);
        $zipCode = trim($_POST['buyer_zipcode']);
        $city = trim($_POST['buyer_city']);
        $state = trim($_POST['buyer_state']);

        $salesTaxes = ($subtotal + 10.00) * 0.06;
        // Calculate total payment amount
        $totalPaymentAmount = $subtotal + 10.00 + $salesTaxes; // Subtotal + Shipping Fee + Sales Taxes
        
        // Validate buyer name, address, phone number, zipcode, city, and state
        if (empty($buyerName) || empty($address) || empty($phoneNumber) || empty($zipCode) || empty($city) || empty($state)) {
            echo '<script>alert("Please fill in all the required fields.");</script>';
        } elseif (!isValidPhoneNumber($phoneNumber)) {
            echo '<script>alert("Please provide a valid phone number.");</script>';
        } else {
            // Proceed with payment confirmation
            // Insert payment details into payment table
            $paymentMethod = $_POST['payment_method']; // Assuming payment method is selected in the form
            // Fetch the cart_id associated with the user
            $productId = isset($cartItems[0]['product_id']) ? $cartItems[0]['product_id'] : null;
            if ($productId) {
                // Inster payment details into payment table
                $stmt = $conn->prepare("INSERT INTO payment (payment_id, product_id, user_id, fullname, address, zipcode, city, state, phone_number, payment_datetime, payment_method, total_payment_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bindParam(1, $paymentId);
                $stmt->bindParam(2, $productId);
                $stmt->bindParam(3, $userId);
                $stmt->bindParam(4, $buyerName);
                $stmt->bindParam(5, $address);
                $stmt->bindParam(6, $zipCode);
                $stmt->bindParam(7, $city);
                $stmt->bindParam(8, $state);
                $stmt->bindParam(9, $phoneNumber);
                $stmt->bindParam(10, $paymentDatetime);
                $stmt->bindParam(11, $paymentMethod);
                $stmt->bindParam(12, $totalPaymentAmount);
                $stmt->execute();
                

                $order_stmt = $conn->prepare("INSERT INTO orders (user_id, order_date, total_amount,payment_id)VALUES(?,NOW(),?,?)");            
                $order_stmt->bindParam(1,$userId);
                $order_stmt->bindParam(2,$totalPaymentAmount);
                $order_stmt->bindParam(3,$paymentId);
                $order_stmt->execute();

                $orderId = $conn->lastInsertId();

                foreach($cartItems as $item){
                    $productId = $item['product_id'];
                    $quantity = $item['quantity'];

                    $order_item_stmt = $conn->prepare("INSERT INTO ordered_items(order_id, product_id, item_quantity)VALUES(?,?,?)");
                    $order_item_stmt->bindParam(1,$orderId);
                    $order_item_stmt->bindParam(2,$productId);
                    $order_item_stmt->bindParam(3,$quantity);
                    $order_item_stmt->execute();
                }

                // Delete cart items associated with the user

                $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->bindParam(1, $userId);
                if ($stmt->execute()) {
                    echo "Cart items deleted successfully."; // Debugging statement

                    
                } else {
                    echo "Error deleting cart items: " . $stmt->errorInfo()[2]; // Output any errors
                }

                // Redirect to payment_gateway.php with success parameter
                header("Location: payment_gateway.php?success=true&payment_method=" . urlencode($paymentMethod));
                exit();
            
            } else {
                    echo "Error: No cart items found for the user.";
                }
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
    <!-- Header -->
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

                    <!-- Buyer Information Form -->
                    <form id="confirm_payment_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <h3 class="card-title mt-4">Buyer Information</h3>
                        <div class="row"><!-- New row -->
                            <div class="col">
                                <!-- Full Name -->
                                <div class="form-group">
                                    <label for="buyer_name">Full Name:</label>
                                    <br>
                                    <small style="color: #A9A9A9;">[Note: You can change your Full Name anytime.]</small>
                                    <input type="text" class="form-control" id="buyer_name" name="buyer_name" placeholder="e.g: Full Name (as per IC/Passport)" value="<?php echo isset($_POST['buyer_name']) ? $_POST['buyer_name'] : $users[0]['name']; ?>" required>
                                </div>
                                <!-- Email -->
                                <div class="form-group">
                                    <label for="buyer_email">Email:</label>
                                    <input type="email" class="form-control" id="buyer_email" value="<?php echo isset($users[0]['email']) ? $users[0]['email'] : '-NA-'; ?>" readonly>
                                </div>
                                <!-- Address -->
                                <div class="form-group">
                                    <label for="buyer_address">Address:</label>
                                    <br>
                                    <small style="color: #A9A9A9;">[Note: You can change your Address anytime.]</small>
                                    <input type="text" class="form-control" name="buyer_address" placeholder="e.g: (House/apartment/flat number), (Street)" value="<?php echo isset($_POST['buyer_address']) ? $_POST['buyer_address'] : $users[0]['address']; ?>" required>
                                </div>

                                <div class="row"><!-- New row -->
                                    <!-- State dropdown -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="buyer_state">State:</label>
                                            <select class="form-control" id="buyer_state" name="buyer_state" required onchange="this.form.submit()">
                                                <option value="">Select State</option>
                                                <?php
                                                    $states = json_decode(file_get_contents('cities.json'), true);
                                                    foreach ($states as $state => $cities) {
                                                        echo '<option value="' . $state . '"';
                                                        if (isset($_POST['buyer_state']) && $_POST['buyer_state'] == $state) {
                                                            echo ' selected';
                                                        }
                                                        echo '>' . $state . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- City dropdown -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="buyer_city">City:</label>
                                            <select class="form-control" id="buyer_city" name="buyer_city" required>
                                                <option value="">Select City</option>
                                                <?php
                                                    if (isset($_POST['buyer_state']) && $_POST['buyer_state'] != '') {
                                                        $selectedState = $_POST['buyer_state'];
                                                        foreach ($states[$selectedState] as $city) {
                                                            echo '<option value="' . $city . '"';
                                                            if (isset($_POST['buyer_city']) && $_POST['buyer_city'] == $city) {
                                                                echo ' selected';
                                                            }
                                                            echo '>' . $city . '</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Zip Code -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="buyer_zipcode">Zip Code:</label>
                                            <input type="text" class="form-control" name="buyer_zipcode" placeholder="e.g: 12345" pattern="\d{5}" value="<?php echo isset($_POST['buyer_zipcode']) ? $_POST['buyer_zipcode'] : $users[0]['zipcode']; ?>" required>
                                        </div>
                                    </div>
                                </div><!-- Row end -->

                                <!-- Phone Number -->
                                <div class="form-group">
                                    <label for="buyer_phone_number">Phone Number:</label>
                                    <br>
                                    <small style="color: #A9A9A9;">[Note: You can change your Phone Number anytime.]</small>
                                    <input type="tel" class="form-control" name="buyer_phone_number" placeholder="e.g: 0123456789"pattern="\d{10,11}" value="<?php echo isset($_POST['buyer_phone_number']) ? $_POST['buyer_phone_number'] : $users[0]['phone_number']; ?>" required>
                                </div>
                            </div>
                        </div><!-- Row end -->

                        <!-- Payment Method Section -->
                        <h4 class="card-title mt-4">Payment Method</h4>
                        <div class="row"><!-- New row -->
                            <div class="col">
                                <div class="form-group">
                                <input type="hidden" name="payment_method" value="<?php echo $paymentMethod; ?>">
                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                        <option value="Debit/Credit Card">Debit/Credit Card</option>
                                        <option value="PayPal">PayPal</option>
                                        <option value="E-Wallet">E-Wallet</option>
                                        <!-- Add more payment methods if needed -->
                                    </select>
                                </div>
                            </div>
                        </div><!-- Row end -->

                        <!-- Total Payment Amount Section -->
                        <div class="row mt-4"><!-- New row -->
                            <div class="col">
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
                        </div><!-- Row end -->

                        <!-- Confirm Payment Button -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-md-6 text-center">
                                <button type="submit" class="btn btn-primary" name="confirm_payment">Confirm Payment</button>
                            </div>
                        </div>
                    </form><!-- Buyer Information Form End-->
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
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
</body>

</html>