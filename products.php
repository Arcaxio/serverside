<?php 
include 'includes/db_connect.php'; 
?>

<!DOCTYPE html>
<html>
<head>
  <title>Products</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"> 
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
                          <img src="images/<?php echo $row['image_path']; ?>" class="card-img-top" alt="...">
                          <div class="card-body">
                              <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                              <p class="card-text">$<?php echo $row['price']; ?></p>
                              <a href="product-details.php?id=<?php echo $row['product_id']; ?>" class="btn btn-primary">View Details</a>
                          </div>
                      </div>
                  </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
