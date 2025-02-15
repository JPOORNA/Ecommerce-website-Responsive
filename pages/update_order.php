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
$message = "";

// Handle form submission for updating the order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $paymentStatus = $_POST['payment_status'];

    $updateStmt = $conn->prepare("UPDATE orders SET status = :status, payment_status = :payment_status WHERE id = :orderId");
    if ($updateStmt->execute([
        'status' => $status,
        'payment_status' => $paymentStatus,
        'orderId' => $orderId
    ])) {
        $message = "Order updated successfully!";
    } else {
        $message = "Failed to update order. Please try again.";
    }
}

// Fetch updated order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Order | ShopEase Admin</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* Entrance animation for the update form */
    .update-order { 
      animation: fadeIn 0.8s ease; 
      padding: 40px 20px;
    }
    @keyframes fadeIn {
       from { opacity: 0; transform: translateY(20px); }
       to { opacity: 1; transform: translateY(0); }
    }
    .order-actions a { margin-right: 10px; }
    .success { color: green; margin-bottom: 20px; font-weight: bold; }\n    .error { color: red; margin-bottom: 20px; font-weight: bold; }
  </style>
</head>
<body>
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
    <section class="update-order">
      <div class="container">
        <?php if (!empty($message)): ?>
          <p class="success"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <?php if ($order): ?>
          <h2 class="section-title">Update Order #<?= htmlspecialchars($order['id']) ?></h2>
          <form method="POST" action="update_order.php?id=<?= htmlspecialchars($order['id']) ?>">
            <div class="form-group">
              <label for="status">Order Status:</label>
              <select name="status" id="status" required>
                <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Processing" <?= $order['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                <option value="Shipped" <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="Delivered" <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
              </select>
            </div>
            <div class="form-group">
              <label for="payment_status">Payment Status:</label>
              <select name="payment_status" id="payment_status" required>
                <option value="Pending" <?= $order['payment_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Success" <?= $order['payment_status'] == 'Success' ? 'selected' : '' ?>>Success</option>
                <option value="Failed" <?= $order['payment_status'] == 'Failed' ? 'selected' : '' ?>>Failed</option>
              </select>
            </div>
            <button type="submit" class="btn">Update Order</button>
          </form>
          <div class="order-actions" style="margin-top:20px; text-align:center;">
            <a href="view_order.php?id=<?= htmlspecialchars($order['id']) ?>" class="btn secondary-btn">View Order Details</a>
            <a href="orders.php" class="btn secondary-btn">Back to Orders</a>
          </div>
        <?php else: ?>
          <h2 class="section-title">Order Not Found</h2>
          <p>No order data available for order ID <?= htmlspecialchars($orderId) ?>.</p>
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
