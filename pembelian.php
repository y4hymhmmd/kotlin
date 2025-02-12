<?php
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "config.php"; // Database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Determine dashboard link based on role
$dashboard = ($_SESSION['role'] == 'admin') ? 'admin_dashboard.php' : 'officer_dashboard.php';

// Fetch list of suppliers
$query = "SELECT * FROM suppliers ORDER BY nama_supplier ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handling new purchase
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $supplier_id = $_POST['supplier_id'];
    $nama_barang = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $harga_per_unit = $_POST['harga_per_unit'];

    try {
        // Insert into 'pembelian' table
        $query = "INSERT INTO pembelian (supplier_id, nama_barang, jumlah, harga_per_unit) 
                  VALUES (:supplier_id, :nama_barang, :jumlah, :harga_per_unit)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':supplier_id', $supplier_id);
        $stmt->bindParam(':nama_barang', $nama_barang);
        $stmt->bindParam(':jumlah', $jumlah);
        $stmt->bindParam(':harga_per_unit', $harga_per_unit);
        $stmt->execute();

        // Check if item already exists in 'barang'
        $query = "SELECT * FROM barang WHERE nama_barang = :nama_barang";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nama_barang', $nama_barang);
        $stmt->execute();
        $existingBarang = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingBarang) {
            // Update stock
            $newStock = $existingBarang['stok'] + $jumlah;
            $query = "UPDATE barang SET stok = :stok WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':stok', $newStock);
            $stmt->bindParam(':id', $existingBarang['id']);
            $stmt->execute();
        } else {
            // Insert as new item
            $query = "INSERT INTO barang (nama_barang, stok, harga) VALUES (:nama_barang, :stok, :harga)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nama_barang', $nama_barang);
            $stmt->bindParam(':stok', $jumlah);
            $stmt->bindParam(':harga', $harga_per_unit);
            $stmt->execute();
        }

        $_SESSION['success'] = "Pembelian berhasil dicatat dan stok diperbarui!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header("Location: pembelian.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 500px;
            margin: 20px auto;
        }

        .card h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
        }

        select, input, button {
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: black;
            color: white;
            cursor: pointer;
            margin-top: 15px;
            border: none;
        }

        button:hover {
            background-color: #333;
        }

        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .history-link {
            display: block;
            text-align: center;
            text-decoration: none;
            color: white;
            background-color: black;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .history-link:hover {
            background-color: #333;
        }
        .back-link img{
            width: 35px;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="card">
    <a href="<?= $dashboard ?>" class="back-link"><img src="assets/back.png" alt=""></a>
        <h2>Pembelian Barang</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-message"><?= $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?= $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="supplier_id">Pilih Supplier:</label>
            <select name="supplier_id" required>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['id']; ?>"><?= htmlspecialchars($supplier['nama_supplier']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="nama_barang">Nama Barang:</label>
            <input type="text" name="nama_barang" required>

            <label for="jumlah">Jumlah:</label>
            <input type="number" name="jumlah" required>

            <label for="harga_per_unit">Harga per Unit:</label>
            <input type="number" name="harga_per_unit" required>

            <button type="submit" name="submit">Beli</button>
            <a href="riwayat.php" class="history-link">Lihat Riwayat Pembelian</a>
        </form>
    </div>
</body>
</html>
