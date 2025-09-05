<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
require 'auth.php';

// Require login
require_login();

$current_user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'Medium';
    $category = $_POST['category'] ?? '';
    $created_by = $_POST['created_by'] ?? $current_user['name'];
    $assigned_to = $_POST['assigned_to'] ?? '';
    $status = 'Open';

    // basic validation
    if ($title === '') { $error = "Title is required."; }
    else {
        $stmt = $mysqli->prepare("INSERT INTO tickets (title, description, priority, status, category, created_by, assigned_to) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $title, $description, $priority, $status, $category, $created_by, $assigned_to);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Create Ticket - EHS Tickets Tracker</title>
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
    <h1>Create New Ticket</h1>
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
    <input name="title" required>
    
    <label>Description</label>
    <textarea name="description"></textarea>
    
    <div class="form-row">
      <div>
        <label>Priority</label>
        <select name="priority">
          <option value="Low">Low</option>
          <option value="Medium" selected>Medium</option>
          <option value="High">High</option>
        </select>
      </div>
      <div>
        <label>Category</label>
        <input name="category">
      </div>
    </div>
    
    <div class="form-row">
      <div>
        <label>Created by</label>
        <input name="created_by" value="<?= htmlspecialchars($current_user['name']) ?>" readonly style="background-color: #f5f5f5;">
      </div>
      <div>
        <label>Assigned to</label>
        <input name="assigned_to">
      </div>
    </div>
    
    <button type="submit">Create Ticket</button>
  </form>
  
  <a href="index.php" class="back-link">← Back to tickets list</a>
</div>

</body>
</html>
