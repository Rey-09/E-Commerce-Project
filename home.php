<?php
// Koneksi ke database
session_start();
$host = "localhost";
$username = "root";
$password = "";
$dbname = "wad";

$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Ambil semua produk di "keranjang" (di contoh ini anggap semua produk ditampilkan)
$sql = "SELECT * FROM subcategories LIMIT 3"; // kamu bisa ganti ini nanti sesuai item yg ditambahkan user
$result = $conn->query($sql);

$cart_items = [];
$sub_total = 0;

$sql = "SELECT c.*, s.name, s.image, s.price, s.quantity AS stock 
        FROM cart c 
        JOIN subcategories s ON c.product_id = s.id
        ORDER BY c.id DESC"; // ⬅ urutkan berdasarkan ID cart paling baru
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($item = $result->fetch_assoc()) {
        $cart_items[] = $item;
    }
}

// HANDLE CHECKOUT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout']) && !empty($_POST['selected'])) {
    $user_id = $_SESSION['user_id'] ?? 1; // Ganti dengan session user kamu
    $status = 'completed';

    foreach ($_POST['selected'] as $product_id) {
        $order_number = 'ORD' . time() . rand(10,99);
        $product_id = intval($product_id);
        $qty = 1; // Default qty

        // Cek qty input dari form (jika kamu tambahkan input qty nanti)
        if (isset($_POST['qty'][$product_id])) {
            $qty = intval($_POST['qty'][$product_id]);
        }

        // Ambil harga & stok
        $result = $conn->query("SELECT price, quantity FROM subcategories WHERE id = $product_id");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $price = $row['price'];
            $stock = $row['quantity'];

            if ($stock >= $qty) {
                $total_price = $qty * $price;

                // Kurangi stok
                $conn->query("UPDATE subcategories SET quantity = quantity - $qty WHERE id = $product_id");

                // Simpan order
                $conn->query("INSERT INTO orders (order_number, user_id, product_id, total_price, order_date, status)
                              VALUES ('$order_number', $user_id, $product_id, $total_price, NOW(), '$status')");

                // Hapus dari cart
                $conn->query("DELETE FROM cart WHERE product_id = $product_id");
            }
        }
    }

    // Redirect biar nggak ke-submit dua kali
    header("Location: home.php");
    exit;
}


// HANDLE ADD TO CART - PAKAI AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && !isset($_POST['delete_selected'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($product_id > 0) {
        $check = $conn->query("SELECT * FROM cart WHERE product_id = $product_id");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE cart SET quantity = quantity + $quantity WHERE product_id = $product_id");
        } else {
            $conn->query("INSERT INTO cart (product_id, quantity) VALUES ($product_id, $quantity)");
        }

        // Cek apakah ini AJAX (fetch) atau form biasa
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo "success";
            exit;
        } else {
            // normal submit (non-ajax)
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        http_response_code(400);
        echo "Invalid product";
        exit;
    }
}


//delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    $ids = $_POST['selected'] ?? [];

    if (!empty($ids)) {
        // Escape dan ubah ke integer
        $id_list = implode(',', array_map('intval', $ids));
        $conn->query("DELETE FROM cart WHERE product_id IN ($id_list)");
    }

    // Redirect to avoid resubmit
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <title>Toko Kawaii</title>
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background:rgb(255, 255, 255);
      overflow-x: hidden;
    }
    .header {
      position: sticky;
      top: 0;
      z-index: 999;
      background: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .logo {
      font-family: 'Montserrat', sans-serif;
      letter-spacing: 2px;
      text-transform: uppercase;
      font-size: 1.8rem;
      font-weight: 700;
      text-align: left;
      color:rgb(102, 4, 55);
    }
    .logo span {
      color:rgb(188, 87, 139);
    }

    .header-buttons .login-btn,
    .header-buttons .signup-btn {
      background-color: #ffb6c1;
      border: none;
      padding: 0.5rem 1rem;
      margin-left: 0.5rem;
      border-radius: 20px;
      cursor: pointer;
    }
    .product-preview {
      text-align: center;
      padding: 2rem;
    }
    .product-preview h2 {
      font-size: 2rem;
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      color:rgb(203, 144, 155);
      margin-bottom: 1.5rem;
    }
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 1.5rem;
      padding: 1rem 2rem;
    }
    .product-card {
      background: #fff;
      border-radius: 15px;
      padding: 1rem;
      box-shadow: 0 0 10px rgba(255,105,180,0.1);
      transition: transform 0.2s ease;
    }
    .product-card:hover {
      transform: scale(1.05);
    }
    .product-card img {
      width: 100%;
      border-radius: 10px;
      margin-bottom: 0.5rem;
    }
    .product-card p {
      font-weight: 500;
      color: #c71585;
    }

    .header-icons {
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .icon-btn {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background-color: #ffe0ed;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: background-color 0.3s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .icon-btn:hover {
      background-color: #f8b6d2;
    }

    .icon {
      width: 20px;
      height: 20px;
      stroke: #d63384;
    }

    .vertical-divider {
      width: 1px;
      height: 24px;
      background-color: #ccc;
    }

    .user-text {
      font-size: 12px;
      line-height: 1.2;
      color: #444;
    }

    .user-text a {
      text-decoration: none;
      color: #000;
      font-weight: bold;
    }

    .container {
      width: 100%;
      padding: 50px;
      height: 400px;
      margin: 0 auto; /* Supaya konten berada di tengah */
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 40px;
      background: #fff0f6;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      overflow: hidden;
    }

    .text-section {
      max-width: 50%;
    }
    .text-section h1 {
      font-size: 3em;
      color: #d63384;
      margin-bottom: 10px;
    }
    .text-section p {
      color: #6c757d;
      margin-bottom: 30px;
    }
    .btn-shop {
      background-color: #d63384;
      color: white;
      padding: 12px 25px;
      font-size: 1em;
      border: none;
      border-radius: 25px;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn-shop:hover {
      background-color:rgb(255, 156, 191);
    }

    .image-section {
      flex: 1;
      height: 100%;
      overflow: hidden;
      position: relative;
      clip-path: ellipse(90% 100% at 100% 50%);
      border: 100%;
      background-image: url('display.jpeg');
      background-size: cover;
      background-position: center;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        text-align: center;
      }
      .text-section, .image-section {
        max-width: 100%;
      }
      .image-section img {
        margin-top: 20px;
      }
    }

    
    /* bagian card */
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 24px; /* Jarak antar card */
      padding: 20px;
      justify-content: center;
    }

    .atc {
      margin-top: 8px; 
      background-color:rgb(235, 154, 194); 
      color: white; 
      padding: 8px 12px; 
      border: none; 
      border-radius: 8px; 
      cursor: pointer; 
      transition: transform 0.3s ease;
    }

    .atc:hover {
      transform: translateY(-2px);
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-bottom: 1px solid #eee;
    }

    .card-body {
      padding: 12px 16px;
    }

    .card-body .category {
      font-size: 12px;
    color: rgb(218, 160, 160);
    margin-bottom: 4px;
  }

  .card-body .product-name {
    font-size: 16px;
    font-weight: 600;
    margin: 4px 0;
    color: rgb(137, 58, 58);
  }

.card-body .price {
  font-size: 15px;
  font-weight: bold;
  color:rgb(181, 133, 133);
  margin-top: 6px;
}

.card-body .quantity {
  font-size: 12px;
  color: #666;
  
}

.header-icons {
  display: flex;
  align-items: center;
  gap: 16px;
}

