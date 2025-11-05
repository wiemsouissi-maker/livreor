<?php

/**
 * Classe de base pour tous les contrôleurs
 */
class Controller
{
    /**
     * Rediriger vers une URL
     */
    protected function redirect($url)
    {
        header("Location: $url");
        exit();
    }

    /**
     * Obtenir l'utilisateur connecté depuis la session
     */
    protected function getAuthUser()
    {
        // La session est déjà démarrée dans index.php
        if (isset($_SESSION['user_id']) && isset($_SESSION['login'])) {
            return [
                'id' => $_SESSION['user_id'],
                'login' => $_SESSION['login']
            ];
        }

        return null;
    }

    /**
     * Vérifier si l'utilisateur est connecté
     */
    protected function requireAuth()
    {
        $user = $this->getAuthUser();
        if (!$user) {
            $this->redirect('/livreor/?action=login');
            exit();
        }
        return $user;
    }

    /**
     * Échapper les données pour l'affichage HTML
     */
    protected function escape($data)
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Afficher un message de succès
     */
    protected function showSuccess($message, $returnLink = '?action=guestbook')
    {
        echo "<div class='auth-container'>";
        echo "<div class='auth-message auth-success'>✅ " . $this->escape($message) . "</div>";
        echo "<div class='auth-links'><a href='" . $returnLink . "'>Continuer</a></div>";
        echo "</div>";
    }

    /**
     * Afficher un message d'erreur
     */
    protected function showError($message, $returnLink = '?action=guestbook')
    {
        echo "<div class='auth-container'>";
        echo "<div class='auth-message auth-error'>❌ " . $this->escape($message) . "</div>";
        echo "<div class='auth-links'><a href='" . $returnLink . "'>Retour</a></div>";
        echo "</div>";
    }
}
