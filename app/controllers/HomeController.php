<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Comment.php';

class HomeController extends Controller
{

    private $commentModel;

    public function __construct()
    {
        $this->commentModel = new Comment();
    }

    public function index()
    {
        $user = $this->getAuthUser();

        echo "<h1>Accueil - Livre d'Or MVC</h1>";
        echo "<p>Bienvenue sur le livre d'or !</p>";

        if ($user) {
            echo "<p>Bonjour <strong>" . htmlspecialchars($user['login']) . "</strong> !</p>";
            echo "<p><a href='?action=guestbook'>Voir le livre d'or</a></p>";
            echo "<p><a href='?action=create_comment'>Ajouter un commentaire</a></p>";
            echo "<p><a href='?action=logout'>Se déconnecter</a></p>";
        } else {
            echo "<p><a href='?action=guestbook'>Voir le livre d'or</a></p>";
            echo "<p><a href='?action=login'>Se connecter</a></p>";
            echo "<p><a href='?action=register'>S'inscrire</a></p>";
        }
    }

    public function guestbook()
    {
        $user = $this->getAuthUser();
        $comments = $this->commentModel->getAll();
        $totalComments = $this->commentModel->count();

        echo "<h1>Livre d'Or</h1>";
        echo "<p><strong>$totalComments</strong> commentaire" . ($totalComments > 1 ? 's' : '') . "</p>";

        if ($user) {
            echo "<p><a href='?action=create_comment' style='background: #3498db; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Ajouter un commentaire</a></p>";
        } else {
            echo "<p>Tu dois <a href='?action=login'>te connecter</a> pour poster un commentaire.</p>";
        }

        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $date = new DateTime($comment['date']);
                $dateFormatted = $date->format('d/m/Y à H:i');

                echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-left: 4px solid #3498db; border-radius: 5px;'>";
                echo "<p style='color: #666; font-size: 0.9em; margin-bottom: 10px;'>";
                echo "Posté le " . htmlspecialchars($dateFormatted) . " par <strong>" . htmlspecialchars($comment['login']) . "</strong>";
                echo "</p>";
                echo "<div style='line-height: 1.6;'>" . nl2br(htmlspecialchars($comment['commentaire'])) . "</div>";

                // Boutons de modification et suppression pour le propriétaire du commentaire
                if ($user && $user['id'] == $comment['id_utilisateur']) {
                    echo "<p style='margin-top: 10px;'>";
                    echo "<a href='?action=edit_comment&id=" . $comment['id'] . "' ";
                    echo "style='background: #f39c12; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 0.8em; margin-right: 5px;'>";
                    echo "Modifier</a>";
                    echo "<a href='?action=delete_comment&id=" . $comment['id'] . "' ";
                    echo "onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce commentaire ?\")' ";
                    echo "style='background: #e74c3c; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 0.8em;'>";
                    echo "Supprimer</a>";
                    echo "</p>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>Aucun commentaire pour le moment.</p>";
        }

        echo "<p><a href='?action=home'>Retour à l'accueil</a></p>";
    }
}
