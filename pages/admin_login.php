<?php
// Database connection
include '../includes/db_config.php';

// Admin credentials
$username = 'admin'; // Change this to your desired username
$password = password_hash('admin123', PASSWORD_BCRYPT); // Replace 'admin123' with your desired password

// Initialize error variable
$error = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    try {
        // Fetch the admin credentials from the database
        $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
        $stmt->execute([$input_username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($input_password, $admin['password'])) {
            // Redirect to admin.php if credentials are correct
            header('Location: admin.php');
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

try {
    // Check if the admin username already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "Admin username already exists.";
    } else {
        // Insert admin credentials into the database
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $password])) {
            echo "Admin credentials added successfully!";
        } else {
            echo "Error adding admin credentials.";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

<style>
/* Global Styles */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #6a11cb 0%, rgb(252, 187, 37) 100%);
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Login Container */
.login-container {
    width: 100%;
    max-width: 400px;
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.login-container h1 {
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
}

/* Error Message */
.error {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
}

/* Form Styles */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    text-align: left;
    font-weight: bold;
    font-size: 14px;
    color: #333;
}

input {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
    box-sizing: border-box;
    transition: border-color 0.3s ease-in-out;
}

input:focus {
    border-color: #6a11cb;
    outline: none;
}

/* Button */
button {
    padding: 10px 15px;
    font-size: 16px;
    color: #fff;
    background-color: #6a11cb;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}

button:hover {
    background-color: #2575fc;
}

/* Footer */
.login-footer {
    margin-top: 20px;
}

.login-footer p {
    font-size: 14px;
    color: #666;
}

.login-footer a {
    color: #6a11cb;
    text-decoration: none;
}

.login-footer a:hover {
    text-decoration: underline;
}
</style>