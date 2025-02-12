<?php
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "config.php"; // Database connection

// Fetch purchase history grouped by supplier
$query = "SELECT suppliers.nama_supplier, pembelian.nama_barang, pembelian.jumlah, pembelian.harga_per_unit, pembelian.tanggal_pembelian 
          FROM pembelian 
          JOIN suppliers ON pembelian.supplier_id = suppliers.id 
          ORDER BY suppliers.nama_supplier ASC, pembelian.tanggal_pembelian DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$pembelianList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group purchases by supplier
$groupedPurchases = [];
$supplierNames = []; // Store supplier names for dropdown

foreach ($pembelianList as $pembelian) {
    $supplier = $pembelian['nama_supplier'];
    if (!isset($groupedPurchases[$supplier])) {
        $groupedPurchases[$supplier] = [];
        $supplierNames[] = $supplier; // Add unique supplier to dropdown
    }
    $groupedPurchases[$supplier][] = $pembelian;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian</title>
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
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
            position: relative; /* Ensure relative positioning */
        }

        .back-link {
            position: absolute;
            left: 20px;
            top: 20px;
        }

        .back-link img {
            width: 50px;
            height: auto;
        }

        .filter-container {
            margin-bottom: 20px;
        }

        .supplier-section {
            display: none; /* Hide by default */
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }

        th {
            background-color: black;
            color: white;
        }
    </style>
</head>
<body>
    <div class="card">
        <a href="pembelian.php" class="back-link"><img src="assets/back.png" alt="Back"></a>
        <h2>Riwayat Pembelian</h2>

        <!-- Dropdown for selecting supplier -->
        <div class="filter-container">
            <label for="supplierFilter">Pilih Supplier:</label>
            <select id="supplierFilter" onchange="filterPurchases()">
                <option value="all">Semua Supplier</option>
                <?php foreach ($supplierNames as $supplier): ?>
                    <option value="<?= htmlspecialchars($supplier); ?>"><?= htmlspecialchars($supplier); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php foreach ($groupedPurchases as $supplier => $purchases): ?>
            <div class="supplier-section" data-supplier="<?= htmlspecialchars($supplier); ?>">
                <h3><?= htmlspecialchars($supplier); ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Harga per Unit</th>
                            <th>Tanggal Pembelian</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalHarga = 0;
                        foreach ($purchases as $pembelian): 
                            $subtotal = $pembelian['jumlah'] * $pembelian['harga_per_unit'];
                            $totalHarga += $subtotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($pembelian['nama_barang']); ?></td>
                                <td><?= $pembelian['jumlah']; ?></td>
                                <td>Rp. <?= number_format($pembelian['harga_per_unit'], 0, ',', '.'); ?></td>
                                <td><?= $pembelian['tanggal_pembelian']; ?></td>
                                <td>Rp. <?= number_format($subtotal, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="4" style="text-align: right; font-weight: bold;">Total Harga:</td>
                            <td style="font-weight: bold;">Rp. <?= number_format($totalHarga, 0, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function filterPurchases() {
            let selectedSupplier = document.getElementById("supplierFilter").value;
            let sections = document.querySelectorAll(".supplier-section");

            sections.forEach(section => {
                if (selectedSupplier === "all" || section.getAttribute("data-supplier") === selectedSupplier) {
                    section.style.display = "block";
                } else {
                    section.style.display = "none";
                }
            });
        }

        // Show all suppliers by default
        filterPurchases();
    </script>
</body>
</html>
