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

// Get all assigned emergencies (including in progress) filtered by department
$assignments_sql = "SELECT emergencies.*, users.Name as Reporter_Name 
                    FROM emergencies 
                    INNER JOIN users ON emergencies.User_Id = users.Id
                    WHERE emergencies.Emergency_Status IN ('Assigned', 'In Progress')
                    AND emergencies.Emergency_Department = '$responder_department'
                    ORDER BY 
                        CASE 
                            WHEN emergencies.Emergency_Status = 'Assigned' THEN 1
                            WHEN emergencies.Emergency_Status = 'In Progress' THEN 2
                        END,
                        emergencies.Reported_Time DESC";
$assignments_result = mysqli_query($conn, $assignments_sql);

// Handle status update
if(isset($_POST['update_status'])){
    $emergency_id = $_POST['emergency_id'];
    $new_status = $_POST['new_status'];

    $update_sql = "UPDATE emergencies SET Emergency_Status = '$new_status' WHERE Emergency_Id = $emergency_id";
    if(mysqli_query($conn, $update_sql)){
        // If status is resolved, check if all emergencies in department are resolved
        if($new_status == 'Resolved'){
            $check_sql = "SELECT COUNT(*) as count FROM emergencies WHERE Emergency_Department = '$responder_department' AND Emergency_Status IN ('Assigned', 'In Progress')";
            $check_result = mysqli_query($conn, $check_sql);
            $check_row = mysqli_fetch_assoc($check_result);
            if($check_row['count'] == 0){
                // All emergencies resolved, set all responders in department to available
                $update_all_responders = "UPDATE responders SET Responder_Status = 'Available' WHERE Department = '$responder_department'";
                mysqli_query($conn, $update_all_responders);
            }
        }
        echo "<script>alert('✅ Status updated successfully!');</script>";
        echo "<script>window.location.href='my_assignments.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating status!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Assignments | ERMS</title>

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
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="my_assignments.php"><i class="fas fa-tasks me-2"></i>My Assignments</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="completed_tasks.php"><i class="fas fa-check-circle me-2"></i>Completed Tasks</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="responder_profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">My Assignments</h2>
            <p class="text-muted">Manage all your assigned emergencies </p>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-8">
                <input type="text" class="form-control text-dark" id="searchAssignment" placeholder="🔍 Search by emergency type, location, or reporter...">
              </div>
              <div class="col-md-4">
                <select class="form-select" id="filterStatus">
                  <option value="all">All Status</option>
                  <option value="assigned">Assigned</option>
                  <option value="in progress">In Progress</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Assignments Table -->
        <div class="card shadow-lg">
          <div class="card-header bg-dark-custom text-white">
            <h5 class="mb-0">
              <i class="fas fa-clipboard-list me-2"></i>All Assignments 
              <span class="badge bg-warning text-dark"><?php echo $responder_department; ?> Dept.</span>
            </h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover align-middle" id="assignmentsTable">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Emergency Type</th>
                    <th>Department</th>
                    <th>Reported By</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Reported On</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if(mysqli_num_rows($assignments_result) > 0){
                      while($row = mysqli_fetch_assoc($assignments_result)){
                          $status = $row['Emergency_Status'];
                          if ($status == 'Assigned') {
                              $badgeClass = 'bg-warning text-dark';
                          } elseif ($status == 'In Progress') {
                              $badgeClass = 'bg-primary';
                          } else {
                              $badgeClass = 'bg-success';
                          }

                          $description = !empty($row['Description']) ? substr($row['Description'], 0, 50) . '...' : 'N/A';

                          echo "<tr>
                                  <td>{$row['Emergency_Id']}</td>
                                  <td><i class='fas fa-exclamation-circle text-danger me-2'></i>{$row['Emergency_Name']}</td>
                                  <td><span class='badge bg-info'>{$row['Emergency_Department']}</span></td>
                                  <td><i class='fas fa-user text-info me-1'></i>{$row['Reporter_Name']}</td>
                                  <td><i class='fas fa-map-marker-alt text-success me-1'></i>{$row['Location']}</td>
                                  <td>{$description}</td>
                                  <td><span class='badge {$badgeClass}'>{$status}</span></td>
                                  <td><i class='fas fa-calendar text-warning me-1'></i>{$row['Reported_Time']}</td>
                                  <td>
                                      <button class='btn btn-sm btn-primary' onclick='openStatusModal({$row['Emergency_Id']}, \"{$row['Emergency_Status']}\")'>
                                        <i class='fas fa-edit me-1'></i>Update
                                      </button>
                                      <button class='btn btn-sm btn-info' onclick='viewDetails({$row['Emergency_Id']})'>
                                        <i class='fas fa-eye me-1'></i>View
                                      </button>
                                  </td>
                              </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='9' class='text-center text-muted py-4'>
                              <i class='fas fa-inbox fs-1 text-secondary mb-3 d-block'></i>
                              <h5>No assignments found</h5>
                              <p>You have no emergency assignments for {$responder_department} Department at the moment</p>
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

  <!-- Status Update Modal -->
  <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-dark-custom text-white">
          <h5 class="modal-title" id="statusModalLabel">
            <i class="fas fa-edit me-2"></i>Update Emergency Status
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="POST" id="statusForm">
            <input type="hidden" id="emergency_id" name="emergency_id">
            
            <div class="mb-3">
              <label class="form-label"><i class="fas fa-tasks me-2"></i>Update Status</label>
              <select class="form-select" id="new_status" name="new_status" required>
                <option value="">Select Status...</option>
                <option value="In Progress">In Progress</option>
                <option value="Resolved">Resolved</option>
              </select>
            </div>

            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              <strong>Note:</strong> Once marked as Resolved, the emergency will be moved to completed tasks.
            </div>

            <button type="submit" name="update_status" class="btn btn-primary w-100">
              <i class="fas fa-check me-2"></i>Update Status
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function openStatusModal(emergencyId, currentStatus) {
      document.getElementById('emergency_id').value = emergencyId;
      
      const statusSelect = document.getElementById('new_status');
      if (currentStatus === 'Assigned') {
        statusSelect.value = 'In Progress';
      }
      
      new bootstrap.Modal(document.getElementById('statusModal')).show();
    }

    function viewDetails(emergencyId) {
      alert('Viewing details for Emergency ID: ' + emergencyId);
      // You can implement a detail view modal here
    }

    // Search and Filter
    const searchInput = document.getElementById('searchAssignment');
    const filterStatus = document.getElementById('filterStatus');
    const rows = document.querySelectorAll('#assignmentsTable tbody tr');

    function applyFilters(){
      const searchVal = searchInput.value.toLowerCase();
      const statusVal = filterStatus.value;

      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const isMatch = text.includes(searchVal);
        const statusBadge = row.querySelector('.badge');
        const status = statusBadge ? statusBadge.innerText.toLowerCase() : '';
        const statusMatch = (statusVal === 'all' || status === statusVal);
        row.style.display = (isMatch && statusMatch) ? '' : 'none';
      });
    }

    searchInput.addEventListener('input', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
  </script>
</body>
</html>