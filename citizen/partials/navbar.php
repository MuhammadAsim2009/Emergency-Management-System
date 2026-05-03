<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark-custom">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard.php">
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

