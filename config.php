<?php

    include('product.php');
    
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=basvuru_case', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $product = new Product($pdo);
    } catch(PDOException $e) {
        echo "Veritabanı bağlantısı başarısız: " . $e->getMessage();
    }
?>