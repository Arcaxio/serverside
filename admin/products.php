<?php
include '../includes/db_connect.php';
session_start();

// Protect the page
if (!isset ($_SESSION['staff_id'])) {
    header('Location: index.php');
}

$username = $_SESSION['username']; // Fetch the username

// Handle actions (add, edit, delete)
if (isset ($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isset ($_GET['id'])) {
        deleteProduct($conn, $_GET['id']);
    } elseif ($_GET['action'] == 'edit' && isset ($_GET['id'])) {
        $product = getProduct($conn, $_GET['id']);
    }
}

// Handle form submissions 
if (isset ($_POST['submit'])) {
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
        $_POST['image_path'],
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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

        table,
        th,
        td {
            border: 1px solid lightgray;
        }
    </style>
</head>

<body>
    <div class="container h-100">
        <div class="row py-3">
            <div class="col-6">
                <h1 class="text-light">Staff Control Panel</h1>
                <p class="text-light fs-4">Welcome,
                    <?php echo $username; ?>
                </p>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="register.php" class="btn btn-secondary">Register Manager</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-danger ms-4">Logout</a>
            </div>
        </div>

        <h1 class="text-light py-3">Product Management</h1>
        <div class="row bg-light p-4 rounded" style="--bs-bg-opacity: .75;">
            <div class="col-6 justify-content-around">
                <h3 class="mb-2">Add Product</h3>
                <form method="post">
                    <input type="hidden" name="product_id"
                        value="<?php echo isset ($product) ? $product['product_id'] : ''; ?>">
                    <div class="py-1">
                        <label for="product_name">Product Name:</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>

                    <div class="py-1">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>

                    <div class="py-1">
                        <label for="price">Price:</label>
                        <input type="number" class="form-control" id="price" name="price" step=".01" required>
                    </div>

                    <div class="py-1">
                        <label for="image_path">Image Link:</label>
                        <input type="text" class="form-control" id="image_path" name="image_path" required>
                    </div>

                    <div class="py-1">
                        <label for="category_id">Category:</label>
                        <select class="form-control" id="category_id" name="category_id">
                            <option value="1">Category 1</option>
                            <option value="2">Category 2</option>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary mt-3">Add Product</button>
                </form>

            </div>

            <div class="col-6">
                <h3 class="mb-3">Products</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th colspan="2" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->query("SELECT * FROM products");
                        while ($row = $stmt->fetch()) { ?>
                            <tr>
                                <td>
                                    <?php echo $row['product_name']; ?>
                                </td>
                                <td>
                                    <?php echo $row['category_id']; ?>
                                </td>
                                <td>
                                    <?php echo $row['price']; ?>
                                </td>
                                <td class="text-center"><a
                                        href="edit_product.php?id=<?php echo $row['product_id']; ?>">Edit</a>
                                </td>
                                <td class="text-center">
                                    <!-- <a href="products.php?action=delete&id=<?php echo $row['product_id']; ?>">Delete</a> -->
                                    <a href="" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                        data-product-id="<?php echo $row['product_id']; ?>">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" class="btn btn-danger" id="confirmDeleteButton">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const confirmModal = document.getElementById('confirmDeleteModal');
            const confirmButton = document.getElementById('confirmDeleteButton');

            confirmModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const productId = button.getAttribute('data-product-id');
                const deleteUrl = 'products.php?action=delete&id=' + productId;
                confirmButton.setAttribute('href', deleteUrl);
            });
        });
    </script>
</body>

</html>