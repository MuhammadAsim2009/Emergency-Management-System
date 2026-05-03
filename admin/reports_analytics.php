<?php
include '../include/db.php';

session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// ====== COUNTS ======

// Total
$count_sql = "SELECT * FROM emergencies";
$count_result = mysqli_query($conn, $count_sql);
$count = mysqli_num_rows($count_result);

// Pending
$pending_count_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'Pending'";
$pending_count_result = mysqli_query($conn, $pending_count_sql);
$pending_count = mysqli_num_rows($pending_count_result);

// In Progress
$progress_count_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'In Progress'";
$progress_count_result = mysqli_query($conn, $progress_count_sql);
$progress_count = mysqli_num_rows($progress_count_result);

// Resolved
$resolved_count_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'Resolved'";
$resolved_count_result = mysqli_query($conn, $resolved_count_sql);
$resolved_count = mysqli_num_rows($resolved_count_result);

// Viewed
$view_count_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'Viewed'";
$view_count_result = mysqli_query($conn, $view_count_sql);
$view_count = mysqli_num_rows($view_count_result);

// Assigned
$assigned_count_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'Assigned'";
$assigned_count_result = mysqli_query($conn, $assigned_count_sql);
$assigned_count = mysqli_num_rows($assigned_count_result);

// ====== DEPARTMENT COUNTS ======
$department_sql = "SELECT Emergency_Department, COUNT(*) as count FROM emergencies GROUP BY Emergency_Department";
$department_result = mysqli_query($conn, $department_sql);

$category_labels = [];
$department_values = [];

while($row = mysqli_fetch_assoc($department_result)){
  $category_labels[] = $row['Emergency_Department'];
  $department_values[] = $row['count'];
}

// ====== RECENT ======
$recent_count_sql = "SELECT emergencies.Emergency_Id, emergencies.Emergency_Name, emergencies.Emergency_Department, emergencies.Location, users.Name, emergencies.Emergency_Status, emergencies.Reported_Time 
FROM emergencies
INNER JOIN users ON emergencies.User_Id = users.Id 
ORDER BY Reported_Time DESC LIMIT 5";
$recent_count_result = mysqli_query($conn, $recent_count_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports & Analytics | ERMS</title>

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
          <li class="nav-item mb-2"><a class="nav-link text-white" href="admin_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="manage_users.php"><i class="fas fa-list me-2"></i>Manage Users</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="manage-emergencies.php"><i class="fas fa-tasks me-2"></i>Manage Emergencies</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="responders.php"><i class="fas fa-users me-2"></i>Responders</a></li>
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="reports_analytics.php"><i class="bi bi-bar-chart-line me-2"></i> Reports & Analytics</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="new_emergency.php"><i class="bi bi-exclamation-triangle-fill me-2"></i> New Emergencies</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">Reports & Analytics</h2>
            <p class="text-muted">Comprehensive emergency statistics and insights</p>
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
                <h3 class="stat-number"><?php echo $count; ?></h3>
                <p class="stat-label">Total Emergencies</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card resolved">
              <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $resolved_count; ?></h3>
                <p class="stat-label">Resolved</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card pending">
              <div class="stat-icon">
                <i class="fas fa-clock"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $pending_count; ?></h3>
                <p class="stat-label">Pending</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card response-time">
              <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $progress_count; ?></h3>
                <p class="stat-label">In Progress</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="row g-4 mb-4">
          <div class="col-lg-6">
            <div class="card shadow-lg h-100">
              <div class="card-header bg-dark-custom text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Emergency Status Overview</h5>
              </div>
              <div class="card-body">
                <canvas id="statusChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card shadow-lg h-100">
              <div class="card-header bg-dark-custom text-white">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Emergencies by Department</h5>
              </div>
              <div class="card-body">
                <canvas id="departmentChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Report Table -->
        <div class="card shadow-lg">
          <div class="card-header bg-dark-custom text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Emergency Reports</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Reported By</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    if(mysqli_num_rows($recent_count_result) > 0){
                      while($row = mysqli_fetch_assoc($recent_count_result)){
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
                                  <td><i class='fas fa-exclamation-circle text-danger me-2'></i>{$row['Emergency_Name']}</td>
                                  <td><i class='fas fa-building text-primary me-1'></i>{$row['Emergency_Department']}</td>
                                  <td><i class='fas fa-map-marker-alt text-success me-1'></i>{$row['Location']}</td>
                                  <td><span class='badge {$badgeClass}'>{$status}</span></td>
                                  <td><i class='fas fa-user text-info me-1'></i>{$row['Name']}</td>
                                  <td><i class='fas fa-calendar text-warning me-1'></i>{$row['Reported_Time']}</td>
                                </tr>";
                      }
                    } else {
                        echo "<tr><td colspan='7' class='text-center text-muted'>No emergency reports found</td></tr>";
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

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Convert PHP data to JS
    const resolvedCount = <?= $resolved_count ?>;
    const progressCount = <?= $progress_count ?>;
    const pendingCount = <?= $pending_count ?>;
    const viewedCount = <?= $view_count ?>;
    const assignedCount = <?= $assigned_count ?>;
    const categoryLabels = <?= json_encode($category_labels) ?>;
    const departmentValues = <?= json_encode($department_values) ?>;

    // Status Chart
    new Chart(document.getElementById('statusChart'), {
      type: 'doughnut',
      data: {
        labels: ['Resolved', 'In Progress', 'Pending', 'Viewed', 'Assigned'],
        datasets: [{
          data: [resolvedCount, progressCount, pendingCount, viewedCount, assignedCount],
          backgroundColor: ['#2ecc71','#3498db','#f1c40f','#95a5a6','#0dcaf0']
        }]
      },
      options: {
        plugins:{legend:{position:'bottom'}},
        cutout:'70%'
      }
    });

    // Category Chart
    new Chart(document.getElementById('departmentChart'), {
      type: 'bar',
      data: {
        labels: categoryLabels,
        datasets: [{
          label: 'No. of Reports',
          data: departmentValues,
          backgroundColor: ['#e74c3c','#3498db','#9b59b6','#f1c40f','#2ecc71','#16a085','#8e44ad']
        }]
      },
      options: {
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true}}
      }
    });
  </script>
</body>
</html>