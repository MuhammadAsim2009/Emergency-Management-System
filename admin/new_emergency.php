<?php
include '../include/db.php';

session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// Handle Mark as Read action
if(isset($_POST['mark_as_read'])){
    $emergency_id = $_POST['emergency_id'];
    $update_sql = "UPDATE emergencies SET Emergency_Status = 'Viewed' WHERE Emergency_Id = $emergency_id";
    mysqli_query($conn, $update_sql);
    header("Location: new_emergency.php");
    exit;
}

$sql = "SELECT emergencies.Emergency_Id, emergencies.Emergency_Name, emergencies.Location, emergencies.Emergency_Status, emergencies.Reported_Time, users.Name FROM emergencies
INNER JOIN users ON users.Id = emergencies.User_Id 
WHERE emergencies.Emergency_Status = 'Pending'
ORDER BY Reported_Time DESC";
$result = mysqli_query($conn, $sql);
$new_emergency_count = mysqli_num_rows($result);

// Get stats for cards
$total_sql = "SELECT * FROM emergencies";
$total_result = mysqli_query($conn, $total_sql);
$total_count = mysqli_num_rows($total_result);

$viewed_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'Viewed'";
$viewed_result = mysqli_query($conn, $viewed_sql);
$viewed_count = mysqli_num_rows($viewed_result);

$assigned_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'Assigned'";
$assigned_result = mysqli_query($conn, $assigned_sql);
$assigned_count = mysqli_num_rows($assigned_result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Emergencies | ERMS</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
          <li class="nav-item mb-2"><a class="nav-link text-white" href="reports_analytics.php"><i class="bi bi-bar-chart-line me-2"></i> Reports & Analytics</a></li>
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="new_emergency.php"><i class="bi bi-exclamation-triangle-fill me-2"></i> New Emergencies</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">New Emergencies</h2>
            <p class="text-muted">Review and acknowledge new emergency reports</p>
          </div>
          <?php if($new_emergency_count > 0): ?>
            <span class="badge bg-danger fs-5">
              <i class="fas fa-bell me-2"></i><?php echo $new_emergency_count; ?> New
            </span>
          <?php endif; ?>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card total-emergencies">
              <div class="stat-icon">
                <i class="fas fa-bell"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $new_emergency_count; ?></h3>
                <p class="stat-label">New Emergencies</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card resolved">
              <div class="stat-icon">
                <i class="fas fa-eye"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $viewed_count; ?></h3>
                <p class="stat-label">Viewed</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card pending">
              <div class="stat-icon">
                <i class="fas fa-tasks"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $assigned_count; ?></h3>
                <p class="stat-label">Assigned</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card response-time">
              <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $total_count; ?></h3>
                <p class="stat-label">Total Emergencies</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Emergencies Table -->
        <div class="card shadow-lg">
          <div class="card-header bg-dark-custom text-white">
            <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Pending Emergency Reports</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover align-middle" id="emergenciesTable">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Emergency</th>
                    <th>Reported By</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Reported On</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="emergenciesBody">
                  <?php
                  if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
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

                      echo "<tr id='row-{$row['Emergency_Id']}'>
                              <td>{$row['Emergency_Id']}</td>
                              <td><i class='fas fa-exclamation-circle text-danger me-2'></i>{$row['Emergency_Name']}</td>
                              <td><i class='fas fa-user text-primary me-2'></i>{$row['Name']}</td>
                              <td><i class='fas fa-map-marker-alt text-success me-1'></i>{$row['Location']}</td>
                              <td><span class='badge {$badgeClass}'>{$status}</span></td>
                              <td><i class='fas fa-calendar text-warning me-1'></i>{$row['Reported_Time']}</td>
                              <td>
                                <form method='POST' style='display:inline;'>
                                  <input type='hidden' name='emergency_id' value='{$row['Emergency_Id']}'>
                                  <button type='submit' class='btn btn-sm btn-primary' name='mark_as_read'>
                                    <i class='fas fa-check me-1'></i>Mark as Read
                                  </button>
                                </form>
                              </td>
                            </tr>";
                    }
                  } else {
                    echo "<tr><td colspan='7' class='text-center text-muted py-4'>
                            <i class='fas fa-check-circle fs-1 text-success mb-3 d-block'></i>
                            <h5>No new emergencies</h5>
                            <p>All emergency reports have been reviewed</p>
                          </td></tr>";
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
</body>
</html>