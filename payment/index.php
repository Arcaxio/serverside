<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payment Details</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="row m-0 min-vh-100">
        <div class="col-2 p-0 bg-dark-subtle">
            <div class="p-4 mx-4 fw-medium">
                <i class="bi bi-app-indicator fs-4 me-3"></i>
                <span class="ms-2 fs-4">Boom Inc</span>
            </div>
            <!-- Side navigation links here -->
        </div>
        <div class="col-10 p-0 bg-body-secondary">
            <!-- Header with user info -->
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-medium fs-3 p-3 mx-3">
                    Payment Details
                </div>
                <div class="px-4 d-flex align-items-center">
                    <!-- User info and logout button -->
                    <span class="fs-6 fw-medium pe-4">Welcome,
                        <?php echo $username; ?>
                    </span>
                    <a href="logout.php" class="btn btn-outline-dark">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </div>

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
    </div>

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
    document.getElementById('confirm_payment').addEventListener('click', function() {
        // Validate buyer information before submitting payment
        const buyerName = document.getElementById('buyer_name').value;
        const buyerEmail = document.getElementById('buyer_email').value;
        const buyerAddress = document.getElementById('buyer_address').value;
        const buyerPhone = document.getElementById('buyer_phone').value;

        // Perform validation here (e.g., check if fields are not empty)

        // If validation passes, proceed with payment submission
        // Otherwise, display error messages to the user

        // Make an AJAX request to insert payment record
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'payment.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                // Further actions after successful payment submission
            } else {
                console.error('Error:', xhr.statusText);
            }
        };
        xhr.onerror = function() {
            console.error('Request failed');
        };
        xhr.send('buyer_name=' + buyerName + '&buyer_email=' + buyerEmail + '&buyer_address=' + buyerAddress + '&buyer_phone=' + buyerPhone);
    });

</script>
</body>
</html>
