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

        echo "<div class='guestbook-container'>";
        echo "<div class='guestbook-header'>";
        echo "<h1 class='guestbook-title'>📖 Livre d'Or</h1>";
        echo "<div class='guestbook-stats'>";
        echo "<strong>$totalComments</strong> commentaire" . ($totalComments > 1 ? 's' : '') . " publié" . ($totalComments > 1 ? 's' : '');
        echo "</div>";

        if ($user) {
            echo "<a href='?action=create_comment' class='add-comment-btn'>✍️ Ajouter un commentaire</a>";
        } else {
            echo "<div class='login-prompt'>";
            echo "💡 <a href='?action=login'>Connectez-vous</a> pour partager votre avis et laisser un commentaire !";
            echo "</div>";
        }
        echo "</div>";

        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $date = new DateTime($comment['date']);
                $dateFormatted = $date->format('d/m/Y à H:i');

                echo "<div class='comment-card'>";
                echo "<div class='comment-meta'>";
                echo "<span class='date'>📅 " . htmlspecialchars($dateFormatted) . "</span>";
                echo "<span class='author'>👤 " . htmlspecialchars($comment['login']) . "</span>";
                echo "</div>";
                echo "<div class='comment-body'>" . nl2br(htmlspecialchars($comment['commentaire'])) . "</div>";

                // Boutons de modification et suppression pour le propriétaire du commentaire
                if ($user && $user['id'] == $comment['id_utilisateur']) {
                    echo "<div class='comment-actions'>";
                    echo "<a href='?action=edit_comment&id=" . $comment['id'] . "' class='comment-btn comment-btn-edit'>";
                    echo "✏️ Modifier</a>";
                    echo "<a href='?action=delete_comment&id=" . $comment['id'] . "' class='comment-btn comment-btn-delete' ";
                    echo "onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce commentaire ?\")'>";
                    echo "🗑️ Supprimer</a>";
                    echo "</div>";
                }

                echo "</div>";
            }
        } else {
            echo "<div class='no-comments'>";
            echo "📝 Aucun commentaire pour le moment.<br>";
            echo "<small>Soyez le premier à partager votre avis !</small>";
            echo "</div>";
        }

        echo "<div class='guestbook-footer'>";
        echo "<a href='?action=home'>← Retour à l'accueil</a>";
        echo "</div>";
        echo "</div>";
    }
}
