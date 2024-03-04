<?php
include '../includes/db_connect.php';
session_start();

// Protect the page
if (!isset($_SESSION['staff_id'])) {
    header('Location: index.php');
}

// Get product ID from the URL 
if (!isset($_GET['id'])) {
    header('Location: products.php'); // Redirect back if no product ID
}
$product_id = $_GET['id'];

// Fetch product details
$product = getProduct($conn, $product_id);

// Handle form submission (similar to how you handle it in products.php)
if (isset($_POST['submit'])) {
    updateProduct($conn);
}

function getProduct($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function updateProduct($conn)
{
    $query = "UPDATE products SET product_name = ?, description = ?, price = ?, category_id = ? ";
    $params = [$_POST['product_name'], $_POST['description'], $_POST['price'], $_POST['category_id']];

    $query .= " WHERE product_id = ?";
    $params[] = $_POST['product_id'];

    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    if ($stmt->execute($params)) {
        header('Location: products.php'); // Redirect on success
        return true;
    } else {
        return "Error updating product";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: rgb(5, 0, 100);
            background: radial-gradient(circle, rgba(0, 0, 0, 1) 0%, rgba(30, 30, 30, 1) 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card p-4">

            <h1>Edit Product</h1>
    
            <?php if (isset($_POST['submit'])) { ?>
                <div class="alert alert-danger">
                </div>
            <?php } ?>
    
            <form method="post">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
    
                <div class="form-group py-2">
                    <label for="product_name">Product Name:</label>
                    <input type="text" class="form-control" id="product_name" name="product_name"
                        value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                </div>
    
                <div class="form-group py-2">
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description"
                        required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
    
                <div class="form-group py-2">
                    <label for="price">Price:</label>
                    <input type="number" class="form-control" id="price" name="price"
                        value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
    
                <div class="form-group py-2">
                    <label for="category_id">Category:</label>
                    <input type="number" class="form-control" id="category_id" name="category_id"
                        value="<?php echo htmlspecialchars($product['category_id']); ?>" required>
                </div>
    
                <button type="submit" name="submit" class="btn btn-primary mt-3">Save Changes</button>
            </form>
        </div>
    </div>
</body>

</html>