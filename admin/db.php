<?php
$host = 'localhost';
$dbname = 'cozastore_db';
$user = 'root';
$pass = ''; // Sur MAMP/Mac, c'est souvent 'root'

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Configuration pour afficher les erreurs SQL pendant le développement
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>