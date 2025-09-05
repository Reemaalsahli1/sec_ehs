<?php
require 'db.php';
require 'auth.php';

// Require login
require_login();

$current_user = get_logged_in_user();

$id = intval($_GET['id'] ?? 0); // get id from URL
if ($id <= 0) {
    die("Invalid ticket ID");
}

$stmt = $mysqli->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->bind_param('i', $id);
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
<title>View Ticket - EHS Tickets Tracker</title>
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
  .ticket-container {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 800px;
  }
  .ticket-header {
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 20px;
    margin-bottom: 30px;
  }
  .ticket-title {
    font-size: 24px;
    color: #1c4792;
    margin: 0 0 10px 0;
  }
  .ticket-meta {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }
  .meta-item {
    display: flex;
    flex-direction: column;
  }
  .meta-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    font-weight: bold;
    margin-bottom: 5px;
  }
  .meta-value {
    font-size: 16px;
    font-weight: bold;
  }
  .priority-high {
    color: #d32f2f;
  }
  .priority-medium {
    color: #f57c00;
  }
  .priority-low {
    color: #388e3c;
  }
  .status-open {
    color: #1976d2;
  }
  .status-progress {
    color: #f57c00;
  }
  .status-closed {
    color: #388e3c;
  }
  .ticket-description {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    margin: 20px 0;
    line-height: 1.6;
  }
  .actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
  }
  .actions a {
    display: inline-block;
    margin-right: 15px;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: bold;
    transition: all 0.3s ease;
  }
  .actions a.edit {
    background-color: #1c4792;
    color: white;
  }
  .actions a.edit:hover {
    background-color: #144070;
  }
  .actions a.delete {
    background-color: #fc7100;
    color: white;
  }
  .actions a.delete:hover {
    background-color: #d65900;
  }
  .actions a.back {
    color: #666;
    border: 1px solid #ddd;
  }
  .actions a.back:hover {
    background-color: #f5f5f5;
  }
</style>
</head>
<body>

<header>
  <div class="header-left">
    <img src="sec_logo.png" alt="Company Logo" class="logo">
    <h1>Ticket Details</h1>
  </div>
  <div class="header-right">
    <div class="user-info">
      <span>Welcome, <span class="user-name"><?= htmlspecialchars($current_user['name']) ?></span></span>
      <span>(<?= htmlspecialchars($current_user['employee_id']) ?>)</span>
    </div>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</header>

<div class="ticket-container">
  <div class="ticket-header">
    <h2 class="ticket-title"><?= htmlspecialchars($ticket['title']) ?></h2>
    <div class="ticket-meta">
      <div class="meta-item">
        <span class="meta-label">Ticket ID</span>
        <span class="meta-value">#<?= $ticket['id'] ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Priority</span>
        <span class="meta-value priority-<?= strtolower($ticket['priority']) ?>"><?= $ticket['priority'] ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Status</span>
        <span class="meta-value status-<?= str_replace(' ', '-', strtolower($ticket['status'])) ?>"><?= $ticket['status'] ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Category</span>
        <span class="meta-value"><?= htmlspecialchars($ticket['category']) ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Created</span>
        <span class="meta-value"><?= $ticket['created_at'] ?></span>
      </div>
    </div>
  </div>

  <div class="ticket-description">
    <strong>Description:</strong><br>
    <?= nl2br(htmlspecialchars($ticket['description'])) ?>
  </div>

  <div class="ticket-meta">
    <div class="meta-item">
      <span class="meta-label">Created By</span>
      <span class="meta-value"><?= htmlspecialchars($ticket['created_by']) ?></span>
    </div>
    <div class="meta-item">
      <span class="meta-label">Assigned To</span>
      <span class="meta-value"><?= htmlspecialchars($ticket['assigned_to']) ?></span>
    </div>
  </div>

  <div class="actions">
    <a href="edit.php?id=<?= $ticket['id'] ?>" class="edit">Edit Ticket</a>
    <a href="delete.php?id=<?= $ticket['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this ticket?');">Delete Ticket</a>
    <a href="index.php" class="back">← Back to List</a>
  </div>
</div>

</body>
</html>
