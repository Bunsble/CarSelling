<?php
session_start();

// Check if user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../dashboard.php");
    exit;
}

require_once "../config/database.php";

$name = $email = $password = $confirm_password = "";
$name_err = $email_err = $password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter your name.";
    } else{
        $name = trim($_POST["name"]);
    }

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else{
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sss", $param_name, $param_email, $param_password);
            
            $param_name = $name;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if(mysqli_stmt_execute($stmt)){
                header("location: login.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EcoWheels</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or
                    <a href="login.php" class="font-medium text-green-600 hover:text-green-500">sign in to your account</a>
                </p>
            </div>
            <form class="mt-8 space-y-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="name" class="sr-only">Full Name</label>
                        <input id="name" name="name" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm <?php echo (!empty($name_err)) ? 'border-red-500' : ''; ?>" placeholder="Full Name" value="<?php echo $name; ?>">
                        <?php if(!empty($name_err)): ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo $name_err; ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="sr-only">Email address</label>
                        <input id="email" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>" placeholder="Email address" value="<?php echo $email; ?>">
                        <?php if(!empty($email_err)): ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo $email_err; ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>" placeholder="Password">
                        <?php if(!empty($password_err)): ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo $password_err; ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="confirm_password" class="sr-only">Confirm Password</label>
                        <input id="confirm_password" name="confirm_password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>" placeholder="Confirm Password">
                        <?php if(!empty($confirm_password_err)): ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo $confirm_password_err; ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 