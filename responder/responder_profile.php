<?php
include '../include/db.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'responder'){
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user profile information
$profile_sql = "SELECT users.*, responders.Department, responders.Experience, responders.Responder_Status, responders.Location 
                FROM users 
                LEFT JOIN responders ON users.Id = responders.User_Id 
                WHERE users.Id = $user_id";
$profile_result = mysqli_query($conn, $profile_sql);
$profile = mysqli_fetch_assoc($profile_result);

// Get statistics
$total_assigned = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM emergencies WHERE Emergency_Status IN ('Assigned', 'In Progress')"));
$total_resolved = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM emergencies WHERE Emergency_Status = 'Resolved'"));

// Handle profile update
if(isset($_POST['update_profile'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    $update_sql = "UPDATE users SET Name = '$name', Phone = '$phone', Address = '$address' WHERE Id = $user_id";
    if(mysqli_query($conn, $update_sql)){
        echo "<script>alert('✅ Profile updated successfully!');</script>";
        echo "<script>window.location.href='responder_profile.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating profile!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | ERMS</title>

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
          <li class="nav-item mb-2"><a class="nav-link text-white" href="completed_tasks.php"><i class="fas fa-check-circle me-2"></i>Completed Tasks</a></li>
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="responder_profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">My Profile</h2>
            <p class="text-muted">Manage your profile information</p>
          </div>
        </div>

        <div class="row g-4">
          <!-- Profile Card -->
          <div class="col-md-4">
            <div class="card shadow-lg">
              <div class="card-body text-center">
                <div class="mb-3">
                  <i class="fas fa-user-circle fs-1 text-primary"></i>
                </div>
                <h4 class="fw-bold"><?php echo htmlspecialchars($profile['Name']); ?></h4>
                <p class="text-muted mb-2">
                  <i class="fas fa-shield-alt me-2"></i><?php echo ucfirst($profile['Role']); ?>
                </p>
                <?php if($profile['Responder_Status']): ?>
                  <span class="badge <?php echo $profile['Responder_Status'] == 'Available' ? 'bg-success' : 'bg-warning'; ?> mb-3">
                    <?php echo $profile['Responder_Status']; ?>
                  </span>
                <?php endif; ?>
                
                <hr>
                
                <div class="d-flex justify-content-around mt-3">
                  <div>
                    <h4 class="text-primary fw-bold"><?php echo $total_assigned; ?></h4>
                    <small class="text-muted">Active</small>
                  </div>
                  <div>
                    <h4 class="text-success fw-bold"><?php echo $total_resolved; ?></h4>
                    <small class="text-muted">Resolved</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Responder Details Card -->
            <?php if($profile['Department']): ?>
            <div class="card shadow-lg mt-4">
              <div class="card-header bg-dark-custom text-white">
                <h5 class="mb-0"><i class="fas fa-id-badge me-2"></i>Responder Details</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <small class="text-muted">Department</small>
                  <p class="mb-0 fw-bold"><i class="fas fa-building text-primary me-2"></i><?php echo $profile['Department']; ?></p>
                </div>
                <div class="mb-3">
                  <small class="text-muted">Experience</small>
                  <p class="mb-0 fw-bold"><i class="fas fa-award text-warning me-2"></i><?php echo $profile['Experience']; ?></p>
                </div>
                <div class="mb-3">
                  <small class="text-muted">Location</small>
                  <p class="mb-0 fw-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i><?php echo $profile['Location']; ?></p>
                </div>
                <div>
                  <small class="text-muted">Joined</small>
                  <p class="mb-0 fw-bold"><i class="fas fa-calendar text-success me-2"></i><?php echo date('M d, Y', strtotime($profile['Join_Date'])); ?></p>
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>

          <!-- Profile Form -->
          <div class="col-md-8">
            <div class="card shadow-lg">
              <div class="card-header bg-dark-custom text-white">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Profile Information</h5>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label"><i class="fas fa-user me-2"></i>Full Name</label>
                      <input type="text" class="form-control text-dark" name="name" value="<?php echo htmlspecialchars($profile['Name']); ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label"><i class="fas fa-envelope me-2"></i>Email</label>
                      <input type="email" class="form-control text-dark" value="<?php echo htmlspecialchars($profile['Email']); ?>" readonly>
                      <small class="text-muted">Email cannot be changed</small>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label"><i class="fas fa-phone me-2"></i>Phone Number</label>
                      <input type="text" class="form-control text-dark" name="phone" value="<?php echo htmlspecialchars($profile['Phone']); ?>" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label"><i class="fas fa-user-tag me-2"></i>Role</label>
                      <input type="text" class="form-control text-dark" value="<?php echo ucfirst($profile['Role']); ?>" readonly>
                    </div>

                    <div class="col-md-12">
                      <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Address</label>
                      <textarea class="form-control text-dark" name="address" rows="3" required><?php echo htmlspecialchars($profile['Address']); ?></textarea>
                    </div>

                    <div class="col-md-12">
                      <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Profile
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>