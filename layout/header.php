<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Livre d'or</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <header class="site-header">
        <div class="container">
            <h1><a href="../config/index.php">Livre d'or</a></h1>
            <nav>
                <a href="../config/index.php">Accueil</a>
                <a href="../config/livre-or.php">Livre d'or</a>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a href="../home/profil.php">Mon profil</a>
                    <a href="../authentifiquation/logout.php">Se déconnecter (<?php echo htmlspecialchars($_SESSION['login']); ?>)</a>
                <?php else: ?>
                    <a href="../authentifiquation/inscription.php">S'inscrire</a>
                    <a href="../authentifiquation/connection.php">Se connecter</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container">