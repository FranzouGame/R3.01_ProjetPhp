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
        // Supprimer un produit de la base
        mysqli_query($link, "DELETE FROM produit WHERE idProd = $idProd");
    }

    // Rediriger vers le backoffice après action
    header('Location: backoffice.php');
    exit();
}
