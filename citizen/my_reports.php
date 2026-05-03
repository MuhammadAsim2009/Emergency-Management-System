<?php
include '../include/db.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'citizen'){
    header("Location: ../login.php");
    exit;
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

// Filter by status if specified
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$filter_condition = $filter_status != '' ? "AND Emergency_Status = '$filter_status'" : '';

// Fetch all reports for this user
$reports = mysqli_query($conn, "SELECT * FROM emergencies WHERE User_Id = '$user_id' $filter_condition ORDER BY Reported_Time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Reports | ERMS</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../admin/style.css">

  <style>
    .stat-content {
      flex: 1;
    }
    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0;
      color: var(--primary-dark);
    }

    /* === Fix for Filter Tabs === */
.filter-tabs .nav-link {
  color: #333 !important;
  background-color: #f8f9fa !important;
  border: 1px solid #dee2e6;
  border-radius: 30px;
  margin-right: 10px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.filter-tabs .nav-link:hover {
  background-color: #c11907ff !important;
  color: #fff !important;
  border-color: #c11907ff !important;
  transform: translateY(-2px);
}

.filter-tabs .nav-link.active {
  background-color: #c11907ff !important;
  color: #fff !important;
  border-color: #c11907ff !important;
  font-weight: 600;
  box-shadow: 0 3px 6px rgba(231, 76, 60, 0.3);
}



.filter-tabs .nav-link:focus {
  box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
  outline: none;
}

  </style>
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
          <li class="nav-item mb-2"><a class="nav-link text-white" href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="report_emergency.php"><i class="fas fa-plus-circle me-2"></i>Report Emergency</a></li>
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="my_reports.php"><i class="fas fa-file-alt me-2"></i>My Reports</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">My Reports</h2>
            <p class="text-muted">View all your reported emergencies</p>
          </div>
          <a href="report_emergency.php" class="btn btn-danger">
            <i class="fas fa-plus me-2"></i>Report New Emergency
          </a>
        </div>

        <!-- Filter Tabs -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <ul class="nav nav-pills filter-tabs" id="statusFilter">
              <li class="nav-item">
                <a class="nav-link <?php echo $filter_status == '' ? 'active' : ''; ?>" href="my_reports.php">All</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $filter_status == 'Pending' ? 'active' : ''; ?>" href="my_reports.php?status=Pending">Pending</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $filter_status == 'In Progress' ? 'active' : ''; ?>" href="my_reports.php?status=In Progress">In Progress</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $filter_status == 'Resolved' ? 'active' : ''; ?>" href="my_reports.php?status=Resolved">Resolved</a>
              </li>
            </ul>
          </div>
        </div>

        <!-- Reports Grid -->
        <div class="row g-4">
          <?php
          if(mysqli_num_rows($reports) > 0){
            while($r = mysqli_fetch_assoc($reports)){
              $badgeClass = match($r['Emergency_Status']) {
                'Pending' => 'bg-warning text-dark',
                'In Progress' => 'bg-primary',
                'Resolved' => 'bg-success',
                default => 'bg-secondary'
              };
              
              // Get icon based on emergency type
              $icon = 'exclamation-circle';
              if (stripos($r['Emergency_Name'], 'fire') !== false) $icon = 'fire';
              elseif (stripos($r['Emergency_Name'], 'medical') !== false || stripos($r['Emergency_Name'], 'health') !== false) $icon = 'heartbeat';
              elseif (stripos($r['Emergency_Name'], 'accident') !== false || stripos($r['Emergency_Name'], 'crash') !== false) $icon = 'car-crash';
              elseif (stripos($r['Emergency_Name'], 'police') !== false || stripos($r['Emergency_Name'], 'crime') !== false) $icon = 'shield-alt';
              elseif (stripos($r['Emergency_Name'], 'natural') !== false) $icon = 'wind';

              echo "
              <div class='col-md-6 col-lg-4'>
                <div class='report-card shadow-sm'>
                  <div class='report-header'>
                    <span class='report-id'><i class='fas fa-hashtag me-1'></i>#{$r['Emergency_Id']}</span>
                    <span class='badge {$badgeClass}'>{$r['Emergency_Status']}</span>
                  </div>
                  <div class='report-body'>
                    <div class='text-center mb-3'>
                      <i class='fas fa-{$icon} fa-3x text-danger'></i>
                    </div>
                    <h5>{$r['Emergency_Name']}</h5>
                    <p class='text-muted mb-2'><i class='fas fa-map-marker-alt me-1'></i>{$r['Location']}</p>
                    ";
              if (!empty($r['Description'])) {
                $desc = substr($r['Description'], 0, 80);
                echo "<p class='text-muted small mb-2'>" . htmlspecialchars($desc) . (strlen($r['Description']) > 80 ? '...' : '') . "</p>";
              }
              echo "
                    <small class='text-muted'><i class='fas fa-clock me-1'></i>Reported: {$r['Reported_Time']}</small>
                  </div>
                  <div class='report-footer'>
                    <small class='text-muted'>Emergency ID: {$r['Emergency_Id']}</small>
                  </div>
                </div>
              </div>";
            }
          } else {
            echo "<div class='col-12'><div class='alert alert-info text-center'><i class='fas fa-info-circle me-2'></i>You haven't reported any emergencies yet. <a href='report_emergency.php' class='alert-link'>Report your first emergency</a></div></div>";
          }
          ?>
        </div>
      </main>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
