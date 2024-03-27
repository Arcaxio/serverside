<?php
    include 'includes/db_connect.php'; // Include database connection
    session_start();

    if (isset ($_SESSION['username'])) {
        $username = $_SESSION['username'];
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
    $orders = [];
    $total = 0;
    if ($userId !== null) {
        $order_stmt = $conn->prepare("SELECT orders.order_id, orders.order_date,orders.order_status, orders.total_amount, ordered_items.item_quantity, products.product_name, products.price, products.image_path 
                                FROM orders 
                                JOIN ordered_items ON orders.order_id = ordered_items.order_id 
                                JOIN products ON 
                                ordered_items.product_id = products.product_id
                                WHERE orders.user_id = ?");
        $order_stmt->bindParam(1, $userId);

    } else {
        // Handle guest cart (optional, you might use a session-based cart) 
    }

    // Fetch buyer's full name from the database based on order_id
    $buyer_stmt = $conn->prepare("SELECT payment.fullname FROM payment 
        JOIN orders ON payment.payment_id = orders.payment_id
        WHERE orders.order_id = ?");
    $buyer_stmt->bindParam(1, $orderId);
    $buyer_stmt->execute();
    $buyer_name = $buyer_stmt->fetchColumn();

    if ($order_stmt) { // Only attempt execution if the statement was prepared
        $order_stmt->execute();
        $results = $order_stmt->fetchAll();

        foreach ($results as $result) {
            $orderId = $result['order_id'];
            if (!isset($orders[$orderId])) {
                $date = new DateTime($result['order_date']);
                $formattedDate = $date->format('F j, Y H:i:s');
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
    }

    // Reverse the order of the $orders array
    $orders = array_reverse($orders, true); // 'true' parameter preserves the keys

    // Pop out message when payment successfully
    if (isset($_GET['success'])) {
        ?>
        <script>
            window.onload = function () {
                const toast = new bootstrap.Toast(document.getElementById('continue'));
                toast.show();
            };
        </script>
        <?php
    }
?>

<script>
        function confirmCancellation(orderId) {
            // Display a confirmation dialog
            var confirmation = confirm("Are you sure to cancel order?");
            
            // If the user confirms, redirect to cancel_order.php with the order_id
            if (confirmation) {
                window.location.href = "cancel_order.php?order_id=" + orderId;
            }
        }
</script>

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

    <style>
        img {
            height: 350px;
            width: 350px;
            object-fit: scale-down;
        }
        .carousel-control-next,
        .carousel-control-prev {
            filter: invert(100%);
        }
        .canceled{
            background-color: #E0E0E0;

        }
        .table-c {
        --bs-table-color-type: initial;
        --bs-table-bg-type: initial;
        --bs-table-color-state: initial;
        --bs-table-bg-state: initial;
        --bs-table-color: var(--bs-emphasis-color);
        --bs-table-bg: #E0E0E0;
        --bs-table-border-color: white;
        --bs-table-accent-bg: transparent;
        --bs-table-striped-color: var(--bs-emphasis-color);
        --bs-table-striped-bg: rgba(var(--bs-emphasis-color-rgb), 0.05);
        --bs-table-active-color: var(--bs-emphasis-color);
        --bs-table-active-bg: rgba(var(--bs-emphasis-color-rgb), 0.1);
        --bs-table-hover-color: var(--bs-emphasis-color);
        --bs-table-hover-bg: rgba(var(--bs-emphasis-color-rgb), 0.075);
        width: 100%;
        margin-bottom: 1rem;
        vertical-align: top;
        border-color: var(--bs-table-border-color)
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

    <div class="container mt-3"> <!-- Container Start -->
        <!-- if there has order -->
        <?php if(!empty($orders)){ ?>
            <h2 class="mb-3 pt-3">Your Orders</h2>
            <div class="row"> <!-- Row Start -->
                <div class="col-md-8"> <!-- Column Start -->
                    <?php
                        foreach ($orders as $order) {
                            $total = 0;

                            // Fetch buyer's full name from the database based on order_id
                            $buyer_stmt = $conn->prepare("SELECT payment.fullname FROM payment 
                                                            JOIN orders ON payment.payment_id = orders.payment_id
                                                            WHERE orders.order_id = ?");
                            $buyer_stmt->bindParam(1, $order['order_id']);
                            $buyer_stmt->execute();
                            $buyer_name = $buyer_stmt->fetchColumn();
                        ?>
                        <div class="card mb-3"><!-- Card Start -->
                            <div class="card-body <?php echo ($order['order_status'] == "cancelled") ? "canceled" : ""; ?>"><!-- Card body Start -->
                                <div class="row">
                                        <h5 class="card-title">
                                            Order # <?php echo $order['order_id']; ?>
                                        </h5>
                                </div>
                                <div class="row">
                                        <h5 class="card-title">
                                            <?php if(!empty($buyer_name)) { ?>
                                                <small class="text-muted">Buyer: <?php echo $buyer_name; ?></small>
                                            <?php } ?>
                                        </h5>
                                </div>
                                <p class="card-text">Date: <?php echo $order['order_date']; ?></p>
                                <p class="card-text">Status: <?php echo $order['order_status']; ?> </p>
                                <table class="table <?php echo ($order['order_status'] == "cancelled") ? "table-c" : ""; ?>">
                                    <!-- Table headers -->
                                    <thead>
                                        <tr>
                                            <th scope="col">Product</th>
                                            <th class="text-end" scope="col">Price</th>
                                            <th class="text-end" scope="col">Quantity</th>
                                            <th class="text-end" scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <!-- Table body -->
                                    <tbody>
                                        <?php foreach($order['products'] as $product) { ?>
                                            <tr>
                                                <td><?php echo $product['product_name']; ?></td>
                                                <td class="text-end">RM <?php echo $product['price']; ?></td>
                                                <td class="text-end"> <?php echo $product['item_quantity']; ?></td>
                                                <td class="text-end">RM <?php echo ($product['price'] * $product['item_quantity']); ?></td>
                                            </tr>
                                        <?php $total += $product['price'] * $product['item_quantity'];} ?>
                                            <tr>
                                                <td>Shipping Fee:</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-end">RM 10.00</td>
                                            </tr>
                                            <tr>
                                                <td>Sales Taxes (6%):</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-end">RM <?php echo number_format(($total + 10) * 0.06,2); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Total: </th>
                                                <td></td>
                                                <td></td>
                                                <th class="text-end">RM <?php echo $order['total_amount']; ?></th>
                                            </tr>
                                        </tbody>
                                </table>
                                <?php if($order['order_status'] == "pending" || $order['order_status'] == "processing") { ?>
                                    <button type="button" class="btn btn-outline-primary" onclick="confirmCancellation(<?php echo $order['order_id']; ?>)">Cancel order</button>
                                    <?php } ?>
                        </div><!-- Card body End -->
                    </div> <!-- Card End -->
                        <?php }
        // Else no order show the products
        }else { ?>
                    <h2 class="mb-3 pt-3">Your Order is Empty</h2>
                    <a class="btn btn-outline-primary m-3 p-3" href="products.php">Continue Shopping</a>
                    <h3 class="mt-3 mb-3"> Discover our irresistible range of products</h3>
                    <div id="carouselExampleControls" class="carousel slide m-3 col-6" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="images/premium_case.jpg" alt="Los Angeles" class="d-block w-100">
                            </div>
                            <?php $stmt = $conn->query("SELECT * FROM products LIMIT 8");
                                while ($row = $stmt->fetch()) { ?>
                                    <div class="carousel-item ">
                                        <a href="product_details.php?id=<?php echo $row['product_id']?>">
                                            <img src="<?php
                                                $imagePath = $row['image_path'];
                                                // Check if the image path starts with "http"
                                                if (strpos($imagePath, 'http') === 0) {
                                                    // Image is from external URL, use as is
                                                    echo $finalImagePath = $imagePath;
                                                } else {
                                                    // Local image, prepend "images/"
                                                    echo $finalImagePath = 'images/' . $imagePath;
                                                }
                                            ?>" class="d-block w-100" alt="...">
                                        </a>
                                    </div>
                                <?php } ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
        <?php } ?>   
                </div><!-- Column End -->
            </div><!-- Row End -->
    </div><!-- Container End -->

    <!-- When payment is successfully and received by database -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="continue" class="toast hide align-items-center text-bg-primary border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex justify-content-between align-items-center">
                <h6 class="m-0 ms-2">Payment successful! Visit the order page for details!</h6>
                <button type="button" class="btn-close btn-close-white me-2" data-bs-dismiss="toast" aria-label="Close">
                </button>
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
