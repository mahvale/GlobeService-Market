<?php 
require_once './db.php'; 

// 1. Récupérer les catégories pour la liste déroulante
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();

// 2. Logique de traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $category_id = $_POST['category_id'];
    $prix = $_POST['prix'];
    $description = htmlspecialchars($_POST['description']);
    $taille = isset($_POST['taille']) ? implode(', ', $_POST['taille']) : '';
    
    // NOUVEAU : Récupération de l'ID de la couleur au lieu du texte
    $color_id = !empty($_POST['color_id']) ? $_POST['color_id'] : null;
    
    $is_new = isset($_POST['is_new']) ? 1 : 0;

    // Gestion de l'image
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $upload_dir = "../images/"; 
    
    $unique_image_name = time() . '_' . $image_name;

    if (move_uploaded_file($image_tmp, $upload_dir . $unique_image_name)) {
        // Insertion en base de données avec color_id
        $sql = "INSERT INTO products (nom, category_id, color_id, prix, description, image, taille, is_new) 
                VALUES (:nom, :cat, :color, :prix, :descr, :img, :taille, :is_new)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'    => $nom,
            ':cat'    => $category_id,
            ':color'  => $color_id, // L'ID de la table colors
            ':prix'   => $prix,
            ':descr'  => $description,
            ':img'    => $unique_image_name,
            ':taille' => $taille,
            ':is_new' => $is_new
        ]);

        header("Location: index.php?msg=Produit ajouté avec succès");
        exit();
    } else {
        $error = "Erreur lors du téléchargement de l'image.";
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
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du produit</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Catégorie</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Choisir...</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['nom']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prix ($)</label>
                                <input type="number" step="0.01" name="prix" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tailles disponibles</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="taille[]" value="S"> S
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="taille[]" value="M"> M
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="taille[]" value="L"> L
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="taille[]" value="XL"> XL
                                </div>
                            </div>

                        <div class="col-md-6 mb-3">
                                <label class="form-label">Couleur dominante</label>
                                <?php $all_colors = $pdo->query("SELECT * FROM colors ORDER BY nom ASC")->fetchAll(); ?>
                                <select name="color_id" class="form-select">
                                    <option value="">Aucune couleur</option>
                                    <?php foreach($all_colors as $c): ?>
                                        <option value="<?php echo $c['id']; ?>">
                                            <?php echo htmlspecialchars($c['nom']); ?> (<?php echo $c['code_hex']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_new" class="form-check-input" id="new">
                            <label class="form-check-label" for="new">Marquer comme "Nouveau"</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Enregistrer le produit</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-3">
                <a href="index.php" class="text-secondary">← Retour à la liste</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>