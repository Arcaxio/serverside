<?php
include 'includes/db_connect.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
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
        <h1>Our Products</h1>
        <div class="row">
            <?php
            $stmt = $conn->query("SELECT * FROM products");
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
                            <a href="product-details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-primary">View
                                Details</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>