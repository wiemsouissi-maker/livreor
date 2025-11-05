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
                // La session est déjà démarrée dans index.php
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login'] = $user['login'];

                $this->showSuccess('Connexion réussie !', '?action=guestbook');
                return;
            } else {
                $this->showError('Login ou mot de passe incorrect.', '?action=login');
                return;
            }
        }

        echo "<div class='auth-container'>";
        echo "<h1 class='auth-title'>🔐 Connexion</h1>";
        echo "<form method='post' class='auth-form'>";
        echo "<div class='form-group'>";
        echo "<label for='login'>Nom d'utilisateur</label>";
        echo "<input type='text' id='login' name='login' placeholder='Entrez votre login' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='password'>Mot de passe</label>";
        echo "<input type='password' id='password' name='password' placeholder='Entrez votre mot de passe' required>";
        echo "</div>";
        echo "<button type='submit' class='auth-button'>Se connecter</button>";
        echo "</form>";
        echo "<div class='auth-links'>";
        echo "<a href='?action=register'>📝 Créer un compte</a>";
        echo "<span class='auth-divider'>|</span>";
        echo "<a href='?action=home'>🏠 Accueil</a>";
        echo "</div>";
        echo "</div>";
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

        echo "<div class='auth-container'>";
        echo "<h1 class='auth-title'>📝 Inscription</h1>";
        echo "<form method='post' class='auth-form'>";
        echo "<div class='form-group'>";
        echo "<label for='login'>Nom d'utilisateur</label>";
        echo "<input type='text' id='login' name='login' placeholder='Choisissez un login' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='password'>Mot de passe</label>";
        echo "<input type='password' id='password' name='password' placeholder='Minimum 4 caractères' required>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label for='password_confirm'>Confirmer le mot de passe</label>";
        echo "<input type='password' id='password_confirm' name='password_confirm' placeholder='Confirmer votre mot de passe' required>";
        echo "</div>";
        echo "<button type='submit' class='auth-button'>Créer mon compte</button>";
        echo "</form>";
        echo "<div class='auth-links'>";
        echo "<a href='?action=login'>🔐 J'ai déjà un compte</a>";
        echo "<span class='auth-divider'>|</span>";
        echo "<a href='?action=home'>🏠 Accueil</a>";
        echo "</div>";
        echo "</div>";
    }

    public function logout()
    {
        // La session est déjà démarrée dans index.php

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
