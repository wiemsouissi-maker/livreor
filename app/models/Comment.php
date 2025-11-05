<?php

require_once __DIR__ . '/../core/Database.php';

class Comment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Créer un nouveau commentaire
     */
    public function create($commentaire, $userId)
    {
        $stmt = $this->db->prepare('INSERT INTO commentaires (commentaire, id_utilisateur, date) VALUES (?, ?, NOW())');
        return $stmt->execute([$commentaire, $userId]);
    }

    /**
     * Récupérer tous les commentaires avec les informations des utilisateurs
     */
    public function getAll()
    {
        $stmt = $this->db->query('
            SELECT c.id, c.commentaire, c.date, c.id_utilisateur, u.login 
            FROM commentaires c 
            JOIN utilisateurs u ON c.id_utilisateur = u.id 
            ORDER BY c.date DESC
        ');
        return $stmt->fetchAll();
    }

    /**
     * Récupérer un commentaire par ID (seulement si c'est le propriétaire)
     */
    public function getById($id, $userId)
    {
        $stmt = $this->db->prepare('
            SELECT c.id, c.commentaire, c.date, c.id_utilisateur, u.login 
            FROM commentaires c 
            JOIN utilisateurs u ON c.id_utilisateur = u.id 
            WHERE c.id = ? AND c.id_utilisateur = ?
        ');
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    /**
     * Modifier un commentaire (seulement si c'est le propriétaire)
     */
    public function update($id, $commentaire, $userId)
    {
        $stmt = $this->db->prepare('UPDATE commentaires SET commentaire = ? WHERE id = ? AND id_utilisateur = ?');
        return $stmt->execute([$commentaire, $id, $userId]);
    }

    /**
     * Supprimer un commentaire (seulement si c'est le propriétaire)
     */
    public function delete($id, $userId)
    {
        $stmt = $this->db->prepare('DELETE FROM commentaires WHERE id = ? AND id_utilisateur = ?');
        return $stmt->execute([$id, $userId]);
    }

    /**
     * Compter le nombre total de commentaires
     */
    public function count()
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM commentaires');
        $result = $stmt->fetch();
        return $result['total'];
    }
}
