<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($login) || empty($password)) {
                $this->showError('Login et mot de passe requis.', '?action=login');
                return;
            }

            $user = $this->userModel->login($login, $password);

            if ($user) {
                // Démarrer la session si ce n'est pas déjà fait
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login'] = $user['login'];

                $this->showSuccess('Connexion réussie !', '?action=guestbook');
                return;
            } else {
                $this->showError('Login ou mot de passe incorrect.', '?action=login');
                return;
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

            if (empty($login) || empty($password) || empty($passwordConfirm)) {
                $this->showError('Tous les champs sont requis.', '?action=register');
                return;
            }

            if ($password !== $passwordConfirm) {
                $this->showError('Les mots de passe ne correspondent pas.', '?action=register');
                return;
            }

            if (strlen($password) < 4) {
                $this->showError('Le mot de passe doit contenir au moins 4 caractères.', '?action=register');
                return;
            }

            if ($this->userModel->loginExists($login)) {
                $this->showError('Ce login existe déjà.', '?action=register');
                return;
            }

            if ($this->userModel->create($login, $password)) {
                $this->showSuccess('Inscription réussie ! Vous pouvez maintenant vous connecter.', '?action=login');
                return;
            } else {
                $this->showError('Erreur lors de l\'inscription.', '?action=register');
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
        // Démarrer la session si ce n'est pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

        $this->showSuccess('Vous avez été déconnecté.', '?action=home');
    }
}
