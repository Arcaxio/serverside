<?php
include 'includes/db_connect.php'; // Include database connection
session_start();

if (isset ($_SESSION['username'])) {
    $username = $_SESSION['username'];
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
$orders = [];
if ($userId !== null) {
    $order_stmt = $conn->prepare("SELECT orders.order_id, orders.order_date,orders.order_status, orders.total_amount, ordered_items.item_quantity, products.product_name, products.price, products.image_path 
                            FROM orders 
                            JOIN ordered_items ON orders.order_id = ordered_items.order_id 
                            JOIN products ON 
                            ordered_items.product_id = products.product_id
                            WHERE orders.customer_id = ?");
    $order_stmt->bindParam(1, $userId);

} else {
    // Handle guest cart (optional, you might use a session-based cart) 
}

if ($order_stmt) { // Only attempt execution if the statement was prepared
    $order_stmt->execute();
    $results = $order_stmt->fetchAll();


    foreach ($results as $result) {
        $orderId = $result['order_id'];
        if (!isset($orders[$orderId])) {
            $date = new DateTime($result['order_date']);
            $formattedDate = $date->format('F j, Y');
            $orders[$orderId] = [
                'order_id' => $orderId,
                'order_date' => $formattedDate,
                'total_amount' => $result['total_amount'],
                'order_status' => $result['order_status'],
                'products' => []
            ];
        }
            $orders[$orderId]['products'][] = [
                'item_quantity' => $result['item_quantity'],
                'product_name' => $result['product_name'],
                'price' => $result['price'],
                'image_path' => $result['image_path']
            ];
        
    }
    echo "<pre>";
    print_r($orders);
    echo "</pre>";}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
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

    <div class="container mt-3">
        <h2 class="mb-3">Your Orders</h2>
        <div class="row">
            <div class="col-md-8">
            <?php
            foreach ($orders as $order){
                ?>
                <div class="card mb-3">
                    <div class="card-body">
                        
                        
                        <h5 class="card-title">Order # <?php echo $order['order_id'];?></h5>
                        
                        
                        
                        <p class="card-text">Date: <?php echo $order['order_date']?></p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($order['products'] as $product){?>
                                <tr>
                                    <td><?php echo $product['product_name']?></td>
                                    <td><?php echo $product['price']?></td>
                                    <td><?php echo $product['item_quantity']?></td>
                                    <td>$100</td>
                                </tr>
                                <?php }?>
                                
                            </tbody>
                        </table>
                        <p class="card-text">Total: $205</p>
                    </div>
                </div>
                <?php } ?>
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
</body>

</html>
