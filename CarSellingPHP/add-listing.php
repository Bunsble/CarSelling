<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: auth/login.php");
    exit;
}

require_once 'config/database.php';

$success_message = $error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $year = intval($_POST['year']);
    $price = floatval($_POST['price']);
    $mileage = intval($_POST['mileage']);
    $fuel_type = mysqli_real_escape_string($conn, $_POST['fuel_type']);
    $transmission = mysqli_real_escape_string($conn, $_POST['transmission']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $features = mysqli_real_escape_string($conn, $_POST['features']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $user_id = $_SESSION['id'];

    // Handle image upload
    $image_url = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = 'uploads/cars/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = $target_file;
        }
    }

    // Insert into database
    $sql = "INSERT INTO cars (user_id, title, brand, model, year, price, mileage, fuel_type, transmission, description, features, image_url, location) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "isssidissssss", 
            $user_id, $title, $brand, $model, $year, $price, $mileage, 
            $fuel_type, $transmission, $description, $features, $image_url, $location);
        
        if(mysqli_stmt_execute($stmt)){
            $success_message = "Car listing added successfully!";
        } else{
            $error_message = "Error adding listing. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Listing - EcoWheels</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'components/navbar.php'; ?>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Add New Car Listing</h1>

            <?php if($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Brand</label>
                        <select name="brand" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Select Brand</option>
                            <option value="Tesla">Tesla</option>
                            <option value="Toyota">Toyota</option>
                            <option value="BMW">BMW</option>
                            <option value="Nissan">Nissan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Model</label>
                        <input type="text" name="model" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Year</label>
                        <input type="number" name="year" required min="1900" max="<?php echo date('Y') + 1; ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price ($)</label>
                        <input type="number" name="price" required min="0" step="0.01"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mileage</label>
                        <input type="number" name="mileage" required min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fuel Type</label>
                        <select name="fuel_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Select Fuel Type</option>
                            <option value="Electric">Electric</option>
                            <option value="Hybrid">Hybrid</option>
                            <option value="Plug-in Hybrid">Plug-in Hybrid</option>
                            <option value="Hydrogen">Hydrogen</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transmission</label>
                        <select name="transmission" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Select Transmission</option>
                            <option value="Automatic">Automatic</option>
                            <option value="Manual">Manual</option>
                            <option value="CVT">CVT</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="4" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Features (one per line)</label>
                    <textarea name="features" rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Car Image</label>
                    <input type="file" name="image" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-green-50 file:text-green-700
                                  hover:file:bg-green-100">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Add Listing
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 