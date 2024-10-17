<?php session_start(); // Démarrer la session
?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <title>Paradis de l'aspi - panier</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Paradis de l'aspi</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Accueil</a>
                        </li>
                    </ul>
                    <a href="panier.php" class="btn btn-secondary">
                        Panier
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <?php

        // Inclusion du fichier de connexion
        include 'connexion.php';
        $total = 0; // Initialisation du total

        // Si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idProd = $_POST['idProd'];

            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'add':
                        $_SESSION['panier'][$idProd]['quantity']++;
                        break;
                    case 'remove':
                        if ($_SESSION['panier'][$idProd]['quantity'] > 1) {
                            $_SESSION['panier'][$idProd]['quantity']--;
                        } else {
                            unset($_SESSION['panier'][$idProd]);
                        }
                        break;
                    case 'delete':
                        unset($_SESSION['panier'][$idProd]);
                        break;
                }
            } else {
                // Requête SQL pour récupérer les informations du produit
                $sql = "SELECT * FROM produit WHERE idProd = $idProd";
                $result = mysqli_query($link, $sql);
                $product = mysqli_fetch_assoc($result);

                if ($product) {
                    // Initialiser le panier si nécessaire
                    if (!isset($_SESSION['panier'])) {
                        $_SESSION['panier'] = [];
                    }

                    // Chemin vers l'image du produit
                    $srcImagePath = $product["image"];

                    // Vérifier si le produit est déjà dans le panier
                    if (isset($_SESSION['panier'][$idProd])) {
                        // Si le produit existe, augmenter la quantité
                        $_SESSION['panier'][$idProd]['quantity']++;
                    } else {
                        $_SESSION['panier'][$idProd] = [
                            'libelle' => $product['libelle'],
                            'prix' => $product['prix'],
                            'image' => $srcImagePath,
                            'quantity' => 1
                        ];
                    }
                }
            }
        }

        // Affichage des produits du panier
        if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
            echo '<h1>Votre panier</h1>';
            foreach ($_SESSION['panier'] as $id => $product) {

                echo '<nav class="navbar navbar-expand-lg bg-light" data-bs-theme="light">';
                // Vérification de l'image avant affichage
                if (!empty($product['image'])) {
                    echo '<img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['libelle']) . '" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">';
                } else {
                    echo '<p>Image non disponible</p>';
                }
                $totalGlobal = $product['prix'] * $product['quantity']; // Sous-total pour chaque produi

                echo '<p>' . htmlspecialchars($product['libelle']) . ' - ' . htmlspecialchars($product['quantity']) . ' x ' . htmlspecialchars($product['prix']) . ' €</p>';

                // Formulaire pour ajouter une quantité
                echo '<form method="POST" action=""  class="ml-5">';
                echo '<input type="hidden" name="idProd" value="' . $id . '">';
                echo '<button type="submit" name="action" value="add" class="btn btn-success">+</button>';
                echo '</form>';

                // Formulaire pour retirer une quantité
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="idProd" value="' . $id . '">';
                echo '<button type="submit" name="action" value="remove" class="btn btn-warning">-</button>';
                echo '</form>';

                // Formulaire pour supprimer l'article
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="idProd" value="' . $id . '">';
                echo '<button type="submit" name="action" value="delete" class="btn btn-danger">Supprimer</button>';
                echo '</form>';
                echo '</nav>';
                echo '<SPACER>';
                $total += $totalGlobal;
            }
        } else {
            echo '<p>Votre panier est vide.</p>';
        }
        echo '<h2>Total du panier : ' . $total . ' €</h2>';
        ?>
    </main>
    <nav>
        <a href="index.php" class="btn btn-secondary">
            Continuer les achats
        </a>
        <a href="panierPayer.php" class="btn btn-secondary">
            Payer
        </a>

    </nav>
</body>

</html>