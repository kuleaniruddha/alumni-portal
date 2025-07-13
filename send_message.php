<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "xie_alumni");

if ($conn->connect_error) {
    die("Database connection failed");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"])) {
    $message = $_POST["message"];
    $sender = "Admin"; // Since it's for the admin, we hardcode 'Admin' as sender
    $created_at = date("Y-m-d H:i:s");

    // Insert the new message into the database
    $sql = "INSERT INTO messages (message, sender, created_at) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $message, $sender, $created_at);
    $stmt->execute();
    
    // Redirect back to the chat page
    header("Location: chat_admin.php");
    exit();
}

$conn->close();
?>
