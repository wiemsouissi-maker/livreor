<?php
session_start();

// Inclusion du fichier de connexion à la BDD
require_once __DIR__ . '/../db/database.php';

// Inclusion de l'entête
require_once __DIR__ . '/../layout/header.php';

// Vérification que l'utilisateur est connecté
if (empty($_SESSION['user_id'])) {
    header('Location: ../authentifiquation/connection.php');
    exit;
}

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_login = trim($_POST['login'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $new_password_confirm = $_POST['password_confirm'] ?? '';

    if ($new_login === '') $errors[] = 'Le login est requis.';
    if ($new_password !== '' && $new_password !== $new_password_confirm) $errors[] = 'Les mots de passe ne correspondent pas.';

    if (empty($errors)) {
        // Vérifier si login déjà pris par un autre
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE login = ? AND id != ?');
        $stmt->execute([$new_login, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $errors[] = 'Ce login est déjà utilisé.';
        } else {
            if ($new_password === '') {
                $stmt = $pdo->prepare('UPDATE utilisateurs SET login = ? WHERE id = ?');
                $stmt->execute([$new_login, $_SESSION['user_id']]);
            } else {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE utilisateurs SET login = ?, password = ? WHERE id = ?');
                $stmt->execute([$new_login, $hash, $_SESSION['user_id']]);
            }
            $_SESSION['login'] = $new_login;
            $success = 'Profil mis à jour.';
        }
    }
}

// Récupérer info actuelle
$stmt = $pdo->prepare('SELECT login FROM utilisateurs WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<h2>Mon profil</h2>

<?php if ($errors): ?>
    <div class="errors">
        <ul><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="post" class="form">
    <label>Login<br><input type="text" name="login" value="<?php echo htmlspecialchars($user['login']); ?>" required></label>
    <p>Si tu veux changer ton mot de passe, saisis-en un nouveau. Sinon laisse vide.</p>
    <label>Nouveau mot de passe<br><input type="password" name="password"></label>
    <label>Confirmer le nouveau mot de passe<br><input type="password" name="password_confirm"></label>
    <button type="submit">Mettre à jour</button>
</form>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>