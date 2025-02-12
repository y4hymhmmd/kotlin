<?php
session_start();
include "config.php";

// Redirect if not logged in or not an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$regmes = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get role selection

    // Hash password
    $hash_password = hash("sha256", $password);

    try {
        $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hash_password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            $regmes = "Akun telah terdaftar, silahkan login.";
        } else {
            $regmes = "Pendaftaran akun gagal, coba lagi.";
        }
    } catch (PDOException $e) {
        $regmes = "Username telah digunakan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        * {
            font-family: Montserrat, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .register-card {
            width: 600px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            border: 2px solid black;
            box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
        }

        .register-card h2 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid black;
            border-radius: 5px;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }

        .register-button {
            width: 100%;
            padding: 10px;
            background: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .register-button:hover {
            background: gray;
        }

        .back-link {
            position: absolute;
            left: 20px;
            top: 20px;
        }

        .back-link img {
            width: 35px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <a href="admin_dashboard.php" class="back-link"><img src="assets/back.png" alt="Back"></a>
        <h2>Register</h2>
        <i><?= $regmes ?></i>
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="admin">Admin</option>
                    <option value="officer">Officer</option>
                </select>
            </div>
            <button type="submit" class="register-button" name="Register">Register</button>
        </form>
    </div>
</body>
</html>
