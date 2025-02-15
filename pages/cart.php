<?php
session_start();
include '../includes/db_config.php';

// Ensure the cart is set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle item removal
if (isset($_GET['remove'])) {
    $productId = $_GET['remove'];
    unset($_SESSION['cart'][$productId]);
    header('Location: cart.php'); // Refresh the page after removal
    exit;
}



// Handle "Add to Cart" action from wishlist or similar
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $size = $_POST['selected_size'] ?? '';  // Default to empty if not provided
    $color = $_POST['selected_color'] ?? ''; // Default to empty if not provided

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    // Ensure that the cart item is stored as an array
    if (!isset($_SESSION['cart'][$productId]) || !is_array($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = [
            'quantity' => 1,
            'size' => $size,
            'color' => $color
        ];
    } else {
        $_SESSION['cart'][$productId]['quantity'] += 1;
    }

    header("Location: cart.php");
    exit();
}


// Fetch cart items from session
$cartItems = $_SESSION['cart'];
$totalPrice = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopEase | Cart</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" href="favicon.png" type="image/x-icon">
</head>
<body>
  <!-- Header Section -->
  <header>
    <div class="container header-container">
      <h1 class="logo">ShopEase</h1>
      <nav class="navigation">
        <ul class="nav-links">
          <li><a href="index.php">Home</a></li>
          <li><a href="cart.php">Cart</a></li>
          <li>
            <form method="POST" action="logout.php" style="display: inline;">
              <button type="submit" class="logout-button">Logout</button>
            </form>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Main Section -->
  <main>
    <section class="cart">
      <div class="container">
        <h3 class="section-title">Your Cart</h3>
        <div class="cart-items">
          <?php if (!empty($cartItems)): ?>
            <?php foreach ($cartItems as $productId => $item): ?>
              <?php
                // If $item is not an array, convert it to an array with a default structure.
                if (!is_array($item)) {
                    $item = [
                        'quantity' => (int)$item,
                        'size' => '',
                        'color' => ''
                    ];
                }
                // Fetch product details from the database
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = :productId");
                $stmt->execute(['productId' => $productId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($product && isset($product['price']) && is_numeric($product['price'])) {
                    $totalPrice += (float)$product['price'] * (int)$item['quantity'];
                }
              ?>
              <div class="cart-item">
                <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                <div class="product-details">
                  <h4 class="product-name"><?= htmlspecialchars($product['name']) ?></h4>
                  <p class="product-price">$<?= number_format((float)$product['price'], 2) ?></p>
                  <p class="product-quantity">Quantity: <?= (int)$item['quantity'] ?></p>
                  <?php if (!empty($item['size'])): ?>
                    <p class="product-size">Size: <?= htmlspecialchars($item['size']) ?></p>
                  <?php endif; ?>
                  <?php if (!empty($item['color'])): ?>
                    <p class="product-color">Color: <?= htmlspecialchars($item['color']) ?></p>
                  <?php endif; ?>
                </div>
                <div class="product-actions">
                  <a href="cart.php?remove=<?= $productId ?>" class="remove-btn">Remove</a>
                </div>
              </div>
            <?php endforeach; ?>
            <div class="order-total">
              <p><strong>Total Price: </strong>$<?= number_format($totalPrice, 2) ?></p>
            </div>
            <div class="cart-footer">
              <a href="payment.php" class="proceed-to-order-btn">Proceed to Checkout</a>
            </div>
          <?php else: ?>
            <p class="empty-cart-message">Your cart is empty. <a href="index.php">Browse Products</a></p>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer Section -->
  <footer>
    <div class="container footer-container">
      <div class="footer-links">
        <ul>
          <li><a href="about.php">About Us</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="privacy.php">Privacy Policy</a></li>
          <li><a href="terms.php">Terms of Service</a></li>
        </ul>
      </div>
      <div class="footer-info">
        <p>&copy; 2025 ShopEase. All Rights Reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>



<style>
/* Basic cart styling */

header {
  background-color: #333;
  color: #fff;
  padding: 15px 0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
header .header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
header .logo {
  font-size: 24px;
  font-weight: bold;
  color: #fff;
}
header .nav-links {
  list-style-type: none;
  display: flex;
  gap: 20px;
  align-items: center;
}
header .nav-links li a {
  color: #fff;
  text-decoration: none;
  font-size: 16px;
  padding: 10px;
  transition: background-color 0.3s ease;
}
header .nav-links li a:hover {
  background-color: #555;
  border-radius: 5px;
}
.cart {
    margin: 50px 0;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #f4f4f4;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin: 0 auto;
}

.cart-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

.cart-item .product-details {
    flex-grow: 1;
    margin-left: 20px;
}

.cart-item .product-details h4 {
    font-size: 16px;
    font-weight: bold;
    color: #333;
}

.cart-item .product-details p {
    font-size: 14px;
    color: #777;
    margin: 5px 0;
}

.cart-item .product-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.cart-item .product-actions button {
    background-color: #ff5c5c;
    color: white;
    border: none;
    padding: 8px 12px;
    font-size: 14px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.cart-item .product-actions button:hover {
    background-color: #e04e4e;
}

.cart-item .product-actions button:active {
    background-color: #d13d3d;
}

/* Empty cart message */
.empty-cart-message {
    text-align: center;
    font-size: 18px;
    color: #888;
    padding: 20px;
}

/* Add to cart and checkout buttons */
.cart-footer {
    text-align: right;
    margin-top: 30px;
}

.cart-footer a {
    padding: 12px 20px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.cart-footer a:hover {
    background-color: #45a049;
}
</style>