<?php
include 'php.php'; // koneksi DB

// DELETE
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM subcategories WHERE id = $delete_id");
    header("Location: Products.php");
    exit();
}

// UPDATE
$error = '';
if (isset($_POST['update_id'])) {
    $update_id = intval($_POST['update_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $image = $conn->real_escape_string($_POST['image']);
    $category_id = intval($_POST['category_id']);

    $update_sql = "UPDATE subcategories 
                   SET name='$name', price=$price, quantity=$quantity, image='$image', category_id=$category_id 
                   WHERE id=$update_id";

    if ($conn->query($update_sql)) {
        header("Location: Products.php");
        exit();
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}


// ADD
if (isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $image = $conn->real_escape_string($_POST['image']);
    $category_id = intval($_POST['category_id']);


    $insert_sql = "INSERT INTO subcategories (name, price, quantity, image, category_id)
               VALUES ('$name', $price, $quantity, '$image', $category_id)";

    if ($conn->query($insert_sql)) {
        header("Location: Products.php");
        exit();
    } else {
        $error = "Error adding product: " . $conn->error;
    }
}

// Form Edit
$edit_product = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $res = $conn->query("SELECT * FROM subcategories WHERE id=$edit_id LIMIT 1");
    if ($res->num_rows > 0) {
        $edit_product = $res->fetch_assoc();
    }
}

// Ambil Produk
$sql = "SELECT s.id, s.name, s.price, s.quantity, s.image, c.name AS category, s.category_id
        FROM subcategories s
        LEFT JOIN categories c ON s.category_id = c.id
        ORDER BY s.id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Query error: " . $conn->error);
}

// Ambil Kategori
$categories = $conn->query("SELECT * FROM categories");
$category_options = [];
while ($cat = $categories->fetch_assoc()) {
    $category_options[$cat['id']] = $cat['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Product Dashboard</title>

  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-image: url('https://i.pinimg.com/736x/00/c8/f0/00c8f06ee225c02d922e7f9f28c87332.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
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
      overflow-y: auto;
      background-color: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(6px);
      border-radius: 12px;
      margin: 20px;
    }

    .title {
      font-size: 28px;
      margin-bottom: 20px;
      font-weight: bold;
    }

    .search-wrapper {
      margin-bottom: 20px;
    }

    .search-input {
      width: 100%;
      max-width: 300px;
      padding: 10px 16px;
      font-size: 16px;
      border-radius: 20px;
      border: 2px solid #fbc9d9;
      background-color: #fff0f5;
      color: #7a3e5c;
      outline: none;
      box-shadow: 0 0 5px rgba(255, 182, 193, 0.3);
      transition: all 0.3s ease;
    }

    .search-input::placeholder {
      color: #c080a3;
    }

    .search-input:focus {
      border-color: #e597ca;
      background-color: #ffe4f1;
      box-shadow: 0 0 8px rgba(249, 183, 204, 0.6);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background-color: rgb(250, 183, 237);
      color: white;
    }

    thead th {
      padding: 12px 10px;
      text-align: left;
      border-bottom: 2px solid rgb(250, 192, 241);
    }

    tbody tr {
      border-bottom: 1px solid #eee;
    }

    tbody tr:hover {
      background-color: #f0f8ff;
    }

    tbody td {
      padding: 10px 8px;
      vertical-align: middle;
    }

    .actions {
      white-space: nowrap;
    }

    .btn-update {
      background-color: #fbc9d9;
      color: #7a3e5c;
      border: none;
      padding: 6px 12px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      margin-right: 6px;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s;
    }

    .btn-update:hover {
      background-color: #f9b7cc;
    }

    .btn-delete {
      background-color: #b3758b;
      color: #fff;
      border: none;
      padding: 6px 12px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s;
    }

    .btn-delete:hover {
      background-color: #9f5f77;
    }

    form.update-form {
      background: white;
      padding: 20px;
      border-radius: 10px;
      max-width: 400px;
      margin-bottom: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    form.update-form label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }

    form.update-form input, form.update-form select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
    }

    .form-buttons {
      margin-top: 15px;
    }

    .btn-cancel {
      background-color: #aaa;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 15px;
      font-weight: bold;
    }

    .btn-cancel:hover {
      background-color: #888;
    }

    .error {
      color: red;
      margin-top: 10px;
    }


    .btn-add {
  background-color: #fbc9d9;
  color: #7a3e5c;
  border: none;
  padding: 6px 12px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  text-decoration: none;
  display: inline-block;
  transition: background-color 0.3s;
}

.btn-add:hover {
  background-color: #f9b7cc;
}


form.add-form {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px 20px;
  max-width: 500px;
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  margin-top: 20px;
}

form.add-form h3 {
  grid-column: 1 / -1;
  margin-bottom: 15px;
  font-size: 22px;
  color: #7a3e5c;
}

form.add-form label {
  margin-top: 0;
  font-weight: bold;
}

form.add-form input,
form.add-form select {
  width: 100%;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #f9b7cc;
  background-color: #fff0f5;
  color: #7a3e5c;
}

form.add-form .form-buttons {
  grid-column: 1 / -1;
  display: flex;
  justify-content: flex-end;
  margin-top: 10px;
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
      <a href="Products.php" class="active">Product</a>
      <a href="Orders.php">Orders</a>
    </nav>
  </aside>

  <main class="main-content">
    <h2 class="title">Product List</h2>

   <div class="search-wrapper" style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">

      <input
        type="text"
        id="searchInput"
        placeholder="🔍 Cari produk..."
        onkeyup="searchTable()"
        class="search-input"
        autocomplete="off"
      />
      <button onclick="toggleAddForm()" class="btn-add">+ Add Product</button>
    </div>

    <div id="addForm" style="display: none; margin-bottom: 20px;">
      <?php if (!$edit_product): ?>
     <form method="post" class="add-form wide-form">
  <h3>Add New Product</h3>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <label for="name">Product Name:</label>
  <input type="text" name="name" required>

  <label for="price">Price:</label>
  <input type="number" name="price" step="0.01" required>

  <label for="quantity">Quantity:</label>
  <input type="number" name="quantity" required>

  <label for="image">Image URL:</label>
  <input type="text" name="image" required>

  <label for="category_id">Category:</label>
  <select name="category_id" required>
    <?php foreach($category_options as $cat_id => $cat_name): ?>
      <option value="<?= $cat_id ?>"><?= htmlspecialchars($cat_name) ?></option>
    <?php endforeach; ?>
  </select>

  <div class="form-buttons">
    <button type="submit" name="add_product" class="btn-add">Add Product</button>
  </div>
</form>

   
      <?php endif; ?>
    </div>

    <?php if ($edit_product): ?>
      <form method="post" class="update-form">
        <h3>Update Product ID #<?= htmlspecialchars($edit_product['id']) ?></h3>
        <?php if ($error): ?>
          <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <input type="hidden" name="update_id" value="<?= $edit_product['id'] ?>">
        <label for="name">Product Name:</label>

        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($edit_product['name']) ?>" />
        <label for="price">Price:</label>

        <input type="number" id="price" name="price" step="0.01" required value="<?= htmlspecialchars($edit_product['price']) ?>" />
        <label for="quantity">Quantity:</label>

         <label for="image">Image URL:</label>
        <input type="text" name="image" required value="<?= htmlspecialchars($edit_product['image'] ?? '') ?>">


      
        <input type="number" id="quantity" name="quantity" required value="<?= htmlspecialchars($edit_product['quantity']) ?>" />
        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required>
          <?php foreach($category_options as $cat_id => $cat_name): ?>
            <option value="<?= $cat_id ?>" <?= $cat_id == $edit_product['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat_name) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-buttons">
          <button type="submit" class="btn-update">Update Product</button>
          <button type="button" onclick="window.location.href='Products.php'" class="btn-cancel">Cancel</button>
        </div>
      </form>
    <?php endif; ?>

  
      <table id="productTable">
  <thead>
    <tr>
      <th>Image</th>
      <th>Product name</th>
      <!-- <th>ID</th> -->
      <th>Price</th>
      <th>Category</th>
      <th>Quantity</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $result->fetch_assoc()) { ?>
      <tr>
        <td>
          <?php if (!empty($row['image'])): ?>
    <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" 
         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; display: block; margin: 0 auto;">
  <?php else: ?>
         
            No Image
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <!-- <td><?= htmlspecialchars($row['id']) ?></td> -->
        <td><?= "Rp " . number_format($row['price'], 0, ',', '.') ?></td>
        <td><?= htmlspecialchars($row['category'] ?? 'Unknown') ?></td>
        <td><?= htmlspecialchars($row['quantity']) ?></td>
        <td class="actions">
          <a href="Products.php?edit_id=<?= $row['id'] ?>" class="btn-update">Update</a>
          <a href="Products.php?delete_id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure to delete this product?')">Delete</a>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>

  </main>
</div>

<script>
  function toggleAddForm() {
    const form = document.getElementById('addForm');
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
  }

  function searchTable() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#productTable tbody tr");

    rows.forEach(row => {
      const name = row.cells[0].textContent.toLowerCase();
      const id = row.cells[1].textContent.toLowerCase();
      const category = row.cells[3].textContent.toLowerCase();
      const match = name.includes(input) || id.includes(input) || category.includes(input);
      row.style.display = match ? "" : "none";
    });
  }
</script>
</body>
</html>
