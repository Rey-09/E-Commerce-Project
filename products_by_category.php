<?php
include 'php.php';

// Ambil category_id dari URL
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

if ($category_id <= 0) {
    die("Kategori tidak valid.");
}

// Query nama kategori
$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$stmt->bind_result($category_name);
if (!$stmt->fetch()) {
    die("Kategori tidak ditemukan.");
}
$stmt->close();

// Query subkategori termasuk image
$stmt = $conn->prepare("SELECT id, name, image FROM subcategories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Categories<?= htmlspecialchars($category_name) ?></title>
<style>
  
  body {
    font-family: Arial, sans-serif;
    padding: 30px;
    background:lavender;
    background-size: cover; /* Agar gambar menutupi seluruh area */
    background-repeat: no-repeat;
    background-position: center;
    color: #333;
}
s
  h1 {
    color: #7a3e5c;
    margin-bottom: 20px;
  }

  .back-button {
    display: inline-block;
    margin-bottom: 25px;
    padding: 10px 18px;
    background-color:r
    gb(232, 198, 243);
    color: #7a3e5c;
    font-weight: bold;
    text-decoration: none;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: background-color 0.3s, transform 0.2s;
  }
  .back-button:hover {
    background-color: #f9a7bf;
    transform: translateY(-2px);
  }

  .subcategory-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
  }

  .subcategory-card {
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    text-align: center;
    transition: box-shadow 0.3s;
  }
  .subcategory-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .subcategory-image {
    width: 100%;
    max-height: 150px;
    object-fit: cover;
    border-radius: 10px;
  }

  .subcategory-name {
    margin-top: 10px;
    font-size: 18px;
    font-weight: bold;
    color: #7a3e5c;
  }

  .header-bar {
  display: flex;
  align-items: center;
  gap: 330px; /* jarak antara tombol dan judul */
  margin-bottom: 20px;
}


</style>
</head>
<body>

<div class="header-bar">
  <a href="categories.php" class="back-button">← Back</a>
  <h1> Categories: <?= htmlspecialchars($category_name) ?></h1>
</div>

<div class="subcategory-list">
  <?php while ($subcategory = $result->fetch_assoc()): ?>
    <div class="subcategory-card" onclick="window.location.href='products.php?subcategory_id=<?= $subcategory['id'] ?>'">
      <?php if (!empty($subcategory['image'])): ?>
        <img src="<?= htmlspecialchars($subcategory['image']) ?>" alt="<?= htmlspecialchars($subcategory['name']) ?>" class="subcategory-image">
      <?php else: ?>
        <img src="placeholder.jpg" alt="No Image" class="subcategory-image">
      <?php endif; ?>
      <div class="subcategory-name"><?= htmlspecialchars($subcategory['name']) ?></div>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
