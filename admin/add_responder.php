<?php
include '../include/db.php';

session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// Handle form submission
if(isset($_POST['add_responder'])){
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    
    // Check if user exists and is not already a responder
    $check_user = "SELECT users.Id, users.Role FROM users WHERE users.Id = $user_id";
    $user_result = mysqli_query($conn, $check_user);
    
    if(mysqli_num_rows($user_result) == 0){
        echo "<script>alert('❌ User ID not found!');</script>";
    } else {
        $user = mysqli_fetch_assoc($user_result);
        
        // Check if user is already a responder
        $check_responder = "SELECT * FROM responders WHERE User_Id = $user_id";
        $responder_result = mysqli_query($conn, $check_responder);
        
        if(mysqli_num_rows($responder_result) > 0){
            echo "<script>alert('❌ This user is already a responder!');</script>";
        } else {
            // Insert into responders table
            $insert_responder = "INSERT INTO responders (User_Id, Department, Experience, Location, Responder_Status) 
                                VALUES ($user_id, '$department', '$experience', '$location', 'Available')";
            
            if(mysqli_query($conn, $insert_responder)){
                // Update user role to responder
                $update_role = "UPDATE users SET Role = 'responder' WHERE Id = $user_id";
                mysqli_query($conn, $update_role);
                
                echo "<script>alert('✅ Responder added successfully!');</script>";
                echo "<script>window.location.href='responders.php';</script>";
            } else {
                echo "<script>alert('❌ Error adding responder!');</script>";
            }
        }
    }
}

// Get counts for stats
$total_responders = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM responders"));
$available_responders = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM responders WHERE Responder_Status = 'Available'"));
$busy_responders = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM responders WHERE Responder_Status = 'Busy'"));

// Get all users who are not responders
$users_sql = "SELECT users.Id, users.Name, users.Email, users.Role 
              FROM users 
              WHERE users.Id NOT IN (SELECT User_Id FROM responders)
              ORDER BY users.Name ASC";
$users_result = mysqli_query($conn, $users_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Responder | ERMS</title>

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
          <li class="nav-item mb-2"><a class="nav-link text-white" href="manage-emergencies.php"><i class="fas fa-tasks me-2"></i>Manage Emergencies</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="manage_users.php"><i class="fas fa-users me-2"></i>Manage Users</a></li>
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="responders.php"><i class="fas fa-user-shield me-2"></i>Responders</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="reports_analytics.php"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="new_emergency.php"><i class="fas fa-exclamation-triangle me-2"></i>New Emergencies</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">Add New Responder</h2>
            <p class="text-muted">Assign responder role to existing users</p>
          </div>
          <a href="responders.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Responders
          </a>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
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
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
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
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
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
        </div>

        <div class="row">
          <!-- Add Responder Form -->
          <div class="col-md-6">
            <div class="card shadow-lg">
              <div class="card-header bg-dark-custom text-white">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add Responder</h5>
              </div>
              <div class="card-body">
                <form method="POST" id="responderForm">
                  <div class="mb-3">
                    <label class="form-label"><i class="fas fa-id-badge me-2"></i>User ID <span class="text-danger">*</span></label>
                    <input type="number" class="form-control text-dark" name="user_id" id="user_id" placeholder="Enter User ID" required>
                    <small class="text-muted">Select from available users table on the right</small>
                  </div>

                  <div class="mb-3">
                    <label class="form-label"><i class="fas fa-building me-2"></i>Department <span class="text-danger">*</span></label>
                    <select class="form-select" name="department" required>
                      <option value="">Select Department</option>
                      <option value="Fire">Fire Department</option>
                      <option value="Police">Police Department</option>
                      <option value="Medical">Medical Department</option>
                      <option value="Rescue">Rescue Department</option>
                      <option value="Disaster Management">Disaster Management</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label"><i class="fas fa-award me-2"></i>Experience <span class="text-danger">*</span></label>
                    <select class="form-select" name="experience" required>
                      <option value="">Select Experience Level</option>
                      <option value="0-1 years">0-1 years</option>
                      <option value="1-3 years">1-3 years</option>
                      <option value="3-5 years">3-5 years</option>
                      <option value="5-10 years">5-10 years</option>
                      <option value="10+ years">10+ years</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-dark" name="location" placeholder="Enter station or base location" required>
                  </div>

                  <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> The user will be assigned "responder" role and set to "Available" status.
                  </div>

                  <button type="submit" name="add_responder" class="btn btn-primary w-100">
                    <i class="fas fa-user-plus me-2"></i>Add Responder
                  </button>
                </form>
              </div>
            </div>
          </div>

          <!-- Available Users Table -->
          <div class="col-md-6">
            <div class="card shadow-lg">
              <div class="card-header bg-dark-custom text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Available Users</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <input type="text" class="form-control" id="searchUser" placeholder="🔍 Search users...">
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                  <table class="table table-hover table-sm align-middle" id="usersTable">
                    <thead class="table-dark sticky-top">
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if(mysqli_num_rows($users_result) > 0){
                          while($row = mysqli_fetch_assoc($users_result)){
                              $roleClass = $row['Role'] == 'admin' ? 'bg-danger' : 'bg-secondary';
                              echo "<tr>
                                      <td>{$row['Id']}</td>
                                      <td><i class='fas fa-user text-primary me-1'></i>{$row['Name']}</td>
                                      <td><small>{$row['Email']}</small></td>
                                      <td><span class='badge {$roleClass}'>{$row['Role']}</span></td>
                                      <td>
                                        <button class='btn btn-sm btn-primary' onclick='selectUser({$row['Id']})'>
                                          <i class='fas fa-hand-pointer'></i>
                                        </button>
                                      </td>
                                  </tr>";
                          }
                      } else {
                          echo "<tr><td colspan='5' class='text-center text-muted'>All users are already responders</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function selectUser(userId) {
      document.getElementById('user_id').value = userId;
      document.getElementById('user_id').focus();
      
      // Visual feedback
      const input = document.getElementById('user_id');
      input.classList.add('is-valid');
      setTimeout(() => {
        input.classList.remove('is-valid');
      }, 2000);
    }

    // Search functionality
    document.getElementById('searchUser').addEventListener('input', function(){
      const v = this.value.toLowerCase();
      document.querySelectorAll('#usersTable tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(v) ? '' : 'none';
      });
    });
  </script>
</body>
</html>