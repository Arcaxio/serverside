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

// Pop out message when payment successfully
if (isset($_GET['success'])) {
    ?>
        <script>
            window.onload = function () {
                const toast = new bootstrap.Toast(document.getElementById('confirmPayment'));
                toast.show();
            };
        </script>
    <?php
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Boom!</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
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
            <section class="featured-products">
                <h2>Featured Products</h2>
                <div id="carousel" class="my-3 carousel slide w-50" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" data-bs-interval="3000">
                            <div class="card">
                                <div class="text-center d-flex flex-column justify-content-center"
                                    style="height: 320px">
                                    <h1 style="font-size: 4rem">You want it?</h1>
                                    <h1 style="font-size: 5rem">We have it!</h1>
                                </div>
                                <div class="card-body" style="height: 141.6px">
                                    <h1 class="card-title text-center">
                                        Have a look
                                        <i class="bi bi-arrow-right ms-3"></i>
                                    </h1>
                                </div>
                            </div>
                        </div>
                        <?php
                        $stmt = $conn->query("SELECT * FROM products LIMIT 10"); // Fetch featured products
                        while ($row = $stmt->fetch()) { ?>
                            <div class="carousel-item" data-bs-interval="3000">
                                <div class="card">
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
                                    ?>" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?php echo $row['product_name']; ?>
                                        </h5>
                                        <p class="card-text">$
                                            <?php echo $row['price']; ?>
                                        </p>
                                        <a href="product_details.php?id=<?php echo $row['product_id']; ?>"
                                            class="btn btn-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- When payment is successfully and received by database -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="confirmPayment" class="toast hide align-items-center text-bg-primary border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex justify-content-between align-items-center">
                <h6 class="m-0 ms-2">Payment successful! Visit the order page for details!</h6>
                <button type="button" class="btn-close btn-close-white me-2" data-bs-dismiss="toast" aria-label="Close">
                </button>
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