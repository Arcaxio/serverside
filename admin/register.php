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
    <title>Manager Registration</title>
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
                <div class="p-3" onclick="location.href='staff_home.php';"
                    style="cursor: pointer;">
                    <i class="bi bi-house-door me-3"></i>
                    Home
                </div>
                <div class="p-3" onclick="location.href='orders.php';" style="cursor: pointer;">
                    <i class="bi bi-cart me-3"></i>
                    Orders
                </div>
                <div class="p-3" onclick="location.href='products.php';" style="cursor: pointer;">
                    <i class="bi bi-box-seam me-3"></i>
                    Products
                </div>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="register.php" class="my-3 btn btn-secondary">Register Manager</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-10 p-0 bg-body-secondary">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-medium fs-3 p-3 mx-3">
                    Manager Registration
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
                <div class="px-3 py-2 border rounded rounded-3">
                    <form method="post" action="register_process.php">
                        <div class="form-group my-2">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group my-2">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary my-2">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>