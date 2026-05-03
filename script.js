/* ==========================================
   Emergency Response Management System
   Custom JavaScript
   ========================================== */

// ==========================================
// Preloader
// ==========================================
window.addEventListener('load', function() {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        setTimeout(() => {
            preloader.classList.add('hide');
        }, 1500);
    }
});

// ==========================================
// AOS (Animate On Scroll) Initialization
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        mirror: false,
        offset: 100
    });
});

// ==========================================
// GSAP Animations
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    // Hero Section Animation
    const heroContent = document.querySelector('.hero-section .container');
    if (heroContent) {
        gsap.from('.hero-section h1', {
            duration: 1.5,
            y: 100,
            opacity: 0,
            ease: 'power3.out',
            stagger: 0.2
        });
        
        gsap.from('.hero-section p', {
            duration: 1,
            y: 50,
            opacity: 0,
            ease: 'power2.out',
            delay: 0.5
        });
        
        gsap.from('.hero-section .btn', {
            duration: 1,
            y: 50,
            opacity: 0,
            ease: 'power2.out',
            delay: 0.7,
            stagger: 0.1
        });
    }

    // Card Hover Effects with GSAP
    const cards = document.querySelectorAll('.category-card, .report-card, .stat-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            gsap.to(this, {
                scale: 1.02,
                duration: 0.3,
                ease: 'power2.out'
            });
        });
        
        card.addEventListener('mouseleave', function() {
            gsap.to(this, {
                scale: 1,
                duration: 0.3,
                ease: 'power2.out'
            });
        });
    });
});

// ==========================================
// Number Counter Animation for Stats
// ==========================================
function animateCounter(element, target) {
    const duration = 2000;
    const increment = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Initialize counter animation when elements are visible
document.addEventListener('DOMContentLoaded', function() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.getAttribute('data-target'));
                animateCounter(entry.target, target);
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.5
    });
    
    statNumbers.forEach(stat => {
        observer.observe(stat);
    });
});

// ==========================================
// Login Page Functionality
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    // User Type Selection
    const userTypeButtons = document.querySelectorAll('[data-user]');
    userTypeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            userTypeButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Toggle Password Visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Login Form Submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // e.preventDefault();
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing in...';
            submitBtn.disabled = true;
            
            // Simulate login process
            setTimeout(() => {
                // Redirect to dashboard
                // window.location.href = 'dashboard.html';
            }, 1500);
        });
    }
});

// ==========================================
// Chart.js Initialization for Dashboard
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const lineChartCtx = document.getElementById('lineChart');
    const doughnutChartCtx = document.getElementById('doughnutChart');

    // Line Chart - Emergency Trends
    if (lineChartCtx) {
        new Chart(lineChartCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Emergencies',
                    data: [12, 19, 15, 25, 22, 18, 16],
                    borderColor: '#D32F2F',
                    backgroundColor: 'rgba(211, 47, 47, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#D32F2F',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }, {
                    label: 'Resolved',
                    data: [8, 15, 12, 20, 18, 14, 13],
                    borderColor: '#1976D2',
                    backgroundColor: 'rgba(25, 118, 210, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#1976D2',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });
    }

    // Doughnut Chart - Emergency Categories
    if (doughnutChartCtx) {
        new Chart(doughnutChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['Fire', 'Medical', 'Police'],
                datasets: [{
                    data: [45, 35, 20],
                    backgroundColor: [
                        '#D32F2F',
                        '#1976D2',
                        '#212121'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }
});

// ==========================================
// Emergency Report Form Handling
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const reportForm = document.getElementById('emergencyReportForm');
    
    if (reportForm) {
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const emergencyType = formData.get('emergencyType');
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Emergency Reported!',
                text: 'Your emergency report has been submitted successfully. Responders are being notified.',
                confirmButtonColor: '#D32F2F',
                confirmButtonText: 'OK'
            }).then(() => {
                // Redirect to my-reports page
                window.location.href = 'my-reports.html';
            });
        });

        // Location button functionality
        const useLocationBtn = reportForm.querySelector('button[type="button"]');
        if (useLocationBtn) {
            useLocationBtn.addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        alert('Location captured: ' + position.coords.latitude + ', ' + position.coords.longitude);
                    }, function() {
                        alert('Unable to get your location');
                    });
                } else {
                    alert('Geolocation is not supported by your browser');
                }
            });
        }
    }
});

// ==========================================
// Report Filter Functionality
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const filterLinks = document.querySelectorAll('.filter-tabs .nav-link');
    
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active state
            filterLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Filter report cards (demo implementation)
            const reportCards = document.querySelectorAll('.report-card');
            reportCards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                } else if (card.classList.contains(filter)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});

// ==========================================
// Navbar Scroll Effect
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            navbar.classList.add('shadow-lg');
        } else {
            navbar.classList.remove('shadow-lg');
        }
        
        lastScroll = currentScroll;
    });
});

// ==========================================
// Smooth Scroll for Anchor Links
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});

// ==========================================
// Loading Animation for Buttons
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.type === 'submit') {
                this.classList.add('loading');
            }
        });
    });
});

// ==========================================
// Intersection Observer for Stats Animation
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const animatedElements = document.querySelectorAll('.stat-card');
    animatedElements.forEach(el => observer.observe(el));
});

// ==========================================
// Sidebar Active State Management
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        if (linkPath === currentPath || currentPath.includes(linkPath)) {
            link.classList.add('active');
        }
    });
});

// ==========================================
// Toast Notification System
// ==========================================
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Add SweetAlert2 CDN dynamically if not present
if (typeof Swal === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    document.head.appendChild(script);
}





