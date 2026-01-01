<?php
include 'php.php';

// Ambil data admin
$resultAdmin = $conn->query("SELECT * FROM users WHERE role = 'admin'");

// Ambil data user biasa
$resultUser = $conn->query("SELECT * FROM users WHERE role = 'user'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Daftar Pengguna</title>
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
      background-attachment: fixed;
      color: #333;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 220px;
      background-color: rgba(255, 255, 255, 0.9);
      color: #444;
      padding: 20px;
      display: flex;
      flex-direction: column;
      border-right: 1px solid #e0e0e0;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      z-index: 2;
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
      transition: background-color 0.3s, color 0.3s;
    }

    .sidebar nav a:hover,
    .sidebar nav a.active {
      background-color: #FBC9D9;
      color: #7a3e5c;
    }

    .main-content {
      margin-left: 220px;
      padding: 30px;
      flex-grow: 1;
      background-color: rgba(255, 255, 255, 0.85);
      min-height: 100vh;
      backdrop-filter: blur(6px);
      border-radius: 12px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    h2 {
      margin: 0;
      font-weight: 600;
      font-size: 28px;
    }

    h3 {
      margin-top: 30px;
      margin-bottom: 10px;
      color: #7a3e5c;
    }

    .sub {
      font-size: 14px;
      color: #888;
      margin-top: 4px;
    }

    .back-button {
      padding: 8px 16px;
      background-color: #FBC9D9;
      color: #7a3e5c;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      text-decoration: none;
      transition: background-color 0.3s;
    }

    .back-button:hover {
      background-color: #e597ca;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid rgb(224, 224, 224);
    }

    th {
      background-color: rgb(249, 193, 250);
      font-weight: 600;
    }

    tbody tr:hover {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>

  <div class="container">
    <aside class="sidebar">
      <h3>Store</h3>
      <nav>
        <a href="homepage.php">Dashboard</a>
        <a href="users.php" class="active">Users</a>
        <a href="Categories.php">Categories</a>
        <a href="Products.php">Products</a>
        <a href="Orders.php">Orders</a>
      </nav>
    </aside>

    <main class="main-content">
      <div class="header">
        <div>
          <h2>User List</h2>
          <div class="sub">List of accounts registered in the system</div>
        </div>
        <a href="homepage.php" class="back-button">← Back</a>
      </div>

      <h3>Admin</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $resultAdmin->fetch_assoc()) { ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <h3>User</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $resultUser->fetch_assoc()) { ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </main>
  </div>

</body>
</html>
