<?php
include 'includes/db_connect.php';
session_start();
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
                    <h5 class="card-title">Payment Information</h5>
                    <!-- Payment ID and Payment Date/Time Section -->
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="payment_id">Payment ID:</label>
                                <input type="text" class="form-control" id="payment_id" readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="payment_datetime">Payment Date & Time:</label>
                                <input type="text" class="form-control" id="payment_datetime" readonly>
                            </div>
                        </div>
                    </div>

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
                                <!-- Order products will be populated dynamically here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Payment Amount Section -->
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="total_amount">Total Payment Amount (RM):</label>
                                <input type="text" class="form-control" id="total_amount" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Buyer Information Form -->
                    <h5 class="card-title">Buyer Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="buyer_name">Name:</label>
                                <input type="text" class="form-control" id="buyer_name" required>
                            </div>
                            <div class="form-group">
                                <label for="buyer_email">Email:</label>
                                <input type="email" class="form-control" id="buyer_email" required>
                            </div>
                            <div class="form-group">
                                <label for="buyer_address">Address:</label>
                                <input type="text" class="form-control" id="buyer_address" required>
                            </div>
                            <div class="form-group">
                                <label for="buyer_phone">Phone:</label>
                                <input type="tel" class="form-control" id="buyer_phone" required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_method">Select Payment Method:</label>
                                <select class="form-control" id="payment_method">
                                    <option value="Debit/Credit Card">Debit/Credit Card</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="E-Wallet">E-Wallet</option>
                                    <!-- Add more payment methods if needed -->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirm Payment Button -->
            <div class="row justify-content-center mt-4">
                <div class="col-md-6 text-center">
                    <button type="submit" class="btn btn-primary" id="confirm_payment">Confirm Payment</button>
                </div>
            </div>
        </div>
    </div>

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

    <script>
    // JavaScript code to fetch and display selected order products, total amount, and payment ID
    // Example code for dynamic population (replace with actual code to fetch cart items)
    // Dummy data for demonstration
    const orderProducts = [
    { no: 1, name: "Product 1", price: 50, orderAmount: 1 },
    { no: 2, name: "Product 2", price: 30, orderAmount: 2 },
    { no: 3, name: "Product 3", price: 25, orderAmount: 1 }
    ];

    // Function to calculate total price for each selected product
    function calculateTotalPrice(product) {
    return product.price * product.orderAmount;
    }

    // Function to calculate total amount
    function calculateTotal() {
    let total = 0;
    orderProducts.forEach(product => {
    total += calculateTotalPrice(product);
    });
    return total;
    }

    // Function to populate selected order products and total amount
    function populateSelectedProducts() {
    const selectedProductsTable = document.getElementById('selected_products');
    selectedProductsTable.innerHTML = '';
    orderProducts.forEach(product => {
    const row = document.createElement('tr');
    row.innerHTML = `
    <td>${product.no}</td>
    <td>${product.name}</td>
    <td>RM ${product.price.toFixed(2)}</td>
    <td>${product.orderAmount}</td>
    <td>RM ${calculateTotalPrice(product).toFixed(2)}</td>`; // Total price column
    selectedProductsTable.appendChild(row);
    });
    const totalAmountInput = document.getElementById('total_amount');
    totalAmountInput.value = `RM ${calculateTotal().toFixed(2)}`;
    }

    // JavaScript code to populate payment ID and payment date/time, selected order products, and total amount on page load
    window.addEventListener('load', () => {
    const paymentIdInput = document.getElementById('payment_id');
    const paymentDatetimeInput = document.getElementById('payment_datetime');

    // Dummy payment ID for demonstration
    const paymentId = "123456";
    paymentIdInput.value = paymentId;

    // Fetch current date and time
    const currentDate = new Date();
    const formattedDate = currentDate.toLocaleDateString('en-US');
    const formattedTime = currentDate.toLocaleTimeString('en-US');
    const paymentDatetime = `${formattedDate} ${formattedTime}`;
    paymentDatetimeInput.value = paymentDatetime;
    populateSelectedProducts();
    });


    // JavaScript code for confirming payment
    document.getElementById('confirm_payment').addEventListener('click', function () {
    // Validate buyer information before submitting payment
    const buyerName = document.getElementById('buyer_name').value;
    const buyerEmail = document.getElementById('buyer_email').value;
    const buyerAddress = document.getElementById('buyer_address').value;
    const buyerPhone = document.getElementById('buyer_phone').value;
    const paymentMethod = document.getElementById('payment_method').value;

    // Perform validation here (e.g., check if fields are not empty)

    // If validation passes, proceed with payment submission
    // Otherwise, display error messages to the user

    // Make an AJAX request to insert payment record
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'payment.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Parse response from server
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Payment successful, redirect to receipt page
                        window.location.href = 'receipt.php?payment_id=' + response.payment_id;
                    } else {
                        // Payment failed, display error message
                        alert('Payment failed. Please try again.');
                    }
                } else {
                    console.error('Error:', xhr.statusText);
                }
            };
            xhr.onerror = function () {
                console.error('Request failed');
            };
            // Send data to server
            xhr.send('buyer_name=' + buyerName + '&buyer_email=' + buyerEmail + '&buyer_address=' + buyerAddress + '&buyer_phone=' + buyerPhone + '&payment_method=' + paymentMethod);
        });
    </script>
</body>
</html>
