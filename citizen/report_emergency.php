<?php
include '../include/db.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'citizen'){
    header("Location: ../login.php");
    exit;
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

if(isset($_POST['report'])){
    $emer_title = mysqli_real_escape_string($conn, $_POST['emer_title']);
    $emer_name = mysqli_real_escape_string($conn, $_POST['emer_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $incident_time = mysqli_real_escape_string($conn, $_POST['incident_time']);

    $sql_insert = "INSERT INTO emergencies 
                  (Emergency_Department, Emergency_Name, User_Id, Description, Location, Incident_Time)
                  VALUES 
                  ('$emer_name', '$emer_title', '$user_id', '$description', '$location', '$incident_time')";

    if(mysqli_query($conn, $sql_insert)){
        echo "<script>
            alert('Emergency reported successfully!');
            window.location.href = 'dashboard.php';
        </script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Emergency | ERMS</title>

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
    .form-control, .form-select {
      border: 1px solid #dee2e6;
      transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
      border-color: #e74c3c;
      box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
    }

    /* Fix invisible text in form inputs */
.form-control, .form-select, textarea {
  color: #212529 !important; /* Dark text color */
  background-color: #fff !important; /* White background */
}

/* Placeholder text color */
::placeholder {
  color: #6c757d !important;
  opacity: 1;
}

/* Optional: make dropdown options readable too */
select option {
  color: #212529;
  background-color: #fff;
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
          <li class="nav-item mb-2"><a class="nav-link active text-white" href="report_emergency.php"><i class="fas fa-plus-circle me-2"></i>Report Emergency</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="my_reports.php"><i class="fas fa-file-alt me-2"></i>My Reports</a></li>
          <li class="nav-item mb-2"><a class="nav-link text-white" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 ms-auto px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold mb-1">Report Emergency</h2>
            <p class="text-muted">Fill out the form below to report an emergency</p>
          </div>
          <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
          </a>
        </div>

        <!-- Report Form -->
        <div class="row">
          <div class="col-lg-8">
            <div class="card shadow-lg">
              <div class="card-header bg-dark-custom text-dark ">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Emergency Information</h5>
              </div>
              <div class="card-body p-4">
                <form method="POST">

                     <!-- Emergency Name -->
                    <div class="mb-4">
                    <label class="form-label fw-bold">
                            <i class="fas fa-heading text-danger me-2"></i>Emergency Name
                    </label>
                    <input type="text" name="emer_title" class="form-control form-control-lg" 
                             placeholder="Enter a short title for the emergency (e.g., Fire in Building 12)" required>
                      </div>

                  <div class="mb-4">
                    <label class="form-label fw-bold"><i class="fas fa-tag text-danger me-2"></i>Emergency Type</label>
                    <select name="emer_name" class="form-select form-select-lg" required>
                      <option value="">Select emergency type...</option>
                      <option value="Fire">🔥 Fire Emergency</option>
                      <option value="Medical">🏥 Medical Emergency</option>
                      <option value="Police">🚨 Police Emergency</option>
                      <option value="Accident">🚑 Traffic Accident</option>
                      <option value="Natural Disaster">🌪️ Natural Disaster</option>
                      <option value="Rescue">🚁 Rescue Operation</option>
                      <option value="Gas Leak">💨 Gas Leak</option>
                      <option value="Structural Damage">🏗️ Structural Damage</option>
                      <option value="Other">⚠️ Other</option>
                    </select>
                  </div>

                  <div class="mb-4">
                    <label class="form-label fw-bold"><i class="fas fa-city text-danger me-2"></i>City</label>
                    <select class="form-select form-select-lg" required>
                      <option value="">Select City...</option>
                      <option value="Larkana">Larkana</option>
                    </select>
                  </div>

                  <div class="mb-4">
                    <label class="form-label fw-bold"><i class="fas fa-file-alt text-danger me-2"></i>Description</label>
                    <textarea name="description" rows="5" class="form-control" placeholder="Describe what happened in detail..." required></textarea>
                    <div class="form-text">Provide as much detail as possible to help responders assess the situation.</div>
                  </div>

                  <div class="mb-4">
                    <label class="form-label fw-bold"><i class="fas fa-map-marker-alt text-danger me-2"></i>Location</label>
                    <input type="text" name="location" class="form-control form-control-lg" placeholder="Enter the exact location (street, landmark, etc.)" required>
                  </div>

                  <div class="mb-4">
                    <label class="form-label fw-bold"><i class="fas fa-clock text-danger me-2"></i>Time of Incident</label>
                    <input type="datetime-local" name="incident_time" class="form-control form-control-lg" required>
                  </div>

                  <div class="d-grid gap-2">
                    <button type="submit" name="report" class="btn btn-danger btn-lg">
                      <i class="fas fa-paper-plane me-2"></i>Submit Emergency Report
                    </button>
                  </div>

                </form>
              </div>
            </div>
          </div>

          <!-- Safety Tips Sidebar -->
          <div class="col-lg-4">
            <div class="card shadow-lg border-0">
              <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Safety Tips</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <i class="fas fa-check-circle text-success me-2"></i>
                  <strong>Stay Calm</strong>
                  <p class="small text-muted mb-0">Take a deep breath and provide accurate information</p>
                </div>
                <div class="mb-3">
                  <i class="fas fa-check-circle text-success me-2"></i>
                  <strong>Stay Safe</strong>
                  <p class="small text-muted mb-0">Don't put yourself in danger to report the emergency</p>
                </div>
                <div class="mb-3">
                  <i class="fas fa-check-circle text-success me-2"></i>
                  <strong>Be Specific</strong>
                  <p class="small text-muted mb-0">Include exact location, type, and any visible details</p>
                </div>
                <div class="mb-3">
                  <i class="fas fa-check-circle text-success me-2"></i>
                  <strong>Stay Connected</strong>
                  <p class="small text-muted mb-0">Keep your phone charged and remain available</p>
                </div>
                <div class="mb-3">
                  <i class="fas fa-check-circle text-success me-2"></i>
                  <strong>Follow Instructions</strong>
                  <p class="small text-muted mb-0">Listen to and follow responder instructions</p>
                </div>
              </div>
            </div>

            <div class="card shadow-lg border-0 mt-3 bg-light">
              <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Important Information</h6>
                <ul class="list-unstyled mb-0">
                  <li class="mb-2"><small><i class="fas fa-phone text-danger me-2"></i>For immediate danger, call 911</small></li>
                  <li class="mb-2"><small><i class="fas fa-clock text-warning me-2"></i>Response time varies by location</small></li>
                  <li class="mb-2"><small><i class="fas fa-shield-alt text-success me-2"></i>False reports are punishable by law</small></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
