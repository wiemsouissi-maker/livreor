<?php
require_once '../db/database.php';
require '../layout/header.php';
if (empty($_SESSION['user_id'])) {
    header('Location: ../authentifiquation/connection.php');
    exit;
}


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentaire = trim($_POST['commentaire'] ?? '');
    if ($commentaire === '') $errors[] = 'Le commentaire ne peut pas être vide.';
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO commentaires (commentaire, id_utilisateur) VALUES (?, ?)');
        $stmt->execute([$commentaire, $_SESSION['user_id']]);
        header('Location: livre-or.php');
        exit;
    }
}
?>
<h2>Ajouter un commentaire</h2>
<?php
if (!empty($errors)) {
    echo '<div class="errors">';
    foreach ($errors as $error) {
        echo '<p>' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
}
?>

<form method="post">
    <div class="form-group">
        <label for="commentaire">Votre commentaire :</label>
        <textarea name="commentaire" id="commentaire" rows="5" required><?php echo htmlspecialchars($_POST['commentaire'] ?? ''); ?></textarea>
    </div>
    <button type="submit" class="btn">Publier le commentaire</button>
</form>

<p><a href="livre-or.php">Retour au livre d'or</a></p>

<?php require '../layout/footer.php'; ?>