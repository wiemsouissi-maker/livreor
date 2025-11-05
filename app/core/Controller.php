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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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
        echo "<p style='color: green; background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px;'>✅ " . $this->escape($message) . "</p>";
        echo "<p><a href='" . $returnLink . "' style='background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;'>Continuer</a></p>";
    }

    /**
     * Afficher un message d'erreur
     */
    protected function showError($message, $returnLink = '?action=guestbook')
    {
        echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>❌ " . $this->escape($message) . "</p>";
        echo "<p><a href='" . $returnLink . "' style='background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;'>Retour</a></p>";
    }
}
