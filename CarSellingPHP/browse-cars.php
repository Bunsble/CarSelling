<?php
session_start();
require_once 'config/database.php';

// Initialize filter variables
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$fuel_type = isset($_GET['fuel_type']) ? $_GET['fuel_type'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';

// Build the SQL query
$sql = "SELECT c.*, u.name as seller_name 
        FROM cars c 
        JOIN users u ON c.user_id = u.id 
        WHERE status = 'Available'";

if (!empty($brand)) {
    $sql .= " AND brand = '" . mysqli_real_escape_string($conn, $brand) . "'";
}
if (!empty($fuel_type)) {
    $sql .= " AND fuel_type = '" . mysqli_real_escape_string($conn, $fuel_type) . "'";
}
if (!empty($min_price)) {
    $sql .= " AND price >= " . floatval($min_price);
}
if (!empty($max_price)) {
    $sql .= " AND price <= " . floatval($max_price);
}
if (!empty($transmission)) {
    $sql .= " AND transmission = '" . mysqli_real_escape_string($conn, $transmission) . "'";
}

$sql .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Cars - EcoWheels</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Filter Cars</h2>
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Brand</label>
                    <select name="brand" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All Brands</option>
                        <option value="Tesla" <?php echo $brand === 'Tesla' ? 'selected' : ''; ?>>Tesla</option>
                        <option value="Toyota" <?php echo $brand === 'Toyota' ? 'selected' : ''; ?>>Toyota</option>
                        <option value="BMW" <?php echo $brand === 'BMW' ? 'selected' : ''; ?>>BMW</option>
                        <option value="Nissan" <?php echo $brand === 'Nissan' ? 'selected' : ''; ?>>Nissan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fuel Type</label>
                    <select name="fuel_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All Types</option>
                        <option value="Electric" <?php echo $fuel_type === 'Electric' ? 'selected' : ''; ?>>Electric</option>
                        <option value="Hybrid" <?php echo $fuel_type === 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                        <option value="Plug-in Hybrid" <?php echo $fuel_type === 'Plug-in Hybrid' ? 'selected' : ''; ?>>Plug-in Hybrid</option>
                        <option value="Hydrogen" <?php echo $fuel_type === 'Hydrogen' ? 'selected' : ''; ?>>Hydrogen</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Min Price</label>
                    <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="Min Price">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Price</label>
                    <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="Max Price">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Transmission</label>
                    <select name="transmission" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All</option>
                        <option value="Automatic" <?php echo $transmission === 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                        <option value="Manual" <?php echo $transmission === 'Manual' ? 'selected' : ''; ?>>Manual</option>
                        <option value="CVT" <?php echo $transmission === 'CVT' ? 'selected' : ''; ?>>CVT</option>
                    </select>
                </div>

                <div class="md:col-span-3 lg:col-span-5 flex justify-end">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Cars Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($car = mysqli_fetch_assoc($result)): ?>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <img src="<?php echo htmlspecialchars($car['image_url'] ?? 'assets/images/default-car.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($car['title']); ?>"
                             class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <?php echo htmlspecialchars($car['title']); ?>
                            </h3>
                            <p class="text-sm text-gray-500">
                                <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' ' . $car['year']); ?>
                            </p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-lg font-bold text-green-600">
                                    $<?php echo number_format($car['price'], 2); ?>
                                </span>
                                <span class="text-sm text-gray-500">
                                    <?php echo number_format($car['mileage']); ?> miles
                                </span>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <i class="fas fa-gas-pump mr-2"></i>
                                <?php echo htmlspecialchars($car['fuel_type']); ?>
                            </div>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-sm text-gray-500">
                                    Listed by <?php echo htmlspecialchars($car['seller_name']); ?>
                                </span>
                                <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                                    <button onclick="toggleFavorite(<?php echo $car['id']; ?>)" class="text-red-500 hover:text-red-600">
                                        <i class="far fa-heart"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <a href="car-details.php?id=<?php echo $car['id']; ?>" 
                               class="mt-4 block w-full text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">No cars found matching your criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleFavorite(carId) {
            // Add favorite functionality here
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
                    // Toggle heart icon
                    const heartIcon = event.target;
                    heartIcon.classList.toggle('fas');
                    heartIcon.classList.toggle('far');
                }
            });
        }
    </script>
</body>
</html> 