.icon-btn {
  background: #fce4ec;
  border: none;
  border-radius: 50%;
  padding: 10px;
  cursor: pointer;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.icon-btn:hover {
  transform: scale(1.1);
}

.icon {
  width: 24px;
  height: 24px;
}

/* Search */
.search-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-input {
  width: 0;
  opacity: 0;
  padding: 8px 12px;
  margin-left: 10px;
  border: 3px solid #e2b6c9;
  border-radius: 20px;
  transition: all 0.3s ease;
  font-size: 14px;
  outline: none; /* hilangkan garis hitam saat aktif */
}

/* Saat aktif (ditambahkan class active lewat JS) */
.search-container.active .search-input {
  width: 180px;
  opacity: 1;
}

html {
  scroll-behavior: smooth;
}

.cart-popup {
  position: fixed;
  top: 70px;
  right: 20px;
  width: 90%;
  max-width: 1000px;
  max-height: 80vh;
  overflow-y: auto;
  z-index: 999;
  background: white;
  box-shadow: 0 1px 15px rgba(255, 203, 232, 0.49);
  border-radius: 15px;
  padding: 20px;
  animation: fadeIn 0.3s ease;
}

.close-btn {
  position: absolute;
  top: -10px;
  right: -10px;
  background: #f8d7da;
  color: #d63384;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  text-align: center;
  font-weight: bold;
  cursor: pointer;
  line-height: 28px;
}

/* DALM POP UP */
.cart-container {
      max-width: 1000px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 1px 15px rgba(255, 203, 232, 0.49);
    }
    .close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #f8d7da;
  color: #d63384;
  border-radius: 50%;
  width: 40px;        /* lebih besar */
  height: 40px;
  font-size: 30px;    /* teks X lebih besar */
  font-weight: bold;
  text-align: center;
  line-height: 40px;  /* biar X-nya di tengah */
  cursor: pointer;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

    .cart-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .cart-table {
      width: 100%;
      border-collapse: collapse;
    }
    .cart-table th, .cart-table td {
      padding: 15px;
      text-align: left;
    }
    .cart-table th {
      border-bottom: 2px solid #ccc;
    }
    .cart-table td {
      border-bottom: 1px solid #eee;
      vertical-align: top;
    }
    .product-info {
      display: flex;
      gap: 15px;
      align-items: center;
    }
    .product-info img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }
    .product-details {
  display: flex;
  flex-direction: column;
}

.product-details small {
  margin-top: 4px;
  color: #777;
  font-size: 12px;
  text-align: left
}

.cart-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 20px;
  flex-wrap: wrap;
}

.cart-footer .btn-group {
  display: flex;
  gap: 10px;
}

  .btn {
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.3s ease;
  }
    .btn:hover {
      transform: scale(1.1);
    }
    .btn.checkout {
      background-color:rgb(233, 147, 190);
      color: white;
    }

    .total-text {
      font-size: 18px;
      font-weight: bold;
    }

.cart-table th,
.cart-table td {
  text-align: center;       /* Horizontal center */
  vertical-align: middle;   /* Vertical center */
}

.qty-control {
  display: inline-flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 999px;
  overflow: hidden;
  padding: 4px 8px;
}

.qty-btn {
  background: none;
  border: none;
  font-size: 18px;
  width: 32px;
  height: 32px;
  cursor: pointer;
}

.qty-input {
  width: 40px;
  text-align: center;
  border: none;
  outline: none;
  font-size: 16px;
}
.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
  </style>
</head>
<body>

  <!-- Header -->
  <header class="header">
    <div class="logo">Toko<span>Kawaii</span></div>
    <div class="header-icons">
    <!-- Search -->
      <div class="search-container">
        <button type="button" class="icon-btn search-toggle" title="Show Search">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="#d63384" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </button>
        <form method="GET" class="search-form">
          <input type="text" class="search-input" name="q" placeholder="Search..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" />
          
        </form>
      </div>

        <!-- Cart -->
      <a href="javascript:void(0);" onclick="toggleCartPopup()" class="icon-btn" title="Cart">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
      </a>
          <!-- Divider -->
      <div class="vertical-divider"></div>
