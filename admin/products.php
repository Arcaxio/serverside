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
        <div class="d-flex align-items-center justify-content-between py-3">
            <h1 class="text-light">Product Management</h1>
            <button onclick="window.location.href='staff.php'" class="btn btn-light me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-return-left mx-1" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5" />
                </svg>
                Back
            </button>
        </div>

        <div class="container bg-light h-100 p-4" style="--bs-bg-opacity: .75;">
            <h3>Add Product</h3>
            <div class="row justify-content-around">
                <form method="post">
                    <input type="hidden" name="product_id"
                        value="<?php echo isset($product) ? $product['product_id'] : ''; ?>">
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

            <h3 class="mt-5">Products</h3>
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
                            <td class="text-center"><a href="edit_product.php?id=<?php echo $row['product_id']; ?>">Edit</a>
                            </td>
                            <td class="text-center"><a
                                    href="products.php?action=delete&id=<?php echo $row['product_id']; ?>">Delete</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>