<?php
session_start();
include '../includes/db_config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch wishlist products from the session
$wishlist = isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : [];
$products = [];

// Fetch product details from the database based on wishlist items
if (!empty($wishlist)) {
    $placeholders = implode(',', array_fill(0, count($wishlist), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_values($wishlist));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if the "add to cart" button is clicked from the wishlist page
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if the product already exists in the cart
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += 1; // Increment quantity
    } else {
        $_SESSION['cart'][$productId] = 1; // Add new product to cart
    }

    // Redirect to the cart page after adding the product
    header("Location: cart.php");
    exit();
}

// Remove from wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $productId = $_POST['product_id'];
    
    if (isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = array_diff($_SESSION['wishlist'], [$productId]);
    }

    header("Location: wishlist.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist | ShopEase</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <h1 class="logo">ShopEase</h1>
            <nav class="navigation">
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="orders.php">Orders</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                </ul>
                <form method="POST" action="logout.php" style="display: inline;">
                    <button type="submit" class="logout-button">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    <!-- Wishlist Section -->
    <main>
        <section class="wishlist">
            <div class="container">
                <h3 class="section-title">Your Wishlist</h3>
                
                <div class="product-grid">
                    <?php if ($products): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                <h4 class="product-name"><?= htmlspecialchars($product['name']) ?></h4>
                                <p class="product-price">$<?= htmlspecialchars($product['price']) ?></p>
                                
                                <form method="POST" action="wishlist.php">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" name="add_to_cart" class="btn secondary-btn">Add to Cart</button>
                                    <button type="submit" name="remove_from_wishlist" class="btn remove-btn">Remove</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Your wishlist is empty.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container footer-container">
            <div class="footer-links">
                <ul>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="privacy.html">Privacy Policy</a></li>
                    <li><a href="terms.html">Terms of Service</a></li>
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
/* Global Reset & Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
body {
  font-family: "Arial", sans-serif;
  background-color: #f8f8f8;
  color: #333;
  line-height: 1.6;
}

/* Container */
.container {
  width: 90%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}

/* Header */
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

/* Wishlist Section */
.wishlist {
  background-color: #fff;
  padding: 40px 0;
  margin-top: 30px;
  border-top: 2px solid #e6e6e6;
}
.wishlist .section-title {
  text-align: center;
  font-size: 30px;
  font-weight: bold;
  color: #333;
  margin-bottom: 30px;
}

/* Product Grid Layout */
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  justify-items: center;
}

/* Product Card Styling */
.product-card {
  background-color: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  padding: 20px;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  width: 100%;
  max-width: 300px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
.product-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.product-card img {
  width: 100%;
  max-height: 200px;
  object-fit: cover;
  border-radius: 10px;
}

/* Product Text Styling */
.product-name {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin: 10px 0;
}
.product-price {
  font-size: 16px;
  color: #27ae60;
  margin-bottom: 15px;
}

/* Buttons */
.btn {
  display: inline-block;
  padding: 10px 15px;
  border-radius: 5px;
  font-size: 14px;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  width: 100%;
  margin-top: 8px;
}
.secondary-btn {
  background-color: #2980b9;
  color: #fff;
  border: none;
  transition: background-color 0.3s ease;
}
.secondary-btn:hover {
  background-color: #3498db;
}
.remove-btn {
  background-color: #ff4c4c;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
  margin-top: 8px;
}
.remove-btn:hover {
  background-color: #e03e3e;
}

/* Form in Product Card */
.product-card form {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

/* Empty Wishlist Message */
.empty-wishlist {
  text-align: center;
  font-size: 18px;
  color: #888;
  padding: 20px 0;
}

/* Responsive Styles */
@media (max-width: 1024px) {
  .product-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 768px) {
  .product-grid {
    grid-template-columns: 1fr;
  }
}


</style>