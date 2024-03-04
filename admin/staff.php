<?php
include '../includes/db_connect.php';
session_start();

// Protect the page 
if (!isset($_SESSION['staff_id'])) {
    header('Location: index.php'); // Redirect to login if not logged in
}

$username = $_SESSION['username']; // Fetch the username
?>

<!DOCTYPE html>
<html>

<head>
    <title>Staff Control Panel</title>
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

        h1{
            font-size: 56px;
        }
    </style>
</head>

<body>
    <div class="row gap-5 w-75">
        <div class="col">
            <h1 class="text-light">Staff Control Panel</h1>
            <p class="text-light fs-4">Welcome,
                <?php echo $username; ?>
            </p>
        </div>

        <div class="col card d-flex flex-row justify-content-center align-items-center gap-5 py-4">
            <a href="products.php" class="btn btn-primary btn-lg">Manage Products</a>

            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="register.php" class="btn btn-secondary btn-lg">Register Manager</a>
            <?php endif; ?>

            <a href="logout.php" class="btn btn-danger btn-lg">Logout</a>
        </div>
    </div>
</body>

</html>