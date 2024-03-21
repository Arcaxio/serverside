<?php
include '../includes/db_connect.php';
session_start();

if (!isset ($_SESSION['staff_id'])) {
    header('Location: index.php');
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Management</title>
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
                <div class="p-3 border rounded rounded-3 bg-white">
                    <i class="bi bi-cart me-3"></i>
                    Order
                </div>
                <div class="p-3" onclick="location.href='products.php';" style="cursor: pointer;">
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
                <div class="fw-medium fs-4 p-3 mx-3">
                    Recent Orders
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
                <div class="px-2 border rounded rounded-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Order</th>
                                <th scope="col">Customer Name</th>
                                <th scope="col">Date</th>
                                <th scope="col">Total</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td>Jacob</td>
                                <td>Thornton</td>
                                <td>@fat</td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td colspan="2">Larry the Bird</td>
                                <td>@twitter</td>
                            </tr>
                            <tr>
                                <th scope="row">1</th>
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td>Jacob</td>
                                <td>Thornton</td>
                                <td>@fat</td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td colspan="2">Larry the Bird</td>
                                <td>@twitter</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>