<?php
include 'includes/db_connect.php'; // Include database connection

$secondsInWeek = 60 * 60 * 24 * 7;
// Set cookie lifetime and start the session
session_set_cookie_params($secondsInWeek);
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    if(isset($_SESSION['role'])) {
        $role = $_SESSION['role'];
    } else {
        // Handle the case when 'role' is not set in session
        // You can set a default role or perform other actions here
        $role = null;
    }
} else {
    // Handle the case when there is no 'username' in session
    // You can perform other actions if needed
    $username = null;
    $role = null;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <style>
        body {
            overflow: unset;
        }

        img {
            height: 320px;
            width: 320px;
            object-fit: cover;
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
                    <?php if (isset($username)) {
                        echo "Welcome, " . $username . "!";
                    } ?>
                </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="products.php">Products</a>
                </li>
                <?php if (isset ($_SESSION['username'])) { 
                    if ($role == null) {?>
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
                            <a class="nav-link fw-medium" href="customer/logout.php">Logout</a>
                        </li>
                    <?php } ?>
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
            <section class="all-products">
                <?php
                // Get all categories
                $categoriesStmt = $conn->query("SELECT * FROM categories");

                while ($categoryRow = $categoriesStmt->fetch()) {
                    $categoryId = $categoryRow['category_id'];

                    // Get products for this category
                    $productsStmt = $conn->query("SELECT * FROM products WHERE category_id = $categoryId");
                    ?>
                    <h2 class="category-title">
                        <?php echo $categoryRow['category_name']; ?>
                    </h2>
                    <div class="row">
                        <?php while ($productRow = $productsStmt->fetch()) { ?>
                            <div class="col-md-4 p-3">
                                <div class="card">
                                    <img src="<?php
                                    $imagePath = $productRow['image_path'];

                                    // Check if the image path starts with "http"
                                    if (strpos($imagePath, 'http') === 0) {
                                        // Image is from external URL, use as is
                                        echo $finalImagePath = $imagePath;
                                    } else {
                                        // Local image, prepend "images/"
                                        echo $finalImagePath = 'images/' . $imagePath;
                                    }
                                    ?>" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?php echo $productRow['product_name']; ?>
                                        </h5>
                                        <p class="card-text">$
                                            <?php echo $productRow['price']; ?>
                                        </p>
                                        <a href="product_details.php?id=<?php echo $productRow['product_id']; ?>"
                                            class="btn btn-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </section>
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