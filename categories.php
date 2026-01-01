<?php
include 'php.php'; // your DB connection file

// Fetch categories
$categories = $conn->query("SELECT id, name FROM categories");


?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Explore by Category</title>




<style>
  * {
    box-sizing: border-box;
  }
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
  }
  .container {
    display: flex;
    height: 100vh;
  }

  /* Sidebar */
  .sidebar {
    width: 220px;
    background-color: #fff;
    color: #444;
    padding: 20px;
    display: flex;
    flex-direction: column;
    border-right: 1px solid #e0e0e0;
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
    cursor: pointer;
  }
  .sidebar nav a:hover,
  .sidebar nav a.active {
    background-color: #FBC9D9;
    color: #7a3e5c;
  }

  /* Main content */
  .main-content {
    flex-grow: 1;
    padding: 30px;
    overflow-y: auto;
    background-color: #fff;
    display: flex;
    flex-direction: column;
  }
  .title {
    font-size: 28px;
    margin-bottom: 20px;
    font-weight: bold;
  }

  /* Cards grid */
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
  }

 .category-card {
  background: #ffe6f0; /* softer pink */
  border: 2px solid #d6336c;
  border-radius: 15px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 5px 15px rgba(214, 51, 108, 0.3);
  cursor: pointer;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover,
.category-card:focus {
  box-shadow: 0 8px 18px rgba(214, 51, 108, 0.4);
  transform: scale(1.03); /* efek membesar */
}


  .category-name {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #7a3e5c;
  }
  .category-arrow {
    text-align: right;
    font-size: 1.5rem;
    color: #FBC9D9;
    font-weight: bold;
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
        <a href="Categories.php" class="active">Categories</a>
        <a href="Products.php">Products</a>
        <a href="Orders.php">Orders</a>
      </nav>
    </aside>

    <main class="main-content">
      <div class="title">Explore by <span style="color:#7a3e5c;">category</span>
      
    </div>

      <div class="grid">
        <?php while ($category = $categories->fetch_assoc()) : ?>
          <div class="category-card" tabindex="0" role="link"
               onclick="window.location.href='products_by_category.php?category_id=<?= $category['id'] ?>'"
               onkeydown="if(event.key==='Enter' || event.key===' ') { window.location.href='products_by_category.php?category_id=<?= $category['id'] ?>'; }">
            <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
            <div class="category-arrow">→</div>
          </div>
        <?php endwhile; ?>
      </div>
    </main>
  </div>
</body>
</html>
