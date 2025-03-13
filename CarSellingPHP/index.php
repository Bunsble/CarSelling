<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>EcoWheels - Your Sustainable Car Marketplace</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/smeta.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Ensure smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
        }
        /* Fix for potential conflicts between Tailwind and Bootstrap */
        .nav-fixed {
            position: fixed;
            width: 100%;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Fixed Navigation -->
    <nav class="bg-white shadow-lg nav-fixed">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-3">
                        <img src="https://static.vecteezy.com/system/resources/thumbnails/027/385/442/small_2x/car-stainless-logo-png.png" 
                             alt="EcoWheels Logo" 
                             class="h-12 w-auto">
                        <span class="text-2xl font-bold text-green-600">SMETAMobile</span>
                    </a>
                </div>

                <!-- Main Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#top" class="text-gray-600 hover:text-green-600 px-3 py-2 text-sm font-medium">Home</a>
                    <a href="#aboutus" class="text-gray-600 hover:text-green-600 px-3 py-2 text-sm font-medium">About Us</a>
                    <a href="./browse-cars.php" class="text-gray-600 hover:text-green-600 px-3 py-2 text-sm font-medium">Products</a>
                    <!-- <a href="#infos" class="text-gray-600 hover:text-green-600 px-3 py-2 text-sm font-medium">Contact</a> -->
                    <div class="flex items-center space-x-2">
                        <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                            <a href="dashboard.php" class="text-green-600 hover:text-green-700 px-4 py-2 text-sm font-medium">
                                <i class="fas fa-user mr-2"></i>Dashboard
                            </a>
                            <a href="auth/logout.php" class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        <?php else: ?>
                            <a href="auth/login.php" class="text-green-600 hover:text-green-700 px-4 py-2 text-sm font-medium">Login</a>
                            <a href="auth/register.php" class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-md text-sm font-medium">Register</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" onclick="toggleMenu()" class="text-gray-600 hover:text-green-600 focus:outline-none focus:text-green-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-200">
                    <a href="#top" class="block text-gray-600 hover:text-green-600 px-3 py-2 text-base font-medium">Home</a>
                    <a href="#aboutus" class="block text-gray-600 hover:text-green-600 px-3 py-2 text-base font-medium">About Us</a>
                    <a href="./browse-cars.php" class="block text-gray-600 hover:text-green-600 px-3 py-2 text-base font-medium">Products</a>
                    <a href="#infos" class="block text-gray-600 hover:text-green-600 px-3 py-2 text-base font-medium">Contact</a>
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                            <a href="dashboard.php" class="block text-green-600 hover:text-green-700 px-3 py-2 text-base font-medium">
                                <i class="fas fa-user mr-2"></i>Dashboard
                            </a>
                            <a href="auth/logout.php" class="block text-red-600 hover:text-red-700 px-3 py-2 text-base font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        <?php else: ?>
                            <a href="auth/login.php" class="block text-green-600 hover:text-green-700 px-3 py-2 text-base font-medium">Login</a>
                            <a href="auth/register.php" class="block text-green-600 hover:text-green-700 px-3 py-2 text-base font-medium">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-20">
        <!-- Banner Section -->
        <section class="main-banner" id="top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="caption header-text">
                            <h6>Online car shop</h6>
                            <div class="line-dec"></div>
                            <h4>Your premier <span>destination </span></h4>
                            <h4>for <em>eco-friendly</em> vehicles</h4>
                            <p>Explore a curated selection of electric, hybrid, and alternative fuel cars designed for a sustainable future. Find the top-ranked vehicles.</p>
                            <div class="main-button scroll-to-section"><a href="#projects">Best Sellers</a></div>
                            <?php if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
                                echo "<span><strong>or</strong></span>"
                                echo "<div class="second-button"><a href="auth/login.php">Log in/Sign up</a></div>"
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services section" id="aboutus">
            <?php include 'sections/services.php'; ?>
        </section>

        <!-- Projects Section -->
        <section class="projects section" id="projects">
            <?php include 'sections/projects.php'; ?>
        </section>

        <!-- Contact Section -->
        <section class="infos section" id="infos">
            <?php include 'sections/contact.php'; ?>
        </section>
    </main>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/animation.js"></script>
    <script src="assets/js/imagesloaded.js"></script>
    <script src="assets/js/custom.js"></script>

    <!-- Mobile menu toggle script -->
    <script>
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const menuButton = document.querySelector('.md\\:hidden button');
            if (!menu.contains(event.target) && !menuButton.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 