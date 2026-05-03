<?php
  include 'include/db.php';

    
if(isset($_POST['login'])){

  // $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];

  $sql = "SELECT * FROM users WHERE Email = '$email'";
  $result = mysqli_query($conn, $sql);

  if(mysqli_num_rows($result) == 1){
    $users = mysqli_fetch_assoc($result);

    if(password_verify($pass, $users['Pass'])){
        session_start();
        $_SESSION['user_id'] = $users['Id'];
        $_SESSION['user_name'] = $users['Name'];
        $_SESSION['email'] = $users['Email'];
        $_SESSION['role'] = $users['Role'];

        if($_SESSION['role'] == 'admin'){
            header("Location: admin/admin_dashboard.php");
        } elseif($_SESSION['role'] == 'citizen'){
            header("Location: citizen/citizen_dashboard.php");
        } elseif($_SESSION['role'] == 'responder'){
            header("Location: responder/responder_dashboard.php");
        }
    }
    else{
        echo "<script>alert('Invalid Password');</script>";
    }

  } else {
    echo "<script>alert('Email not Found');</script>";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Emergency Response System</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<style>
    .form-control::placeholder {
        color: #fff;
    }
</style>
<body class="login-page">

    <section class="login-section min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                <!-- Login Form -->
                    <div class="glass-card">
                        <div class="text-center mb-4">
                            <div class="login-icon mb-3">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h3 class="text-white fw-bold">Welcome Back</h3>
                            <p class="text-white">Sign in to your account</p>
                        </div>

                        <form id="loginForm"  method="POST" action="">
                            <div class="mb-3">
                                <label class="text-white mb-2">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-white">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control bg-transparent text-white border-start-0" 
                                           placeholder="Enter your email"   name="email" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-white mb-2">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-white">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control bg-transparent text-white border-start-0" 
                                           placeholder="Enter your password" id="passwordInput"   name="pass" required>
                                    <span class="input-group-text bg-transparent border-start-0 text-white">
                                        <i class="fas fa-eye" id="togglePassword"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" name="login" class="btn btn-danger btn-lg w-100 mb-3">
                                login 
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>

                            <div class="text-center">
                                <a href="register.php" class="text-white text-decoration-none">Create an account</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
         <!-- Animated Background -->
        <div class="bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </section>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <!-- <script src="script.js"></script> -->
    
    <script>
        // Initialize AOS for login page
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                easing: 'ease-in-out',
                once: true
            });
        });
    </script>

  </body>
</html>
