<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    die("Anda harus login terlebih dahulu.");
}

// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "wad";

$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id']; // ID user yang login

// Ambil role user
$roleQuery = $conn->prepare("SELECT role FROM users WHERE id = ?");
$roleQuery->bind_param("i", $user_id);
$roleQuery->execute();
$roleResult = $roleQuery->get_result();
$userData = $roleResult->fetch_assoc();
$role = $userData['role'] ?? 'user'; // default ke user kalau tidak ada data

// Query data orders
if ($role === 'admin') {
    // Ambil semua order
    $sql = "SELECT o.id, o.order_number, u.username AS customer_name, s.name AS product_name, o.total_price, o.order_date, o.status
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN subcategories s ON o.product_id = s.id
            ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($sql);
} else {
    // Ambil order user sendiri
    $sql = "SELECT o.id, o.order_number, u.username AS customer_name, s.name AS product_name, o.total_price, o.order_date, o.status
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN subcategories s ON o.product_id = s.id
            WHERE o.user_id = ?
            ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Order Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-image: url('https://i.pinimg.com/736x/00/c8/f0/00c8f06ee225c02d922e7f9f28c87332.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      color: #333;
    }

    .container {
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 220px;
      background-color: rgba(255, 255, 255, 0.9);
      color: #444;
      padding: 20px;
      display: flex;
      flex-direction: column;
      border-right: 1px solid #e0e0e0;
      backdrop-filter: blur(6px);
    }

    .sidebar h3 {
      font-size: 24px;
      margin-bottom: 20px;
    }

    .sidebar nav a {
      display: block;
      color: #444;
      text-decoration: none;
      margin-bottom: 15px;
      font-size: 16px;
      padding: 8px 12px;
      border-radius: 4px;
      transition: 0.3s;
    }

    .sidebar nav a:hover,
    .sidebar nav a.active {
      background-color: #FBC9D9;
      color: #7a3e5c;
    }

    .main-content {
      flex-grow: 1;
      padding: 30px;
      background-color: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(6px);
      border-radius: 12px;
      margin: 20px;
      overflow-y: auto;
    }

    .title {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    thead {
      background-color: rgb(243, 184, 237);
      color: white;
    }

    thead th {
      padding: 12px 10px;
      text-align: left;
      border-bottom: 2px solid rgb(245, 112, 199);
    }

    tbody tr {
      border-bottom: 1px solid #eee;
    }

    tbody tr:hover {
      background-color: #f0f8ff;
    }

    tbody td {
      padding: 10px 8px;
    }
  </style>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <h3>Store</h3>
      <nav>
        <a href="homepage.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="Categories.php">Categories</a>
        <a href="Products.php">Products</a>
        <a href="Orders.php" class="active">Orders</a>
      </nav>
    </aside>

    <main class="main-content">
      <h2 class="title">Order List</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Order Number</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Total</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
   
  

<tbody>
  <?php while($row = $result->fetch_assoc()) { ?>
    <tr>
      <td><?= htmlspecialchars($row['id']) ?></td>
      <td><?= htmlspecialchars($row['order_number']) ?></td>
      <td><?= htmlspecialchars($row['customer_name']) ?></td>
      <td><?= htmlspecialchars($row['product_name']) ?></td>
      <td><?= "Rp " . number_format($row['total_price'], 0, ',', '.') ?></td>
      <td><?= htmlspecialchars(date("d M Y", strtotime($row['order_date']))) ?></td>
      <td><?= htmlspecialchars($row['status']) ?></td>
      <td>

      </td>
    </tr>
  <?php } ?>
</tbody>

      </table>
    </main>
  </div>
</body>
</html>
