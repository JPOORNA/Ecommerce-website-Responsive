<?php
session_start();
include '../includes/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the current user
$orderStmt = $conn->prepare("SELECT o.id, p.name, p.image, o.quantity, o.total_price, o.status, o.created_at 
                              FROM orders o 
                              JOIN products p ON o.product_id = p.id 
                              WHERE o.user_id = :userId 
                              ORDER BY o.created_at DESC");
$orderStmt->execute(['userId' => $user_id]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
if (!$orders) {
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders - ShopEase</title>
  <link rel="stylesheet" href="styles.css">
  <!-- Font Awesome for icons if needed -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    /* ========== Global Variables & Animations ========== */
    :root {
      --primary-color: #17252A;
      --accent-color: #2B7A78;
      --light-accent: #5AAFA9;
      --bg-light: #DEF2F1;
      --white: #FEFFFF;
      --text-color: #17252A;
      --transition-speed: 0.3s;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* ========== Orders Page Layout ========== */
    .orders-container {
      display: flex;
      gap: 20px;
      padding: 20px;
      animation: fadeIn 0.8s ease;
    }
    /* Sidebar with Filters */
    .sidebar {
      width: 250px;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .sidebar h3 {
      margin-bottom: 15px;
      font-size: 20px;
      color: var(--primary-color);
    }
    .sidebar .filter-group {
      margin-bottom: 20px;
    }
    .sidebar .filter-group h4 {
      margin-bottom: 10px;
      font-size: 16px;
      color: var(--accent-color);
    }
    .sidebar .filter-group label {
      display: block;
      font-size: 14px;
      color: #333;
      margin-bottom: 5px;
    }
    /* Main Orders Section */
    .orders-main {
      flex: 1;
    }
    .orders-main h2 {
      margin-bottom: 20px;
      font-size: 28px;
      color: var(--primary-color);
      text-align: center;
    }
    /* Order Cards (Card View) */
    .order-card {
      display: flex;
      align-items: center;
      background: #fff;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
      animation: fadeIn 0.8s ease;
    }
    .order-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
    .order-card img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      margin-right: 20px;
    }
    .order-info {
      flex: 1;
    }
    .order-info h3 {
      font-size: 20px;
      margin-bottom: 10px;
      color: var(--primary-color);
    }
    .order-info p {
      font-size: 16px;
      margin-bottom: 5px;
      color: var(--accent-color);
    }
    .order-info .status {
      font-weight: bold;
    }
    .order-info .status.delivered {
      color: #388E3C;
    }
    .order-info .status.cancelled {
      color: #E53935;
    }
    .order-info .status.pending {
      color: #FBC02D;
    }
    .order-info .status.processing {
      color: var(--accent-color);
    }
    /* Order Status Section (Table View) */ 
    .order-status-section {
      padding: 40px 20px;
      background: var(--bg-light);
      animation: fadeIn 0.8s ease;
      margin-top: 40px;
    }
    .order-status-section h3 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 32px;
      color: var(--primary-color);
    }
    table.order-status-table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px auto;
      animation: fadeIn 0.8s ease;
    }
    table.order-status-table th, table.order-status-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    table.order-status-table th {
      background: var(--primary-color);
      color: var(--white);
    }
    table.order-status-table tr:hover {
      background: #f9f9f9;
      transition: background var(--transition-speed) ease;
    }
    
    /* ========== Footer ========== */
    footer {
      background: var(--primary-color);
      color: var(--white);
      padding: 20px 0;
      text-align: center;
      font-size: 14px;
    }
    footer .container {
      max-width: 1200px;
      margin: auto;
      padding: 0 20px;
    }
    footer ul {
      list-style: none;
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 10px;
    }
    footer ul li a {
      color: var(--white);
      transition: color var(--transition-speed);
    }
    footer ul li a:hover {
      color: #ffd700;
    }
    
    /* ========== Responsive Styles ========== */
    @media (max-width: 768px) {
      .order-card {
        flex-direction: column;
        align-items: flex-start;
      }
      .order-card img {
        width: 100%;
        height: auto;
      }
      .orders-container {
        flex-direction: column;
      }
      .sidebar {
        width: 100%;
        margin-bottom: 20px;
      }
    }
    @media (max-width: 480px) {
      .order-card, table.order-status-table {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  
<header>
    <div class="container header-container">
        <h1 class="logo"><i class="fa fa-shopping-bag"></i> ShopEase</h1>
        <nav class="navigation">
            <ul class="nav-links">
                <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

  
<main>
    <div class="orders-container">
        <!-- Sidebar with Filters -->
        <div class="sidebar">
            <h3>Filters</h3>
            <div class="filter-group">
                <h4>Order Status</h4>
                <label><input type="checkbox" class="status-filter" value="pending"> Pending</label>
                <label><input type="checkbox" class="status-filter" value="processing"> Processing</label>
                <label><input type="checkbox" class="status-filter" value="delivered"> Delivered</label>
                <label><input type="checkbox" class="status-filter" value="cancelled"> Cancelled</label>
            </div>
            <div class="filter-group">
                <h4>Order Time</h4>
                <label><input type="checkbox" class="date-filter" value="last30"> Last 30 days</label>
                <label><input type="checkbox" class="date-filter" value="2023"> 2023</label>
                <label><input type="checkbox" class="date-filter" value="2022"> 2022</label>
                <label><input type="checkbox" class="date-filter" value="older"> Older</label>
            </div>
        </div>
        
        <!-- Main Orders Section -->
        <div class="orders-main">
            <h2>Your Orders</h2>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <?php 
                        // Calculate the timestamp for the created_at date.
                        $orderTimestamp = strtotime($order['created_at']); 
                        // Set a lowercase status for easier filtering.
                        $orderStatus = strtolower($order['status']);
                    ?>
                    <div class="order-card" data-status="<?= htmlspecialchars($orderStatus) ?>" data-timestamp="<?= $orderTimestamp ?>">
                        <img src="images/<?= htmlspecialchars($order['image'] ?? 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($order['name']) ?>">
                        <div class="order-info">
                            <h3>Order #<?= htmlspecialchars($order['id']) ?></h3>
                            <p><strong>Product:</strong> <?= htmlspecialchars($order['name']) ?></p>
                            <p><strong>Quantity:</strong> <?= (int)$order['quantity'] ?></p>
                            <p><strong>Total:</strong> $<?= number_format((float)$order['total_price'], 2) ?></p>
                            <p><strong>Status:</strong> <span class="status <?= htmlspecialchars($orderStatus) ?>"><?= htmlspecialchars($order['status']) ?></span></p>
                            <p><strong>Ordered On:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>
  
<footer>
    <div class="container">
        <p>&copy; 2025 ShopEase. All Rights Reserved.</p>
    </div>
</footer>

<!-- JavaScript for filtering orders -->
<script>
  // Function to filter orders based on the selected filters.
  function filterOrders() {
    // Get all checked status filters (e.g. pending, processing, etc.)
    const selectedStatuses = Array.from(document.querySelectorAll('.status-filter:checked')).map(cb => cb.value);
    // Get all checked date filters (e.g. last30, 2023, etc.)
    const selectedDates = Array.from(document.querySelectorAll('.date-filter:checked')).map(cb => cb.value);
    
    const orderCards = document.querySelectorAll('.order-card');
    const now = Date.now();
    const THIRTY_DAYS = 30 * 24 * 3600 * 1000; // in milliseconds

    orderCards.forEach(card => {
      const status = card.dataset.status; // e.g. "pending", "processing", etc.
      // Multiply by 1000 because PHP timestamp is in seconds while JS works in milliseconds.
      const timestamp = parseInt(card.dataset.timestamp, 10) * 1000;
      const orderDate = new Date(timestamp);
      const orderYear = orderDate.getFullYear();
      
      // If no status filter is checked, the order passes the status check.
      const statusMatch = (selectedStatuses.length === 0) || selectedStatuses.includes(status);
      
      // Check the date filter.
      let dateMatch = false;
      if (selectedDates.length === 0) {
        dateMatch = true;
      } else {
        selectedDates.forEach(filterValue => {
          if (filterValue === 'last30' && (now - timestamp <= THIRTY_DAYS)) {
            dateMatch = true;
          } else if (filterValue === '2023' && orderYear === 2023) {
            dateMatch = true;
          } else if (filterValue === '2022' && orderYear === 2022) {
            dateMatch = true;
          } else if (filterValue === 'older' && orderYear < 2022) {
            dateMatch = true;
          }
        });
      }
      
      // Show the order card only if it passes both filters.
      if (statusMatch && dateMatch) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  }

  // Add event listeners to all filter checkboxes.
  document.querySelectorAll('.status-filter, .date-filter').forEach(cb => {
    cb.addEventListener('change', filterOrders);
  });
</script>

</body>
</html>

<style>
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
</style>
