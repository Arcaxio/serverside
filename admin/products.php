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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="row m-0 min-vh-100">
        <div class="col-2 p-0 bg-dark-subtle">
            <div class="p-4 mx-4 fw-medium">
                <i class="bi bi-app-indicator fs-4 me-3"></i>
                <span class="ms-2 fs-4">Boom Inc</span>
                </a>
            </div>
            <div class="mx-4">
                <div class="p-3">
                    <i class="bi bi-house-door me-3"></i>
                    Home
                </div>
                <div class="p-3" onclick="location.href='orders.php';" style="cursor: pointer;">
                    <i class="bi bi-cart me-3"></i>
                    Order
                </div>
                <div class="p-3 border rounded rounded-3 bg-white">
                    <i class="bi bi-box-seam me-3"></i>
                    Product
                </div>
                <div class="p-3">
                    <i class="bi bi-people me-3"></i>
                    Customer
                </div>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="register.php" class="my-3 btn btn-secondary">Register Manager</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-10 p-0 bg-body-secondary">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-medium fs-3 p-3 mx-3">
                    Product Management
                </div>
                <div class="px-4 d-flex align-items-center">
                    <span class="fs-6 fw-medium pe-4">Welcome,
                        <?php echo $username; ?>
                    </span>
                    <a href="logout.php" class="btn btn-outline-dark">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white p-4">
                <div class="p-3 border rounded rounded-3">

                    <div class="row">
                        <div class="col-6 justify-content-around">
                            <h4 class="mb-2">Add Product</h4>
                            <form method="post">
                                <input type="hidden" name="product_id"
                                    value="<?php echo isset ($product) ? $product['product_id'] : ''; ?>">
                                <div class="py-1">
                                    <label for="product_name">Product Name:</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name"
                                        required>
                                </div>

                                <div class="py-1">
                                    <label for="description">Description:</label>
                                    <textarea class="form-control" id="description" name="description"></textarea>
                                </div>

                                <div class="py-1">
                                    <label for="price">Price:</label>
                                    <input type="number" class="form-control" id="price" name="price" step=".01"
                                        required>
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
                            <h4 class="mb-3">Products</h4>
                            <table class="table border">
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