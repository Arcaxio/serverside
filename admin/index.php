<!DOCTYPE html>

<head>
    <title>Staff Login</title>
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

        h2 {
            font-size: 5rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row align-items-center gap-5">
            <div class="col border-end border-5 ">
                <h2 class="text-uppercase text-light d-flex justify-content-center">Staff</h2>
                <h2 class="text-uppercase text-light d-flex justify-content-center">Login</h2>
            </div>
            <div class="col">
                <div class="card p-3">
                    <form method="post" action="login.php">
                        <div class="form-group m-3">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group m-3">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group m-3">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>