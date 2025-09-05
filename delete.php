<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
require 'auth.php';

// Require login
require_login();

if (!isset($_GET['id'])) {
    die("Ticket ID is required.");
}

$id = (int)$_GET['id'];

$stmt = $mysqli->prepare("DELETE FROM tickets WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php");
    exit;
} else {
    echo "Failed to delete ticket: " . $stmt->error;
}
?>
