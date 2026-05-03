<?php
include '../include/db.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'responder'){
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$responder_name = $_SESSION['user_name'];

// Get responder's department
$dept_sql = "SELECT Department FROM responders WHERE User_Id = $user_id";
$dept_result = mysqli_query($conn, $dept_sql);
$dept_row = mysqli_fetch_assoc($dept_result);
$responder_department = $dept_row['Department'];

// Get all completed emergencies filtered by department
$completed_sql = "SELECT emergencies.*, users.Name as Reporter_Name 
                  FROM emergencies 
                  INNER JOIN users ON emergencies.User_Id = users.Id
                  WHERE emergencies.Emergency_Status = 'Resolved'
                  AND emergencies.Emergency_Department = '$responder_department'
                  ORDER BY emergencies.Reported_Time DESC";
$completed_result = mysqli_query($conn, $completed_sql);
$completed_count = mysqli_num_rows($completed_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Completed Tasks | ERMS</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../admin/style.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark-custom">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">
        <i class="fas fa-ambulance text-danger me-2"></i>Emergency Response - Responder
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
          <li class="nav-item mb-2"><a class="nav-link text-white" href="responder_dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="my_assignments.php"><i class="fas fa-tasks me-2"></i>My Assignments</a></li>
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="completed_tasks.php"><i class="fas fa-check-circle me-2"></i>Completed Tasks</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="responder_profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">Completed Tasks</h2>
            <p class="text-muted">View all your resolved emergencies </p>
          </div>
          <span class="badge bg-success fs-5">
            <i class="fas fa-check-circle me-2"></i><?php echo $completed_count; ?> Completed
          </span>
        </div>

        <!-- Stats Card -->
        <div class="row g-4 mb-4">
          <div class="col-md-12">
            <div class="stat-card resolved">
              <div class="stat-icon">
                <i class="fas fa-check-double"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $completed_count; ?></h3>
                <p class="stat-label">Total Completed Emergencies (<?php echo $responder_department; ?> Dept.)</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Search Bar -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-12">
                <input type="text" class="form-control text-dark" id="searchCompleted" placeholder="🔍 Search completed tasks by emergency type, location, or reporter...">
              </div>
            </div>
          </div>
        </div>

        <!-- Completed Tasks Table -->
        <div class="card shadow-lg">
          <div class="card-header bg-dark-custom text-white">
            <h5 class="mb-0">
              <i class="fas fa-check-circle me-2"></i>Resolved Emergencies 
              <span class="badge bg-success"><?php echo $responder_department; ?> Dept.</span>
            </h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover align-middle" id="completedTable">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Emergency Type</th>
                    <th>Department</th>
                    <th>Reported By</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Completed On</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if(mysqli_num_rows($completed_result) > 0){
                      while($row = mysqli_fetch_assoc($completed_result)){
                          $description = !empty($row['Description']) ? substr($row['Description'], 0, 50) . '...' : 'N/A';

                          echo "<tr>
                                  <td>{$row['Emergency_Id']}</td>
                                  <td><i class='fas fa-check-circle text-success me-2'></i>{$row['Emergency_Name']}</td>
                                  <td><span class='badge bg-info'>{$row['Emergency_Department']}</span></td>
                                  <td><i class='fas fa-user text-info me-1'></i>{$row['Reporter_Name']}</td>
                                  <td><i class='fas fa-map-marker-alt text-success me-1'></i>{$row['Location']}</td>
                                  <td>{$description}</td>
                                  <td><span class='badge bg-success'>Resolved</span></td>
                                  <td><i class='fas fa-calendar text-warning me-1'></i>{$row['Reported_Time']}</td>
                              </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='8' class='text-center text-muted py-4'>
                              <i class='fas fa-clipboard-check fs-1 text-secondary mb-3 d-block'></i>
                              <h5>No completed tasks yet</h5>
                              <p>Your resolved emergencies for {$responder_department} Department will appear here</p>
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
  <script>
    // Search functionality
    document.getElementById('searchCompleted').addEventListener('input', function(){
      const v = this.value.toLowerCase();
      document.querySelectorAll('#completedTable tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(v) ? '' : 'none';
      });
    });
  </script>
</body>
</html>