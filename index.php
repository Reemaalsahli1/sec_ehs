<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
require 'auth.php';

// Require login
require_login();

$result = $mysqli->query("SELECT * FROM tickets ORDER BY created_at DESC");
if (!$result) {
    die("Query error: " . $mysqli->error);
}

$current_user = get_logged_in_user();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>EHS Tickets</title>
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
    color: #1c4792; /* blue */
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
  a.button {
    display: inline-block;
    margin-bottom: 15px;
    text-decoration: none;
    background-color: #fc7100; /* orange */
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }
  a.button:hover {
    background-color: #d65900; /* darker orange */
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
  table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-size: 14px;
  }
  th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
    vertical-align: top;
  }
  th {
    background-color: #1c4792; /* blue */
    color: white;
    font-weight: bold;
  }
  tr:nth-child(even) {
    background-color: #f2f6fc;
  }
  tr:hover {
    background-color: #e8f4fd;
  }
  td.actions a {
    margin-right: 10px;
    font-weight: bold;
    text-decoration: none;
    cursor: pointer;
  }
  td.actions a.edit {
    color: #1c4792;
  }
  td.actions a.edit:hover {
    text-decoration: underline;
  }
  td.actions a.delete {
    color: #fc7100;
  }
  td.actions a.delete:hover {
    text-decoration: underline;
  }
  .description {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .priority-high {
    color: #d32f2f;
    font-weight: bold;
  }
  .priority-medium {
    color: #f57c00;
    font-weight: bold;
  }
  .priority-low {
    color: #388e3c;
    font-weight: bold;
  }
  .status-open {
    color: #1976d2;
    font-weight: bold;
  }
  .status-progress {
    color: #f57c00;
    font-weight: bold;
  }
  .status-closed {
    color: #388e3c;
    font-weight: bold;
  }
</style>
</head>
<body>

<header>
  <div class="header-left">
    <img src="sec_logo.png" alt="Company Logo" class="logo">
    <h1>EHS Tickets Tracker System</h1>
  </div>
  <div class="header-right">
    <div class="user-info">
      <span>Welcome, <span class="user-name"><?= htmlspecialchars($current_user['name']) ?></span></span>
      <span>(<?= htmlspecialchars($current_user['employee_id']) ?>)</span>
    </div>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</header>

<a href="create.php" class="button">+ New Ticket</a>

<table>
<tr>
    <th>ID</th>
    <th>Title</th>
    <th>Description</th>
    <th>Priority</th>
    <th>Status</th>
    <th>Category</th>
    <th>Created By</th>
    <th>Assigned To</th>
    <th>Created</th>
    <th>Actions</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['title']) ?></td>
    <td class="description" title="<?= htmlspecialchars($row['description']) ?>"><?= htmlspecialchars($row['description']) ?></td>
    <td class="priority-<?= strtolower($row['priority']) ?>"><?= $row['priority'] ?></td>
    <td class="status-<?= str_replace(' ', '-', strtolower($row['status'])) ?>"><?= $row['status'] ?></td>
    <td><?= htmlspecialchars($row['category']) ?></td>
    <td><?= htmlspecialchars($row['created_by']) ?></td>
    <td><?= htmlspecialchars($row['assigned_to']) ?></td>
    <td><?= $row['created_at'] ?></td>
    <td class="actions">
      <a href="edit.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
      <a href="delete.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this ticket?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
