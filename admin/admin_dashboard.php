<?php
include '../include/db.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// ✅ Total Counts
$count_sql = "SELECT * FROM emergencies";
$count_result = mysqli_query($conn, $count_sql);
$count = mysqli_num_rows($count_result);

$pending_count_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'Pending'";
$pending_count_result = mysqli_query($conn, $pending_count_sql);
$pending_count = mysqli_num_rows($pending_count_result);

$progress_count_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'In Progress'";
$progress_count_result = mysqli_query($conn, $progress_count_sql);
$progress_count = mysqli_num_rows($progress_count_result);

$resolved_count_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'Resolved'";
$resolved_count_result = mysqli_query($conn, $resolved_count_sql);
$resolved_count = mysqli_num_rows($resolved_count_result);

// ✅ Recent Emergencies
$recent_count_sql = "
  SELECT emergencies.Emergency_Id, emergencies.Emergency_Name, emergencies.Location, users.Name, emergencies.Emergency_Status, emergencies.Reported_Time 
  FROM emergencies
  INNER JOIN users ON emergencies.User_Id = users.Id 
  ORDER BY Reported_Time DESC 
  LIMIT 5";
$recent_count_result = mysqli_query($conn, $recent_count_sql);

// ✅ 7-Day Trend for Line Chart
$trend_query = mysqli_query($conn, "
  SELECT DATE(Reported_Time) AS date, COUNT(*) AS count
  FROM emergencies
  WHERE Reported_Time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
  GROUP BY DATE(Reported_Time)
  ORDER BY date ASC
");

$dates = [];
$counts = [];
while($row = mysqli_fetch_assoc($trend_query)){
  $dates[] = $row['date'];
  $counts[] = $row['count'];
}

// ✅ Category Breakdown for Doughnut Chart
$category_query = mysqli_query($conn, "
  SELECT Emergency_Department AS category, COUNT(*) AS total
  FROM emergencies
  GROUP BY Emergency_Department
");
$categories = [];
$category_counts = [];
while($row = mysqli_fetch_assoc($category_query)){
  $categories[] = $row['category'];
  $category_counts[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | ERMS</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark-custom">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">
        <i class="fas fa-ambulance text-danger me-2"></i>Emergency Response
      </a>
      <div class="d-flex align-items-center">
        <span class="text-white me-3">
          <i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </span>
        <a href="../logout.php" class="btn btn-outline-light btn-sm">
          <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-3 col-lg-2 bg-dark-custom sidebar vh-100 position-fixed pt-4">
        <ul class="nav flex-column">
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="admin_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="manage_users.php"><i class="fas fa-list me-2"></i>Manage Users</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="manage-emergencies.php"><i class="fas fa-tasks me-2"></i>Manage Emergencies</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="responders.php"><i class="fas fa-users me-2"></i>Responders</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="reports_analytics.php"><i class="bi bi-bar-chart-line me-2"></i> Reports & Analytics</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="new_emergency.php"><i class="bi bi-exclamation-triangle-fill me-2"></i> New Emergencies</a></li>
        </ul>
      </nav>

      <!-- Main Dashboard -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">Admin Dashboard</h2>
            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
          </div>
        </div>

        <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-card total-emergencies">
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stat-content">
                                <!-- <h3 class="stat-number" data-target="245">0</h3> -->
                                <p class="stat-label">Total Emergencies</p>
                                <?php echo $count; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-card resolved">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <!-- <h3 class="stat-number" data-target="189">0</h3> -->
                                <p class="stat-label">Resolved</p>
                                <?php echo $resolved_count; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-card pending">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <!-- <h3 class="stat-number" data-target="56">0</h3> -->
                                <p class="stat-label">Pending</p>
                                <?php echo $pending_count; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                        <div class="stat-card response-time">
                            <div class="stat-icon">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                            <div class="stat-content">
                                <!-- <h3 class="stat-number" data-target="3.2">0</h3> -->
                                <p class="stat-label">In Progress</p>
                                <?php echo $progress_count; ?>

                            </div>
                        </div>
                    </div>
                </div>

        <!-- Charts -->
        <div class="row g-4 mb-4">
          <div class="col-lg-8">
            <div class="card shadow-lg h-100">
              <div class="card-header bg-dark-custom text-white"><h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Emergency Trends (Last 7 Days)</h5></div>
              <div class="card-body"><canvas id="lineChart"></canvas></div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card shadow-lg h-100">
              <div class="card-header bg-dark-custom text-white"><h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Emergency Categories</h5></div>
              <div class="card-body"><canvas id="doughnutChart"></canvas></div>
            </div>
          </div>
        </div>

        <!-- Recent Emergencies -->
        <div class="card shadow-lg">
          <div class="card-header bg-dark-custom text-white"><h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Emergencies</h5></div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Reported By</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Reported On</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if(mysqli_num_rows($recent_count_result) > 0){
                      while($row = mysqli_fetch_assoc($recent_count_result)){
                  // Badge Color
                $status = $row['Emergency_Status'];

                if ($status == 'Pending') {
                    $badgeClass = 'bg-warning text-dark';
                } elseif ($status == 'In Progress') {
                    $badgeClass = 'bg-primary';
                } elseif ($status == 'Resolved' || $status == 'Under Control') {
                    $badgeClass = 'bg-success';
                } elseif ($status == 'Viewed') {
                    $badgeClass = 'bg-secondary';
                } elseif ($status == 'Assigned') {
                    $badgeClass = 'bg-info';
                } else {
                    $badgeClass = 'bg-danger';
                }
                          echo "<tr>
                            <td>{$row['Emergency_Id']}</td>
                            <td>{$row['Emergency_Name']}</td>
                            <td>{$row['Name']}</td>
                            <td>{$row['Location']}</td>
                            <td><span class='badge {$badgeClass}'>{$status}</span></td>
                            <td>{$row['Reported_Time']}</td>
                          </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='6' class='text-center text-muted'>No emergencies found</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  const trendLabels = <?php echo json_encode($dates); ?>;
  const trendData = <?php echo json_encode($counts); ?>;
  const categoryLabels = <?php echo json_encode($categories); ?>;
  const categoryData = <?php echo json_encode($category_counts); ?>;

  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: { labels: trendLabels, datasets: [{ label: 'Emergencies', data: trendData, borderColor: '#e74c3c', backgroundColor: 'rgba(231,76,60,0.2)', tension: 0.3, fill: true }] },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });

  new Chart(document.getElementById('doughnutChart'), {
    type: 'doughnut',
    data: { labels: categoryLabels, datasets: [{ data: categoryData, backgroundColor: ['#e74c3c','#3498db','#2ecc71','#f1c40f','#9b59b6'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });
  </script>
</body>
</html>
