<?php

// Récupération des identifiants de connexion dans le fichier json
$connexion = file_get_contents("connexion.json");
$connexionData = json_decode($connexion, true);


// Test des connexions possibles
/* Nécéssaire car nous travaillons sur localhost et sur 
    lakartxela en fonction de la base de donnée disponible */
foreach ($connexionData["hosts"] as $hosts) {
    // Récupération des données de connexion
    $host = $hosts[0];
    $bdd = $hosts[1];
    $usr = $hosts[2];
    $pwd = $hosts[3];

    // Connexion à la base de données
    $link = mysqli_connect($host, $usr, $pwd, $bdd);

    // Vérification de la connexion
    if ($link) {
        echo "Connexion réussie à la base de données $bdd sur $host.";
        break; // On sort de la boucle si la connexion est réussie
    } else {
        // Affichage d'une erreur pour le debug
        error_log("Échec de la connexion à $host: " . mysqli_connect_error());
        echo "Échec de la connexion à $host: " . mysqli_connect_error();
    }
}

if (!$link) {
    die("Connexion échouée : " . mysqli_connect_error());
}
?>