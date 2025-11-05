<?php

require_once __DIR__ . '/../core/Database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function create($login, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('INSERT INTO utilisateurs (login, password) VALUES (?, ?)');
        return $stmt->execute([$login, $hashedPassword]);
    }

    /**
     * Vérifier les informations de connexion
     */
    public function login($login, $password)
    {
        $stmt = $this->db->prepare('SELECT id, login, password FROM utilisateurs WHERE login = ?');
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return [
                'id' => $user['id'],
                'login' => $user['login']
            ];
        }

        return false;
    }

    /**
     * Vérifier si un login existe déjà
     */
    public function loginExists($login)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM utilisateurs WHERE login = ?');
        $stmt->execute([$login]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT id, login FROM utilisateurs WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Compter le nombre total d'utilisateurs
     */
    public function count()
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM utilisateurs');
        $result = $stmt->fetch();
        return $result['total'];
    }
}
