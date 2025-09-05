<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

// If user is already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = trim($_POST['employee_id'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($employee_id) || empty($password)) {
        $error = "Employee ID and password are required.";
    } else {
        // Check if users table exists, if not create it with sample data
        $check_table = $mysqli->query("SHOW TABLES LIKE 'users'");
        if ($check_table->num_rows == 0) {
            // Create users table
            $create_table = "CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $mysqli->query($create_table);
            
            // Insert sample users (password: 123456)
            $sample_users = [
                ['EMP001', 'John Doe', 'john.doe@company.com'],
                ['EMP002', 'Jane Smith', 'jane.smith@company.com'],
                ['EMP003', 'Mike Johnson', 'mike.johnson@company.com']
            ];
            
            $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO users (employee_id, password, name, email) VALUES (?, ?, ?, ?)");
            
            foreach ($sample_users as $user) {
                $stmt->bind_param("ssss", $user[0], $hashed_password, $user[1], $user[2]);
                $stmt->execute();
            }
        }
        
        // Authenticate user
        $stmt = $mysqli->prepare("SELECT id, employee_id, password, name FROM users WHERE employee_id = ?");
        $stmt->bind_param("s", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['employee_id'] = $user['employee_id'];
            $_SESSION['user_name'] = $user['name'];
            
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid employee ID or password.";
        }
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login - EHS Tickets Tracker</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f7f9fc;
    color: #333;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
  }
  
  .login-container {
    background-color: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
  }
  
  .logo-section {
    text-align: center;
    margin-bottom: 30px;
  }
  
  .logo-section img {
    height: 80px;
    margin-bottom: 15px;
  }
  
  h1 {
    color: #1c4792;
    margin: 0;
    font-size: 24px;
    text-align: center;
  }
  
  .subtitle {
    text-align: center;
    color: #666;
    margin-top: 5px;
    font-size: 14px;
  }
  
  .error {
    color: #d32f2f;
    background-color: #ffebee;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    text-align: center;
    font-size: 14px;
  }
  
  .form-group {
    margin-bottom: 20px;
  }
  
  label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
    font-size: 14px;
  }
  
  input[type=text], input[type=password] {
    width: 100%;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
  }
  
  input[type=text]:focus, input[type=password]:focus {
    outline: none;
    border-color: #1c4792;
  }
  
  button {
    width: 100%;
    background-color: #fc7100;
    color: white;
    padding: 14px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }
  
  button:hover {
    background-color: #d65900;
  }
  
  .demo-info {
    margin-top: 30px;
    padding: 15px;
    background-color: #e3f2fd;
    border-radius: 5px;
    font-size: 13px;
    color: #1976d2;
  }
  
  .demo-info h4 {
    margin: 0 0 10px 0;
    color: #1565c0;
  }
  
  .demo-info ul {
    margin: 0;
    padding-left: 20px;
  }
  
  .demo-info li {
    margin-bottom: 5px;
  }
</style>
</head>
<body>

<div class="login-container">
  <div class="logo-section">
    <img src="sec_logo.png" alt="Company Logo">
    <h1>EHS Tickets Tracker</h1>
    <p class="subtitle">Employee Login</p>
  </div>
  
  <?php if(!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  
  <form method="post">
    <div class="form-group">
      <label for="employee_id">Employee ID</label>
      <input type="text" id="employee_id" name="employee_id" value="<?= htmlspecialchars($_POST['employee_id'] ?? '') ?>" required autofocus>
    </div>
    
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>
    
    <button type="submit">Login</button>
  </form>
  
  <div class="demo-info">
    <h4>Demo Login Credentials:</h4>
    <ul>
      <li><strong>Employee ID:</strong> EMP001, EMP002, or EMP003</li>
      <li><strong>Password:</strong> 123456</li>
    </ul>
  </div>
</div>

</body>
</html>
