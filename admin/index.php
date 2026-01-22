<?php 
require_once '../admin/db.php'; 

// Récupérer tous les produits avec leur catégorie
$sql = "SELECT p.*, c.nom as cat_nom 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - CozaStore</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #222; color: white; padding: 20px; }
        .sidebar a { color: #ccc; text-decoration: none; display: block; padding: 10px 0; }
        .sidebar a:hover { color: white; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <h3>GlobeService-Market ADMIN</h3>
            <hr>
            <a href="index.php">Produits</a>
            <a href="categories.php">Catégories</a>
            <a href="manage_slider.php">manage slider</a>
            <hr>
            <a href="../index.php" target="_blank">Voir le site</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between mb-4">
                <h2>Gestion des Produits</h2>
                <a href="add_product.php" class="btn btn-primary">Ajouter un produit</a>
            </div>

            <table class="table table-striped bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td><img src="../images/<?php echo $p['image']; ?>" width="50"></td>
                        <td><?php echo $p['nom']; ?></td>
                        <td><?php echo $p['cat_nom']; ?></td>
                        <td><?php echo $p['prix']; ?> $</td>
                        <td>
                            <a href="edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <a href="delete.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>