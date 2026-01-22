<?php 
require_once './db.php'; // Assure-toi du chemin vers ton fichier de connexion

// --- LOGIQUE D'AJOUT ---
if (isset($_POST['add_category'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $filtre = htmlspecialchars($_POST['filtre_isotope']);
    $sous_titre = htmlspecialchars($_POST['sous_titre_banner']);
    $is_banner = isset($_POST['is_banner']) ? 1 : 0; // Vérifie si la case est cochée

    $image_banner = ""; // Valeur par défaut vide

    // Gestion de l'image de bannière si elle existe
    if (!empty($_FILES['image_banner']['name'])) {
        $upload_dir = "../images/";
        $image_banner = "banner_" . time() . "_" . $_FILES['image_banner']['name'];
        move_uploaded_file($_FILES['image_banner']['tmp_name'], $upload_dir . $image_banner);
    }

    if (!empty($nom)) {
        // Nouvelle requête avec les champs de bannière
        $sql = "INSERT INTO categories (nom, filtre_isotope, image_banner, sous_titre_banner, is_banner) 
                VALUES (:nom, :filtre, :img, :sous_titre, :is_banner)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'       => $nom,
            ':filtre'    => $filtre,
            ':img'       => $image_banner,
            ':sous_titre' => $sous_titre,
            ':is_banner' => $is_banner
        ]);

        header("Location: categories.php?success=1");
        exit();
    }
}

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM categories WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    header("Location: categories.php?deleted=1");
    exit();
}

// --- RÉCUPÉRATION DES CATÉGORIES ---
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Catégories - Admin</title>
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

        <div class="col-md-10 p-4">
            <h2>Gestion des Catégories</h2>
            
            <div class="card my-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ajouter une nouvelle catégorie</h5>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row bg-white p-3 shadow-sm rounded">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nom de la catégorie</label>
                                <input type="text" name="nom" class="form-control" placeholder="Ex: Femmes" required>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Filtre Isotope</label>
                                <input type="text" name="filtre_isotope" class="form-control" placeholder="Ex: .women">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Image de Bannière</label>
                                <input type="file" name="image_banner" class="form-control">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Sous-titre Bannière</label>
                                <input type="text" name="sous_titre_banner" class="form-control" placeholder="Ex: Été 2026">
                            </div>

                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_banner" value="1" id="isBanner">
                                    <label class="form-check-label" for="isBanner">
                                        Afficher comme bannière sur la page d'accueil
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" name="add_category" class="btn btn-primary">Enregistrer la catégorie</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <table class="table table-striped bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom de la catégorie</th>
                        <th>Filtre Isotope</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td><strong><?php echo $cat['nom']; ?></strong></td>
                        <td><code><?php echo $cat['filtre_isotope']; ?></code></td>
                        <td>
                            <a href="categories.php?delete=<?php echo $cat['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Attention : supprimer cette catégorie peut impacter les produits liés ! Continuer ?')">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($categories)): ?>
                        <tr><td colspan="4" class="text-center">Aucune catégorie pour le moment.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>