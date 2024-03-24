<?php
include '../includes/db_connect.php';
session_start();

if (!isset ($_SESSION['staff_id'])) {
    header('Location: index.php');
}

$username = $_SESSION['username'];

if (isset ($_GET['order_id'])) {
    $orderId = (int) $_GET['order_id'];

    // Fetch order details
    $stmt = $conn->prepare("SELECT orders.order_id, orders.order_date, orders.total_amount, orders.order_status, users.username 
                            FROM orders 
                            JOIN users ON orders.user_id = users.user_id
                            WHERE orders.order_id = ?");
    $stmt->bindParam(1, $orderId);
    $stmt->execute();

    $orderDetails = $stmt->fetch();

    // Fetch order items
    $stmt = $conn->prepare("SELECT oi.order_item_id, oi.item_quantity, p.product_name, p.price
                            FROM ordered_items oi
                            JOIN products p ON oi.product_id = p.product_id
                            WHERE oi.order_id = ?");
    $stmt->bindParam(1, $orderId);
    $stmt->execute();

    $orderItems = $stmt->fetchAll();
} else {
    // Handle missing order_id
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="row m-0 min-vh-100">
        <div class="col-2 p-0 bg-dark-subtle">
            <div class="p-4 mx-4 fw-medium">
                <i class="bi bi-app-indicator fs-4 me-3"></i>
                <span class="ms-2 fs-4">Boom Inc</span>
                </a>
            </div>
            <div class="mx-4">
                <div class="p-3" onclick="location.href='staff_home.php';" style="cursor: pointer;">
                    <i class="bi bi-house-door me-3"></i>
                    Home
                </div>
                <div class="p-3 border rounded rounded-3 bg-white" onclick="location.href='orders.php';" style="cursor: pointer;">
                    <i class="bi bi-cart me-3"></i>
                    Orders
                </div>
                <div class="p-3" onclick="location.href='products.php';" style="cursor: pointer;">
                    <i class="bi bi-box-seam me-3"></i>
                    Products
                </div>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="register.php" class="my-3 btn btn-secondary">Register Manager</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-10 p-0 bg-body-secondary">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-medium fs-3 p-3 mx-3">
                    Order Management
                </div>
                <div class="px-4 d-flex align-items-center">
                    <span class="fs-6 fw-medium pe-4">Welcome,
                        <?php echo $username; ?>
                    </span>
                    <a href="logout.php" class="btn btn-outline-dark">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white p-4">
                <div class="p-3 border rounded rounded-3">
                    <div id="order-details">
                        <p><strong>Order ID:</strong>
                            <?php echo $orderId; ?>
                        </p>
                        <p><strong>Customer:</strong>
                            <?php echo $orderDetails['username']; ?>
                        </p>
                        <p><strong>Order Date:</strong>
                            <?php echo $orderDetails['order_date']; ?>
                        </p>
                        <p><strong>Total Amount:</strong> RM
                            <?php echo number_format($orderDetails['total_amount'], 2); ?>
                        </p>

                        <h3>Order Items</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item) { ?>
                                    <tr>
                                        <td>
                                            <?php echo $item['product_name']; ?>
                                        </td>
                                        <td>RM
                                            <?php echo number_format($item['price'], 2); ?>
                                        </td>
                                        <td>
                                            <?php echo $item['item_quantity']; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>


                    <div id="status-update">
                        <div id="status-update">
                            <h3>Update Status</h3>
                            <form id="update-status-form">
                                <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                                <select class="form-select" name="status">
                                    <option value="pending" <?php echo ($orderDetails['order_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo ($orderDetails['order_status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo ($orderDetails['order_status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo ($orderDetails['order_status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo ($orderDetails['order_status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-outline-primary mt-3">Update</button>
                                <a href="orders.php" class="btn btn-outline-primary mt-3">Back</a>

                            </form>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#update-status-form').submit(function (event) {
                event.preventDefault(); // Prevent default form submission

                const form = $(this);
                const formData = form.serialize(); // Get form data 

                $.ajax({
                    url: 'update_order_status.php',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        console.log(response);
                        showAlert();
                    },
                    error: function (error) {
                        console.error('Error updating status:', error);
                    }
                });
            });
            function showAlert() {
                alert('Status updated successfully.');
            }


        });

    </script>
</body>

</html>