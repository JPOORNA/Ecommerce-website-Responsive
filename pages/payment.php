<?php
session_start();
include '../includes/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cartItems = $_SESSION['cart'] ?? [];
$totalPrice = 0;

// Calculate total price
foreach ($cartItems as $productId => $quantity) {
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = :productId");
    $stmt->execute(['productId' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && isset($product['price']) && is_numeric($product['price'])) {
        $totalPrice += (float) $product['price'] * (int) $quantity;
    }
}

// Handle Payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_payment'])) {
    $paymentStatus = $_POST['payment_status'] ?? '';

    if ($paymentStatus == 'success') {
        // Proceed with Order Confirmation
        try {
            $conn->beginTransaction();

            foreach ($cartItems as $productId => $quantity) {
                $stmt = $conn->prepare("SELECT price FROM products WHERE id = :productId");
                $stmt->execute(['productId' => $productId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $orderStmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price, status, created_at) 
                    VALUES (:userId, :productId, :quantity, :totalPrice, 'Pending', NOW())");

                    $orderStmt->execute([
                        'userId' => $user_id,
                        'productId' => $productId,
                        'quantity' => (int) $quantity,
                        'totalPrice' => (float) $totalPrice
                    ]);
                }
            }

            $conn->commit();
            unset($_SESSION['cart']);
            echo "<script>alert('Your order has been confirmed!'); window.location.href='index.php';</script>";
        } catch (Exception $e) {
            $conn->rollBack();
            echo "<script>alert('Order failed. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Payment failed. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase | Payments</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
  <div class="container header-container">
    <h1 class="logo"><i class="fa fa-shopping-bag"></i> ShopEase</h1>
    <nav class="navigation">
      <ul class="nav-links">
        <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
        <li>
          <form method="POST" action="logout.php" class="logout-form">
            <button type="submit" class="logout-button"><i class="fa fa-sign-out-alt"></i> Logout</button>
          </form>
        </li>
      </ul>
    </nav>
  </div>
</header>


<main>
    <section class="orders">
        <div class="container">
            <h3>Your Order For Payment</h3>
            <div class="order-items">
                <?php if (!empty($cartItems)): ?>
                    <?php foreach ($cartItems as $productId => $quantity): ?>
                        <?php
                            $stmt = $conn->prepare("SELECT * FROM products WHERE id = :productId");
                            $stmt->execute(['productId' => $productId]);
                            $product = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div class="order-item">
                            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="product-details">
                                <h4><?= htmlspecialchars($product['name']) ?></h4>
                                <p>Price: $<?= number_format((float)$product['price'], 2) ?></p>
                                <p>Quantity: <?= (int)$quantity ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="order-total">
                        <p><strong>Total Price: </strong>$<?= number_format($totalPrice, 2) ?></p>
                    </div>

                    <!-- Payment Form -->
                    <form method="POST">
                        <h4>Select Payment Method</h4>
                        <label for="payment_status">Payment Status:</label>
                        <select name="payment_status" required>
                            <option value="">--Select--</option>
                            <option value="success">Success</option>
                            <option value="failure">Failure</option>
                        </select>

                        <button type="submit" name="confirm_payment" class="confirm-payment-btn">Confirm Payment</button>
                    </form>
                <?php else: ?>
                    <p>Your cart is empty. <a href="index.php">Go Shopping</a></p>
                <?php endif; ?>
            </div>
        </div>
    </section>
  
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 ShopEase. All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>

<style>
/* Updated Order Page Styling */

/* Container for Order Items */
.order-items {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-top: 20px;
}

/* Each Order Item */
.order-item {
  display: flex;
  align-items: center;
  background-color: #ffffff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  max-width: 900px;
  margin: 0 auto;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.order-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.order-item img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
}

.order-item .product-details {
  margin-left: 20px;
  flex: 1;
}

.order-item .product-details h4 {
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 8px;
  color: #17252A;  /* Dark base color */
}

.order-item .product-details p {
  font-size: 16px;
  margin-bottom: 4px;
  color: #2B7A78;  /* Strong accent color */
}

/* Order Total Price Display */
.order-total {
  text-align: right;
  font-size: 20px;
  font-weight: bold;
  margin: 30px auto;
  max-width: 900px;
  padding-right: 10px;
  color: #17252A;
}

/* Payment Form Container */
.orders form {
  max-width: 900px;
  margin: 30px auto;
  padding: 20px;
  background-color: #ffffff;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.orders form h4 {
  font-size: 22px;
  margin-bottom: 10px;
  color: #17252A;
}

.orders form label {
  font-size: 16px;
  color: #333;
}

.orders form select {
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 5px;
  outline: none;
  transition: border-color 0.3s;
}

.orders form select:focus {
  border-color: #2B7A78;
}

/* Confirm Payment Button */
.confirm-payment-btn {
  background-color: #2B7A78;
  color: #fff;
  padding: 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 18px;
  transition: background-color 0.3s, transform 0.3s;
  margin-top: 10px;
}

.confirm-payment-btn:hover {
  background-color: #5AAFA9;
  transform: scale(1.02);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .order-item {
    flex-direction: column;
    align-items: flex-start;
  }
  .order-item img {
    width: 100%;
    height: auto;
  }
  .order-total {
    text-align: center;
  }
}

</style>