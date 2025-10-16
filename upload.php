<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pochette'])) {
    $fichier = $_FILES['pochette'];

    // Vérifier erreurs
    if ($fichier['error'] === 0) {
        $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
        $extensionsOK = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extension, $extensionsOK)) {
            if ($fichier['size'] <= 2 * 1024 * 1024) { // max 2 Mo
                $dossier = "uploads/pochettes/";
                if (!is_dir($dossier)) {
                    mkdir($dossier, 0755, true);
                }

                $nouveauNom = "pochette_" . time() . "." . $extension;
                $chemin = $dossier . $nouveauNom;

                if (move_uploaded_file($fichier['tmp_name'], $chemin)) {
                    echo "✅ Pochette uploadée avec succès : <br>";
                    echo "<img src='$chemin' width='200'>";
                } else {
                    echo "❌ Erreur lors du déplacement du fichier.";
                }
            } else {
                echo "⚠️ Fichier trop volumineux (max 2 Mo).";
            }
        } else {
            echo "⚠️ Extension non autorisée ($extension).";
        }
    } else {
        echo "❌ Erreur lors de l’upload.";
    }
}
?>
