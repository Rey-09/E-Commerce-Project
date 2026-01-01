<?php
include 'php.php'; // koneksi DB

$keyword = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if ($keyword === '') {
    echo json_encode([]);
    exit;
}

// Query cari berdasarkan nama produk (subcategories)
$sql = "SELECT id, name, price, image FROM subcategories
        WHERE name LIKE '%$keyword%'
        ORDER BY name ASC";

$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image' => $row['image'] ?: 'https://via.placeholder.com/240x150?text=No+Image'
        ];
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);

$conn->close();
