<?php 
require_once './db.php'; 

// --- LOGIQUE D'AJOUT ---
if (isset($_POST['add_slide'])) {
    $sous_titre = htmlspecialchars($_POST['sous_titre']);
    $titre_principal = htmlspecialchars($_POST['titre_principal']);
    $lien = htmlspecialchars($_POST['lien_bouton']);
    $ordre = intval($_POST['ordre']);

    // Gestion de l'image
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $upload_dir = "../images/";
    $unique_name = "slide_" . time() . "_" . $image_name;

    if (move_uploaded_file($image_tmp, $upload_dir . $unique_name)) {
        $sql = "INSERT INTO sliders (image, sous_titre, titre_principal, lien_bouton, ordre) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$unique_name, $sous_titre, $titre_principal, $lien, $ordre]);
        header("Location: manage_slider.php?success=1");
        exit();
    }
}

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Récupérer le nom de l'image pour la supprimer du dossier
    $stmt = $pdo->prepare("SELECT image FROM sliders WHERE id = ?");
    $stmt->execute([$id]);
    $s = $stmt->fetch();
    
    if ($s) {
        if (file_exists("../images/" . $s['image'])) {
            unlink("../images/" . $s['image']);
        }
        $pdo->prepare("DELETE FROM sliders WHERE id = ?")->execute([$id]);
    }
    header("Location: manage_slider.php?deleted=1");
    exit();
}

// --- RÉCUPÉRATION DES SLIDES ---
$sliders = $pdo->query("SELECT * FROM sliders ORDER BY ordre ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Slider - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #222; color: white; padding: 20px; }
        .sidebar a { color: #ccc; text-decoration: none; display: block; padding: 10px 0; }
        .sidebar a:hover { color: white; }
        .img-preview { width: 150px; height: 80px; object-fit: cover; border-radius: 5px; }
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

        <div class="col-md-10 p-4">
            <h2>Gestion du Slider d'accueil</h2>

            <div class="card my-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ajouter un nouveau Slide</h5>
                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Sous-titre (ex: Collection 2024)</label>
                            <input type="text" name="sous_titre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Titre Principal (ex: NOUVEAUTÉS)</label>
                            <input type="text" name="titre_principal" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Image (1920x930px)</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lien du bouton</label>
                            <input type="text" name="lien_bouton" class="form-control" value="product.php">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Ordre</label>
                            <input type="number" name="ordre" class="form-control" value="0">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="add_slide" class="btn btn-success w-100">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>

            <table class="table table-striped bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Textes</th>
                        <th>Ordre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($sliders as $slide): ?>
                    <tr>
                        <td><img src="../images/<?php echo $slide['image']; ?>" class="img-preview"></td>
                        <td>
                            <strong><?php echo $slide['sous_titre']; ?></strong><br>
                            <small><?php echo $slide['titre_principal']; ?></small>
                        </td>
                        <td><?php echo $slide['ordre']; ?></td>
                        <td>
                            <a href="manage_slider.php?delete=<?php echo $slide['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Supprimer ce slide ?')">Supprimer</a>
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