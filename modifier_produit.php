<?php
session_start();
include 'connexion.php';

// Vérification si un ID de produit est fourni
if (!isset($_POST['idProd'])) {
    header('Location: backoffice.php'); // Redirection si l'ID n'est pas spécifié
    exit();
}

$idProd = $_POST['idProd'];

// Récupération des données actuelles du produit
$sql = 'SELECT * FROM produit WHERE idProd = ?';
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, 'i', $idProd);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: backoffice.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <title>Modifier Produit</title>
</head>

<body>
    <div class="container mt-5">
        <h1>Modifier le produit</h1>
        <form method="POST" action="maj_produit.php" enctype="multipart/form-data">
            <input type="hidden" name="idProd" value="<?= htmlspecialchars($_POST['idProd']) ?>">
            <input type="hidden" name="action" value="modifier">

            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé</label>
                <input type="text" class="form-control" id="libelle" name="libelle" value="<?= htmlspecialchars($product['libelle']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="descriptif" class="form-label">Descriptif</label>
                <textarea class="form-control" id="descriptif" name="descriptif" required><?= htmlspecialchars($product['descriptif']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="prix" class="form-label">Prix (€)</label>
                <input type="number" class="form-control" id="prix" name="prix" value="<?= htmlspecialchars($product['prix']) ?>" min="0" required>
            </div>
            <div class="mb-3">
                <label for="quantiter" class="form-label">Quantité</label>
                <input type="number" class="form-control" id="quantiter" name="quantiter" value="<?= htmlspecialchars($product['quantiter']) ?>" min="0" required>
            </div>

            <!-- Champ de téléchargement d'image -->
            <div class="mb-3">
                <label for="image" class="form-label">Image du produit (facultatif)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="backoffice.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>