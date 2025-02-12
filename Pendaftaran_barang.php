<?php
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "config.php"; // Database connection

// Determine dashboard based on role
$dashboard = ($_SESSION['role'] == 'admin') ? "admin_dashboard.php" : "officer_dashboard.php";

// Handling item deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete item from 'barang' table
    $query = "DELETE FROM barang WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Reset ID sequence
    $query = "SET @num = 0; 
              UPDATE barang SET id = @num := (@num+1);
              ALTER TABLE barang AUTO_INCREMENT = 1;";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $_SESSION['success'] = "Barang berhasil dihapus dan ID diperbarui!";
    header("Location: pendaftaran_barang.php");
    exit();
}

// Fetch items
$query = "SELECT id, nama_barang, stok, harga FROM barang ORDER BY id ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$barangList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftaran Barang</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Montserrat, sans-serif;
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
            max-width: 900px;
            margin: 0 auto; /* Center horizontally */
        }

        .card h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .table-container {
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid black;
        }

        th {
            background-color: black;
            color: white;
        }

        .btn-delete {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 15px;
            cursor: pointer;
            border-radius: 5px;
        }

        .back-link img{
            width: 35px;
            height: 100%;
        }
    </style>
</head>
<body>

    <div class="card">
        <a href="<?= $dashboard; ?>" class="back-link"><img src="assets/back.png" alt=""></a>
        <h2>Daftar Barang</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;"><?= $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="table-container">
            <h3 class="text-center">Daftar Barang</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barangList as $barang): ?>
                        <tr>
                            <td><?= $barang['id']; ?></td>
                            <td><?= htmlspecialchars($barang['nama_barang']); ?></td>
                            <td><?= $barang['stok']; ?></td>
                            <td>Rp. <?= number_format($barang['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <button class="btn-delete" onclick="confirmDelete(<?= $barang['id']; ?>)">X</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script>    
        function confirmDelete(id) {
            if (confirm("Apakah Anda yakin ingin menghapus barang ini?")) {
                window.location.href = "pendaftaran_barang.php?delete=" + id;
            }
        }
    </script>

</body>
</html>
