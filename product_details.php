<?php
include 'includes/db_connect.php'; // Include database connection

$secondsInWeek = 60 * 60 * 24 * 7;
// Set cookie lifetime and start the session
session_set_cookie_params($secondsInWeek);
session_start();

if (isset ($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    // Handle the case when there is no 'username' in session (Optional)
    $username = null;  // Set a default, or perform other actions if needed
}

$product_id = isset ($_GET['id']) ? (int) $_GET['id'] : null;

if (!$product_id) {
    echo "Error";
}

$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bindParam(1, $product_id);
$stmt->execute();
$product = $stmt->fetch();

if (!$product) {
    echo "Error";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>
        <?php echo $product['product_name']; ?>
    </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <style>
        img {
            width: 500px;
            height: 500px;
            object-fit: scale-down;
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

    <div class="container p-3">
        <main class="">
            <div class="row py-3 justify-content-between">
                <div class="col-6 p-0 border rounded rounded-3" style="height: 502px; width: 502px">
                    <img src="<?php
                    $imagePath = $product['image_path'];

                    // Check if the image path starts with "http"
                    if (strpos($imagePath, 'http') === 0) {
                        // Image is from external URL, use as is
                        echo $finalImagePath = $imagePath;
                    } else {
                        // Local image, prepend "images/"
                        echo $finalImagePath = 'images/' . $imagePath;
                    }

                    ?>" alt="<?php echo $product['product_name']; ?>" />
                </div>
                <div class="col-6">
                    <div class="details">
                        <h1 class="text-uppercase">
                            <?php echo $product['product_name']; ?>
                        </h1>
                        <h4 class="my-5">Price: RM
                            <?php echo $product['price']; ?>
                        </h4>
                        <h4>
                            Description:
                        </h4>
                        <div style="max-height: 228px; overflow: auto;">
                            <h4>
                                <?php echo $product['description']; ?>
                            </h4>
                        </div>
                    </div>
                    <div class="row pt-3">
                        <div class="col-4">
                            <div class="input-group h-100">
                                <span class="input-group-text" id="quantity">Quantity</span>
                                <input type="number" class="form-control" id="quantity" value="1">
                            </div>
                        </div>
                        <div class="col-8">
                            <a class="btn btn-primary btn-lg">Add to Cart</a>
                        </div>
                    </div>

                </div>
            </div>
        </main>
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