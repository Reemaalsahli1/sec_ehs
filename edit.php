<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
require 'auth.php';

// Require login
require_login();

$current_user = get_logged_in_user();  // Updated function call here

if (!isset($_GET['id'])) {
    die("Ticket ID is required.");
}

$id = (int)$_GET['id'];

// On form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? '';
    $status = $_POST['status'] ?? '';
    $category = trim($_POST['category'] ?? '');
    $created_by = trim($_POST['created_by'] ?? '');
    $assigned_to = trim($_POST['assigned_to'] ?? '');

    // basic validation
    if ($title === '') { 
        $error = "Title is required."; 
    } else {
        $stmt = $mysqli->prepare("UPDATE tickets SET title=?, description=?, priority=?, status=?, category=?, created_by=?, assigned_to=? WHERE id=?");
        $stmt->bind_param("sssssssi", $title, $description, $priority, $status, $category, $created_by, $assigned_to, $id);
        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Update failed: " . $stmt->error;
        }
    }
}

// Fetch existing ticket data
$stmt = $mysqli->prepare("SELECT title, description, priority, status, category, created_by, assigned_to FROM tickets WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

if (!$ticket) {
    die("Ticket not found.");
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Ticket - EHS Tickets Tracker</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f7f9fc;
    color: #333;
    margin: 20px;
  }
  header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
  }
  .header-left {
    display: flex;
    align-items: center;
  }
  .header-right {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  header img.logo {
    height: 60px;
    margin-right: 15px;
  }
  h1 {
    color: #1c4792;
    margin: 0;
  }
  .user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #666;
    font-size: 14px;
  }
  .user-name {
    font-weight: bold;
    color: #1c4792;
  }
  a.logout {
    text-decoration: none;
    color: #fc7100;
    font-weight: bold;
    padding: 8px 15px;
    border: 2px solid #fc7100;
    border-radius: 5px;
    transition: all 0.3s ease;
  }
  a.logout:hover {
    background-color: #fc7100;
    color: white;
  }
  .form-container {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 600px;
  }
  .error {
    color: #d32f2f;
    background-color: #ffebee;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 20px;
  }
  label {
    display: block;
    margin-top: 15px;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
  }
  input[type=text], select, textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
  }
  textarea {
    height: 100px;
    resize: vertical;
  }
  select {
    background-color: white;
  }
  button {
    margin-top: 20px;
    background-color: #fc7100;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #d65900;
  }
  a.back-link {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #1c4792;
    font-weight: bold;
  }
  a.back-link:hover {
    text-decoration: underline;
  }
  .form-row {
    display: flex;
    gap: 20px;
  }
  .form-row > div {
    flex: 1;
  }
</style>
</head>
<body>

<header>
  <div class="header-left">
    <img src="sec_logo.png" alt="Company Logo" class="logo">
    <h1>Edit Ticket #<?= $id ?></h1>
  </div>
  <div class="header-right">
    <div class="user-info">
      <span>Welcome, <span class="user-name"><?= htmlspecialchars($current_user['name']) ?></span></span>
      <span>(<?= htmlspecialchars($current_user['employee_id']) ?>)</span>
    </div>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</header>

<div class="form-container">
  <?php if(!empty($error)) echo "<p class='error'>".htmlspecialchars($error)."</p>"; ?>
  
  <form method="post">
    <label>Title *</label>
    <input type="text" name="title" value="<?= htmlspecialchars($ticket['title']) ?>" required>
    
    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($ticket['description']) ?></textarea>
    
    <div class="form-row">
      <div>
        <label>Priority *</label>
        <select name="priority" required>
          <option value="Low" <?= $ticket['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
          <option value="Medium" <?= $ticket['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
          <option value="High" <?= $ticket['priority'] === 'High' ? 'selected' : '' ?>>High</option>
        </select>
      </div>
      <div>
        <label>Status *</label>
        <select name="status" required>
          <option value="Open" <?= $ticket['status'] === 'Open' ? 'selected' : '' ?>>Open</option>
          <option value="In Progress" <?= $ticket['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
          <option value="Closed" <?= $ticket['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
        </select>
      </div>
    </div>
    
    <label>Category</label>
    <input type="text" name="category" value="<?= htmlspecialchars($ticket['category']) ?>">
    
    <div class="form-row">
      <div>
        <label>Created By</label>
        <input type="text" name="created_by" value="<?= htmlspecialchars($ticket['created_by']) ?>">
      </div>
      <div>
        <label>Assigned To</label>
        <input type="text" name="assigned_to" value="<?= htmlspecialchars($ticket['assigned_to']) ?>">
      </div>
    </div>
    
    <button type="submit">Save Changes</button>
  </form>
  
  <a href="index.php" class="back-link">← Back to tickets list</a>
</div>

</body>
</html>
