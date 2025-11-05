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

        echo "<div class='comment-form-container'>";
        echo "<h1 class='comment-form-title'>✍️ Ajouter un commentaire</h1>";
        echo "<p style='text-align: center; color: #6c757d; margin-bottom: 25px;'>Connecté en tant que <strong>" . $this->escape($user['login']) . "</strong></p>";
        echo "<form method='post' action='?action=store_comment' class='comment-form'>";
        echo "<div class='comment-form-group'>";
        echo "<label for='commentaire'>Votre message</label>";
        echo "<textarea id='commentaire' name='commentaire' class='comment-textarea' placeholder='Partagez votre expérience, vos impressions, vos suggestions...' required maxlength='1000'></textarea>";
        echo "<div class='char-counter'><span id='char-count'>0</span>/1000 caractères</div>";
        echo "</div>";
        echo "<button type='submit' class='comment-submit-btn'>📝 Publier mon commentaire</button>";
        echo "</form>";
        echo "<div class='comment-back-link'>";
        echo "<a href='?action=guestbook'>← Retour au livre d'or</a>";
        echo "</div>";
        echo "</div>";

        // Ajouter un petit script pour le compteur de caractères
        echo "<script>";
        echo "document.getElementById('commentaire').addEventListener('input', function() {";
        echo "  const count = this.value.length;";
        echo "  const counter = document.getElementById('char-count');";
        echo "  const counterDiv = counter.parentElement;";
        echo "  counter.textContent = count;";
        echo "  counterDiv.className = 'char-counter';";
        echo "  if (count > 800) counterDiv.classList.add('warning');";
        echo "  if (count > 950) counterDiv.classList.add('danger');";
        echo "});";
        echo "</script>";
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
            echo "<div class='auth-container'>";
            echo "<div class='auth-message auth-error'>❌ Commentaire introuvable ou vous n'êtes pas autorisé à le modifier.</div>";
            echo "<div class='auth-links'><a href='?action=guestbook'>Retour au livre d'or</a></div>";
            echo "</div>";
            return;
        }

        echo "<div class='comment-form-container'>";
        echo "<h1 class='comment-form-title'>✏️ Modifier le commentaire</h1>";
        echo "<p style='text-align: center; color: #6c757d; margin-bottom: 25px;'>Modification en tant que <strong>" . $this->escape($user['login']) . "</strong></p>";
        echo "<form method='post' action='?action=update_comment&id=" . $comment['id'] . "' class='comment-form'>";
        echo "<div class='comment-form-group'>";
        echo "<label for='commentaire'>Votre message</label>";
        echo "<textarea id='commentaire' name='commentaire' class='comment-textarea' required maxlength='1000'>" . $this->escape($comment['commentaire']) . "</textarea>";
        echo "<div class='char-counter'><span id='char-count'>" . strlen($comment['commentaire']) . "</span>/1000 caractères</div>";
        echo "</div>";
        echo "<button type='submit' class='comment-submit-btn'>💾 Enregistrer les modifications</button>";
        echo "</form>";
        echo "<div class='comment-back-link'>";
        echo "<a href='?action=guestbook'>← Annuler et retourner</a>";
        echo "</div>";
        echo "</div>";

        // Ajouter le script pour le compteur de caractères
        echo "<script>";
        echo "document.getElementById('commentaire').addEventListener('input', function() {";
        echo "  const count = this.value.length;";
        echo "  const counter = document.getElementById('char-count');";
        echo "  const counterDiv = counter.parentElement;";
        echo "  counter.textContent = count;";
        echo "  counterDiv.className = 'char-counter';";
        echo "  if (count > 800) counterDiv.classList.add('warning');";
        echo "  if (count > 950) counterDiv.classList.add('danger');";
        echo "});";
        echo "</script>";
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
