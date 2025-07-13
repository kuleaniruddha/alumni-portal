<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "xie_alumni");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all alumni users for dropdown
$alumni_result = $conn->query("SELECT user_id, name FROM users WHERE role = 'alumni'");
$alumni = $alumni_result->fetch_all(MYSQLI_ASSOC);

// Get the receiver's user_id (alumni) from the URL
$receiver_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$messages = [];

// Process form submission for sending a new message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message']) && $receiver_id > 0) {
    $message_text = $_POST['message'];
    $insert_sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iis", $_SESSION["user_id"], $receiver_id, $message_text);
    $insert_stmt->execute();
    header("Location: chat.php?user_id=" . $receiver_id);
    exit();
}

// If an alumni is selected, fetch chat history
if ($receiver_id > 0) {
    $sql = "SELECT messages.message_id, users.name, messages.message_text, messages.timestamp
            FROM messages
            JOIN users ON users.user_id = messages.sender_id
            WHERE (messages.sender_id = ? AND messages.receiver_id = ?)
               OR (messages.sender_id = ? AND messages.receiver_id = ?)
            ORDER BY messages.timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $_SESSION["user_id"], $receiver_id, $receiver_id, $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with Alumni</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #003366;
        }

        .dropdown-form {
            margin-bottom: 20px;
        }

        .dropdown-form select {
            padding: 10px;
            font-size: 1em;
        }

        .messages {
            height: 350px;
            overflow-y: scroll;
            border: 1px solid #ddd;
            background: #fafafa;
            padding: 10px;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 15px;
        }

        .message .sender {
            font-weight: bold;
        }

        .message .timestamp {
            font-size: 0.8em;
            color: #777;
        }

        .form {
            display: flex;
        }

        .form input[type="text"] {
            flex: 1;
            padding: 10px;
            font-size: 1em;
        }

        .form button {
            background: #003366;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .form button:hover {
            background: #0055a2;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Chat with Alumni</h2>

    <!-- Alumni Selection Dropdown -->
    <form class="dropdown-form" method="GET" action="chat.php">
        <label for="user_id"><strong>Select Alumni:</strong></label>
        <select name="user_id" id="user_id" onchange="this.form.submit()" required>
            <option value="">-- Select --</option>
            <?php foreach ($alumni as $a): ?>
                <option value="<?= $a['user_id']; ?>" <?= ($receiver_id == $a['user_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($receiver_id): ?>
        <!-- Displaying the chat history -->
        <div class="messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message">
                    <p class="sender"><?= htmlspecialchars($msg['name']); ?> 
                        <span class="timestamp"><?= $msg['timestamp']; ?></span></p>
                    <p><?= nl2br(htmlspecialchars($msg['message_text'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Form to send a new message -->
        <form class="form" method="POST">
            <input type="text" name="message" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
