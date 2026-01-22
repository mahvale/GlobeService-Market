<?php
// Inclusion de ta connexion PDO
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $code_hex = htmlspecialchars($_POST['code_hex']);

    if (!empty($nom) && !empty($code_hex)) {
        $sql = "INSERT INTO colors (nom, code_hex) VALUES (:nom, :hex)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nom' => $nom, ':hex' => $code_hex]);
        
        echo "<p style='color:green;'>Couleur ajoutée !</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit - Admin</title>
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
            <h3>GlobeService-Market</h3>
            <hr>
            <a href="index.php">Produits</a>
            <a href="categories.php">Catégories</a>
            <a href="manage_slider.php">slider</a>
            <a href="add-color.php">couleur</a>
            <hr>
            <a href="../index.php" target="_blank">Voir le site</a>
        </div>

        <div class="col-md-8 p-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Ajouter un nouveau produit</h4>
                </div>
         <form method="POST">
    <label>Nom de la couleur (ex: Bleu Marine) :</label><br>
    <input type="text" name="nom" required placeholder="Rouge"><br><br>

    <label>Code Hexadécimal :</label><br>
    <input type="color" name="code_hex" required style="height:40px; width:100px;"><br>
    <small>Clique sur le carré pour choisir la couleur</small><br><br>

    <button type="submit">Enregistrer la couleur</button>
</form>

<hr>
<h3>Couleurs existantes :</h3>
<ul>
    <?php
    $list = $pdo->query("SELECT * FROM colors")->fetchAll();
    foreach($list as $c) {
        echo "<li><span style='color:".$c['code_hex']."'>⬤</span> ".$c['nom']." (".$c['code_hex'].")</li>";
    }
    ?>
</ul>
            </div>
            <div class="mt-3">
                <a href="index.php" class="text-secondary">← Retour à la liste</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>