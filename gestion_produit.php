<?php
include 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idProd = $_POST['idProd'];
    $action = $_POST['action'];

    if ($action == 'plus') {
        // Ajouter la quantité d'un produit
        mysqli_query($link, "UPDATE produit SET quantiter = quantiter + 1 WHERE idProd = $idProd");
    } elseif ($action == 'moins') {
        // Diminuer la quantité d'un produit
        mysqli_query($link, "UPDATE produit SET quantiter = GREATEST(quantiter - 1, 0) WHERE idProd = $idProd");
    } elseif ($action == 'modifier') {
        // Rediriger vers une page de modification spécifique (à créer)
        header("Location: modifier_produit.php?id=$idProd");
        exit();
    } elseif ($action == 'supprimer') {
        // Récupère le chemin de l'image avant la suppression
        $result = mysqli_query($link, "SELECT image FROM produit WHERE idProd = $idProd");
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $cheminImage = $row['image'];
            $nomImage = basename($cheminImage);
            $cheminOriginal = "images/" . $nomImage;
            $cheminVignette = "thumbnails/" . $nomImage;

            // Supprime les fichiers d'image s'ils existent
            if (file_exists($cheminOriginal)) {
                unlink($cheminOriginal);
            }
            if (file_exists($cheminVignette)) {
                unlink($cheminVignette);
            }

            // Supprimer le produit de la base de données
            mysqli_query($link, "DELETE FROM produit WHERE idProd = $idProd");
        }
    }

    // Redirige vers le backoffice après action
    header('Location: backoffice.php');
    exit();
} else {
    // Redirige vers le backoffice de toute façon
    header('Location: backoffice.php');
}
