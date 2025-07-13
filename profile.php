<?php
session_start();
require 'db.php';  // Database connection

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT name, batch, current_position, profile_pic FROM alumni WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode(["success" => true, "name" => $data["name"], "batch" => $data["batch"], "current_position" => $data["current_position"], "profile_pic" => $data["profile_pic"]]);
} else {
    echo json_encode(["success" => false, "message" => "Profile not found"]);
}
?>
