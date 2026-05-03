<?php
include '../include/db.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'citizen'){
    header("Location: ../login.php");
    exit;
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE Id = '$user_id'"));

$success_msg = '';
$error_msg = '';

// Update profile information
if(isset($_POST['update_profile'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $update = mysqli_query($conn, "UPDATE users SET Name = '$name', Email = '$email', Phone = '$phone', Address = '$address' WHERE Id = '$user_id'");
    if($update){
        $success_msg = "Profile updated successfully!";
        $_SESSION['user_name'] = $name;
        // Refresh user data
        $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE Id = '$user_id'"));
    } else {
        $error_msg = "Failed to update profile: " . mysqli_error($conn);
    }
}

// Change password
if(isset($_POST['change_password'])){
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if(password_verify($current_pass, $user['Pass'])){
        if($new_pass == $confirm_pass){
            if(strlen($new_pass) >= 6){
                $hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);
                $update_pass = mysqli_query($conn, "UPDATE users SET Pass = '$hashed_pass' WHERE Id = '$user_id'");
                if($update_pass){
                    $success_msg = "Password changed successfully!";
                } else {
                    $error_msg = "Failed to update password: " . mysqli_error($conn);
                }
            } else {
                $error_msg = "New password must be at least 6 characters long.";
            }
        } else {
            $error_msg = "New password and confirm password do not match.";
        }
    } else {
        $error_msg = "Current password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile | ERMS</title>

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
    .profile-icon {
      width: 100px;
      height: 100px;
      background: linear-gradient(135deg, #c11907ff);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      color: white;
      margin: 0 auto 2rem;
      box-shadow: 0 10px 30px rgba(231, 76, 60, 0.3);
    }
    .section-card {
      border: none;
      border-radius: 15px;
      transition: all 0.3s ease;
    }
    .section-card:hover {
      transform: translateY(-5px);
    }
    /* === Profile Information Section Styling === */
.section-card {
  border: none;
  border-radius: 15px;
  background: #fff;
  transition: all 0.3s ease;
}

.section-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.section-card .card-header.bg-dark-custom {
  background: linear-gradient(135deg, #c11907ff) !important;
  color: #fff !important;
  border: none;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
}

/* Label + Icon styling fix */
.section-card .form-label {
  color: #333;
  font-weight: 600;
}

.section-card .form-label i {
  color: #c11907ff !important;
}

/* Input field hover/focus consistency */
.section-card .form-control {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.section-card .form-control:focus {
  border-color: ##c11907ff;
  box-shadow: 0 0 0 0.15rem rgba(231, 76, 60, 0.25);
}

/* Update button fix */
.section-card .btn-danger {
  background: linear-gradient(135deg, #c11907ff, #b12a1d);
  border: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.section-card .btn-danger:hover {
  background: #b12a1d;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
}
/* Fix invisible input text issue */
.section-card .form-control {
  color: #212529 !important; /* Bootstrap default text color */
  background-color: #fff !important;
}

/* Optional: fix placeholder too */
.section-card .form-control::placeholder {
  color: #6c757d !important;
  opacity: 1;
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
          <li class="nav-item mb-2"><a class="nav-link text-white" href="my_reports.php"><i class="fas fa-file-alt me-2"></i>My Reports</a></li>
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">My Profile</h2>
            <p class="text-muted">Manage your account information</p>
          </div>
        </div>

        <!-- Alert Messages -->
        <?php if($success_msg != ''): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if($error_msg != ''): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <div class="row">
          <!-- Profile Picture & Info -->
          <div class="col-lg-4 mb-4">
            <div class="card section-card shadow-lg">
              <div class="card-body text-center">
                <div class="profile-icon">
                  <i class="fas fa-user"></i>
                </div>
                <h4 class="fw-bold"><?php echo htmlspecialchars($user['Name']); ?></h4>
                <p class="text-muted mb-3"><?php echo htmlspecialchars($user['Email']); ?></p>
                <div class="border-top pt-3">
                  <div class="row text-center">
                    <div class="col-6 mb-3">
                      <div class="stat-card total-emergencies p-3">
                        <div class="text-center">
                          <?php 
                          $emergency_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM emergencies WHERE User_Id = '$user_id'"));
                          echo "<h3 class='mb-0'>{$emergency_count}</h3>";
                          ?>
                          <small class="text-muted">Reports</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-6 mb-3">
                      <div class="stat-card resolved p-3">
                        <div class="text-center">
                          <?php 
                          $resolved_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM emergencies WHERE User_Id = '$user_id' AND Emergency_Status = 'Resolved'"));
                          echo "<h3 class='mb-0'>{$resolved_count}</h3>";
                          ?>
                          <small class="text-muted">Resolved</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile Settings -->
          <div class="col-lg-8">
            <!-- Update Profile Information -->
            <div class="card section-card shadow-lg mb-4">
              <div class="card-header bg-dark-custom text-white">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Profile Information</h5>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold"><i class="fas fa-user text-danger me-2"></i>Full Name</label>
                      <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['Name']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold"><i class="fas fa-envelope text-danger me-2"></i>Email</label>
                      <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold"><i class="fas fa-phone text-danger me-2"></i>Phone Number</label>
                      <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i>Address</label>
                      <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['Address'] ?? ''); ?>">
                    </div>
                  </div>
                  <button type="submit" name="update_profile" class="btn btn-danger">
                    <i class="fas fa-save me-2"></i>Update Profile
                  </button>
                </form>
              </div>
            </div>

            <!-- Change Password
            <div class="card section-card shadow-lg">
              <div class="card-header bg-dark-custom text-white">
                <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="mb-3">
                    <label class="form-label fw-bold"><i class="fas fa-key text-danger me-2"></i>Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-bold"><i class="fas fa-key text-danger me-2"></i>New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                    <div class="form-text">Password must be at least 6 characters long.</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-bold"><i class="fas fa-key text-danger me-2"></i>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                  </div>
                  <button type="submit" name="change_password" class="btn btn-danger">
                    <i class="fas fa-lock me-2"></i>Change Password
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div> -->

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
