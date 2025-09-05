<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "EHS Ticket Tracker";
$socket = "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock";

// Create connection with socket parameter
$mysqli = new mysqli($servername, $username, $password, $dbname, null, $socket);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
