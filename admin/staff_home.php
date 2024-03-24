<?php
include '../includes/db_connect.php';
session_start();

// Protect the page
if (!isset ($_SESSION['staff_id'])) {
  header('Location: index.php');
}

$username = $_SESSION['username']; // Fetch the username

// Function to fetch revenue data for this month
function getRevenueData($conn)
{
  // Get the current month and year
  $currentMonth = date('m');
  $currentYear = date('Y');

  // Array to store revenue data for each day of the month
  $revenueData = [
    'days' => [],
    'revenue' => []
  ];

  // Get the total revenue for each day of the current month
  for ($day = 1; $day <= date('t'); $day++) {
    // Construct the start and end date for the current day
    $startDate = date('Y-m-d', mktime(0, 0, 0, $currentMonth, $day, $currentYear));
    $endDate = date('Y-m-d', mktime(23, 59, 59, $currentMonth, $day, $currentYear));

    // Query to fetch total revenue for the current day
    $stmt = $conn->prepare("SELECT SUM(total_amount) AS total_revenue FROM orders WHERE order_date BETWEEN ? AND ?");
    $stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    $totalRevenue = $stmt->fetchColumn();

    // Store the day and revenue in the array
    $revenueData['days'][] = "Day $day";
    $revenueData['revenue'][] = $totalRevenue ? $totalRevenue : 0; // If no revenue, set to 0
  }

  return $revenueData;
}

// Call the function and pass the database connection object
$revenueData = getRevenueData($conn);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Staff Home</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="row m-0 min-vh-100">
    <div class="col-2 p-0 bg-dark-subtle">
      <div class="p-4 mx-4 fw-medium">
        <i class="bi bi-app-indicator fs-4 me-3"></i>
        <span class="ms-2 fs-4">Boom Inc</span>
      </div>
      <div class="mx-4">
        <div class="p-3 border rounded rounded-3 bg-white" onclick="location.href='staff_home.php';" style="cursor: pointer;">
          <i class="bi bi-house-door me-3"></i>
          Home
        </div>
        <div class="p-3" onclick="location.href='orders.php';" style="cursor: pointer;">
          <i class="bi bi-cart me-3"></i>
          Orders
        </div>
        <div class="p-3" onclick="location.href='products.php';" style="cursor: pointer;">
          <i class="bi bi-box-seam me-3"></i>
          Product
        </div>
        <?php if ($_SESSION['role'] == 'admin'): ?>
          <a href="register.php" class="my-3 btn btn-secondary">Register Manager</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-10 p-0 bg-body-secondary">
      <div class="d-flex justify-content-between align-items-center">
        <div class="fw-medium fs-3 p-3 mx-3">
          Staff Home
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
        <div class="row">
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-header bg-primary text-white">
                Revenue This Month
              </div>
              <div class="card-body">
                <canvas id="revenueChart"></canvas>
                <script>
                  const revenueChart = document.getElementById('revenueChart');
                  const ctx = revenueChart.getContext('2d');

                  // Chart data retrieved from PHP
                  const chartData = {
                    labels: <?php echo json_encode($revenueData['days']); ?>,
                    datasets: [{
                      label: 'Revenue',
                      data: <?php echo json_encode($revenueData['revenue']); ?>,
                      backgroundColor: 'rgba(75, 192, 192, 0.2)',
                      borderColor: 'rgba(75, 192, 192, 1)',
                      borderWidth: 1,
                    }]
                  };

                  new Chart(ctx, {
                    type: 'bar',
                    data: chartData,
                    options: {
                      scales: {
                        yAxes: [{
                          ticks: {
                            beginAtZero: true
                          }
                        }]
                      }
                    }
                  });
                </script>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-4">
    <div class="text-muted">
      <p><b>Total Sales Revenue: RM</b> <?php echo array_sum($revenueData['revenue']); ?></p>
    </div>
  </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>