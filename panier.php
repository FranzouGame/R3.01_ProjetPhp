<?php
session_start(); // Démarrer la session 
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
                    <?php if (isset($_SESSION['username'])): ?>
                        <span class="navbar-text me-3">
                            Connecté en tant que <?= $_SESSION['username']; ?>
                        </span>
                        <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
                        <a href="backoffice.php" class="btn btn-warning ms-2">
                            Backoffice
                        </a>
                    <?php else: ?>
                        <a href="backoffice.php" class="btn btn-success ms-2">Se connecter</a>
                    <?php endif; ?>
                    <a href="panier.php" class="btn btn-secondary ms-2">
                        Panier
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container mt-5">
            <?php
            // Ajout du fichier de connexion
            include 'connexion.php';
            $total = 0; // Initialisation du total

            // Si le formulaire a été soumis
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $idProd = $_POST['idProd'];

                // Requête SQL pour récupérer les détails du produit
                $sql = "SELECT quantiter, libelle, prix, image FROM produit WHERE idProd = ?";
                $stmt = mysqli_prepare($link, $sql);
                mysqli_stmt_bind_param($stmt, 'i', $idProd);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($result && $product = mysqli_fetch_assoc($result)) {
                    $quantiterDispo = $product['quantiter'];

                    // Initialiser le panier s'il n'existe pas
                    if (!isset($_SESSION['panier'])) {
                        $_SESSION['panier'] = [];
                    }

                    // Vérifier l'action
                    if (isset($_POST['action'])) {
                        switch ($_POST['action']) {
                            case 'add':
                                // Vérifier si le produit est déjà dans le panier
                                if (isset($_SESSION['panier'][$idProd])) {
                                    // Vérification si la quantité maximale peut être atteinte
                                    if ($_SESSION['panier'][$idProd]['quantity'] < $quantiterDispo) {
                                        $_SESSION['panier'][$idProd]['quantity']++;
                                    } else {
                                        echo "<div class='alert alert-warning'>Quantité maximale atteinte pour ce produit !</div>";
                                    }
                                } else {
                                    // Ajouter le produit au panier
                                    $_SESSION['panier'][$idProd] = [
                                        'libelle' => $product['libelle'],
                                        'prix' => $product['prix'],
                                        'image' => $product['image'],
                                        'quantity' => 1
                                    ];
                                }
                                break;

                            case 'remove':
                                if (isset($_SESSION['panier'][$idProd]) && $_SESSION['panier'][$idProd]['quantity'] > 1) {
                                    $_SESSION['panier'][$idProd]['quantity']--;
                                } else {
                                    unset($_SESSION['panier'][$idProd]);
                                }
                                break;

                            case 'delete':
                                unset($_SESSION['panier'][$idProd]);
                                break;
                        }
                    }
                } else {
                    echo "<div class='alert alert-danger'>Le produit n'existe pas ou n'a pas pu être trouvé.</div>";
                }

                header('Location: panier.php');
                exit();
            }

            // Affichage des produits du panier
            if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
                echo '<h1>Votre panier</h1>';
                foreach ($_SESSION['panier'] as $id => $product) {
                    // Récupérer la quantité disponible pour chaque produit
                    $sql = "SELECT quantiter FROM produit WHERE idProd = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $quantiterProd = mysqli_fetch_assoc($result);
                    $quantiterDispo = $quantiterProd['quantiter'];

                    echo '<div class="d-flex justify-content-between align-items-center bg-light p-3 mb-3">';

                    // Vérification de l'image avant de l'afficher
                    if (!empty($product['image'])) {
                        $cheminImage = htmlspecialchars($product['image']);
                        $nomImage = basename($cheminImage);
                        $cheminVignette = "thumbnails/" . $nomImage;

                        if (file_exists($cheminVignette)) {
                            // Afficher la vignette
                            echo '<img src="' . $cheminVignette . '" alt="' . htmlspecialchars($product['libelle']) . '" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">';
                        } else {
                            // Si pas vignette alors afficher l'image d'origine
                            echo '<img src="' . $cheminImage . '" alt="' . htmlspecialchars($product['libelle']) . '" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">';
                        }
                    } else {
                        echo '<p>Image non disponible</p>';
                    }

                    echo '<div class="mx-3">';
                    echo '<h4>' . htmlspecialchars($product['libelle']) . ' - ' . htmlspecialchars($product['quantity']) . ' x ' . htmlspecialchars($product['prix']) . ' €</h4>';
                    echo '</div>';

                    echo '<div class="d-flex align-items-center">';

                    // Formulaire pour ajouter augmenter la quantité
                    echo '<form method="POST" action="" class="mx-2">';
                    echo '<input type="hidden" name="idProd" value="' . $id . '">';
                    echo '<button type="submit" name="action" value="add" class="btn ';

                    if ($product['quantity'] >= $quantiterDispo) {
                        echo 'btn-secondary" disabled';  // Désactive le bouton et le rend gris
                    } else {
                        echo 'btn-success"';  // Applique le style de succès donc le rend vert
                    }

                    echo '>+</button>';
                    echo '</form>';

                    // Formulaire pour retirer la quantité
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
            <nav>
                <a href="index.php" class="btn btn-secondary">Continuer les achats</a>
                <a href="panierPayer.php" class="btn btn-secondary <?php echo $total == 0 ? 'disabled' : ''; ?>">Payer</a>
            </nav>
        </div>
    </main>
</body>

</html>

<?php
// À la fin du script PHP
ob_end_flush(); // Envoyer tout le contenu tamponné
?>