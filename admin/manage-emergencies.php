<?php
session_start();
// Require DB connection
include '../include/db.php';

// Optional: Protect page for admins only (adjust as needed)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Handle status update action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $emergencyId = isset($_POST['emergency_id']) ? intval($_POST['emergency_id']) : 0;
    $newStatus = isset($_POST['status']) ? $_POST['status'] : '';
    $assignedResponder = isset($_POST['assigned_responder']) ? trim($_POST['assigned_responder']) : null;

    if ($emergencyId > 0 && $newStatus !== '' && isset($conn)) {
        $stmt = mysqli_prepare($conn, "UPDATE emergencies SET Emergency_Status = ?, Assigned_Responder = COALESCE(?, Assigned_Responder) WHERE Emergency_Id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssi', $newStatus, $assignedResponder, $emergencyId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // If status is resolved, check if all emergencies in department are resolved
            if($newStatus == 'Resolved'){
                // Get the department of the resolved emergency
                $dept_sql = "SELECT Emergency_Department FROM emergencies WHERE Emergency_Id = $emergencyId";
                $dept_result = mysqli_query($conn, $dept_sql);
                $dept_row = mysqli_fetch_assoc($dept_result);
                $department = $dept_row['Emergency_Department'];

                // Check if all emergencies in department are resolved
                $check_sql = "SELECT COUNT(*) as count FROM emergencies WHERE Emergency_Department = '$department' AND Emergency_Status IN ('Assigned', 'In Progress')";
                $check_result = mysqli_query($conn, $check_sql);
                $check_row = mysqli_fetch_assoc($check_result);
                if($check_row['count'] == 0){
                    // All emergencies resolved, set all responders in department to available
                    $update_all_responders = "UPDATE responders SET Responder_Status = 'Available' WHERE Department = '$department'";
                    mysqli_query($conn, $update_all_responders);
                }
            }

            $update_success = true;
        } else {
            $update_error = mysqli_error($conn);
        }
    }
}

// Filters
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
$filterType = isset($_GET['type']) ? $_GET['type'] : 'all';

// Fetch emergencies
$emergencies = [];
if (isset($conn)) {
    $where = [];
    $params = [];
    $types = '';
    if ($filterStatus !== 'all') { $where[] = 'Emergency_Status = ?'; $params[] = $filterStatus; $types .= 's'; }
    if ($filterType !== 'all') { $where[] = 'Emergency_Department = ?'; $params[] = $filterType; $types .= 's'; }
    $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';
    $sql = "SELECT emergencies.Emergency_Id as id, emergencies.Emergency_Department as type, emergencies.Location as location, users.Name as reporter_name, emergencies.Reported_Time as created_at, emergencies.Emergency_Status as status, emergencies.Description as description FROM emergencies INNER JOIN users ON emergencies.User_Id = users.Id $whereSql ORDER BY emergencies.Reported_Time DESC";
    if (count($where)) {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) { $emergencies[] = $row; }
            mysqli_stmt_close($stmt);
        }
    } else {
        $result = mysqli_query($conn, $sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) { $emergencies[] = $row; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Emergencies - Emergency Response System</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
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
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="manage-emergencies.php"><i class="fas fa-tasks me-2"></i>Manage Emergencies</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="responders.php"><i class="fas fa-users me-2"></i>Responders</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="reports_analytics.php"><i class="bi bi-bar-chart-line me-2"></i> Reports & Analytics</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="new_emergency.php"><i class="bi bi-exclamation-triangle-fill me-2"></i> New Emergencies</a></li>
        </ul>
      </nav>

            <!-- Content -->
            <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
                    <div>
                        <h2 class="fw-bold mb-1">Manage Emergencies</h2>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (!empty($update_success)) : ?>
                    <div class="alert alert-success shadow-sm" role="alert" data-aos="fade-down">
                        <i class="fas fa-check-circle me-2"></i>Status updated successfully.
                    </div>
                <?php endif; ?>
                <?php if (!empty($update_error)) : ?>
                    <div class="alert alert-danger shadow-sm" role="alert" data-aos="fade-down">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($update_error); ?>
                    </div>
                <?php endif; ?>

                <!-- Emergencies Table -->
                <div class="card shadow-lg" data-aos="fade-up">
                    <div class="card-header bg-dark-custom text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Emergencies</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Reporter</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($emergencies)) : ?>
                                        <?php foreach ($emergencies as $em) : ?>
                                            <?php
                                                $statusClass = 'bg-secondary';
                                                if ($em['status'] === 'Pending') $statusClass = 'bg-warning';
                                                if ($em['status'] === 'In Progress') $statusClass = 'bg-primary';
                                                if ($em['status'] === 'Resolved') $statusClass = 'bg-success';
                                                if ($em['status'] === 'Cancelled') $statusClass = 'bg-dark';
                                                $typeClass = $em['type'] === 'Fire' ? 'bg-danger' : ($em['type'] === 'Medical' ? 'bg-info' : 'bg-dark');
                                                $sevClass = 'bg-secondary';
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($em['id']); ?></td>
                                                <td><span class="badge <?php echo $typeClass; ?>"><?php echo ucfirst(htmlspecialchars($em['type'])); ?></span></td>
                                                <td><?php echo htmlspecialchars($em['location']); ?></td>
                                                <td><?php echo htmlspecialchars($em['reporter_name']); ?></td>
                                                <td><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($em['created_at']))); ?></td>
                                                <td><span class="badge <?php echo $statusClass; ?>"><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($em['status']))); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">No emergencies found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script src="script.js"></script>
</body>
</html>


