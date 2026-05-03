<?php
include '../include/db.php';

session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// Total Responders Count
$total_responders_sql = "SELECT * FROM responders";
$total_responders_result = mysqli_query($conn, $total_responders_sql);
$total_responders = mysqli_num_rows($total_responders_result);

// Available Responders Count
$available_responders_sql = "SELECT * FROM responders WHERE Responder_Status = 'available'";
$available_responders_result = mysqli_query($conn, $available_responders_sql);
$available_responders = mysqli_num_rows($available_responders_result);

// Busy Responders Count
$busy_responders_sql = "SELECT * FROM responders WHERE Responder_Status = 'busy'";
$busy_responders_result = mysqli_query($conn, $busy_responders_sql);
$busy_responders = mysqli_num_rows($busy_responders_result);

// Assigned Reports Count
$emergency_reports_sql = "SELECT * FROM emergencies WHERE Emergency_Status = 'Assigned'";
$emergency_reports_result = mysqli_query($conn, $emergency_reports_sql);
$emergency_reports = mysqli_num_rows($emergency_reports_result);

// Fetch all responders
$total_responders_sql = "SELECT responders.Responder_Id, responders.Department, responders.Experience, responders.Responder_Status, users.Name, users.Phone, responders.Location, users.Join_Date FROM responders
INNER JOIN users ON responders.User_Id = users.Id";
$total_responders_result = mysqli_query($conn, $total_responders_sql);

// Fetch all emergencies for modal
$emergencies_sql = "SELECT emergencies.Emergency_Id, emergencies.Emergency_Name, emergencies.Emergency_Department, users.Name FROM emergencies
INNER JOIN users ON emergencies.User_Id = users.Id 
WHERE Emergency_Status != 'Resolved' AND Emergency_Status != 'Under Control' AND Emergency_Status != 'Assigned'";
$emergencies_result = mysqli_query($conn, $emergencies_sql);

