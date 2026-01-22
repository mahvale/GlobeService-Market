<?php
require_once './db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Récupérer le nom de l'image avant de supprimer le produit
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $image_name = $product['image'];
        $path = "../images/" . $image_name;

        // 2. Supprimer le fichier image du dossier si il existe
        if (file_exists($path) && !empty($image_name)) {
            unlink($path);
        }

        // 3. Supprimer le produit de la base de données
        $delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $delete->execute([$id]);
    }
}

// Redirection vers le dashboard avec un message
header("Location: index.php?msg=Produit supprimé");
exit();