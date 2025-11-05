<?php
require_once '../db/database.php';
require '../layout/header.php';


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';


    if ($login === '' || $password === '') {
        $errors[] = 'Login et mot de passe requis.';
    } else {
        $stmt = $pdo->prepare('SELECT id, login, password FROM utilisateurs WHERE login = ?');
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            // connexion réussie
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $user['login'];
            header('Location: ../config/livre-or.php');
            exit;
        } else {
            $errors[] = 'Login ou mot de passe incorrect.';
        }
    }
}
?>
<h2>Connexion</h2>
<?php if ($errors): ?>
    <div class="errors">
        <ul><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>
<form method="post" class="form">
    <label>Login<br><input type="text" name="login" required></label>
    <label>Mot de passe<br><input type="password" name="password" required></label>
    <button type="submit">Se connecter</button>
</form>
<?php require '../layout/footer.php'; ?>