<?php
// Get current page name for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="index.php" class="flex items-center space-x-3">
                    <img src="https://static.vecteezy.com/system/resources/thumbnails/027/385/442/small_2x/car-stainless-logo-png.png" 
                         alt="EcoWheels Logo" 
                         class="h-10 w-auto">
                    <span class="text-xl font-bold text-green-600">SMETAMobile</span>
                </a>
            </div>

            <!-- Main Menu -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="index.php" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium <?php echo $current_page === 'index' ? 'text-green-600' : ''; ?>">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                
                <a href="browse-cars.php" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium <?php echo $current_page === 'browse-cars' ? 'text-green-600' : ''; ?>">
                    <i class="fas fa-search mr-2"></i>Browse Cars
                </a>

                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <a href="dashboard.php" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium <?php echo $current_page === 'dashboard' ? 'text-green-600' : ''; ?>">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="add-listing.php" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium <?php echo $current_page === 'add-listing' ? 'text-green-600' : ''; ?>">
                        <i class="fas fa-plus mr-2"></i>Add Listing
                    </a>
                    <div class="flex items-center space-x-4 ml-4 border-l pl-4">
                        <span class="text-gray-600">
                            <i class="fas fa-user mr-2"></i><?php echo htmlspecialchars($_SESSION["email"]); ?>
                        </span>
                        <a href="auth/logout.php" class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex items-center space-x-2">
                        <a href="auth/login.php" class="text-green-600 hover:text-green-700 px-4 py-2 text-sm font-medium">Login</a>
                        <a href="auth/register.php" class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-md text-sm font-medium">Register</a>
                    </div>
                <?php endif; ?>
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
                <a href="index.php" class="block text-gray-600 hover:text-green-600 px-3 py-2 text-base font-medium <?php echo $current_page === 'index' ? 'text-green-600' : ''; ?>">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="browse-cars.php" class="block text-gray-600 hover:text-green-600 px-3 py-2 text-base font-medium <?php echo $current_page === 'browse-cars' ? 'text-green-600' : ''; ?>">
                    <i class="fas fa-search mr-2"></i>Browse Cars
                </a>

                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <a href="dashboard.php" class="block text-gray-600 hover:text-green-600 px-3 py-2 text-base font-medium <?php echo $current_page === 'dashboard' ? 'text-green-600' : ''; ?>">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="add-listing.php" class="block text-gray-600 hover:text-green-600 px-3 py-2 text-base font-medium <?php echo $current_page === 'add-listing' ? 'text-green-600' : ''; ?>">
                        <i class="fas fa-plus mr-2"></i>Add Listing
                    </a>
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <span class="block text-gray-600 px-3 py-2 text-base font-medium">
                            <i class="fas fa-user mr-2"></i><?php echo htmlspecialchars($_SESSION["email"]); ?>
                        </span>
                        <a href="auth/logout.php" class="block text-red-600 hover:text-red-700 px-3 py-2 text-base font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <a href="auth/login.php" class="block text-green-600 hover:text-green-700 px-3 py-2 text-base font-medium">Login</a>
                        <a href="auth/register.php" class="block text-green-600 hover:text-green-700 px-3 py-2 text-base font-medium">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

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
</script> 