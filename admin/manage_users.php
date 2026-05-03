<?php
include '../include/db.php';

session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

$users_count_sql = "SELECT * FROM users";
$users_count_result = mysqli_query($conn, $users_count_sql);
$total_users = mysqli_num_rows($users_count_result);

// Count by role
$admin_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE Role = 'admin'"));
$user_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE Role = 'citizen'"));
$responder_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE Role = 'responder'"));

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users | ERMS</title>

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
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="manage_users.php"><i class="fas fa-list me-2"></i>Manage Users</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="manage-emergencies.php"><i class="fas fa-tasks me-2"></i>Manage Emergencies</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="responders.php"><i class="fas fa-users me-2"></i>Responders</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="reports_analytics.php"><i class="bi bi-bar-chart-line me-2"></i> Reports & Analytics</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="new_emergency.php"><i class="bi bi-exclamation-triangle-fill me-2"></i> New Emergencies</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">Manage Users</h2>
            <p class="text-muted">View and manage all system users</p>
          </div>
          <a href="../register.php" class="btn btn-danger">
            <i class="fas fa-user-plus me-2"></i>Add New User
          </a>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card total-emergencies">
              <div class="stat-icon">
                <i class="fas fa-users"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $total_users; ?></h3>
                <p class="stat-label">Total Users</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card resolved">
              <div class="stat-icon">
                <i class="fas fa-user-shield"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $admin_count; ?></h3>
                <p class="stat-label">Admins</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card pending">
              <div class="stat-icon">
                <i class="fas fa-user"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $user_count; ?></h3>
                <p class="stat-label">Citizens</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card response-time">
              <div class="stat-icon">
                <i class="fas fa-hands-helping"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $responder_count; ?></h3>
                <p class="stat-label">Responders</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Search Bar -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-12">
                <input type="text" class="form-control text-black" id="userSearch" placeholder="🔍 Search users by name, email, role, or phone...">
              </div>
            </div>
          </div>
        </div>

        <!-- Users Table -->
        <div class="card shadow-lg">
          <div class="card-header bg-dark-custom text-white">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>User List</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover align-middle" id="usersTable">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Join Date</th>
                    <!-- <th>Actions</th> -->
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if(mysqli_num_rows($users_count_result) > 0){
                      while($row = mysqli_fetch_assoc($users_count_result)){
                          // Role badge color
                          $role = $row['Role'];
                          if ($role == 'admin') {
                              $badgeClass = 'bg-danger';
                          } elseif ($role == 'responder') {
                              $badgeClass = 'bg-primary';
                          } else {
                              $badgeClass = 'bg-secondary';
                          }

                          echo "<tr>
                                  <td>{$row['Id']}</td>
                                  <td><i class='fas fa-user-circle text-primary me-2'></i>{$row['Name']}</td>
                                  <td><i class='fas fa-envelope text-success me-1'></i>{$row['Email']}</td>
                                  <td><span class='badge {$badgeClass}'>{$row['Role']}</span></td>
                                  <td><i class='fas fa-phone text-info me-1'></i>{$row['Phone']}</td>
                                  <td><i class='fas fa-map-marker-alt text-danger me-1'></i>{$row['Address']}</td>
                                  <td><i class='fas fa-calendar text-warning me-1'></i>{$row['Join_Date']}</td>
                                  <td>
                                      
                                  </td>
                              </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='8' class='text-center text-muted'>No users found</td></tr>";
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
    document.getElementById('userSearch').addEventListener('input', function(){
      const v = this.value.toLowerCase();
      document.querySelectorAll('#usersTable tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(v) ? '' : 'none';
      });
    });
  </script>
</body>
</html>