<?php
include 'includes/db_connect.php'; // Include database connection
?>

<!DOCTYPE html>
<html>

<head>
    <title>Online Order Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        img {
            height: 320px;
            width: 320px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <h1>Welcome to Our Store</h1>
        </header>

        <main>
            <section class="featured-products">
                <h2>Featured Products</h2>
                <div class="row">
                    <?php
                    $stmt = $conn->query("SELECT * FROM products LIMIT 4"); // Fetch featured products
                    while ($row = $stmt->fetch()) { ?>
                        <div class="col-md-3">
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
                                    <a href="product-details.php?id=<?php echo $row['product_id']; ?>"
                                        class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section>
            <?php
            ?>
        </main>

        <footer>
        </footer>

    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

</body>

</html>