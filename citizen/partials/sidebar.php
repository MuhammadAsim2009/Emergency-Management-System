<!-- Sidebar -->
<nav class="col-md-3 col-lg-2 bg-dark-custom sidebar vh-100 position-fixed pt-4">
  <ul class="nav flex-column">
    <li class="nav-item mb-2">
      <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
        <i class="fas fa-home me-2"></i>Dashboard
      </a>
    </li>
    <li class="nav-item mb-2">
      <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'report_emergency.php' ? 'active' : ''; ?>" href="report_emergency.php">
        <i class="fas fa-plus-circle me-2"></i>Report Emergency
      </a>
    </li>
    <li class="nav-item mb-2">
      <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'my_reports.php' ? 'active' : ''; ?>" href="my_reports.php">
        <i class="fas fa-file-alt me-2"></i>My Reports
      </a>
    </li>
    <li class="nav-item mb-2">
      <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="profile.php">
        <i class="fas fa-user me-2"></i>Profile
      </a>
    </li>
  </ul>
</nav>

