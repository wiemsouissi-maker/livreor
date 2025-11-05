<?php
require_once '../db/database.php';
require '../layout/header.php';
?>
<h2>Livre d'or</h2>
<?php
// récupérer commentaires, du plus récent au plus ancien
$stmt = $pdo->query('SELECT c.commentaire, c.date, u.login FROM commentaires c JOIN utilisateurs u ON c.id_utilisateur = u.id ORDER BY c.date DESC');
$comments = $stmt->fetchAll();


if (!empty($comments)) {
    foreach ($comments as $c) {
        $dt = new DateTime($c['date']);
        $dateStr = $dt->format('d/m/Y');
        echo '<article class="comment">';
        echo '<p class="meta">Posté le ' . htmlspecialchars($dateStr) . ' par <strong>' . htmlspecialchars($c['login']) . '</strong></p>';
        echo '<div class="body">' . nl2br(htmlspecialchars($c['commentaire'])) . '</div>';
        echo '</article>';
    }
} else {
    echo '<p>Aucun commentaire pour le moment.</p>';
}


if (!empty($_SESSION['user_id'])) {
    echo '<p><a class="btn" href="commantaire.php">Ajouter un commentaire</a></p>';
} else {
    echo '<p>Tu dois <a href="../authentifiquation/connection.php">te connecter</a> pour poster un commentaire.</p>';
}
?>
<?php require '../layout/footer.php'; ?>