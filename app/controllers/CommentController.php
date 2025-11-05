<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Comment.php';

class CommentController extends Controller
{

    private $commentModel;

    public function __construct()
    {
        $this->commentModel = new Comment();
    }

    /**
     * Afficher le formulaire d'ajout de commentaire
     */
    public function create()
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getAuthUser();
        if (!$user) {
            $this->redirect('/livreor/?action=login');
            return;
        }

        echo "<h1>Ajouter un commentaire</h1>";
        echo "<form method='post' action='?action=store_comment'>";
        echo "<p><textarea name='commentaire' placeholder='Votre commentaire...' rows='5' cols='50' required></textarea></p>";
        echo "<p><button type='submit'>Publier le commentaire</button></p>";
        echo "</form>";
        echo "<p><a href='?action=guestbook'>Retour au livre d'or</a></p>";
    }

    /**
     * Traiter l'ajout d'un commentaire
     */
    public function store()
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getAuthUser();
        if (!$user) {
            $this->redirect('/livreor/?action=login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/livreor/?action=create_comment');
            return;
        }

        $commentaire = trim($_POST['commentaire'] ?? '');

        if (empty($commentaire)) {
            echo "<p style='color: red;'>Le commentaire ne peut pas être vide.</p>";
            echo "<p><a href='?action=create_comment'>Retour</a></p>";
            return;
        }

        if ($this->commentModel->create($commentaire, $user['id'])) {
            echo "<p style='color: green;'>Commentaire ajouté avec succès !</p>";
            echo "<p><a href='?action=guestbook'>Voir le livre d'or</a></p>";
        } else {
            echo "<p style='color: red;'>Erreur lors de l'ajout du commentaire.</p>";
            echo "<p><a href='?action=create_comment'>Retour</a></p>";
        }
    }

    /**
     * Afficher le formulaire de modification d'un commentaire
     */
    public function edit()
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getAuthUser();
        if (!$user) {
            $this->redirect('/livreor/?action=login');
            return;
        }

        $commentId = $_GET['id'] ?? 0;
        $comment = $this->commentModel->getById($commentId, $user['id']);

        if (!$comment) {
            echo "<p style='color: red;'>Commentaire introuvable ou vous n'êtes pas autorisé à le modifier.</p>";
            echo "<p><a href='?action=guestbook'>Retour au livre d'or</a></p>";
            return;
        }

        echo "<h1>Modifier le commentaire</h1>";
        echo "<form method='post' action='?action=update_comment&id=" . $comment['id'] . "'>";
        echo "<p><textarea name='commentaire' rows='5' cols='50' required>" . htmlspecialchars($comment['commentaire']) . "</textarea></p>";
        echo "<p><button type='submit'>Modifier le commentaire</button></p>";
        echo "</form>";
        echo "<p><a href='?action=guestbook'>Annuler</a></p>";
    }

    /**
     * Traiter la modification d'un commentaire
     */
    public function update()
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getAuthUser();
        if (!$user) {
            $this->redirect('/livreor/?action=login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/livreor/?action=guestbook');
            return;
        }

        $commentId = $_GET['id'] ?? 0;
        $commentaire = trim($_POST['commentaire'] ?? '');

        if (empty($commentaire)) {
            echo "<p style='color: red;'>Le commentaire ne peut pas être vide.</p>";
            echo "<p><a href='?action=edit_comment&id=" . $commentId . "'>Retour</a></p>";
            return;
        }

        if ($this->commentModel->update($commentId, $commentaire, $user['id'])) {
            echo "<p style='color: green;'>Commentaire modifié avec succès !</p>";
        } else {
            echo "<p style='color: red;'>Impossible de modifier ce commentaire. Vous ne pouvez modifier que vos propres commentaires.</p>";
        }

        echo "<p><a href='?action=guestbook'>Retour au livre d'or</a></p>";
    }

    /**
     * Supprimer un commentaire
     */
    public function delete()
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getAuthUser();
        if (!$user) {
            $this->redirect('/livreor/?action=login');
            return;
        }

        $commentId = $_GET['id'] ?? 0;

        if ($this->commentModel->delete($commentId, $user['id'])) {
            echo "<p style='color: green;'>Commentaire supprimé avec succès !</p>";
        } else {
            echo "<p style='color: red;'>Impossible de supprimer ce commentaire. Vous ne pouvez supprimer que vos propres commentaires.</p>";
        }

        echo "<p><a href='?action=guestbook'>Retour au livre d'or</a></p>";
    }
}
