<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <title>Back Office</title>
</head>
<body>
    <main>
            <h1>rorduits</h1>
            <table id="matable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Libelle</th>
                        <th>Description</th>
                        <th>Prix</th>

                    </tr>
                </thead>
                <tbody>



            <?php

            //Connexion à la base de données
            include 'connexion.php';

            //Construction de la requête
            $sql = "SELECT idProd, libelle, prix, descriptif, vignette FROM produit";
            $results = mysqli_query($link, $sql);

                    foreach ($results as $produit) {
                        ?>

                        <!--Les cartes-->
                        <tr>
                            <td><img src='<?= "image/" . $produit['vignette'] ?>' alt=""></td>
                            <td><?= $recette['categorie_nom'] ?></td>

                            <!-- <td><a href="recette.php?id_recette=<?=$produit['recette_id']?>"><?= $produit['recette_nom'] ?></a></td> -->
                        </tr>



            <?php } ?>
                </tbody>
            </table>
    </main>
</body>