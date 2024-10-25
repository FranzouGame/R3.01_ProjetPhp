<?php
session_start();
ob_start(); // Activer le tampon de sortie
?>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <title>Paradis de l'aspi - backoffice</title>
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
                        <a href="backoffice.php" class="btn btn-warning ms-2">Backoffice</a>
                    <?php else: ?>
                        <a href="backoffice.php" class="btn btn-success ms-2">Se connecter</a>
                    <?php endif; ?>
                    <a href="panier.php" class="btn btn-secondary ms-2">Panier</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mt-5">
        <?php
        // Si l'utilisateur est connecté
        if (isset($_SESSION['username'])) {
            echo "<h1>Bienvenue dans le backoffice, " . $_SESSION['username'] . "!</h1>";
            echo "<p>C'est ici que vous pourrez gérer les produits dans la base de données.</p>";

            // Inclusion du fichier de connexion
            include 'connexion.php';

            // Requête pour récupérer les produits
            $sql = "SELECT * FROM produit"; // Table contenant les produits
            $result = mysqli_query($link, $sql);

            if ($result->num_rows > 0) {
                // Affichage des produits dans un tableau
                echo "<table class='table table-striped'>
                <thead>
                    <tr>
                        <th style='min-width: 100px;'>Vignette</th>
                        <th style='min-width: 100px;'>ID Produit</th>
                        <th style='min-width: 80px;'>Libelle</th>
                        <th style='min-width: 200px;'>Descriptif</th>
                        <th style='min-width: 80px;'>Prix</th>
                        <th style='min-width: 80px;'>Quantite</th>
                        <th style='min-width: 250px;'>Actions</th>
                    </tr>
                </thead>
                <tbody>";


                while ($row = mysqli_fetch_assoc($result)) {
                    $nomImage = basename($row['image']);
                    $cheminVignette = "thumbnails/" . $nomImage;

                    echo '<tr>
                            <td><img src="' . $cheminVignette . '" alt="' . $row["libelle"] . '" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;"></td>
                            <td>' . $row["idProd"] . '</td>
                            <td>' . $row['libelle'] . '</td>
                            <td>' . $row['descriptif'] . '</td>
                            <td>' . $row['prix'] . ' €</td>
                            <td>' . $row['quantiter'] . '</td>
                            <td>
                                <form action="gestion_produit.php" method="post" class="d-inline">
                                    <input type="hidden" name="idProd" value="' . $row['idProd'] . '">
                                    <button type="submit" name="action" value="plus" class="btn btn-success btn-sm">+</button>
                                    <button type="submit" name="action" value="moins" class="btn btn-warning btn-sm" ' . ($row['quantiter'] <= 0 ? 'disabled' : '') . '> - </button>
                                    <button type="submit" name="action" value="modifier" class="btn btn-primary btn-sm">Modifier</button>
                                    <button type="submit" name="action" value="supprimer" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>';
                }

                echo "</tbody>
                    </table>";
            } else {
                echo "<p>Aucun produit disponible dans la base de données.</p>";
            }
        } else {
            // Si l'utilisateur n'est pas connecté, afficher le formulaire de connexion
            echo '<h1>Connexion au Backoffice</h1>';

            // Si le formulaire est soumis
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $username = $_POST['username'];
                $password = $_POST['password'];

                // Charger le fichier JSON contenant les identifiants
                $jsonData = file_get_contents('authentification.json');
                $userData = json_decode($jsonData, true);

                // Vérifier si les informations sont correctes
                foreach ($userData['users'] as $user) {
                    if ($user['username'] == $username && $user['password'] == $password) {
                        $_SESSION['username'] = $username;  // Stocker le nom d'utilisateur dans la session
                        header('Location: backoffice.php'); // Rediriger vers le backoffice
                        exit();
                    }
                }
                $error = "Nom d'utilisateur ou mot de passe incorrect";
            }

            // Affichage du formulaire de connexion
            if (isset($error)) {
                echo '<div class="alert alert-danger">' . $error . '</div>';
            }

            echo '<form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d\'utilisateur</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Connexion</button>
            </form>';
        }
        ?>
    </div>
</body>

</html>

<?php
ob_end_flush(); // Envoyer tout le contenu tamponné
?>