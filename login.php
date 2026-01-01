<?php
include 'php.php'; // koneksi ke database
session_start();

$message = "";

// Ambil input
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'user'; // default ke user

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi dasar
    if (empty($email) || empty($password)) {
        $message = "Email dan password harus diisi.";
    } else {
        // Cek apakah user sudah ada
        $checkQuery = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $checkQuery->bind_param("ss", $email, $username);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows === 0) {
            // Belum ada: lakukan registrasi
            if (empty($username)) {
                $message = "Username harus diisi untuk pendaftaran.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $insertStmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $insertStmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

                if ($insertStmt->execute()) {
                    // Simpan session setelah berhasil daftar
                    $_SESSION['user'] = $username;
                    $_SESSION['role'] = $role;
                    $_SESSION['user_id'] = $conn->insert_id; // ✅ ID user baru

                    header("Location: " . ($role === 'admin' ? 'homepage.php' : 'home.php'));
                    exit;
                } else {
                    $message = "Gagal menyimpan ke database.";
                }
            }
        } else {
            // Sudah ada: login
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['role'] = $user['role'] ?? 'user';
                $_SESSION['user_id'] = $user['id']; // ✅ Tambahkan ini

                header("Location: " . ($user['role'] === 'admin' ? 'homepage.php' : 'home.php'));
                exit;
            } else {
                $message = "Password salah.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login </title>
  <style>
    /* Styles tetap seperti yang kamu buat */
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }
    .bg {
      background-image: url('https://i.pinimg.com/736x/80/33/8a/80338ad730089d9b1c48fdd972683113.jpg');
      background-size: cover;
      background-position: center;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      background: rgba(249, 229, 235, 0.9);
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    .login-container h1 {
      text-align: center;
      margin-bottom: 25px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
      width: 90%;
      padding: 12px;
      margin: 10px auto;
      border: 1px solid #ccc;
      border-radius: 10px;
      display: block;
    }
    .role-selection {
      text-align: center;
      margin: 10px 0;
      font-size: 14px;
    }
    .role-selection label {
      margin: 0 10px;
      cursor: pointer;
    }
    .role-selection input[type="radio"] {
      margin-right: 5px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: rgb(239, 155, 187);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
    }
    .message {
      text-align: center;
      margin-bottom: 10px;
      color: #d60000;
    }
  </style>
</head>
<body>
  <div class="bg">
    <div class="login-container">
      <h1>Login</h1>
      <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
      <form method="POST" action="">
        <input type="text" name="username" placeholder="Username (isi saat daftar)" />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <div class="role-selection">
          <label><input type="radio" name="role" value="user" checked /> User</label>
          <label><input type="radio" name="role" value="admin" /> Admin</label>
        </div>
        <button type="submit">Submit</button>
      </form>
    </div>
  </div>
</body>
</html>