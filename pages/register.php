<?php
include '../includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email    = trim($_POST['email']);

    // Validate input
    if (empty($username) || empty($password) || empty($email)) {
        echo "All fields are required!";
        exit;
    }

    try {
        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkEmail->execute([$email]);
        $result = $checkEmail->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "Email already exists!";
        } else {
            // Insert user into database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt->execute([$username, $hashedPassword, $email])) {
                echo "Registration successful!";
            } else {
                echo "Error: Could not complete registration.";
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register | Shopeease</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    /* Reset and Base Styles */
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

    /* Animated Gradient Background */
    body {
      background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .main-wrapper {
      display: flex;
      height: 100vh;
    }

    .image-side {
      flex: 1;
      background: url('https://www.tranquilitymarketing.co.uk/wp-content/uploads/2023/03/e-commerce-girl.png') no-repeat center center;
      background-size: cover;
      position: relative;
      transition: all 0.3s ease;
    }

    .image-side:hover {
      transform: scale(1.02);
    }

    .image-text {
      position: absolute;
      bottom: 40px;
      left: 40px;
      color: white;
      font-size: 2rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
      max-width: 400px;
      line-height: 1.4;
    }

    .form-side {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(5px);
    }

    .form-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      width: 80%;
      max-width: 500px;
      transform-style: preserve-3d;
      transition: all 0.3s ease;
    }

    .form-container:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }

    h2 {
      color: #2c3e50;
      margin-bottom: 2rem;
      font-size: 2rem;
      text-align: center;
      position: relative;
    }

    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      color: #34495e;
      font-weight: 500;
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
      font-size: 1.1rem;
    }

    input {
      width: 100%;
      padding: 12px 20px 12px 45px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.9);
    }

    input:focus {
      border-color: #3498db;
      box-shadow: 0 0 15px rgba(52,152,219,0.3);
    }

    button {
      width: 100%;
      padding: 15px;
      background: linear-gradient(45deg, #3498db, #6c5ce7);
      border: none;
      border-radius: 8px;
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 1rem;
    }

    button:hover {
      background: linear-gradient(45deg, #6c5ce7, #3498db);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .login-btn-container {
      text-align: center;
      margin-top: 1.5rem;
    }

    .login-btn-container a {
      color: #3498db;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .login-btn-container a:hover {
      color: #2980b9;
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .main-wrapper {
        flex-direction: column;
      }

      .image-side {
        height: 40vh;
      }

      .image-text {
        font-size: 1.5rem;
        bottom: 20px;
        left: 20px;
      }

      .form-container {
        width: 90%;
        padding: 1.5rem;
        margin: 2rem auto;
      }
    }

    @media (max-width: 480px) {
      .form-container {
        padding: 1.2rem;
      }

      h2 {
        font-size: 1.8rem;
      }

      input {
        padding: 10px 15px 10px 40px;
      }
    }
  </style>
</head>
<body>
  <div class="main-wrapper">
    <div class="image-side">
      <div class="image-text">Join Our Growing Community</div>
    </div>
    
    <div class="form-side">
      <div class="form-container">
        <h2>Create Account</h2>
        <form action="register.php" method="POST">
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" id="username" name="username" placeholder="Username" required>
          </div>
          
          <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" id="email" name="email" placeholder="Email" required>
          </div>
          
          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Password" required>
          </div>
          
          <div class="input-group">
            <i class="fas fa-check-circle"></i>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
          </div>
          
          <button type="submit">Register Now</button>
          <div class="login-btn-container">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>