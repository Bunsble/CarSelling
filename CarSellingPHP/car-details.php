<?php
session_start();
require_once 'config/database.php';

// Get car ID from URL
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch car details
$sql = "SELECT c.*, u.name as seller_name, u.email as seller_email 
        FROM cars c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.id = ?";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $car_id);
    
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1){
            $car = mysqli_fetch_assoc($result);
        } else {
            // Car not found
            header("location: browse-cars.php");
            exit();
        }
    } else {
        // Error in query
        header("location: browse-cars.php");
        exit();
    }
    
    mysqli_stmt_close($stmt);
} else {
    header("location: browse-cars.php");
    exit();
}

// Check if car is in user's favorites
$is_favorite = false;
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']){
    $fav_sql = "SELECT id FROM favorites WHERE user_id = ? AND car_id = ?";
    if($fav_stmt = mysqli_prepare($conn, $fav_sql)){
        mysqli_stmt_bind_param($fav_stmt, "ii", $_SESSION['id'], $car_id);
        mysqli_stmt_execute($fav_stmt);
        mysqli_stmt_store_result($fav_stmt);
        $is_favorite = mysqli_stmt_num_rows($fav_stmt) > 0;
        mysqli_stmt_close($fav_stmt);
    }
}

// Convert features string to array
$features_array = !empty($car['features']) ? explode("\n", $car['features']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($car['title']); ?> - EcoWheels</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Image Section -->
            <div class="relative h-96">
                <img src="<?php echo htmlspecialchars($car['image_url'] ?? 'assets/images/default-car.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($car['title']); ?>"
                     class="w-full h-full object-cover">
                <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                    <button onclick="toggleFavorite(<?php echo $car['id']; ?>)" 
                            class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-md text-red-500 hover:text-red-600">
                        <i class="<?php echo $is_favorite ? 'fas' : 'far'; ?> fa-heart text-xl"></i>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Content Section -->
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($car['title']); ?></h1>
                        <p class="text-lg text-gray-600">
                            <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' ' . $car['year']); ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-green-600">$<?php echo number_format($car['price'], 2); ?></p>
                        <p class="text-sm text-gray-500">Listed on <?php echo date('M d, Y', strtotime($car['created_at'])); ?></p>
                    </div>
                </div>

                <!-- Key Details -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Mileage</p>
                        <p class="text-lg font-semibold"><?php echo number_format($car['mileage']); ?> miles</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Fuel Type</p>
                        <p class="text-lg font-semibold"><?php echo htmlspecialchars($car['fuel_type']); ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Transmission</p>
                        <p class="text-lg font-semibold"><?php echo htmlspecialchars($car['transmission']); ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Location</p>
                        <p class="text-lg font-semibold"><?php echo htmlspecialchars($car['location']); ?></p>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Description</h2>
                    <p class="text-gray-600 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>
                </div>

                <!-- Features -->
                <?php if(!empty($features_array)): ?>
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Features</h2>
                    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        <?php foreach($features_array as $feature): ?>
                            <li class="flex items-center text-gray-600">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <?php echo htmlspecialchars(trim($feature)); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Seller Information -->
                <div class="border-t pt-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Seller Information</h2>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600">Listed by <?php echo htmlspecialchars($car['seller_name']); ?></p>
                            <p class="text-gray-500"><?php echo htmlspecialchars($car['seller_email']); ?></p>
                        </div>
                        <button onclick="openContactModal()" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-envelope mr-2"></i>Contact Seller
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contactModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Contact Seller</h3>
                    <button onclick="closeContactModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="contactForm" onsubmit="submitContactForm(event)" class="space-y-4">
                    <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                    <input type="hidden" name="seller_id" value="<?php echo $car['user_id']; ?>">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Your Name</label>
                        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                            <input type="text" name="name" required value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <?php else: ?>
                            <input type="text" name="name" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Your Email</label>
                        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                            <input type="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['email']); ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <?php else: ?>
                            <input type="email" name="email" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone (optional)</label>
                        <input type="tel" name="phone"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea name="message" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                  placeholder="I'm interested in this car. Please provide more information..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeContactModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleFavorite(carId) {
            fetch('api/toggle-favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ car_id: carId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const heartIcon = event.target.closest('button').querySelector('i');
                    heartIcon.classList.toggle('fas');
                    heartIcon.classList.toggle('far');
                }
            });
        }

        function openContactModal() {
            document.getElementById('contactModal').classList.remove('hidden');
        }

        function closeContactModal() {
            document.getElementById('contactModal').classList.add('hidden');
        }

        function submitContactForm(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('api/send-message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully!');
                    closeContactModal();
                    form.reset();
                } else {
                    alert(data.message || 'Error sending message. Please try again.');
                }
            })
            .catch(error => {
                alert('Error sending message. Please try again.');
            });
        }

        // Close modal when clicking outside
        document.getElementById('contactModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeContactModal();
            }
        });
    </script>
</body>
</html> 