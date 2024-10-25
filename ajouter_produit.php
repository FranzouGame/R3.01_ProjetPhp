<?php
session_start();
include 'connexion.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <title>Ajouter un Produit</title>
</head>

<body>
    <div class="container mt-5">
        <h1>Ajouter un produit</h1>

        <?php
        // Affichage des messages d'erreur
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Effacer le message après l'affichage
        }
        ?>

        <form method="POST" action="maj_produit.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="ajouter">

            <div class="mb-3">
                <label for="idProd" class="form-label">ID Produit</label>
                <input type="text" class="form-control" id="idProd" name="idProd" placeholder="ID Produit" required>
            </div>
            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé</label>
                <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Entrez le libellé du produit" required>
            </div>
            <div class="mb-3">
                <label for="descriptif" class="form-label">Descriptif</label>
                <textarea class="form-control" id="descriptif" name="descriptif" placeholder="Entrez une description du produit" required></textarea>
            </div>
            <div class="mb-3">
                <label for="prix" class="form-label">Prix (€)</label>
                <input type="number" class="form-control" id="prix" name="prix" placeholder="Entrez le prix du produit" min="0" required>
            </div>
            <div class="mb-3">
                <label for="quantiter" class="form-label">Quantité</label>
                <input type="number" class="form-control" id="quantiter" name="quantiter" placeholder="Entrez la quantité du produit" min="0" required>
            </div>

            <!-- Champ de téléchargement d'image -->
            <div class="mb-3">
                <label for="image" class="form-label">Image du produit (facultatif)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Ajouter le produit</button>
            <a href="backoffice.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>