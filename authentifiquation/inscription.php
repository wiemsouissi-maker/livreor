<?php
require_once '../db/database.php';
require '../layout/header.php';


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';


    if ($login === '') $errors[] = 'Le login est requis.';
    if ($password === '') $errors[] = 'Le mot de passe est requis.';
    if ($password !== $password_confirm) $errors[] = 'Les mots de passe ne correspondent pas.';


    if (empty($errors)) {
        // vérifier si login existe
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE login = ?');
        $stmt->execute([$login]);
        if ($stmt->fetch()) {
            $errors[] = 'Ce login est déjà utilisé.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (login, password) VALUES (?, ?)');
            $stmt->execute([$login, $hash]);
            header('Location: connection.php');
            exit;
        }
    }
}
?>
<h2>Inscription</h2>
<?php if ($errors): ?>
    <div class="errors">
        <ul>
            <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<form method="post" class="form">
    <label>Login<br><input type="text" name="login" value="<?php echo htmlspecialchars($login ?? ''); ?>" required></label>
    <label>Mot de passe<br><input type="password" name="password" required></label>
    <label>Confirmer le mot de passe<br><input type="password" name="password_confirm" required></label>
    <button type="submit">S'inscrire</button>
</form>
<?php require '../layout/footer.php'; ?>