<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "xie_alumni");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Alumni
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_alumni"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $batch = $_POST["batch"];
    $job = $_POST["job"];
    $role = "alumni";

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, batch, job, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $password, $batch, $job, $role);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete Alumni
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $delete_id = $_POST["delete_id"];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'alumni'");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch All Alumni
$result = $conn->query("SELECT user_id, name, email, batch, job FROM users WHERE role = 'alumni'");
$alumni = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }
        
        h2, h3 {
            font-family: 'Roboto', sans-serif;
            color: #003366;
            text-align: center;
        }
        
        h2 {
            margin-top: 20px;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
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

        td button {
            padding: 8px 16px;
            background-color: #ff4c4c;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        td button:hover {
            background-color: #e44b47;
        }

        form {
            margin-top: 40px;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        form input[type="text"], form input[type="email"], form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        form input[type="submit"] {
            background-color: #003366;
            color: white;
            border: none;
            padding: 12px 24px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }

        form input[type="submit"]:hover {
            background-color: #005fa6;
        }

        .danger {
            background-color: #f44336;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
        }

        .danger:hover {
            background-color: #e53935;
        }

        .logout {
            background-color: #003366;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-top: 10px;
            text-align: center;
        }

        .logout:hover {
            background-color: #005fa6;
        }

        /* Chat button */
        .chat-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            border-radius: 50%;
            font-size: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            border: none;
        }

        .chat-btn:hover {
            background-color: #005fa6;
        }

        .chat-popup {
            display: none;
            position: fixed;
            bottom: 100px;
            right: 30px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
            max-width: 300px;
            width: 100%;
        }

        .chat-popup textarea {
            width: 100%;
            padding: 10px;
            height: 100px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .chat-popup button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .chat-popup button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, Admin <?php echo htmlspecialchars($_SESSION["name"]); ?></h2>
    <a href="logout.php" class="logout">Logout</a>
    <a href="view_messages.php" class="logout">View Messages</a> <!-- Add this link -->

    <h3>All Alumni</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Batch</th>
            <th>Job</th>
            <th>Action</th>
        </tr>
        <?php foreach ($alumni as $a): ?>
        <tr>
            <td><?php echo $a["user_id"]; ?></td>
            <td><?php echo htmlspecialchars($a["name"]); ?></td>
            <td><?php echo htmlspecialchars($a["email"]); ?></td>
            <td><?php echo htmlspecialchars($a["batch"]); ?></td>
            <td><?php echo htmlspecialchars($a["job"]); ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?php echo $a["user_id"]; ?>">
                    <button type="submit" class="danger" onclick="return confirm('Delete this alumni?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Add New Alumni</h3>
    <form method="POST">
        <input type="hidden" name="add_alumni" value="1">
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="text" name="batch" placeholder="Batch (e.g. 2021)" required><br>
        <input type="text" name="job" placeholder="Job Title" required><br>
        <input type="submit" value="Add Alumni">
    </form>
</div>

<!-- Chat Button -->
<a href="chat.php" target="_blank" class="chat-btn" style="position:fixed; bottom:20px; right:20px; background:#003366; color:white; padding:12px 20px; border-radius:50px; text-decoration:none; font-size:18px; z-index:999;">
    💬 Chat
</a>

</body>
</html>
