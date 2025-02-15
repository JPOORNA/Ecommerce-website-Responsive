<?php
session_start();
include '../includes/db_config.php';

// Fetch products from the database (with optional search filtering)
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';
$query = "SELECT * FROM products";
if ($searchTerm) {
    $query .= " WHERE name LIKE :searchTerm";
}
$stmt = $conn->prepare($query);
if ($searchTerm) {
    $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%');
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += 1;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }
}

// Handle Add to Wishlist
if (isset($_POST['add_to_wishlist'])) {
    $productId = $_POST['product_id'];
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    if (!in_array($productId, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $productId;
    }
}

// Redirect to login if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopEase - Home</title>
  <link rel="stylesheet" href="styles.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Header Section -->
  <header>
  <div class="container header-container">
    <h1 class="logo"><i class="fa fa-shopping-bag"></i> ShopEase</h1>
    
    <!-- Main Navigation -->
    <nav class="main-nav">
      <ul>
        <li class="dropdown">
          <a >Men </a>
          <div class="dropdown-content">
            <a href="#">T-Shirts</a>
            <a href="#">Casual Shirts</a>
            <a href="#">Formal Shirts</a>
            <a href="#">Jeans</a>
            <a href="#">Casual Shoes</a>
            <a href="#">Sports Shoes</a>
          </div>
        </li>
        <li class="dropdown">
          <a >Women </a>
          <div class="dropdown-content">
            <a href="#">Tops</a>
            <a href="#">Dresses</a>
            <a href="#">Jeans</a>
            <a href="#">Heels</a>
            <a href="#">Sandals</a>
            <a href="#">Handbags</a>
          </div>
        </li>
        <li class="dropdown">
          <a >Kids </a>
          <div class="dropdown-content">
            <a href="#">T-Shirts</a>
            <a href="#">Shorts</a>
            <a href="#">Shoes</a>
            <a href="#">Toys</a>
            <a href="#">Accessories</a>
          </div>
        </li>
        <li class="dropdown">
          <a >Home & Living </a>
          <div class="dropdown-content">
            <a href="#">Furniture</a>
            <a href="#">Decor</a>
            <a href="#">Kitchenware</a>
            <a href="#">Bedding</a>
          </div>
        </li>
      </ul>
    </nav>

    <!-- User Navigation -->
    <nav class="user-nav">
      <ul class="nav-links">
        <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="cart.php"><i class="fa fa-shopping-cart"></i> Cart</a></li>
        <li><a href="orders.php"><i class="fa fa-list-alt"></i> Orders</a></li>
        <li><a href="wishlist.php"><i class="fa fa-heart"></i> Wishlist</a></li>
      </ul>
      <form method="POST" action="logout.php" class="logout-form">
        <button type="submit" class="logout-button"><i class="fa fa-sign-out-alt"></i> Logout</button>
      </form>
    </nav>
  </div>
</header>
  <!-- Promo Section -->
  <section class="promo-section">
    <div class="promo-overlay">
      <div class="promo-content">
        <h2>Discover Exclusive Deals</h2>
        <p>Unlock the best offers and elevate your shopping experience.</p>
        <a href="#feature" class="cta-btn">Visit</a>
      </div>
    </div>
    <img src="online.jpg" alt="Promo Image">
  </section>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1>Welcome to ShopEase</h1>
      <p>Your ultimate destination for quality &amp; trendy products</p>
      <img src="images_hero-banner.png" alt="Hero Banner">
      <ul>
      <a href="#products" class="cta-btn">Shop Now</a>
    </div>
  </section>
   
    <!-- Promo Codes -->
    <div class="promo-codes">
      <h3>Exclusive Offers</h3>
      <div class="promo-code-card">
        <h4>GET 25% OFF</h4>
        <p>Up to ₹400</p>
        <p class="code">USE CODE: POORNACSD</p>
      </div>
      <div class="promo-code-card">
        <h4>GET 30% OFF</h4>
        <p>Up to ₹500</p>
        <p class="code">USE CODE: POORNACSD</p>
      </div>
    </div>
  </section>

  

  <!-- Categories Slider Section -->
  <section class="categories-slider">
    <div class="container">
      <h2>Shop by Category</h2>
      <div class="slider-container">
        <div class="slider" id="categorySlider">
          <div class="slider-item">
            <div class="category-card">
              <img src="men.jpg" alt="Men's Clothing">
              <h3>Men's Clothing</h3>
              <a href="cart.php?category=men" class="slider-btn">view</a>
            </div>
          </div>
          <div class="slider-item">
            <div class="category-card">
              <img src="womens.jpg" alt="Women's Clothing">
              <h3>Women's Clothing</h3>
              <a href="cart.php?category=women" class="slider-btn">view</a>
            </div>
          </div>
          <div class="slider-item">
            <div class="category-card">
              <img src="kids.jpg" alt="Kids' Clothing">
              <h3>Kids' Clothing</h3>
              <a href="cart.php?category=kids" class="slider-btn">view</a>
            </div>
          </div>
          <div class="slider-item">
            <div class="category-card">
              <img src="shoes.jpg" alt="Shoes">
              <h3>Shoes</h3>
              <a href="cart.php?category=shoes" class="slider-btn">view</a>
            </div>
            </div>
            <div class="slider-item">
            <div class="category-card">
              <img src="bag.jpg" alt="Bags">
              <h3>Bags</h3>
              <a href="cart.php?category=shoes" class="slider-btn">view</a>
            </div>
            </div>
            <div class="slider-item">
            <div class="category-card">
              <img src="shorts.jpg" alt="shorts">
              <h3>Shorts</h3>
              <a href="cart.php?category=shoes" class="slider-btn">view</a>
            </div>
            </div>
            </div>
            </div>
          </div>
        </div>
      </div>
      <div class="slider-nav">
        <span class="prev" onclick="moveSlide(-1)">&#10094;</span>
        <span class="next" onclick="moveSlide(1)">&#10095;</span>
      </div>
    </div>
  </section>

  <section class="featured-section" id="feature">
            <div class="featured-grid">
                <div class="main-image">
                    <img src="soft.jpg" alt="Soft Romantic">
                    <div class="text-overlay">SOFT ROMANTIC</div>
                </div>
                <div class="side-images">
                    <div class="side-image">
                        <img src="denim.png" alt="Denim Destinations">
                        <div class="text-overlay">DENIM DESTINATIONS</div>
                    </div>
                    <div class="side-image">
                        <img src="shirt.jpg" alt="Athleisure">
                        <div class="text-overlay">ATHLEISURE</div>
                    </div>
                </div>
            </div>
        </section>

  <!-- Featured Products Section -->
  <section class="products-section" id="products">
    <div class="container">
      <h2>Featured Products</h2>
      <div class="products-grid">
        <?php foreach ($products as $product): ?>
          <div class="product-card">
            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <div class="card-content">
              <h3><?= htmlspecialchars($product['name']) ?></h3>
              <p>$<?= number_format($product['price'], 2) ?></p>
            </div>
            <div class="card-actions">
              <form method="POST" action="index.php">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <button type="submit" name="add_to_cart">Add to Cart</button>
              </form>
              <form method="POST" action="index.php">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <button type="submit" name="add_to_wishlist">Wishlist</button>
              </form>
              <a href="products_details.php?id=<?= $product['id'] ?>">View Details</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Why Choose Us Section -->
  <section class="why-choose-us">
    <div class="container">
      <h2>Why Choose ShopEase</h2>
      <div class="features">
        <div class="feature">
          <i class="fa fa-check-circle"></i>
          <h3>Quality Products</h3>
          <p>Only the best products from trusted brands.</p>
        </div>
        <div class="feature">
          <i class="fa fa-truck"></i>
          <h3>Fast Delivery</h3>
          <p>Quick and reliable delivery to your doorstep.</p>
        </div>
        <div class="feature">
          <i class="fa fa-lock"></i>
          <h3>Secure Payment</h3>
          <p>Safe and secure payment methods for peace of mind.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonials">
    <div class="container">
      <h2>What Our Customers Say</h2>
      <div class="testimonial-cards">
        <div class="testimonial-card">
          <p>“ShopEase has completely transformed my shopping experience. The products are top-notch and the service is outstanding!”</p>
          <div class="client-info">
            <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Client 1">
            <h4>Jane Doe</h4>
          </div>
        </div>
        <div class="testimonial-card">
          <p>“I love the variety and quality of products available. Fast delivery and excellent customer support.”</p>
          <div class="client-info">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Client 2">
            <h4>John Smith</h4>
          </div>
        </div>
        <div class="testimonial-card">
          <p>“A seamless shopping experience from start to finish. Highly recommend ShopEase for all your needs.”</p>
          <div class="client-info">
            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Client 3">
            <h4>Emily Clark</h4>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Newsletter Section -->
<section class="newsletter">
  <div class="container">
    <h2>Join Our Newsletter</h2>
    <p>Subscribe for the latest updates, news, and exclusive offers.</p>
    <form method="POST" action="subscribe.php">
      <input type="text" name="name" placeholder="Enter your name" required>
      <input type="tel" name="phone" placeholder="Enter your phone number" required>
      <input type="email" name="email" placeholder="Enter your email" required>
      <button type="submit">Subscribe</button>
    </form>
  </div>
</section>
<div id="chatbot-container">
  <!-- Bot icon (small logo) -->
  <div id="chatbot-icon" onclick="toggleChat()">
    <span>🤖</span> <!-- This can be your bot symbol -->
  </div>

  <!-- Chatbot window -->
  <div id="chatbot">
    <div id="chat-window">
      <div id="chat-messages">
        <div class="chat-message bot-message">Hi! This is the chatbot 👋</div> <!-- Default message -->
      </div>
    </div>
    <div id="chat-input">
      <input type="text" placeholder="Type a message..." />
      <button>Send</button>
    </div>
  </div>
</div>


  <script>
  let chatbotVisible = false;

function toggleChat() {
  const chatWindow = document.getElementById("chatbot");
  const chatMessages = document.getElementById("chat-messages");
  chatbotVisible = !chatbotVisible;

  if (chatbotVisible) {
    chatWindow.style.display = "flex"; // Show chat window

    // Add welcome message if the chat box is opened for the first time
    if (chatMessages.children.length === 1) {
      chatMessages.innerHTML = `<div class="chat-message bot-message">Hi! This is the chatbot 👋</div>`;
    }
  } else {
    chatWindow.style.display = "none"; // Hide chat window
  }
}


  
  </script>

  <!-- Footer Section -->
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
      <p>&copy; 2025 ShopEase. All rights reserved.</p>
    </div>
  </footer>

  <script>
    let currentSlide = 0;
    function moveSlide(step) {
      const slider = document.getElementById('categorySlider');
      const totalSlides = slider.children.length;
      currentSlide = (currentSlide + step + totalSlides) % totalSlides;
      const offset = -currentSlide * (100 / totalSlides);
      slider.style.transform = `translateX(${offset}%)`;
    }
  </script>
  <script>
    // Brand Slider Functionality
    let currentBrandSlide = 0;
    const brandSlides = document.querySelectorAll('.brand-slide');
    const totalBrandSlides = brandSlides.length;

    function showSlide(index) {
      brandSlides.forEach((slide, i) => {
        slide.style.transform = `translateX(${100 * (i - index)}%)`;
      });
    }

    function nextSlide() {
      currentBrandSlide = (currentBrandSlide + 1) % totalBrandSlides;
      showSlide(currentBrandSlide);
    }

    function prevSlide() {
      currentBrandSlide = (currentBrandSlide - 1 + totalBrandSlides) % totalBrandSlides;
      showSlide(currentBrandSlide);
    }

    // Initialize the first slide
    showSlide(currentBrandSlide);
  </script>
</body>
</html>