if (isset($_POST['assign_report'])) {
    $emergencyId = $_POST['emergency_select'];
    $responder_name = $_POST['responder_name'];

    // Update query
    $update_sql = "
        UPDATE emergencies 
        SET Emergency_Status = 'Assigned' 
        WHERE Emergency_Id = '$emergencyId'
    ";

    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('✅ Report has been successfully assigned to $responder_name!');</script>";

        // Update responder status to 'busy'
        $update_responder_sql = "
            UPDATE responders 
            SET Responder_Status = 'Busy' 
            WHERE Responder_Id = (SELECT Responder_Id FROM users WHERE Name = '$responder_name')
        ";
        mysqli_query($conn, $update_responder_sql);
    } else {
        echo "<script>alert('❌ Error assigning report. Please try again.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responders | ERMS</title>

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
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="responders.php"><i class="fas fa-users me-2"></i>Responders</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="reports_analytics.php"><i class="bi bi-bar-chart-line me-2"></i> Reports & Analytics</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="new_emergency.php"><i class="bi bi-exclamation-triangle-fill me-2"></i> New Emergencies</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">Responders Management</h2>
            <p class="text-muted">Manage and assign emergency responders</p>
          </div>
          <button class="btn btn-danger" data-bs-toggle="modal">
            <i class="fas fa-plus-circle me-2"></i><a href="add_responder.php" class="text-white decoration-none">Add Responder</a>
          </button>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card total-emergencies">
              <div class="stat-icon">
                <i class="fas fa-user-shield"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $total_responders; ?></h3>
                <p class="stat-label">Total Responders</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card resolved">
              <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $available_responders; ?></h3>
                <p class="stat-label">Available</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card pending">
              <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $busy_responders; ?></h3>
                <p class="stat-label">Busy</p>
              </div>
            </div>
          </div>
          <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card response-time">
              <div class="stat-icon">
                <i class="fas fa-clipboard-check"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number"><?php echo $emergency_reports; ?></h3>
                <p class="stat-label">Assigned Reports</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-lg mb-4">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <input type="text" class="form-control text-black" id="searchResponder" placeholder="🔍 Search responder by name, department, or location...">
              </div>
              <div class="col-md-3">
                <select class="form-select" id="filterStatus">
                  <option value="all">All Status</option>
                  <option value="available">Available</option>
                  <option value="busy">Busy</option>
                </select>
              </div>
              <div class="col-md-3">
                <button class="btn btn-outline-secondary w-100" onclick="applyFilters()">
                  <i class="fas fa-filter me-2"></i>Apply Filters
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Responders Table -->
        <div class="card shadow-lg">
          <div class="card-header bg-dark-custom text-white">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Responder List</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover align-middle" id="respondersTable">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Responder Name</th>
                    <th>Department</th>
                    <th>Experience</th>
                    <th>Status</th>
                    <th>Join Date</th>
                    <th>Location</th>
                    <th>Phone</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if($total_responders > 0){
                      mysqli_data_seek($total_responders_result, 0);
                      while($row = mysqli_fetch_assoc($total_responders_result)){
                          $status = $row['Responder_Status'];

                          if ($status == 'Available') {
                              $badgeClass = 'bg-success';
                          } elseif ($status == 'Busy') {
                              $badgeClass = 'bg-warning text-dark';
                          } else {
                              $badgeClass = 'bg-secondary';
                          }

                          echo "<tr>
                                  <td>{$row['Responder_Id']}</td>
                                  <td><i class='fas fa-user-circle text-primary me-2'></i>{$row['Name']}</td>
                                  <td>{$row['Department']}</td>
                                  <td>{$row['Experience']}</td>
                                  <td><span class='badge {$badgeClass}'>{$row['Responder_Status']}</span></td>
                                  <td>{$row['Join_Date']}</td>
                                  <td><i class='fas fa-map-marker-alt text-danger me-1'></i>{$row['Location']}</td>
                                  <td><i class='fas fa-phone text-success me-1'></i>{$row['Phone']}</td>
                                  <td>
                                      <button class='btn btn-sm btn-primary' onclick='openAssignModal(this)' data-name='{$row['Name']}' data-dept='{$row['Department']}'>
                                        <i class='fas fa-tasks me-1'></i>Assign
                                      </button>
                                  </td>
                              </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='9' class='text-center text-muted'>No responders found</td></tr>";
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

  <!-- Assign Modal -->
  <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-dark-custom text-white">
          <h5 class="modal-title" id="assignModalLabel">
            <i class="fas fa-clipboard-check me-2"></i>Assign Emergency Report
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label"><i class="fas fa-user me-2"></i>Responder Name</label>
              <input type="text" id="responderName" class="form-control" name="responder_name" readonly>
            </div>

            <div class="mb-3">
              <label class="form-label"><i class="fas fa-exclamation-triangle me-2"></i>Select Emergency Report</label>
              <select class="form-select" id="emergencySelect" name="emergency_select" required>
                <option value="" data-dept="">Choose report...</option>
                <?php 
                mysqli_data_seek($emergencies_result, 0);
                while ($emergency = mysqli_fetch_assoc($emergencies_result)) { ?>
                  <option value="<?php echo $emergency['Emergency_Id']; ?>" data-dept="<?php echo $emergency['Emergency_Department']; ?>">
                    <?php echo $emergency['Emergency_Name']; ?> (<?php echo $emergency['Emergency_Department']; ?>)
                  </option>
                <?php } ?>
              </select>
            </div>

            <button type="submit" name="assign_report" class="btn btn-primary w-100">
              <i class="fas fa-check me-2"></i>Assign Now
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function openAssignModal(button) {
      const name = button.getAttribute('data-name');
      const dept = button.getAttribute('data-dept');
      document.getElementById('responderName').value = name;
      const options = document.querySelectorAll('#emergencySelect option');
      options.forEach(option => {
        const optionDept = option.getAttribute('data-dept');
        if (optionDept === dept || option.value === '') {
          option.style.display = '';
        } else {
          option.style.display = 'none';
        }
      });
      document.getElementById('emergencySelect').value = '';
      new bootstrap.Modal(document.getElementById('assignModal')).show();
    }

    // Search + Filter
    const searchInput = document.getElementById('searchResponder');
    const filterStatus = document.getElementById('filterStatus');
    const rows = document.querySelectorAll('#respondersTable tbody tr');

    function applyFilters(){
      const searchVal = searchInput.value.toLowerCase();
      const statusVal = filterStatus.value;

      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const isMatch = text.includes(searchVal);
        const status = row.querySelector('.badge') ? row.querySelector('.badge').innerText.toLowerCase() : '';
        const statusMatch = (statusVal === 'all' || status === statusVal);
        row.style.display = (isMatch && statusMatch) ? '' : 'none';
      });
    }

    searchInput.addEventListener('input', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
  </script>
</body>
</html>