<a href="login.php" class="icon-btn" title="User">👤</a>
      <!-- Login/Signup text -->
  <div class="user-text">
    
    <span>Hello,</span><br>
    <strong>
      <?php
      if (isset($_SESSION['user'])) {
        echo '<a href="login.php" style="text-decoration: none; color: black; font-weight: bold;">' . htmlspecialchars($_SESSION['user']) . '</a>';
      } else {
        echo '<a href="login.php" style="text-decoration: none; color: black; font-weight: bold;">Login/Sign Up</a>';
      } 
      ?>
    </strong>
  </div>
</div>
 
<!-- Cart Popup -->
  <div id="cartPopup" class="cart-popup" style="display: none;">
    <span class="close-btn" onclick="toggleCartPopup()">&times;</span>
    <div class="cart-header">
      <div>You have <?= count($cart_items) ?> products in your cart</div>
    </div>

    <form method="POST"> <!-- FORM MULAI DI SINI -->
      <table class="cart-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($cart_items as $row): 
            $quantity = 1;
            $total = $row['price'] * $quantity;
            $sub_total += $total;
          ?>
          <tr>
            <td>
              <div class="product-info">
                <input type="checkbox" class="cart-checkbox" name="selected[]" value="<?= $row['product_id'] ?>" style="margin-right: 10px;">
                <img src="<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                <div class="product-details">
                  <strong><?= $row['name'] ?></strong>
                  <small>In Stock (<?= $row['stock'] ?> Pcs)</small>
                </div>
              </div>
            </td>
            <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
            <td>
              <div class="qty-control">
                  <button type="button" class="qty-btn">−</button>
                  <input type="number" class="qty-input" name="qty[<?= $row['product_id'] ?>]" value="1" min="1" data-price="<?= $row['price'] ?>">
                  <button type="button" class="qty-btn">+</button>
               </div>
            </td>
            <td class="total-cell">Rp <?= number_format($total, 0, ',', '.') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="cart-footer">
        <div class="total-text">Sub Total: Rp 0</div>
          <div class="btn-group">
            <label style="display: flex; align-items: center; gap: 6px; font-size: 14px;">
              <input type="checkbox" id="selectAllCheckbox">Select All
            </label>
            <button type="submit" name="delete_selected" class="btn" style="background-color: #f8d7da; color: #d63384;">
              DELETE SELECTED

              
            </button>
            <button type="submit" name="checkout" class="btn checkout">GO TO CHECKOUT</button>
          </div>
        </div>
      </div>   
    </form> <!-- FORM BERAKHIR DI SINI -->
  </div>
</header>

  <div class="container">
    <div class="text-section">
      <h1>Overflowing with Cuteness, Just for You!</h1>
      <p>From soft plushies to pastel pens, find everything your kawaii-loving heart desires in one magical place.</p>
      <a href="#products" class="btn-shop">Shop Now</a>
    </div>
    <div class="image-section">
    </div>
  </div>
  <!-- Produk -->
<main>
  <section class="product-preview" id="products">
    <h2>Our Products</h2>
    <div class="product-grid">
        <?php
        $q = $_GET['q'] ?? '';
        if (!empty($q)) {
        $query = "SELECT s.*, c.name AS category_name FROM subcategories s
            JOIN categories c ON s.category_id = c.id
            WHERE s.name LIKE '%$q%'";
        } else {
        $query = "SELECT s.*, c.name AS category_name FROM subcategories s
            JOIN categories c ON s.category_id = c.id";
        }
        $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
    ?>
    <div class="product-card">
      <img src="<?= $row['image'] ?>" alt="<?= htmlspecialchars($row['name']) ?>">
      <div class="card-body">
        <p class="category"><?= htmlspecialchars($row['category_name']) ?></p>
        <h3 class="product-name"><?= htmlspecialchars($row['name']) ?></h3>
        <p class="price">Rp <?= number_format($row['price'], 0, ',', '.') ?></p>
        <p class="quantity">Stock: <?= $row['quantity'] ?></p>

        <form method="POST" class="add-to-cart-form">
          <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
          <input type="hidden" name="quantity" value="1">
          <button type="submit" class="atc">Add to Cart</button>
        </form>
      </div>
    </div>

