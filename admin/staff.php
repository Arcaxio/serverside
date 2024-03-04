<?php
include '../includes/db_connect.php';
session_start();

// Protect the page 
if (!isset($_SESSION['staff_id'])) {
    header('Location: index.php'); // Redirect to login if not logged in
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Staff Control Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1>Staff Control Panel</h1>

        <p>Welcome,
            <?php echo $_SESSION['staff_id']; // Example: display a welcome message  ?>
        </p>

        <a href="products.php" class="btn btn-primary">Manage Products</a>

        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="register.php" class="btn btn-secondary">Register Manager</a>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>

</html>