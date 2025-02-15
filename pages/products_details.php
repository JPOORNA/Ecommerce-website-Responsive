<?php
session_start();
include '../includes/db_config.php';

// Fetch product details based on ID
if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Redirect if product not found
if (!$product) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?> | ShopEase</title>
  <link rel="stylesheet" href="styles.css">
  <!-- Font Awesome for any additional icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <!-- Inline CSS for Product Details specific customizations -->
  <style>
    /* Product Details Section */
    .product-details {
      padding: 40px 20px;
      background-color: #f4f4f4;
    }
    .product-details .container {
      max-width: 1200px;
      margin: auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      align-items: center;
    }
    /* Product Image */
    .product-image img {
      width: 100%;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }
    .product-image img:hover {
      transform: scale(1.05);
    }
    /* Product Info */
    .product-info h1 {
      font-size: 2.5em;
      margin-bottom: 20px;
      color: #333;
    }
    .product-info .price {
      font-size: 1.5em;
      color: #f57f00;
      margin-bottom: 20px;
    }
    /* Size Selector */
    .size-selector {
      margin-bottom: 20px;
    }
    .size-selector label {
      font-weight: bold;
      margin-bottom: 8px;
      display: block;
      color: #333;
    }
    .size-selector select {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ddd;
      border-radius: 5px;
      background-color: #fff;
      transition: border-color 0.3s;
    }
    .size-selector select:focus {
      border-color: #007bff;
    }
    /* Color Selector */
    .color-selector {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }
    .color-selector label {
      font-weight: bold;
      color: #333;
    }
    .color-options {
      display: flex;
      gap: 10px;
    }
    .color-option {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      cursor: pointer;
      border: 2px solid transparent;
      transition: border-color 0.3s, transform 0.3s;
    }
    .color-option:hover {
      transform: scale(1.1);
    }
    .color-option.selected {
      border-color: #000;
    }
    /* Hide the hidden input if desired */
    #selected_color {
      display: none;
    }
    /* Add to Cart Button */
    .btn {
      display: inline-block;
      background-color: #007bff;
      color: #fff;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      font-size: 1em;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.3s;
      margin-top: 20px;
    }
    .btn:hover {
      background-color: #0056b3;
      transform: scale(1.03);
    }
    /* Responsive Styles */
    @media (max-width: 768px) {
      .product-details .container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <!-- Product Details Section -->
  <section class="product-details">
    <div class="container">
      <!-- Left Column: Product Image -->
      <div class="product-image">
        <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
      </div>
      <!-- Right Column: Product Info -->
      <div class="product-info">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p class="price">$<?= number_format($product['price'], 2) ?></p>
        <!-- Size Selection -->
        <div class="size-selector">
          <label for="size">Size:</label>
          <select name="size" id="size" required>
            <option value="">Select Size</option>
            <option value="X">X</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
            <option value="XXL">XXL</option>
          </select>
        </div>
        <!-- Color Selection -->
        <div class="color-selector">
          <label>Color:</label>
          <div class="color-options">
            <div class="color-option red" data-color="Red" style="background-color: #ff0000;"></div>
            <div class="color-option blue" data-color="Blue" style="background-color: #0000ff;"></div>
            <div class="color-option green" data-color="Green" style="background-color: #00ff00;"></div>
            <div class="color-option black" data-color="Black" style="background-color: #000000;"></div>
            <div class="color-option white" data-color="White" style="background-color: #ffffff; border: 1px solid #ccc;"></div>
          </div>
          <input type="hidden" name="selected_color" id="selected_color" required>
        </div>
        <!-- Add to Cart Form -->
        <form method="POST" action="cart.php">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
          <input type="hidden" name="size" id="selected_size">
          <input type="hidden" name="color" id="selected_color_input">
          <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
        </form>
      </div>
    </div>
  </section>

  <!-- JavaScript for Selection Handling -->
  <script>
    // Update hidden input for size selection
    document.getElementById('size').addEventListener('change', function() {
      document.getElementById('selected_size').value = this.value;
    });

    // Color selection handler
    document.querySelectorAll('.color-option').forEach(option => {
      option.addEventListener('click', function() {
        // Remove any previous selection
        document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
        // Add selected class to clicked option
        this.classList.add('selected');
        // Update hidden input value with selected color
        document.getElementById('selected_color_input').value = this.dataset.color;
        document.getElementById('selected_color').value = this.dataset.color;
      });
    });

    // Basic form validation to ensure selections are made
    document.querySelector('form').addEventListener('submit', function(e) {
      if (!document.getElementById('selected_size').value || !document.getElementById('selected_color_input').value) {
        e.preventDefault();
        alert('Please select both size and color before adding to cart.');
      }
    });
  </script>
</body>
</html>
