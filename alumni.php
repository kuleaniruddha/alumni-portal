<?php
session_start(); // Start the session

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Database connection
$conn = new mysqli("localhost", "root", "", "xie_alumni");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch current user data based on session user_id
$sql = "SELECT name, email, batch, job, password FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if data was found
if (!$user) {
    die("User not found");
}

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_profile"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $batch = $_POST["batch"];
    $job = $_POST["job"];

    // Update the user data in the database
    $update_sql = "UPDATE users SET name = ?, email = ?, batch = ?, job = ? WHERE user_id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("sssii", $name, $email, $batch, $job, $user_id);
    $stmt_update->execute();

    // Redirect to the same page to show updated profile and show the success message
    header("Location: alumni.php?update=success");
    exit();
}

// Handle Password Change
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Check if the current password matches
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $stmt_update_password = $conn->prepare($update_password_sql);
            $stmt_update_password->bind_param("si", $hashed_password, $user_id);
            $stmt_update_password->execute();
            header("Location: alumni.php?password_update=success");
            exit();
        } else {
            $password_error = "New password and confirmation do not match!";
        }
    } else {
        $password_error = "Current password is incorrect!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alumni Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff; /* Light blue background */
        }
        .container {
            width: 100%;
            max-width: 900px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #007bff; /* Blue top border */
        }
        h2 {
            color: #333;
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            color: #555;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #007bff;
        }
        .form-group input[type="text"], 
        .form-group input[type="email"], 
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 14px;
        }
        .form-group input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #28a745; /* Green background */
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-group input[type="submit"]:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .success {
            color: green;
            margin-top: 10px;
        }
        hr {
            margin: 20px 0;
            border-top: 1px solid #ddd;
        }
        .logout {
            display: block;
            text-align: center;
            font-size: 16px;
            color: #007bff;
            margin-top: 20px;
            text-decoration: none;
        }
        .logout:hover {
            text-decoration: underline;
        }
        /* Chat button */
        .chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #ff5733; /* Red background */
            color: #fff;
            padding: 15px 20px;
            border-radius: 50%;
            font-size: 18px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            border: none;
            text-align: center;
        }
        .chat-button:hover {
            background-color: #e64a19;
        }
    </style>
    <script>
        // Show a popup if the profile update or password change is successful
        window.onload = function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('update') && urlParams.get('update') === 'success') {
                alert("Your profile has been updated successfully!");
            }

            if (urlParams.has('password_update') && urlParams.get('password_update') === 'success') {
                alert("Your password has been updated successfully!");
            }
        };
    </script>
</head>
<body>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($user["name"]); ?></h2>

        <!-- Profile Update Form -->
        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" required>
            </div>
            <div class="form-group">
                <label for="batch">Batch:</label>
                <input type="text" id="batch" name="batch" value="<?php echo htmlspecialchars($user["batch"]); ?>" required>
            </div>
            <div class="form-group">
                <label for="job">Job:</label>
                <input type="text" id="job" name="job" value="<?php echo htmlspecialchars($user["job"]); ?>" required>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user["name"]); ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" name="update_profile" value="Update Profile">
            </div>
        </form>

        <hr>

        <!-- Password Change Form -->
        <h3>Change Password</h3>
        <form method="POST">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <?php if (isset($password_error)): ?>
                <p class="error"><?php echo $password_error; ?></p>
            <?php endif; ?>
            <div class="form-group">
                <input type="submit" name="change_password" value="Change Password">
            </div>
        </form>

        <a href="logout.php" class="logout">Logout</a>
    </div>

    <!-- Chat Button -->
    <a href="chat_admin.php" class="chat-button">💬</a>

</body>
</html>
