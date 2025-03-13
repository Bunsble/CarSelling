<?php
session_start();

// Check if the user is logged in, if not redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: auth/login.php");
    exit;
}

require_once "config/database.php";

// Fetch user's listings
$listings_sql = "SELECT * FROM cars WHERE user_id = ? ORDER BY created_at DESC";
$listings = [];
if($stmt = mysqli_prepare($conn, $listings_sql)){
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $listings[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Fetch user's favorites
$favorites_sql = "SELECT c.*, u.name as seller_name 
                 FROM cars c 
                 JOIN favorites f ON c.id = f.car_id 
                 JOIN users u ON c.user_id = u.id 
                 WHERE f.user_id = ? 
                 ORDER BY f.created_at DESC";
$favorites = [];
if($stmt = mysqli_prepare($conn, $favorites_sql)){
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $favorites[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Fetch messages received
$messages_sql = "SELECT sm.*, c.title as car_title, c.image_url 
                FROM seller_messages sm 
                JOIN cars c ON sm.car_id = c.id 
                WHERE sm.seller_id = ? 
                ORDER BY sm.created_at DESC";
$messages = [];
if($stmt = mysqli_prepare($conn, $messages_sql)){
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EcoWheels</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php include 'components/navbar.php'; ?>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Welcome back, <?php echo htmlspecialchars($_SESSION["email"]); ?>!</h1>
            <div class="flex space-x-4">
                <a href="add-listing.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>Add New Listing
                </a>
                <a href="browse-cars.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-search mr-2"></i>Browse Cars
                </a>
            </div>
        </div>

        <!-- Messages Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="messages">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-envelope mr-2"></i>Messages
                <?php if(count($messages) > 0): ?>
                    <span class="ml-2 text-sm bg-green-100 text-green-800 py-1 px-2 rounded-full"><?php echo count($messages); ?> new</span>
                <?php endif; ?>
            </h2>
            <div class="border-t border-gray-200 pt-4">
                <?php if(count($messages) > 0): ?>
                    <div class="space-y-4">
                        <?php foreach($messages as $msg): ?>
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                <img src="<?php echo htmlspecialchars($msg['image_url'] ?? 'assets/images/default-car.jpg'); ?>" 
                                     alt="Car Image" 
                                     class="w-24 h-24 object-cover rounded-md">
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                Re: <?php echo htmlspecialchars($msg['car_title']); ?>
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                From: <?php echo htmlspecialchars($msg['name']); ?> 
                                                (<?php echo htmlspecialchars($msg['email']); ?>)
                                                <?php if(!empty($msg['phone'])): ?>
                                                    • <?php echo htmlspecialchars($msg['phone']); ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <span class="text-sm text-gray-500">
                                            <?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?>
                                        </span>
                                    </div>
                                    <p class="mt-2 text-gray-700"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                    <div class="mt-3">
                                        <button onclick="openReplyModal(<?php echo htmlspecialchars(json_encode([
                                            'recipient_name' => $msg['name'],
                                            'recipient_email' => $msg['email'],
                                            'car_title' => $msg['car_title'],
                                            'original_message' => $msg['message']
                                        ])); ?>)" 
                                                class="inline-flex items-center text-sm text-green-600 hover:text-green-700">
                                            <i class="fas fa-reply mr-2"></i>Reply via Email
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600 text-center py-4">No messages yet. When someone inquires about your listings, you'll see their messages here.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- My Listings Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="my-listings">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-list mr-2"></i>My Listed Cars
            </h2>
            <div class="border-t border-gray-200 pt-4">
                <?php if(count($listings) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach($listings as $car): ?>
                            <div class="bg-gray-50 rounded-lg overflow-hidden">
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
                                            Status: <?php echo htmlspecialchars($car['status']); ?>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <a href="car-details.php?id=<?php echo $car['id']; ?>" 
                                           class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600 text-center py-4">You haven't listed any cars yet. Start selling by adding your first listing!</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Favorites Section -->
        <div class="bg-white rounded-lg shadow-sm p-6" id="favorites">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-heart mr-2"></i>Favorite Cars
            </h2>
            <div class="border-t border-gray-200 pt-4">
                <?php if(count($favorites) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach($favorites as $car): ?>
                            <div class="bg-gray-50 rounded-lg overflow-hidden">
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
                                            Listed by <?php echo htmlspecialchars($car['seller_name']); ?>
                                        </span>
                                    </div>
                                    <div class="mt-4 flex space-x-2">
                                        <a href="car-details.php?id=<?php echo $car['id']; ?>" 
                                           class="flex-1 text-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                            View Details
                                        </a>
                                        <button onclick="toggleFavorite(<?php echo $car['id']; ?>)" 
                                                class="px-4 py-2 border border-red-500 text-red-500 rounded-md hover:bg-red-50">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600 text-center py-4">No favorites yet. Browse cars and click the heart icon to save them here!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Reply Modal -->
    <div id="replyModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Reply to Inquiry</h3>
                    <button onclick="closeReplyModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="replyForm" onsubmit="submitReplyForm(event)" class="space-y-4">
                    <input type="hidden" id="reply_to_email" name="reply_to_email">
                    <input type="hidden" id="reply_to_name" name="reply_to_name">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">To</label>
                        <p id="recipient_display" class="mt-1 text-sm text-gray-600"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" id="reply_subject" name="subject" readonly
                               class="mt-1 block w-full rounded-md bg-gray-50 border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Original Message</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-md text-sm text-gray-600">
                            <p id="original_message"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Your Reply</label>
                        <textarea name="message" id="reply_message" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeReplyModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                            Send Reply
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
                    // Refresh the page to update the favorites list
                    window.location.reload();
                }
            });
        }

        function openReplyModal(data) {
            document.getElementById('replyModal').classList.remove('hidden');
            document.getElementById('reply_to_email').value = data.recipient_email;
            document.getElementById('reply_to_name').value = data.recipient_name;
            document.getElementById('recipient_display').textContent = `${data.recipient_name} <${data.recipient_email}>`;
            document.getElementById('reply_subject').value = `Re: ${data.car_title}`;
            document.getElementById('original_message').textContent = data.original_message;
            
            // Set default reply message
            const defaultReply = `Dear ${data.recipient_name},\n\nThank you for your interest in ${data.car_title}.\n\n`;
            document.getElementById('reply_message').value = defaultReply;
        }

        function closeReplyModal() {
            document.getElementById('replyModal').classList.add('hidden');
        }

        function submitReplyForm(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('api/send-reply.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reply sent successfully!');
                    closeReplyModal();
                    form.reset();
                } else {
                    alert(data.message || 'Error sending reply. Please try again.');
                }
            })
            .catch(error => {
                alert('Error sending reply. Please try again.');
            });
        }

        // Close modal when clicking outside
        document.getElementById('replyModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeReplyModal();
            }
        });
    </script>
</body>
</html> 