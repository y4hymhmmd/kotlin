<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'officer') {
  header("Location: login.php");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Officer</title>

  <style>
    * {
      font-family: Montserrat, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      display: flex;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: black;
      color: white;
      padding: 20px;
      position: fixed;
    }

    .sidebar h3 {
      padding-top: 30px;
      margin-bottom: 20px;
      padding-left: 20px;
      font-size: 30px;
    }

    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      padding-left: 20px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .sidebar a:hover {
      background-color: gray;
    }

    .main-content {
      margin-left: 270px;
      padding: 20px;
      width: 100%;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .back-btn {
      font-size: 20px;
      color: black;
      text-decoration: none;
    }

    .card {
      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
      height: 200px;
      width: 300px;
      text-align: center;
      border-radius: 10px;
      background-color: transparent;
      color: black;
      border: 2px solid black;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      margin: 10px;
    }


    .card:hover {
      background-color: gray;
    }

    .card h4 {
      margin: 0;
      padding-right: 20px;
      
      
    }

    .flex-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      align-items: center;
      justify-content: center;
      padding-top: 50px;
    }
    .card img{
      width: 100%px;
      height: 50px;
      padding-left: 15px;
      padding-right: 20px;
    }
    .header{
      padding-top: 100px;
      justify-content: center;
    }
  </style>
</head>

<body>
  <div class="sidebar">
    <h3>Welcome Officer </h3>
    <a href="pendaftaran_barang.php">Daftar Barang</a>
    <a href="pembelian.php">Pembelian</a>
    <a href="riwayat.php">Riwayat</a>
    <a href="logout.php">Logout</a>
    
  </div>

  <div class="main-content">
    <div class="header">
      <h2>Dashboard Officer</h2>
    </div>
    <div class="flex-container">
      <a href="pendaftaran_barang.php" class="card">
        <img src="assets/clipboard.png">
        <h4>Daftar Barang</h4>
      </a>
      <a href="pembelian.php" class="card">
        <img src="assets/parcel.png">
        <h4>Pembelian</h4>
      </a>
      <a href="riwayat.php" class="card">
      <img src="assets/file (2).png">
        <h4>Riwayat Pembelian</h4>
      </a>
    </div>
  </div>
</body>

</html>