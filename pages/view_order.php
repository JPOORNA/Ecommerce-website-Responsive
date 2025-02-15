<?php
session_start();
include '../includes/db_config.php';

// (Optional) Check if admin is logged in


// Validate and retrieve order ID from URL
if (!isset($_GET['id'])) {
    echo "No order ID specified.";
    exit();
}
$orderId = $_GET['id'];

// Fetch order details along with customer info (adjust field names as needed)
$stmt = $conn->prepare("SELECT orders.*, users.name AS customer 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        WHERE orders.id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Order | ShopEase Admin</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* Simple entrance animation for order details */
    .order-details { 
      animation: fadeIn 0.8s ease; 
      padding: 40px 20px;
    }
    @keyframes fadeIn {
       from { opacity: 0; transform: translateY(20px); }
       to { opacity: 1; transform: translateY(0); }
    }
    .order-actions a { margin-right: 10px; }
  </style>
</head>
<body>
  <!-- Header Section -->
  <header>
    <div class="container header-container">
      <h1 class="logo">ShopEase Admin</h1>
      <nav class="navigation">
        <ul class="nav-links">
          <li><a href="orders.php">Orders</a></li>
          <li><a href="admin.php">Dashboard</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <main>
    <section class="order-details">
      <div class="container">
        <?php if ($order): ?>
          <h2 class="section-title">Order #<?= htmlspecialchars($order['id']) ?></h2>
          <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer']) ?></p>
          <p><strong>Total Price:</strong> $<?= number_format($order['total_price'], 2) ?></p>
          <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
          <p><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status'] ?? 'N/A') ?></p>
          <p><strong>Order Date:</strong> <?= htmlspecialchars($order['created_at'] ?? '') ?></p>
          <div class="order-actions">
            <a href="update_order.php?id=<?= htmlspecialchars($order['id']) ?>" class="btn">Update Order</a>
            <a href="orders.php" class="btn secondary-btn">Back to Orders</a>
          </div>
        <?php else: ?>
          <h2 class="section-title">Order Not Found</h2>
          <p>The order with ID <?= htmlspecialchars($orderId) ?> was not found.</p>
          <a href="orders.php" class="btn secondary-btn">Back to Orders</a>
        <?php endif; ?>
      </div>
    </section>
  </main>
  <footer>
    <div class="container footer-container">
      <p>&copy; 2025 ShopEase. All Rights Reserved.</p>
    </div>
  </footer>
</body>
</html>
