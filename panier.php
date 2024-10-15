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
        session_start();

        // Inclusion du fichier de connexion
        include 'connexion.php';

        // Si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idProd = $_POST['idProd'];

            // Requête SQL pour récupérer les informations du produit
            $sql = "SELECT * FROM produit WHERE idProd = $idProd";
            $result = mysqli_query($link, $sql);
            $product = mysqli_fetch_assoc($result);

            if ($product) {
                // Initialiser le panier si nécessaire
                if (!isset($_SESSION['panier'])) {
                    $_SESSION['panier'] = [];
                }

                // Chemin vers la vignette du produit
                $thumbImagePath = 'thumbnails/' . basename($product['image']);

                // Vérifier si le produit est déjà dans le panier
                if (isset($_SESSION['panier'][$idProd])) {
                    // Si le produit existe, augmenter la quantité
                    $_SESSION['panier'][$idProd]['quantity']++;
                } else {
                    // Sinon, ajouter le produit avec la quantité 1
                    $_SESSION['panier'][$idProd] = [
                        'libelle' => $product['libelle'],
                        'prix' => $product['prix'],
                        'image' => $thumbImagePath, // Ajouter le chemin de la vignette
                        'quantity' => 1
                    ];
                }
            }
        }

        // Affichage des produits du panier
        if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
            echo '<h1>Votre panier</h1>';
            foreach ($_SESSION['panier'] as $id => $product) {
                echo '<p>' . $product['libelle'] . ' - ' . $product['quantity'] . ' x ' . $product['prix'] . ' €</p>';
            }
        } else {
            echo '<p>Votre panier est vide.</p>';
        }

        ?>
    </main>


    <section class="h-100 gradient-custom">
        <div class="container py-5">
            <div class="row d-flex justify-content-center my-4">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header py-3">
                            <h5 class="mb-0">Cart - <?= count($_SESSION['panier']); ?> items</h5>
                        </div>
                        <div class="card-body">
                            <!-- Parcourir chaque produit dans le panier -->
                            <?php foreach ($_SESSION['panier'] as $idProd => $product): ?>
                                <div class="row">
                                    <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
                                        <!-- Image du produit -->
                                        <div class="bg-image hover-overlay hover-zoom ripple rounded" data-mdb-ripple-color="light">
                                            <img src="<?= htmlspecialchars($product['image']); ?>" class="w-100" alt="<?= htmlspecialchars($product['libelle']); ?>" />
                                            <a href="#!">
                                                <div class="mask" style="background-color: rgba(251, 251, 251, 0.2)"></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                                        <p><strong><?= htmlspecialchars($product['libelle']); ?></strong></p>
                                        <p>Prix: <?= htmlspecialchars($product['prix']); ?> €</p>
                                        <p>Quantité: <?= htmlspecialchars($product['quantity']); ?></p>
                                    </div>
                                </div>
                                <hr class="my-4" />
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



</body>

</html>