<?php session_start(); // Démarrer la session 
ob_start(); // Activer le tampon de sortie
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
                    <a href="panier.php" class="btn btn-secondary">Panier</a>
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
                // Requête SQL pour récupérer la quantité disponible
                $sql = "SELECT quantiter FROM produit WHERE idProd = $idProd";
                $result = mysqli_query($link, $sql);
                $quantiterProd = mysqli_fetch_assoc($result);
                $quantiterDispo = $quantiterProd['quantiter'];

                switch ($_POST['action']) {
                    case 'add':
                        // Vérification du stock avant d'ajouter
                        if (isset($_SESSION['panier'][$idProd]) && $_SESSION['panier'][$idProd]['quantity'] < $quantiterDispo) {
                            $_SESSION['panier'][$idProd]['quantity']++;
                        }
                        // Requête SQL pour récupérer la quantité disponible du produit
                        $sql = "SELECT quantiter FROM produit WHERE idProd = ?";
                        $stmt = mysqli_prepare($link, $sql);
                        mysqli_stmt_bind_param($stmt, 'i', $idProd);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        // Vérification si le produit existe dans la base de données
                        if ($result && $product = mysqli_fetch_assoc($result)) {
                            $quantiteDispo = $product['quantiter'];

                            // Vérification si le produit est dans le panier et si on peut encore ajouter une unité
                            if (isset($_SESSION['panier'][$idProd])) {
                                if ($_SESSION['panier'][$idProd]['quantity'] == $quantiteDispo) {
                                    echo "<div class='alert alert-warning'>Quantité maximale atteinte pour ce produit !</div>";
                                } else {
                                    $_SESSION['panier'][$idProd]['quantity']++;
                                }
                            } else {
                                // Si le produit n'est pas encore dans le panier, l'ajouter
                                $_SESSION['panier'][$idProd]['quantity'] = 1;
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Le produit n'existe pas ou n'a pas pu être trouvé.</div>";
                        }
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
                    // Récupérer la quantité disponible
                    $quantiterDispo = $product['quantiter'];

                    // Initialiser le panier
                    if (!isset($_SESSION['panier'])) {
                        $_SESSION['panier'] = [];
                    }

                    // Vérifier si le produit est déjà dans le panier
                    if (isset($_SESSION['panier'][$idProd])) {
                        // Si la quantité actuelle dans le panier est inférieure à la quantité disponible
                        if ($_SESSION['panier'][$idProd]['quantity'] < $quantiterDispo) {
                            $_SESSION['panier'][$idProd]['quantity']++;
                        }
                    } else {
                        $_SESSION['panier'][$idProd] = [
                            'libelle' => $product['libelle'],
                            'prix' => $product['prix'],
                            'image' => $product["image"],
                            'quantity' => 1
                        ];
                    }
                }
            }
            header('Location: panier.php');
            exit();
        }

        // Affichage des produits du panier
        if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
            echo '<h1>Votre panier</h1>';
            foreach ($_SESSION['panier'] as $id => $product) {

                // Récupérer la quantité disponible pour chaque produit
                $sql = "SELECT quantiter FROM produit WHERE idProd = $id";
                $result = mysqli_query($link, $sql);
                $quantiterProd = mysqli_fetch_assoc($result);
                $quantiterDispo = $quantiterProd['quantiter'];

                echo '<div class="d-flex justify-content-between align-items-center bg-light p-3 mb-3">';

                // Vérification de l'image avant affichage
                if (!empty($product['image'])) {
                    $cheminImage = htmlspecialchars($product['image']);
                    $nomImage = basename($cheminImage);
                    $cheminVignette = "thumbnails/" . $nomImage;

                    if (file_exists($cheminVignette)) {
                        // Afficher la vignette
                        echo '<img src="' . $cheminVignette . '" alt="' . htmlspecialchars($product['libelle']) . '" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">';
                    } else {
                        // Si la vignette n'existe pas, afficher l'image d'origine
                        echo '<img src="' . $cheminImage . '" alt="' . htmlspecialchars($product['libelle']) . '" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">';
                    }
                } else {
                    echo '<p>Image non disponible</p>';
                }

                // Informations sur le produit et la quantité
                echo '<div class="mx-3">';
                echo '<h4>' . htmlspecialchars($product['libelle']) . ' - ' . htmlspecialchars($product['quantity']) . ' x ' . htmlspecialchars($product['prix']) . ' €</h4>';
                echo '</div>';

                echo '<div class="d-flex align-items-center">';

                // Formulaire pour ajouter une quantité
                echo '<form method="POST" action="" class="mx-2">';
                echo '<input type="hidden" name="idProd" value="' . $id . '">';
                echo '<button type="submit" name="action" value="add" class="btn ';

                if ($product['quantity'] >= $quantiterDispo) {
                    echo 'btn-secondary" disabled';  // Désactive le bouton et le rend gris
                } else {
                    echo 'btn-success"';  // Applique le style de succès vert
                }

                echo '>+</button>';
                echo '</form>';

                // Formulaire pour retirer une quantité
                echo '<form method="POST" action="" class="mx-2">';
                echo '<input type="hidden" name="idProd" value="' . $id . '">';
                echo '<button type="submit" name="action" value="remove" class="btn btn-warning">-</button>';
                echo '</form>';

                // Formulaire pour supprimer l'article
                echo '<form method="POST" action="" class="ms-auto">';
                echo '<input type="hidden" name="idProd" value="' . $id . '">';
                echo '<button type="submit" name="action" value="delete" class="btn btn-danger">Supprimer</button>';
                echo '</form>';

                echo '</div>';
                echo '</div>';
                $total += $product['prix'] * $product['quantity'];
            }
        } else {
            echo '<p>Votre panier est vide.</p>';
        }
        echo '<h2>Total du panier : ' . $total . ' €</h2>';
        ?>
    </main>

    <nav>
        <a href="index.php" class="btn btn-secondary">Continuer les achats</a>
        <a href="panierPayer.php" class="btn btn-secondary">Payer</a>
    </nav>
</body>

</html>

<?php
// À la fin du script PHP
ob_end_flush(); // Envoyer tout le contenu tamponné
?>