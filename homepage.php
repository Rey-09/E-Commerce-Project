<?php 
include 'php.php';

// Hitung total orders
$orderResult = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
$totalOrders = ($orderResult && $orderResult->num_rows > 0) ? $orderResult->fetch_assoc()['total_orders'] : 0;

// Hitung total revenue
$revenueResult = $conn->query("SELECT SUM(total_price) AS total_revenue FROM orders");
$totalRevenue = ($revenueResult && $revenueResult->num_rows > 0) ? $revenueResult->fetch_assoc()['total_revenue'] : 0;

// Hitung total produk
$productResult = $conn->query("SELECT COUNT(*) AS total_products FROM subcategories");
$totalProducts = ($productResult && $productResult->num_rows > 0) ? $productResult->fetch_assoc()['total_products'] : 0;

// Hitung total user
$userResult = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = ($userResult && $userResult->num_rows > 0) ? $userResult->fetch_assoc()['total_users'] : 0;

// Ambil produk untuk preview (tambahkan kolom stock)
$previewQuery = $conn->query("SELECT name, image, quantity FROM subcategories ORDER BY quantity ASC, id DESC");

$previewProducts = ($previewQuery && $previewQuery->num_rows > 0) ? $previewQuery->fetch_all(MYSQLI_ASSOC) : [];


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Store Dashboard</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-image: url('https://i.pinimg.com/736x/00/c8/f0/00c8f06ee225c02d922e7f9f28c87332.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      color: #333;
    }

    .overlay {
      background-color: rgba(255, 255, 255, 0.85);
      min-height: 100vh;
      display: flex;
    }

    /* Sidebar konsisten dengan halaman lain */
    .sidebar {
      width: 220px;
      height: 100vh;
      background-color: #fff;
      position: fixed;
      border-right: 1px solid #e0e0e0;
      padding: 20px 15px;
      display: flex;
      flex-direction: column;
      z-index: 2;
    }

    .sidebar h3 {
      font-size: 24px;
      margin-bottom: 20px;
      color: #444;
    }

    .sidebar nav a {
      display: block;
      color: #444;
      text-decoration: none;
      margin-bottom: 15px;
      font-size: 16px;
      padding: 8px 12px;
      border-radius: 4px;
      transition: background-color 0.3s, color 0.3s;
    }

    .sidebar nav a:hover,
    .sidebar nav a.active {
      background-color: #FBC9D9;
      color: #7a3e5c;
    }

    /* Main content dengan margin agar tidak tertutup sidebar */
    .main {
      margin-left: 220px;
      padding: 30px;
      flex-grow: 1;
      max-width: 1000px;
      margin-right: auto;
      margin-left: 220px;
      background-color: transparent; /* biar background cover tetap terlihat */
    }

    .header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  width: 100%;
}

.header-container h2 {
  margin: 0;
}

.login-button {
  display: inline-flex;
  align-items: center;
  padding: 8px 20px;
  background-color: #FBC9D9;
  color: #7a3e5c;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.login-button:hover {
  background-color: #e597ca;
}

.login-icon {
  width: 20px;
  height: 20px;
  margin-right: 8px;
  border-radius: 50%;
}


    .header h2 {
      margin: 0;
      font-weight: 600;
      font-size: 28px;
      color: #222;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }

    .card {
      background: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
      text-align: left;
      color: #222;
    }

    .card h4 {
      margin: 0 0 10px;
      font-size: 16px;
      color: #555;
    }

    .card p {
      font-size: 22px;
      margin: 0;
      font-weight: bold;
    }

    .quick-links {
      margin-top: 40px;
      text-align: center;
    }

    .quick-links h3 {
      margin-bottom: 20px;
      color: #444;
    }

    .quick-links a {
      display: inline-block;
      margin: 10px 10px;
      padding: 10px 15px;
      background-color: #FBC9D9;
      color: #7a3e5c;
      border-radius: 8px;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .quick-links a:hover {
      background-color: #e597ca;
      color: #5e2f43;
    }


    .product-preview {
  margin-top: 50px;
}

.product-preview h3 {
  text-align: center;
  margin-bottom: 20px;
  color: #444;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 20px;
}

.product-card {
  background-color: #fff;
  border-radius: 10px;
  padding: 10px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s ease;
}

.product-card:hover {
  transform: scale(1.03);
}

.product-card img {
  width: 100%;
  height: 150px; /* Ukuran tinggi seragam */
  object-fit: cover; /* Potong dan isi proporsional */
  border-radius: 6px;
}

.product-card p {
  margin-top: 10px;
  font-size: 14px;
  color: #555;
}

.low-stock-btn {
  margin-top: 8px;
  background-color: #ff6b6b;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 5px;
  font-size: 13px;
  cursor: pointer;
}


  </style>
</head>
<body>
  <div class="overlay">
    <aside class="sidebar">
      <h3>Store</h3>
      <nav>
        <a href="homepage.php"  class="active">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="categories.php">Categories</a>
        <a href="products.php">Products</a>
        <a href="orders.php">Orders</a>
      </nav>
    </aside>

    <main class="main">
      <div class="header-container">
  <h2>Welcome to Kawaii Shop!</h2>

  
  <a href="login.php" class="login-button">
    <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Profile Icon" class="login-icon" />
    Logout
  </a>
</div>

<button class="low-stock-btn">⚠ Low Stock</button>


     <div class="cards">
  <div class="card">
    <h4>Total Orders</h4>
    <p><?= $totalOrders ?></p>
  </div>
  <div class="card">
    <h4>Total Revenue</h4>
    <p>Rp <?= number_format($totalRevenue, 0, ',', '.') ?></p>
  </div>
  <div class="card">
    <h4>Products</h4>
    <p><?= $totalProducts ?></p>
  </div>
  <div class="card">
    <h4>Users</h4>
    <p><?= $totalUsers ?></p>
  </div>
</div>


 <div class="product-preview">
  <h3>Preview Produk</h3>
  <div class="product-grid">
    <?php if (!empty($previewProducts)): ?>
      <?php foreach ($previewProducts as $product): ?>
        <div class="product-card">
          <?php if (!empty($product['image'])): ?>
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          <?php else: ?>
            <img src="https://via.placeholder.com/150x150/ffe4f0/ffffff?text=No+Image" alt="No Image">
          <?php endif; ?>
          
          <p><?= htmlspecialchars($product['name']) ?></p>
          <p><strong>Stok:</strong> <?= intval($product['quantity']) ?></p>

          <?php if ($product['quantity'] < 10): ?>
            <button style="
              margin-top: 8px;
              background-color: #ff6b6b;
              color: white;
              border: none;
              padding: 6px 12px;
              border-radius: 5px;
              font-size: 13px;
              cursor: pointer;
            ">
              ⚠ Low Stock
            </button>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center; color:#777;">Belum ada produk untuk ditampilkan.</p>
    <?php endif; ?>
  </div>
</div>



      
    </main>
  </div>
</body>
</html>
