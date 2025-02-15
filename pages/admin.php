<?php
session_start();
include '../includes/db_config.php';

// Handle delete user request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$deleteId])) {
        $_SESSION['message'] = "User with ID $deleteId deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting user with ID $deleteId.";
    }
    header("Location: admin.php");
    exit();
}

// Handle delete product request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id']) && is_numeric($_POST['delete_product_id'])) {
    $deleteProductId = intval($_POST['delete_product_id']);

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$deleteProductId])) {
        $_SESSION['message'] = "Product with ID $deleteProductId deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting product with ID $deleteProductId.";
    }
    header("Location: admin.php");
    exit();
}

// Fetch users
$users = $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

// Fetch orders with joined user data
$ordersStmt = $conn->query("SELECT orders.*, users.name AS username FROM orders JOIN users ON orders.user_id = users.id");
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Fetch subscriptions
$subscriptions = $conn->query("SELECT * FROM subscriptions ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Panel | ShopEase</title>
  <!-- You can link an external CSS file if you prefer -->
  <style>
    /* ============================
       Global & Reset Styles
    ============================ */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    html, body {
      font-family: 'Poppins', sans-serif;
      background: #f4f4f4;
      color: #333;
      height: 100%;
    }
    a {
      text-decoration: none;
      transition: color 0.3s;
    }
    /* ============================
       Header
    ============================ */
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
    header h1 {
      font-size: 28px;
      font-weight: 700;
    }
    header nav {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    header nav a {
      color: #fff;
      font-size: 16px;
      font-weight: 500;
    }
    header nav a:hover {
      color: #ffd700;
    }
    /* ============================
       Main Content & Cards
    ============================ */
    main {
      padding: 40px 20px;
      flex-grow: 1;
    }
    .container-main {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr;
      gap: 40px;
    }
    /* Card container for each section */
    .card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 20px 30px;
      transition: transform 0.3s;
    }
    .card:hover {
      transform: translateY(-3px);
    }
    .card h3 {
      margin-bottom: 20px;
      font-size: 24px;
      color: #333;
      border-bottom: 2px solid #ddd;
      padding-bottom: 10px;
    }
    /* ============================
       Tables Styling
    ============================ */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    table thead {
      background: #333;
      color: #fff;
    }
    table th, table td {
      padding: 12px 15px;
      text-align: left;
    }
    table tbody tr {
      border-bottom: 1px solid #f0f0f0;
      transition: background 0.3s;
    }
    table tbody tr:hover {
      background: #f9f9f9;
    }
    table tbody tr:last-child {
      border-bottom: none;
    }
    table td a {
      color: #2980b9;
      margin-right: 10px;
    }
    table td a:hover {
      text-decoration: underline;
    }
    .delete-btn {
      background-color: #e74c3c;
      color: #fff;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .delete-btn:hover {
      background-color: #c0392b;
    }
    /* ============================
       Messages
    ============================ */
    .success, .error {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      font-weight: bold;
      font-size: 16px;
      text-align: center;
    }
    .success {
      background-color: #28a745;
      color: #fff;
    }
    .error {
      background-color: #f8d7da;
      color: #721c24;
    }
    /* ============================
       Footer
    ============================ */
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
    /* ============================
       Responsive
    ============================ */
    @media (max-width: 768px) {
      header .container {
        flex-direction: column;
        align-items: flex-start;
      }
      header nav {
        margin-top: 10px;
      }
    }
  </style>
</head>
<body>
  <header>
      <div class="container">
          <h1>ShopEase Admin</h1>
          <nav>
              <a href="products.php">Products</a>
              <a href="admin.php">Dashboard</a>
              <a href="logout.php">Logout</a>
          </nav>
      </div>
  </header>

  <main>
      <div class="container-main">
          <h2>Admin Dashboard</h2>

          <!-- Success/Error Messages -->
          <?php if (isset($_SESSION['message'])): ?>
              <div class="success"><?= htmlspecialchars($_SESSION['message']) ?></div>
              <?php unset($_SESSION['message']); ?>
          <?php elseif (isset($_SESSION['error'])): ?>
              <div class="error"><?= htmlspecialchars($_SESSION['error']) ?></div>
              <?php unset($_SESSION['error']); ?>
          <?php endif; ?>

          <!-- Users Section Card -->
          <div class="card">
              <h3>Users List</h3>
              <table>
                  <thead>
                      <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Role</th>
                          <th>Created At</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ($users as $user): ?>
                          <tr>
                              <td><?= htmlspecialchars($user['id']) ?></td>
                              <td><?= htmlspecialchars($user['name']) ?></td>
                              <td><?= htmlspecialchars($user['email']) ?></td>
                              <td><?= ucfirst(htmlspecialchars($user['role'])) ?></td>
                              <td><?= htmlspecialchars($user['created_at']) ?></td>
                              <td>
                                  <a href="view_user.php?id=<?= $user['id'] ?>">View</a>
                                  <form method="POST" action="admin.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                      <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
                                      <button type="submit" class="delete-btn">Delete</button>
                                  </form>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
          </div>

          <!-- Products Section Card -->
          <div class="card">
              <h3>Products List</h3>
              <table>
                  <thead>
                      <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Price</th>
                          <th>Description</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ($products as $product): ?>
                          <tr>
                              <td><?= htmlspecialchars($product['id']) ?></td>
                              <td><?= htmlspecialchars($product['name']) ?></td>
                              <td>$<?= number_format($product['price'], 2) ?></td>
                              <td><?= htmlspecialchars($product['description']) ?></td>
                              <td>
                                  <a href="edit_product.php?id=<?= $product['id'] ?>">Edit</a>
                                  <form method="POST" action="admin.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                      <input type="hidden" name="delete_product_id" value="<?= $product['id'] ?>">
                                      <button type="submit" class="delete-btn">Delete</button>
                                  </form>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
          </div>

          <!-- Orders Section Card -->
          <div class="card">
              <h3>Orders List</h3>
              <table>
                  <thead>
                      <tr>
                          <th>Order ID</th>
                          <th>User</th>
                          <th>Total Price</th>
                          <th>Status</th>
                          <th>Payment Status</th>
                          <th>Order Date</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ($orders as $order): ?>
                          <tr>
                              <td><?= htmlspecialchars($order['id']) ?></td>
                              <td><?= htmlspecialchars($order['username']) ?></td>
                              <td>$<?= number_format($order['total_price'], 2) ?></td>
                              <td><?= ucfirst(htmlspecialchars($order['status'])) ?></td>
                              <td><?= htmlspecialchars($order['payment_status'] ?? 'N/A') ?></td>
                              <td><?= htmlspecialchars($order['order_date'] ?? 'N/A') ?></td>
                              <td>
                                  <a href="view_order.php?id=<?= $order['id'] ?>">View</a>
                                  <a href="update_order.php?id=<?= $order['id'] ?>">Update</a>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
          </div>
          
          <!-- Subscriptions Section Card -->
          <div class="card">
              <h3>Subscriptions List</h3>
              <table>
                  <thead>
                      <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Phone</th>
                          <th>Email</th>
                          <th>Subscribed On</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ($subscriptions as $subscription): ?>
                          <tr>
                              <td><?= htmlspecialchars($subscription['id']) ?></td>
                              <td><?= htmlspecialchars($subscription['name']) ?></td>
                              <td><?= htmlspecialchars($subscription['phone']) ?></td>
                              <td><?= htmlspecialchars($subscription['email']) ?></td>
                              <td><?= htmlspecialchars($subscription['created_at']) ?></td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
          </div>
      </div>
  </main>

  <footer>
      <div class="container">
          ShopEase. All Rights Reserved.
      </div>
  </footer>
</body>
</html>
