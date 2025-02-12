<?php
try {
    $host = 'localhost'; 
    $dbname = 'auth_privilege';
    $username = 'root';
    $password = ''; 

    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
