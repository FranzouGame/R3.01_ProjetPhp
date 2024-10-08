<?php

// Récupération des identifiants de connexion dans le fichier json
$connexion = file_get_contents("connexion.json");
$connexionData = json_decode($connexion, true);


// Test des connexions possibles
    /* Nécéssaire car nous travaillons sur localhost et sur 
    lakartxela en fonction de la base de donnée disponible */
foreach ($connexionData["hosts"] as $hosts)
{
    // Récupération des données de connexion
    $host = $hosts;
    $bdd = $hosts[0];
    $usr = $hosts[1];
    $pwd = $hosts[2];

    // Test de la connexion : 
    $link = mysqli_connect($host,$user,$pass,$bdd);
    if ($link != "")
    {   
        break;
    }
}    

?>