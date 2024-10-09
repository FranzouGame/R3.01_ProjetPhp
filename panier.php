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


</body>

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

            // Vérifier si le produit est déjà dans le panier
            if (isset($_SESSION['panier'][$idProd])) {
                // Si le produit existe, augmenter la quantité
                $_SESSION['panier'][$idProd]['quantity']++;
            } else {
                // Sinon, ajouter le produit avec la quantité 1
                $_SESSION['panier'][$idProd] = [
                    'libelle' => $product['libelle'],
                    'prix' => $product['prix'],
                    'quantity' => 1
                ];
            }
        }

        // Rediriger vers la page du panier
        header('Location: panier.php');
        exit();
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

</html>