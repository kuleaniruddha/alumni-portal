<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.html");
    exit();
}

// Create connection to the database
$conn = new mysqli("localhost", "root", "", "xie_alumni");

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all messages with sender name and message text from the 'messages' table
$sql = "SELECT messages.message_id, users.name, messages.message_text, messages.timestamp
        FROM messages
        JOIN users ON users.user_id = messages.sender_id
        ORDER BY messages.timestamp DESC";
$result = $conn->query($sql);

// Check if any messages were returned
if ($result->num_rows > 0) {
    $messages = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $messages = [];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Alumni Messages</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }

        h2 {
            font-family: 'Roboto', sans-serif;
            color: #003366;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #003366;
            color: white;
        }

        td {
            background-color: #fafafa;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        a {
            color: #003366;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Messages from Alumni</h2>

    <!-- Table to display the messages -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Message</th>
            <th>Timestamp</th>
        </tr>

        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?php echo $msg["message_id"]; ?></td>
                    <td><?php echo htmlspecialchars($msg["name"]); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($msg["message_text"])); ?></td>
                    <td><?php echo $msg["timestamp"]; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">No messages found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Link back to the admin dashboard -->
    <a href="admin.php">Back to Dashboard</a>
</div>

</body>
</html>
