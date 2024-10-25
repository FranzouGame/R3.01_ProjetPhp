<?php
session_start();
include 'connexion.php';

// Vérification de la méthode POST et de l'action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $libelle = $_POST['libelle'];
    $descriptif = $_POST['descriptif'];
    $prix = intval($_POST['prix']);
    $quantiter = intval($_POST['quantiter']);
    $imagePath = null;

    // Gestion de l'image uploadée
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'Images/';
        $fileName = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;

        // Validation du type de fichier
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $validExtensions)) {
            include("createVignette.php");

            // Dimensions pour la vignette
            $thumbWidth = 50;
            $thumbHeight = 50;
            $thumbImagePath = 'thumbnails/' . $fileName;

            // Déplacement de l'image vers le dossier de destination
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Création de la vignette
                createThumbnail($targetFile, $thumbImagePath, $thumbWidth, $thumbHeight);
                $imagePath = $fileName;
            } else {
                $_SESSION['error_message'] = "Erreur lors du téléchargement de l'image.";
                header('Location: ajouter_produit.php');
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Type de fichier non valide. Veuillez choisir une image JPG, JPEG, PNG ou GIF.";
            header('Location: ajouter_produit.php');
            exit();
        }
    }

    if ($action == 'modifier') {
        // Vérification de l'ID du produit à modifier
        if (!isset($_POST['idProd'])) {
            $_SESSION['error_message'] = "ID du produit manquant.";
            header('Location: ajouter_produit.php');
            exit();
        }

        $idProd = intval($_POST['idProd']);

        // Récupérer le chemin de l'ancienne image depuis la base de données
        $sqlOldImage = "SELECT image FROM produit WHERE idProd = ?";
        $stmtOld = mysqli_prepare($link, $sqlOldImage);
        mysqli_stmt_bind_param($stmtOld, 'i', $idProd);
        mysqli_stmt_execute($stmtOld);
        $resultOld = mysqli_stmt_get_result($stmtOld);
        $oldProduct = mysqli_fetch_assoc($resultOld);

        if (!$oldProduct) {
            $_SESSION['error_message'] = "Produit introuvable.";
            header('Location: ajouter_produit.php');
            exit();
        }

        $oldImagePath = 'Images/' . $oldProduct['image'] ?? null;

        // Conserver l'ancienne image si aucune nouvelle image n'est uploadée
        if (empty($imagePath)) {
            $imagePath = $oldProduct['image'];
        }

        // Mise à jour du produit dans la base de données
        $sqlUpdate = "UPDATE produit SET libelle = ?, descriptif = ?, prix = ?, quantiter = ?, image = ? WHERE idProd = ?";
        $stmt = mysqli_prepare($link, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'ssiiss', $libelle, $descriptif, $prix, $quantiter, $imagePath, $idProd);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: backoffice.php');
            exit();
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour : " . mysqli_error($link);
            header('Location: ajouter_produit.php');
            exit();
        }
    } elseif ($action == 'ajouter') {
        // Vérification si l'ID du produit existe déjà
        if (isset($_POST['idProd'])) {
            $idProd = intval($_POST['idProd']);
            $sqlCheckExistence = "SELECT COUNT(*) FROM produit WHERE idProd = ?";
            $stmtCheck = mysqli_prepare($link, $sqlCheckExistence);
            mysqli_stmt_bind_param($stmtCheck, 'i', $idProd);
            mysqli_stmt_execute($stmtCheck);
            mysqli_stmt_bind_result($stmtCheck, $count);
            mysqli_stmt_fetch($stmtCheck);
            mysqli_stmt_close($stmtCheck);

            if ($count > 0) {
                $_SESSION['error_message'] = "Erreur : un produit avec cet ID existe déjà.";
                header('Location: ajouter_produit.php');
                exit();
            }
        }

        // Insertion du produit dans la base de données
        $sqlInsert = "INSERT INTO produit (idProd, libelle, descriptif, prix, quantiter, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sqlInsert);
        mysqli_stmt_bind_param($stmt, 'issiis', $idProd, $libelle, $descriptif, $prix, $quantiter, $imagePath);

        // Exécuter la requête d'insertion
        if (mysqli_stmt_execute($stmt)) {
            header('Location: backoffice.php');
            exit();
        } else {
            $_SESSION['error_message'] = "Erreur lors de l'ajout : " . mysqli_error($link);
            header('Location: ajouter_produit.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Action non valide.";
        header('Location: ajouter_produit.php');
        exit();
    }
} else {
    header('Location: backoffice.php'); // Redirection si la méthode n'est pas POST ou si l'action n'est pas définie
    exit();
}
