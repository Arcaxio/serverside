<?php
include 'includes/db_connect.php'; // Include database connection
session_start();

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

// Fetch cart items
$cartItems = [];
if ($userId !== null) {
    $stmt = $conn->prepare("SELECT cart.cart_id, cart.product_id, cart.quantity, products.product_name, products.price, products.image_path 
                            FROM cart 
                            JOIN products ON cart.product_id = products.product_id 
                            WHERE cart.user_id = ?");
    $stmt->bindParam(1, $userId);
} else {
    // Handle guest cart (optional, you might use a session-based cart) 
}

if ($stmt) { // Only attempt execution if the statement was prepared
    $stmt->execute();
    $cartItems = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <style>
        td {
            vertical-align: middle;
        }
    </style>
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

    <div class="container mt-3">
        <h2 class="mb-3">Shopping Cart</h2>
        <div class="row">
            <div class="col-md-8">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Total</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $index => $item) { ?>
                            <tr>
                                <td>
                                    <?php echo $index + 1; ?>
                                </td>
                                <td>
                                    <?php if (!empty ($item['image_path'])) { ?>
                                        <img src="<?php
                                        $imagePath = $item['image_path'];

                                        // Check if the image path starts with "http"
                                        if (strpos($imagePath, 'http') === 0) {
                                            // Image is from external URL, use as is
                                            echo $finalImagePath = $imagePath;
                                        } else {
                                            // Local image, prepend "images/"
                                            echo $finalImagePath = 'images/' . $imagePath;
                                        }
                                        ?>" alt="<?php echo $item['product_name']; ?>"
                                            style="max-width: 56px; margin-right: 0.5rem">
                                    <?php } ?>
                                    <?php echo $item['product_name']; ?>
                                </td>
                                <td>RM
                                    <?php echo number_format($item['price'], 2); ?>
                                </td>
                                <td>
                                    <?php echo $item['quantity']; ?>
                                </td>
                                <td>RM
                                    <span class="cart-item-price">
                                        <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="remove_from_cart.php?cart_id=<?php echo $item['cart_id']; ?>"
                                        class="btn btn-danger btn-sm">Remove</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body" style="height: 55vh">
                        <h5 class="card-title">Cart Summary</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Subtotal:
                                <span id="subtotal"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Shipping Fee:
                                <span id="shipping-fee">RM 10.00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sales Taxes (6%):
                                <span id="taxes"></span>
                            </li>
                            <!-- Additional summary items (taxes, shipping, etc.) can be added here -->
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total:
                                <span id="total"></span>
                            </li>
                        </ul>
                        <a href="checkout.php" id="proceedToCheckoutBtn" class="btn btn-primary btn-block mt-3 disabled">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

    <footer class="bg-body-secondary mt-auto">
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

    <!-- Display "No items in cart!" when there is no item in cart -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="noItemsToast" class="toast hide align-items-center text-white bg-danger border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex justify-content-between align-items-center">
                <h6 class="m-0 ms-2">No items in cart!</h6>
                <button type="button" class="btn-close btn-close-white me-2" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        function calculateTotals() {
            let subtotal = 0;
            const priceElements = document.querySelectorAll('.cart-item-price'); // Adjust selector if needed

            priceElements.forEach(element => {
                subtotal += parseFloat(element.textContent.replace(',', ''));
            });

            document.getElementById('subtotal').textContent = 'RM ' + subtotal.toFixed(2);

            const shippingFee = 10.00;
            const taxRate = 0.06;

            const taxableAmount = subtotal + shippingFee;
            const taxes = taxableAmount * taxRate;
            document.getElementById('taxes').textContent = 'RM ' + taxes.toFixed(2);

            // Calculate total
            const total = subtotal + shippingFee + taxes;
            document.getElementById('total').textContent = 'RM ' + total.toFixed(2);
        }

        // Call the function to calculate totals when the page loads
        window.onload = calculateTotals; 
        
        // Function to check if there are any items in the cart
        function checkCartItems() {
            // Get the table body element
            const cartTableBody = document.querySelector('tbody');
            
            // Get the "Proceed to Checkout" button
            const proceedToCheckoutBtn = document.getElementById('proceedToCheckoutBtn');
            
            // Check if there are any rows in the table body
            if (cartTableBody.rows.length > 0) {
                // Enable the button if there are items in the cart
                proceedToCheckoutBtn.classList.remove('disabled');
            } else {
                // Disable the button if there are no items in the cart
                proceedToCheckoutBtn.classList.add('disabled');
                // Show the toast message for no items in cart
                showNoItemsToast();
            }
        }
        
        // Function to show the "No items in cart!" toast message
        function showNoItemsToast() {
            var noItemsToast = new bootstrap.Toast(document.getElementById('noItemsToast'));
            noItemsToast.show();
        }
        
        // Call the function initially
        checkCartItems();

    </script>
</body>

</html>