<?php
  }
} else {
  echo "<p style='grid-column: 1 / -1; text-align: center;'>Barang tidak ditemukan 😢</p>";
}
?>
    </div>
  </section>
</main>
<div id="toast" style="
  position: fixed;
  top: 20px;
  right: 20px;
  background:rgb(255, 176, 216);
  color: white;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 14px;
  display: none;
  z-index: 9999;">
</div>


<script>
// select
document.addEventListener('DOMContentLoaded', function () {
  const selectAllCheckbox = document.getElementById('selectAllCheckbox');
  const checkboxes = document.querySelectorAll('.cart-checkbox');

  selectAllCheckbox.addEventListener('change', function () {
    checkboxes.forEach(cb => cb.checked = this.checked);
    const event = new Event('change');
    checkboxes.forEach(cb => cb.dispatchEvent(event)); // biar subtotal keupdate
  });
});

document.querySelectorAll('.add-to-cart-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault(); // Cegah reload

    const formData = new FormData(this);

    fetch('', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest' // tambahkan untuk identifikasi AJAX
      },
      body: formData
    })
    .then(res => res.text())
    .then(res => {
      if (res.trim() === 'success') {
        showToast("Product successfully added to cart!");
      } else {
        showToast("Failed to add product.");
      }
    })
    .catch(() => showToast("An error occurred."));
    setTimeout(() => {
      location.reload(); // reload setelah 1.5 detik
    }, 1500);
  });
});



function showToast(msg) {
  const toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.style.display = 'block';
  setTimeout(() => {
    toast.style.display = 'none';
  }, 3000);
}

  document.addEventListener('DOMContentLoaded', function () {
  const subtotalText = document.querySelector('.total-text');

  function formatRupiah(value) {
    return 'Rp ' + value.toLocaleString('id-ID');
  }

  function updateSubtotal() {
    let subtotal = 0;
    document.querySelectorAll('tbody tr').forEach(row => {
      const checkbox = row.querySelector('.cart-checkbox');
      const input = row.querySelector('.qty-input');
      const price = parseInt(input.dataset.price);
      const qty = parseInt(input.value);
      if (checkbox.checked) {
        subtotal += price * qty;
      }
    });
    subtotalText.textContent = 'Sub Total: ' + formatRupiah(subtotal);
  }

  // Tambah event listener untuk tombol +/- dan checkbox
  document.querySelectorAll('.qty-control').forEach(control => {
    const minus = control.querySelector('.qty-btn:first-child');
    const plus = control.querySelector('.qty-btn:last-child');
    const input = control.querySelector('.qty-input');
    const row = control.closest('tr');
    const totalCell = row.querySelector('.total-cell');
    const checkbox = row.querySelector('.cart-checkbox');
    const price = parseInt(input.dataset.price);

    function updateRowTotal() {
      const qty = parseInt(input.value);
      const total = price * qty;
      totalCell.textContent = formatRupiah(total);
    }

    plus.addEventListener('click', () => {
      input.value = Math.min(99, parseInt(input.value) + 1);
      updateRowTotal();
      updateSubtotal();
    });

    minus.addEventListener('click', () => {
      input.value = Math.max(1, parseInt(input.value) - 1);
      updateRowTotal();
      updateSubtotal();
    });

    input.addEventListener('change', () => {
      updateRowTotal();
      updateSubtotal();
    });

    checkbox.addEventListener('change', updateSubtotal);

    // Inisialisasi
    updateRowTotal();
    updateSubtotal();

  });
});

function toggleCartPopup() {
  const cart = document.getElementById("cartPopup");
  cart.style.display = (cart.style.display === "block") ? "none" : "block";
}

  document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.search-toggle');
    const container = document.querySelector('.search-container');

    toggleBtn.addEventListener('click', function () {
      container.classList.toggle('active');
      const input = container.querySelector('.search-input');
      if (container.classList.contains('active')) {
        input.focus();
      }
    });
  });
</script>


</body>
</html>
