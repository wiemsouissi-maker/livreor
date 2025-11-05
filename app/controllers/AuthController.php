<?php

// Check if Controller class exists, if not try different paths
if (!class_exists('Controller')) {
    $possiblePaths = [
        __DIR__ . '/../core/Controller.php',
        __DIR__ . '/../../core/Controller.php',
        __DIR__ . '/../models/Controller.php'
    ];

    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }

    // If still not found, create a basic Controller class
    if (!class_exists('Controller')) {
        class Controller
        {
            // Basic controller functionality
        }
    }
}

class AuthController extends Controller
{

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            // Simulation de connexion (à remplacer par vraie authentification)
            if (!empty($login) && !empty($password)) {
                $_SESSION['user_id'] = 1; // ID fictif
                $_SESSION['login'] = $login;

                echo "<p style='color: green;'>Connexion réussie !</p>";
                echo "<p><a href='?action=guestbook'>Aller au livre d'or</a></p>";
                return;
            } else {
                echo "<p style='color: red;'>Login et mot de passe requis.</p>";
            }
        }

        echo "<h1>Connexion</h1>";
        echo "<form method='post'>";
        echo "<p><input type='text' name='login' placeholder='Login' required></p>";
        echo "<p><input type='password' name='password' placeholder='Mot de passe' required></p>";
        echo "<p><button type='submit'>Se connecter</button></p>";
        echo "</form>";
        echo "<p><a href='?action=register'>S'inscrire</a> | <a href='?action=home'>Accueil</a></p>";
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (empty($login) || empty($password)) {
                echo "<p style='color: red;'>Tous les champs sont requis.</p>";
            } elseif ($password !== $passwordConfirm) {
                echo "<p style='color: red;'>Les mots de passe ne correspondent pas.</p>";
            } else {
                echo "<p style='color: green;'>Inscription réussie ! Vous pouvez maintenant vous connecter.</p>";
                echo "<p><a href='?action=login'>Se connecter</a></p>";
                return;
            }
        }

        echo "<h1>Inscription</h1>";
        echo "<form method='post'>";
        echo "<p><input type='text' name='login' placeholder='Login' required></p>";
        echo "<p><input type='password' name='password' placeholder='Mot de passe' required></p>";
        echo "<p><input type='password' name='password_confirm' placeholder='Confirmer' required></p>";
        echo "<p><button type='submit'>S'inscrire</button></p>";
        echo "</form>";
        echo "<p><a href='?action=login'>Se connecter</a> | <a href='?action=home'>Accueil</a></p>";
    }

    public function logout()
    {
        // Détruire la session
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        echo "<p style='color: green;'>Vous avez été déconnecté.</p>";
        echo "<p><a href='?action=home'>Retour à l'accueil</a></p>";
    }
}
