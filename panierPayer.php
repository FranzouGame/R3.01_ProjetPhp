<?php session_start(); // Démarrer la session
?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <title>Paradis de l'aspi - payer</title>
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
        $erreur = ['numCarte' => '', 'csc' => '', 'dateDexpi' => ''];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $card_number = $_POST['numCarte'];
            $csc = $_POST['csc'];
            $expiry_date = $_POST['dateDexpi'];

            // Vérification du numéro de carte (16 chiffres)
            if (strlen($card_number) !== 16) {
                $erreur['numCarte'] = 'Le numéro de carte doit être composé de 16 chiffres !';
            } elseif (!ctype_digit($card_number)) {
                $erreur['numCarte'] = 'Le numéro de carte doit contenir que des chiffres !';
            }

            //Vérification du numéro csc (3 chiffres)
            if (strlen($csc) !== 3) {
                $erreur['csc'] = 'Le csc doit êtres composé de 3 chiffres !';
            } elseif (!ctype_digit($csc)) {
                $erreur['csc'] = 'Le csc doit contenir que des chiffres !';
            }

            $dateActuelle = new DateTime();
            $dateDexpiDeLaCarte = DateTime::createFromFormat('Y-m-d', $expiry_date); // Changement pour gérer le format date
            $dateValide = $dateActuelle->modify('+3 months');

            if ($dateDexpiDeLaCarte < $dateValide) {
                $erreur['dateDexpi'] = 'La date d\'expiration doit être supérieure à 3 mois à partir d\'aujourd\'hui.';
            }

            // Si aucune erreur, traiter le formulaire
            if (empty($erreur['numCarte']) && empty($erreur['csc']) && empty($erreur['dateDexpi'])) {
                if (isset($_SESSION['panier'])) {
                    unset($_SESSION['panier']); // Vider le panier
                }
                header("Refresh: 2; url=index.php"); // Redirection vers index au bout de 2 secondes
                echo "<div class='alert alert-success'>Le paiement a été validé avec succès.</div>";
            }
        }

        ?>


    </main>

    <div class="container mt-5">
        <h2 class="mb-4">Formulaire de Paiement par Carte</h2>
        <form action="" method="post">
            <!-- Numéro de carte -->
            <div class="form-group">
                <label>Numéro de Carte</label>
                <input type="text" name="numCarte" id="numCarte" class="form-control" placeholder="1234 5678 9123 4567" maxlength="16" required>
                <p class="text-danger"><?= $erreur['numCarte'] ?></p>
            </div>

            <!-- CSC -->
            <div class="form-group">
                <label>CSC</label>
                <input type="text" name="csc" id="csc" class="form-control" placeholder="123" maxlength="3" required>
                <p class="text-danger"><?= $erreur['csc'] ?></p>
            </div>

            <!-- Date d'expiration -->
            <div class="form-group">
                <label>Date d'Expiration</label>
                <input type="date" name="dateDexpi" id="dateDexpi" class="form-control" required>
                <p class="text-danger"><?= $erreur['dateDexpi'] ?></p>
            </div>

            <!-- Bouton de validation -->
            <button type="submit" class="btn btn-primary mt-5">Valider le Paiement</button>
        </form>
    </div>


</body>

</html>