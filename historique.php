<?php
// historique.php - Affiche les scores passés

$historique = [];
if (file_exists("scores.json")) {
    $contenu = file_get_contents("scores.json");
    $historique = json_decode($contenu, true) ?? [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Historique des scores - QuizMusic 🎵</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen text-white p-8">
  <h1 class="text-3xl font-bold mb-6">🏆 Historique des scores</h1>

  <?php if (empty($historique)): ?>
      <p>Aucun score enregistré pour l’instant.</p>
  <?php else: ?>
      <table class="w-full border border-purple-400 text-left bg-white text-gray-800 rounded-xl overflow-hidden">
          <thead class="bg-purple-600 text-white">
              <tr>
                  <th class="p-3">Joueur</th>
                  <th class="p-3">Thème</th>
                  <th class="p-3">Score</th>
                  <th class="p-3">Date</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($historique as $score): ?>
              <tr class="border-t">
                  <td class="p-3"><?php echo htmlspecialchars($score['joueur']); ?></td>
                  <td class="p-3"><?php echo htmlspecialchars($score['theme']); ?></td>
                  <td class="p-3"><?php echo $score['score'] . "/" . $score['total']; ?></td>
                  <td class="p-3"><?php echo $score['date']; ?></td>
              </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
  <?php endif; ?>
</body>
</html>