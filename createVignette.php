<?php
function createThumbnail($src, $dest, $width, $height)
{
    // Vérifier si l'image source existe
    if (!file_exists($src)) {
        return false; // Image source n'existe pas
    }

    // Charger l'image source
    $sourceImage = imagecreatefromjpeg($src);
    if (!$sourceImage) {
        return false; // Échec du chargement de l'image
    }

    // Obtenir les dimensions de l'image source
    list($srcWidth, $srcHeight) = getimagesize($src);

    // Créer une nouvelle image vide pour la vignette
    $thumbnail = imagecreatetruecolor($width, $height);

    // Redimensionner l'image source et copier dans la vignette
    imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);

    // Enregistrer la vignette
    imagejpeg($thumbnail, $dest);

    // Libérer la mémoire
    imagedestroy($sourceImage);
    imagedestroy($thumbnail);

    return true; // Vignette créée avec succès
}
