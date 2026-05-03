<?php
include "include/db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Emergency Response System</title>
    
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
                            <h3 class="text-white fw-bold">Welcome</h3>
                            <p class="text-white">Create a new account</p>
                        </div>

                        <form id="loginForm"  method="POST" action="">
                            <div class="mb-3">
                                <label class="text-white mb-2">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-white">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control bg-transparent text-white border-start-0" 
                                           placeholder="Enter your full name"   name="name" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-white mb-2">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-white">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control bg-transparent text-white border-start-0" 
                                           placeholder="Enter your email" id="emailInput"   name="email" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-white mb-2">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-white">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control bg-transparent text-white border-start-0" 
                                           placeholder="Enter your phone number" id="phoneInput"   name="phone" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-white mb-2">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-white">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="text" class="form-control bg-transparent text-white border-start-0" 
                                           placeholder="Enter your address" id="addressInput" name="address" required>
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
                            <!--
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label text-white" for="rememberMe">
                                    Remember me
                                </label>
                            </div> -->

                            <button type="submit" name="register" class="btn btn-danger btn-lg w-100 mb-3">
                                Register <i class="fas fa-arrow-right ms-2"></i>
                            </button>

                            <div class="text-center">
                                <a href="login.php" class="text-white text-decoration-none">Already have an account? Login</a>
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

<?php

if(isset($_POST['register'])){
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];
  $password = password_hash($_POST['pass'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (Name, Email, Phone, Pass, Address)
     VALUES ('$name', '$email', '$phone', '$password', 'address')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('You are registered successfully');</script>";
    } else {
        echo "<p>Your record not inserted" . mysqli_error($conn) . "</p>";
    }
}


?>