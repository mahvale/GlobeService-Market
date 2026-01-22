<?php 
require_once './db.php'; 

// 1. Vérifier si l'ID est présent
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// 2. Récupérer les catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();

// 3. Récupérer les infos actuelles du produit
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Produit introuvable.");
}

// 4. Logique de mise à jour
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $category_id = $_POST['category_id'];
    $prix = $_POST['prix'];
    $description = htmlspecialchars($_POST['description']);
    $taille = isset($_POST['taille']) ? implode(', ', $_POST['taille']) : '';
    $couleur = htmlspecialchars($_POST['couleur']);
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    
    $image_name = $product['image']; // Par défaut, on garde l'ancienne image

    // Si une nouvelle image est téléchargée
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = "../images/";
        $image_name = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        
        // Optionnel : supprimer l'ancienne image du serveur pour gagner de la place
        if (file_exists($upload_dir . $product['image'])) {
            unlink($upload_dir . $product['image']);
        }
    }

    $sql = "UPDATE products SET 
            nom = :nom, category_id = :cat, prix = :prix, 
            description = :descr, image = :img, taille = :taille, 
            couleur = :couleur, is_new = :is_new 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'     => $nom,
        ':cat'     => $category_id,
        ':prix'    => $prix,
        ':descr'   => $description,
        ':img'     => $image_name,
        ':taille'  => $taille,
        ':couleur' => $couleur,
        ':is_new'  => $is_new,
        ':id'      => $id
    ]);

    header("Location: index.php?msg=Produit mis à jour");
    exit();
}

// On transforme la chaîne "S, M, L" en tableau pour cocher les cases facilement
$current_tailles = explode(', ', $product['taille']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Produit - Admin</title>
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
            <h3>COZA ADMIN</h3>
            <hr>
            <a href="index.php">Produits</a>
            <a href="categories.php">Catégories</a>
            <hr>
            <a href="../index.php" target="_blank">Voir le site</a>
        </div>

        <div class="col-md-8 p-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Modifier le produit : <?php echo $product['nom']; ?></h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du produit</label>
                                <input type="text" name="nom" class="form-control" value="<?php echo $product['nom']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Catégorie</label>
                                <select name="category_id" class="form-select" required>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                            <?php echo $cat['nom']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prix ($)</label>
                                <input type="number" step="0.01" name="prix" class="form-control" value="<?php echo $product['prix']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Changer l'image (laisser vide pour garder l'actuelle)</label>
                                <input type="file" name="image" class="form-control">
                                <div class="mt-2 text-muted">Image actuelle : <strong><?php echo $product['image']; ?></strong></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo $product['description']; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tailles disponibles</label><br>
                                <?php 
                                $tailles_possibles = ['S', 'M', 'L', 'XL'];
                                foreach($tailles_possibles as $t): 
                                ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="taille[]" value="<?php echo $t; ?>" 
                                        <?php echo in_array($t, $current_tailles) ? 'checked' : ''; ?>> <?php echo $t; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Couleurs</label>
                                <input type="text" name="couleur" class="form-control" value="<?php echo $product['couleur']; ?>">
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_new" class="form-check-input" id="new" <?php echo ($product['is_new']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="new">Produit "Nouveau"</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>