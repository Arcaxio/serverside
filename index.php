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
if (isset ($_GET['success'])) {
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
            object-fit: scale-down;
        }

        img.secondary {
            height: 90px;
            width: 90px;
        }

        .carousel-control-next,
        .carousel-control-prev {
            filter: invert(100%);
        }

        .choose-us {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .custom-card {
            background: linear-gradient(90deg, rgba(0, 147, 255, 1) 0%, rgba(137, 25, 255, 1) 100%);
            color: white;
            border-radius: 0px;
        }

        .custom-pattern {
            background:
                linear-gradient(-45deg, #0000 1.3%, #FFFFFF 0 32%, #0000 0),
                linear-gradient(45deg, #0000 48%, #F2F2F2 0 52%, #0000 0),
                linear-gradient(45deg, #0000 1.3%, #FFFFFF 0 32%, #0000 0) calc(53px/2) calc(53px/2),
                linear-gradient(-45deg, #0000 48%, #F2F2F2 0 52%, #0000 0) #FFFFFF;
            background-size: calc(2*53px) 53px, 53px 53px;
        }

        @keyframes slide {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-100%);
            }
        }

        .logos {
            overflow: hidden;
            padding: 60px 0;
            background: white;
            white-space: nowrap;
            position: relative;
        }

        .logos:before,
        .logos:after {
            position: absolute;
            top: 0;
            width: 250px;
            height: 100%;
            content: "";
            z-index: 2;
        }

        .logos:before {
            left: 0;
            background: linear-gradient(to left, rgba(255, 255, 255, 0), white);
        }

        .logos:after {
            right: 0;
            background: linear-gradient(to right, rgba(255, 255, 255, 0), white);
        }

        .logos:hover .logos-slide {
            animation-play-state: paused;
        }

        .logos-slide {
            display: inline-block;
            animation: 35s slide infinite linear;
        }

        .logos-slide img {
            height: 80px;
            margin: 0 40px;
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
    <div class="custom-pattern">
        <div class="container px-3">
            <section class="featured-products">
                <div class="row">
                    <div class="col-6 p-3 d-flex flex-column align-items-center justify-content-center">

                        <h1 class="p-4 mb-4">Prices? Let it BOOM!</h1>
                        <h4>Want to buy electronics but no matter where you look, it's always too expensive?</h4>
                        <h4>Don't worry, we got your back! Here at Boom Inc, we offer the most competitive pricing!</h4>

                    </div>
                    <div class="col-6">
                        <div id="carousel" class="my-3 carousel slide w-100" data-bs-ride="carousel">
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
                                                <h5 class="card-title text-center">
                                                    <?php echo $row['product_name']; ?>
                                                </h5>
                                                <p class="card-text text-center">$
                                                    <?php echo $row['price']; ?>
                                                </p>
                                                <div class="d-flex justify-content-center">
                                                    <a href="product_details.php?id=<?php echo $row['product_id']; ?>"
                                                        class="btn btn-primary">View Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <button class="carousel-control-prev text-primary" type="button" data-bs-target="#carousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="bg-body-secondary text-center">
        <h3 class="py-4 m-0">Why choose us?</h3>
        <div class="row px-5 pb-3">
            <div class="col-4 px-3 choose-us">
                <img src="images/fast.png" class="secondary">
                <h5 class="text-center mt-3">
                    We offer a competitive shipping fee, you can't find this anywhere else!
                </h5>
            </div>

            <div class="col-4 px-3 choose-us">
                <img src="images/medal.png" class="secondary">
                <h5 class="text-center mt-3">
                    All our products are genuine and authentic! Quality assured!
                </h5>
            </div>

            <div class="col-4 px-3 choose-us">
                <img src="images/easy-return_128.png" class="secondary">
                <h5 class="text-center mt-3">
                    Still having doubts? No problem! We offer 30 day free returns!
                </h5>
            </div>
        </div>
    </div>

    <div>
        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="https://c1.neweggimages.com/WebResource/Themes/Nest/bgs/Illus_Subscription@4x.png"
                                    class="img-fluid" alt="Deals illustration">
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title py-2">Deals Just For You!</h5>
                                <p class="card-text py-2">Sign up to receive exclusive offers!</p>
                                <form target="" name="Newsletter">
                                    <div class="input-group pe-5 py-2">
                                        <input type="text" class="form-control" placeholder="Enter your e-mail address"
                                            aria-label="Enter your e-mail address" name="LoginName" value=""
                                            maxlength="128">
                                        <button type="button" class="btn btn-primary">Sign Up</button>
                                    </div>
                                    <input type="hidden" value="1" name="Subscribe">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="logos">
                    <div class="logos-slide">
                        <img src="images/acer.svg" />
                        <img src="images/anker.svg" />
                        <img src="images/apple.svg" />
                        <img src="images/asus.svg" />
                        <img src="images/gigabyte.svg" />
                        <img src="images/lenovo.svg" />
                        <img src="images/microsoft.svg" />
                        <img src="images/rog.svg" />
                    </div>
                </div>
            </div>
        </div>
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

    <!-- Script for logo carousel -->
    <script>
        var copy = document.querySelector(".logos-slide").cloneNode(true);
        document.querySelector(".logos").appendChild(copy);
    </script>
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