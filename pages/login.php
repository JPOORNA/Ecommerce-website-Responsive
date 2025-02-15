<?php
// Start session
session_start();

// Include database configuration
include '../includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate user credentials
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Store the role of the user

            echo "<script>alert('Login successful!');</script>";
            // Redirect to dashboard or homepage
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "Username does not exist.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Shopeease</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
    }

    body {
      background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96c93d);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .welcome-text {
      position: absolute;
      top: 10%;
      color: white;
      font-size: 2.5rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
      animation: fadeInDown 1s ease;
    }

    .container {
      background: rgba(255, 255, 255, 0.95);
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      width: 400px;
      transform-style: preserve-3d;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }

    .container:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }

    .container::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
      transform: rotate(45deg);
      animation: shine 3s infinite;
    }

    h2 {
      color: #2c3e50;
      margin-bottom: 1.5rem;
      font-size: 2rem;
      position: relative;
      z-index: 2;
    }

    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
      z-index: 2;
    }

    .input-group {
      position: relative;
      margin-bottom: 1.5rem;
    }

    .input-group i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #7f8c8d;
    }

    .input-group input {
      width: 100%;
      padding: 12px 20px 12px 45px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.9);
    }

    .input-group input:focus {
      border-color: #3498db;
      box-shadow: 0 0 15px rgba(52,152,219,0.3);
    }

    button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(45deg, #3498db, #6c5ce7);
      border: none;
      border-radius: 8px;
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    button:hover {
      background: linear-gradient(45deg, #6c5ce7, #3498db);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .link {
      margin-top: 1rem;
      text-align: center;
    }

    .link a {
      color: #3498db;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .link a:hover {
      color: #2980b9;
    }

    .admin-btn-container {
      margin-top: 1.5rem;
      text-align: center;
    }

    .admin-btn {
      display: inline-block;
      padding: 10px 20px;
      background: linear-gradient(45deg, #e74c3c, #e67e22);
      color: white;
      border-radius: 6px;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .admin-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes shine {
      to {
        left: 150%;
        top: 150%;
      }
    }

    @media (max-width: 480px) {
      .container {
        width: 90%;
        padding: 1.5rem;
      }
      .welcome-text {
        font-size: 2rem;
        top: 15%;
      }
    }
  </style>
</head>
<body>
  <h1 class="welcome-text">Welcome to ShopEase</h1>
  <div class="container">
    <h2>Member Login</h2>
    <?php if (!empty($error)) { echo "<p style='color: #e74c3c; margin-bottom: 1rem;'>$error</p>"; } ?>
    <form method="POST" action="login.php">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit">Sign In</button>
      <p class="link">New user? <a href="register.php">Create account</a></p>
    </form>
    <div class="admin-btn-container">
      <a href="admin_login.php" class="admin-btn">Admin Dashboard</a>
    </div>
  </div>
</body>
</html>