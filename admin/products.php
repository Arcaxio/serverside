<?php
include '../includes/db_connect.php';
session_start();

// Protect the page
if (!isset($_SESSION['staff_id'])) {
    header('Location: index.php');
}

// Handle actions (add, edit, delete)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        deleteProduct($conn, $_GET['id']);
    } elseif ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $product = getProduct($conn, $_GET['id']);
    }
}

// Handle form submissions 
if (isset($_POST['submit'])) {
    if ($_POST['product_id'] == '') {
        addProduct($conn);
    } else {
        updateProduct($conn);
    }
}

// Helper functions
function addProduct($conn)
{
    $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, image_path, category_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['product_name'],
        $_POST['description'],
        $_POST['price'],
        '', // Set image_path to empty for now
        $_POST['category_id']
    ]);
    header('Location: products.php');
}

function updateProduct($conn)
{
    $query = "UPDATE products SET product_name = ?, description = ?, price = ?, category_id = ? ";
    $params = [$_POST['product_name'], $_POST['description'], $_POST['price'], $_POST['category_id']];

    $query .= " WHERE product_id = ?";
    $params[] = $_POST['product_id'];

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    header('Location: products.php');
}

function deleteProduct($conn, $id)
{
    // TODO: Optionally delete the product image file
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    header('Location: products.php');
}

function getProduct($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Product Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Product Management</h1>

        <h3>Add Product</h3>
        <form method="post">
            <input type="hidden" name="product_id" value="<?php echo isset($product) ? $product['product_id'] : ''; ?>">
            <button type="submit" name="submit" class="btn btn-primary">Save Product</button>
        </form>

        <h3>Products</h3>
        <table class="table">
            <thead>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT * FROM products");
                while ($row = $stmt->fetch()) { ?>
                    <tr>
                        <td><a href="products.php?action=edit&id=<?php echo $row['product_id']; ?>">Edit</a></td>
                        <td><a href="products.php?action=delete&id=<?php echo $row['product_id']; ?>">Delete</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>