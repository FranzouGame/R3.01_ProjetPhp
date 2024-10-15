<?php
// Activer les exceptions pour les erreurs MySQLi
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);

// Récupération des identifiants de connexion dans le fichier json
$connexion = file_get_contents("connexion.json");
$connexionData = json_decode($connexion, true);

$link = false;  // Initialisation de la connexion

// Test des connexions possibles
/* Nécessaire car nous travaillons sur localhost et sur 
   lakartxela en fonction de la base de données disponible */
foreach ($connexionData["hosts"] as $hosts) {
    // Récupération des données de connexion
    $host = $hosts[0];
    $bdd = $hosts[1];
    $usr = $hosts[2];
    $pwd = $hosts[3];
    
    try {
        // Connexion à la base de données
        $link = mysqli_connect($host, $usr, $pwd, $bdd);

        // Vérification de la connexion
        if ($link) {
            // echo "Connexion réussie à la base de données $bdd sur $host.\n";
            break; // Sortir de la boucle si la connexion est réussie
        }
    } catch (mysqli_sql_exception $e) {
        // Affichage de l'erreur pour le debug sans stopper le programme
        // error_log("Échec de la connexion à $host : " . $e->getMessage() . "\n");
        // echo "Échec de la connexion à $host : " . $e->getMessage() . "\n";
    }
}

// Si aucune connexion n'a été établie
if (!$link) {
    die("Connexion échouée à toutes les bases de données.\n");
}
?>
