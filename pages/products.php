<?php
session_start();
include '../includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image'];

    // Save the uploaded image
    $imageName = time() . '_' . basename($image['name']);
    $targetDir = "images/";
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($image['tmp_name'], $targetFile)) {
        // Insert product into the database
        $stmt = $conn->prepare("INSERT INTO products (name, price, image) VALUES (:name, :price, :image)");
        $stmt->execute(['name' => $name, 'price' => $price, 'image' => $imageName]);

        header("Location: products.php");
        exit();
    } else {
        $error = "Failed to upload the image.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product | ShopEase</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet">
  <style>
    /* Reset & Global Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    html, body {
      font-family: 'Poppins', sans-serif;
      background: #f0f2f5;
      color: #333;
      line-height: 1.6;
    }
    a {
      text-decoration: none;
      transition: color 0.3s;
    }
    /* Header */
    header {
      background: linear-gradient(90deg, #a75d14, #d4a373);
      color: #fff;
      padding: 20px 0;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    header .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    header .logo {
      font-size: 28px;
      font-weight: 600;
    }
    header nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      align-items: center;
    }
    header nav ul li a {
      color: #fff;
      font-size: 16px;
      font-weight: 500;
    }
    header nav ul li a:hover {
      color: #ffd700;
    }
    header nav ul li form button {
      background: none;
      border: none;
      color: #fff;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: color 0.3s;
    }
    header nav ul li form button:hover {
      color: #ffd700;
    }
    /* Main Section */
    main {
      padding: 40px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: calc(100vh - 160px);
    }
    .form-card {
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 600px;
      transition: transform 0.3s;
    }
    .form-card:hover {
      transform: translateY(-3px);
    }
    .form-card h3 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
      font-size: 24px;
      font-weight: 600;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #555;
    }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="file"] {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
      transition: border 0.3s;
    }
    .form-group input:focus {
      border-color: #007bff;
      outline: none;
    }
    .btn {
      display: inline-block;
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, #007bff, #0056b3);
      color: #fff;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: 600;
      text-transform: uppercase;
      cursor: pointer;
      transition: background 0.3s, transform 0.3s;
    }
    .btn:hover {
      background: linear-gradient(90deg, #0056b3, #003f7f);
      transform: scale(1.02);
    }
    .error-message {
      text-align: center;
      color: #e74c3c;
      margin-top: 10px;
      font-weight: 500;
    }
    /* Footer */
    footer {
      background: #2f343e;
      color: #fff;
      padding: 20px 0;
      text-align: center;
      font-size: 14px;
    }
    footer .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    /* Responsive */
    @media (max-width: 768px) {
      header .container {
        flex-direction: column;
        align-items: flex-start;
      }
      main {
        padding: 20px;
      }
      .form-card {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <!-- Header Section -->
  <header>
    <div class="container header-container">
      <div class="logo">ShopEase</div>
      <nav>
        <ul>
          <li><a href="products.php">Products</a></li>
          <li><a href="admin.php">Dashboard</a></li>
          <li>
            <form method="POST" style="display: inline;">
              <button type="submit" name="logout">Logout</button>
            </form>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Main Section -->
  <main>
    <div class="form-card">
      <h3>Add New Product</h3>
      <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="name">Product Name:</label>
          <input type="text" id="name" name="name" placeholder="Enter product name" required>
        </div>
        <div class="form-group">
          <label for="price">Price ($):</label>
          <input type="number" id="price" name="price" step="0.01" placeholder="Enter product price" required>
        </div>
        <div class="form-group">
          <label for="image">Product Image:</label>
          <input type="file" id="image" name="image" accept="image/*" required>
        </div>
        <button type="submit" class="btn">Add Product</button>
      </form>
      <?php if (isset($error)): ?>
        <p class="error-message"><?= $error ?></p>
      <?php endif; ?>
    </div>
  </main>

  <!-- Footer Section -->
  <footer>
    <div class="container footer-container">
      <p>&copy; 2025 ShopEase. All Rights Reserved.</p>
    </div>
  </footer>
</body>
</html>
