<?php
// Database connection (replace with your actual credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "xie_alumni"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search term from query parameters (default empty string)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// If search term is empty, don't fetch any data
if (empty($search)) {
    echo json_encode([]); // Return an empty array when no search term is provided
    exit;
}

// SQL query to fetch users matching search term
$sql = "SELECT name, email, batch, job FROM users WHERE name LIKE ? OR batch LIKE ? OR job LIKE ?";
$stmt = $conn->prepare($sql);

// Prepare the search term for LIKE clause
$searchTerm = "%" . $search . "%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Prepare users data array
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Return the users data as JSON
echo json_encode($users);

// Close connection
$stmt->close();
$conn->close();
?>
