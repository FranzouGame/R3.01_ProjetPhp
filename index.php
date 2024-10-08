<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <title>Document</title>
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
                            <a class="nav-link active" aria-current="page" href="#">Accueil</a>
                        </li>
                    </ul>
                    <a href="pannier.php" class="btn btn-secondary">
                        Pannier
                    </a>

                </div>
            </div>
        </nav>
    </header>

    <h1 class="container mt-5">Articles : </h1>
    <?php
    // Inclusion du fichier de connexion
    include 'connexion.php';

    // Requête SQL pour récupérer les produits
    $sql = "SELECT idProd, libelle, descriptif, image, vignette FROM produit";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<main class="container"><div class="row justify-content-center">';

        // Boucle pour chaque produit
        while ($row = mysqli_fetch_assoc($result)) {
            // Génération de la carte pour chaque produit
            echo '
        <div class="col-md-3 mb-4 d-flex justify-content-center">
            <div class="card clickable-card" data-bs-toggle="modal" data-bs-target="#modal' . $row["idProd"] . '" style="width: 18rem;">
                <img src="' . $row["vignette"] . '" class="card-img-top" alt="' . $row["libelle"] . '">
                <div class="card-body">
                    <h5 class="card-title">' . $row["libelle"] . '</h5>
                    <p class="card-text">' . substr($row["descriptif"], 0, 100) . '...</p>
                </div>
            </div>
        </div>

        <!-- Modale pour le produit ' . $row["idProd"] . ' -->
        <div class="modal fade" id="modal' . $row["idProd"] . '" tabindex="-1" aria-labelledby="modalLabel' . $row["idProd"] . '" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel' . $row["idProd"] . '">' . $row["libelle"] . '</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="' . $row["image"] . '" class="img-fluid mb-3" alt="' . $row["libelle"] . '">
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

</body>


</html>