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

// Fetch messages where the logged-in user is either the sender or receiver
$sql = "SELECT m.message_text, m.timestamp, u.name AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE (m.sender_id = ? OR m.receiver_id = ?)
        ORDER BY m.timestamp ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle message submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["send_message"])) {
    $message = $_POST["message"];
    $receiver_id = $_POST["receiver_id"]; // Assuming you're sending the message to an admin or another user

    // Insert the new message into the database
    $insert_sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("iis", $user_id, $receiver_id, $message);
    $stmt_insert->execute();

    // Redirect to refresh and show the new message
    header("Location: chat_admin.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with Admin</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff;
        }
        .chat-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .chat-messages {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding-right: 10px;
        }
        .chat-message {
            background-color: #f1f1f1;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .chat-message .sender {
            font-weight: bold;
        }
        .chat-input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 14px;
        }
        .send-button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .send-button:hover {
            background-color: #218838;
        }
        .logout {
            display: block;
            text-align: center;
            font-size: 16px;
            color: #007bff;
            margin-top: 20px;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="chat-container">
        <h2>Chat with Admin</h2>

        <div class="chat-messages">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="chat-message">
                    <p class="sender"><?php echo htmlspecialchars($row["sender_name"]); ?>:</p>
                    <p><?php echo nl2br(htmlspecialchars($row["message_text"])); ?></p>
                    <small><?php echo $row["timestamp"]; ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Message input -->
        <form method="POST">
            <textarea name="message" class="chat-input" placeholder="Type your message..." required></textarea>
            <input type="hidden" name="receiver_id" value="1"> <!-- Assuming admin has user_id 1 -->
            <button type="submit" name="send_message" class="send-button">Send Message</button>
        </form>

        <a href="alumni.php" class="logout">Back to Profile</a>
    </div>

</body>
</html>
