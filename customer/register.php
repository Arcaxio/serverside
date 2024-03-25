<?php
include '../includes/db_connect.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $zipcode = $_POST['zipcode'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty ($username) || empty ($email) || empty ($password) || empty ($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Prepare a SQL statement
        $stmt = $conn->prepare("INSERT INTO users (username, name, email, address, state, city, zipcode, phone_number, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $fullname);
        $stmt->bindParam(3, $email);
        $stmt->bindParam(4, $address);
        $stmt->bindParam(5, $state);
        $stmt->bindParam(6, $city);
        $stmt->bindParam(7, $zipcode);
        $stmt->bindParam(8, $phone_number);
        $stmt->bindParam(9, $hashed_password);

        if ($stmt->execute()) {
            // Set session variable to indicate logged-in state 
            $_SESSION['username'] = $username;
            // Redirect to index.php
            header("Location: ../index.php");
            exit;
        } else {
            $error = "Error during registration. Please try again.";
        }
    }
}

// Include the JSON file
$citiesJson = file_get_contents('../cities.json');
$citiesData = json_decode($citiesJson, true);
$states = array_keys($citiesData);

// Step 7: Define a function to validate phone numbers
function isValidPhoneNumber($phoneNumber)
{
    // Validate phone number format (assuming 10 or 11-digit phone number)
    $phoneNumberPattern = '/^\d{10,11}$/'; // Accepts 10 or 11-digit phone number
    return preg_match($phoneNumberPattern, $phoneNumber);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Customer Registration</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Register</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty ($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <!-- Registration Form -->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <!-- Username -->
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" class="form-control" required>
                            </div>
                            <!-- Full Name -->
                            <div class="form-group">
                                <label for="fullname">Full Name:</label>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    placeholder="e.g: Full Name (as per IC/Passport)" required>
                            </div>
                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="e.g: mail@mail.com" required>
                            </div>
                            <!-- Address -->
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" class="form-control" name="address"
                                    placeholder="e.g: (House/apartment/flat number), (Street)" required>
                            </div>
                            <!-- New row -->
                            <div class="row">
                                <!-- State dropdown -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="state">State:</label>
                                        <select class="form-control" id="state" name="state" required
                                            onchange="this.form.submit()">
                                            <option value="">Select State</option>
                                            <?php
                                            $states = json_decode(file_get_contents('../cities.json'), true);
                                            foreach ($states as $state => $cities) {
                                                echo '<option value="' . $state . '"';
                                                if (isset ($_POST['state']) && $_POST['state'] == $state) {
                                                    echo ' selected';
                                                }
                                                echo '>' . $state . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- City dropdown -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city">City:</label>
                                        <select class="form-control" id="city" name="city" required>
                                            <option value="">Select City</option>
                                            <?php
                                            if (isset ($_POST['state']) && $_POST['state'] != '') {
                                                $selectedState = $_POST['state'];
                                                foreach ($states[$selectedState] as $city) {
                                                    echo '<option value="' . $city . '"';
                                                    if (isset ($_POST['city']) && $_POST['city'] == $city) {
                                                        echo ' selected';
                                                    }
                                                    echo '>' . $city . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Zip Code -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="zipcode">Zip Code:</label>
                                        <input type="text" class="form-control" name="zipcode" placeholder="e.g: 12345"
                                            pattern="\d{5}" required>
                                    </div>
                                </div>
                            </div><!-- Row end -->

                            <!-- Phone Number -->
                            <div class="form-group">
                                <label for="phone_number">Phone Number:</label>
                                <input type="tel" class="form-control" name="phone_number" placeholder="e.g: 0123456789"
                                    pattern="\d{10,11}" required>
                            </div>
                            <!-- Password -->
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <!-- Confirm Password -->
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password"
                                    class="form-control" required>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>