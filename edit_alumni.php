<?php
session_start();

// Check if the user is logged in and is an alumni
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "alumni") {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "xie_alumni");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the alumni details based on the logged-in user's ID
$user_id = $_SESSION["user_id"];
$sql = "SELECT user_id, name, email, batch, job FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$alumni = $result->fetch_assoc();
$stmt->close();

if (!$alumni) {
    echo "Alumni not found.";
    exit();
}

// Handle form submission to update profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $batch = $_POST["batch"];
    $job = $_POST["job"];

    $update_sql = "UPDATE users SET name = ?, email = ?, batch = ?, job = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $name, $email, $batch, $job, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Reload the page to reflect changes
    header("Location: edit_alumni.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        h2 { color: #333; }
        form { margin-top: 30px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 250px; padding: 8px; margin: 5px 0;
        }
        input[type="submit"] { padding: 10px 20px; }
    </style>
</head>
<body>

<h2>Edit Profile</h2>
<a href="logout.php">Logout</a>

<h3>Your Details</h3>
<form method="POST">
    <input type="text" name="name" value="<?php echo htmlspecialchars($alumni['name']); ?>" placeholder="Name" required><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($alumni['email']); ?>" placeholder="Email" required><br>
    <input type="text" name="batch" value="<?php echo htmlspecialchars($alumni['batch']); ?>" placeholder="Batch (e.g. 2021)" required><br>
    <input type="text" name="job" value="<?php echo htmlspecialchars($alumni['job']); ?>" placeholder="Job Title" required><br>
    <input type="submit" value="Update Profile">
</form>

</body>
</html>
