<?php session_start(); // Démarrer la session
?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <title>Paradis de l'aspi</title>
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

    <div class="container mt-5">
        <h1>Articles</h1>

        <?php
        // Inclusion du fichier de connexion et de la creation de vignette
        include 'connexion.php';
        include 'createVignette.php';

        // Requête SQL pour récupérer les produits
        $sql = "SELECT idProd, libelle, prix, descriptif, image, quantiter FROM produit";
        $result = mysqli_query($link, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo '<main class="container"><div class="row justify-content-center">';

            // Boucle pour chaque produit
            while ($row = mysqli_fetch_assoc($result)) {
                // Génération de la carte pour chaque produit
                // Dimensions souhaitées pour la vignette
                $thumbWidth = 50;
                $thumbHeight = 50;

                // Chemin vers l'image source
                $srcImagePath = 'Images/' . $row["image"];
                // Chemin vers la vignette
                $thumbImagePath = 'thumbnails/' .  $row["image"];

                // Créer la vignette si elle n'existe pas déjà
                if (!file_exists($thumbImagePath)) {
                    createThumbnail($srcImagePath, $thumbImagePath, $thumbWidth, $thumbHeight);
                };

                // Afficher la vignette
                echo '
                <div class="col-md-3 mb-4 d-flex justify-content-center">
                    <div class="card clickable-card" data-bs-toggle="modal" data-bs-target="#modal' . $row["idProd"] . '" style="width: 18rem;">
                        <img src="' . $thumbImagePath . '" class="card-img-top" alt="' . $row["libelle"] . '">
                        <div class="card-body">
                            <h5 class="card-title">' . $row["libelle"] . '</h5>
                            <p class="card-text">' . substr($row["descriptif"], 0, 100) . '...</p>
                            <p class="card-text"> Prix : ' . $row["prix"] . ' € </p>';

                if ($row["quantiter"] > 0) {
                    echo '<p class="card-text"> Disponibles : ' . $row["quantiter"] . '</p>';
                    echo '
                    <form method="POST" action="panier.php">
                        <input type="hidden" name="idProd" value="' . $row["idProd"] . '">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                    </form>';
                } else {
                    // Si la quantité est 0, afficher "Hors stock" et désactiver le bouton
                    echo '<p class="card-text text-danger">Hors stock</p>';
                    echo '<button class="btn btn-secondary" disabled>Ajouter au panier</button>';
                }

                echo '
                        </div>
                    </div>
                </div>';

                // Code du modal
                echo '
                <div class="modal fade" id="modal' . $row["idProd"] . '" tabindex="-1" aria-labelledby="modalLabel' . $row["idProd"] . '" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel' . $row["idProd"] . '">' . $row["libelle"] . '</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="' . $srcImagePath . '" class="img-fluid mb-3" alt="' . $row["libelle"] . '">
                                <p>' . $row["descriptif"] . '</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>';
            }

            echo '</div></main>';
        } else {
            echo "Aucun produit trouvé.";
        }

        // Fermeture de la connexion
        mysqli_close($link);
        ?>


        <!-- Bootstrap 4.6.2 JS and dependencies (jQuery and Popper.js) -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
    </div>
</body>


</html>