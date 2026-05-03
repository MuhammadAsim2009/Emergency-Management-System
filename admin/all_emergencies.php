<?php
include '../include/db.php';
session_start();

// ✅ User Authentication
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// ✅ Fetch all emergencies
$emergencies_query = "
SELECT e.Emergency_Id, e.Emergency_Name, e.Location, u.Name AS UserName, e.Emergency_Status, e.Reported_Time
FROM emergencies e
INNER JOIN users u ON e.User_Id = u.Id
ORDER BY e.Reported_Time DESC
";
$emergencies_result = mysqli_query($conn, $emergencies_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Emergencies | ERMS</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      <span class="text-white me-3"><i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
      <a href="../logout.php" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-3 col-lg-2 sidebar pt-4" style="height: 100vh; position:fixed;">
      <ul class="nav flex-column">
        <li class="nav-item mb-2"><a href="admin_dashboard.php" class="nav-link"><i class="fas fa-home me-2"></i>Dashboard</a></li>
        <li class="nav-item mb-2"><a href="all_emergencies.php" class="nav-link active"><i class="fas fa-list me-2"></i>All Emergencies</a></li>
        <li class="nav-item mb-2"><a href="#" class="nav-link"><i class="fas fa-users me-2"></i>Responders</a></li>
        <li class="nav-item mb-2"><a href="map_view.php" class="nav-link"><i class="fas fa-map-marked-alt me-2"></i>Map View</a></li>
        <!-- <li class="nav-item mb-2"><a href="#" class="nav-link"><i class="fas fa-cog me-2"></i>Settings</a></li> -->
      </ul>
    </nav>

    <!-- Main Content -->
    <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
      <h2 class="fw-bold mb-3">All Emergencies</h2>

      <div class="card shadow-lg">
        <div class="card-header bg-dark-custom text-white">
          <i class="fas fa-list me-2"></i>Emergency Records
        </div>
        <div class="card-body table-responsive">
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
              if(mysqli_num_rows($emergencies_result) > 0){
                while($row = mysqli_fetch_assoc($emergencies_result)){
                  $status = $row['Emergency_Status'];
                  $badgeClass = $status == 'Pending' ? 'bg-warning text-dark' :
                                ($status == 'In Progress' ? 'bg-primary' :
                                ($status == 'Resolved' ? 'bg-success' : 'bg-secondary'));
                  echo "<tr>
                    <td>{$row['Emergency_Id']}</td>
                    <td>{$row['Emergency_Name']}</td>
                    <td>{$row['UserName']}</td>
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

    </main>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
