<?php
// Script de test pour vérifier la base de données

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/app/core/Database.php';

echo "<h1>🔍 Test de la base de données</h1>";

try {
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✅ Connexion à la base de données réussie !</p>";

    // Vérifier si les tables existent
    $tables = ['utilisateurs', 'commentaires'];

    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table '$table' existe</p>";

            // Compter les enregistrements
            $count = $db->query("SELECT COUNT(*) as count FROM $table")->fetch();
            echo "<p>📊 Nombre d'enregistrements dans '$table': " . $count['count'] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' n'existe PAS</p>";
        }
    }

    echo "<hr>";
    echo "<h2>📝 Session actuelle :</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    echo "<hr>";
    echo "<h2>🔧 Actions de test :</h2>";
    echo "<p><a href='?action=test_user'>Créer un utilisateur de test</a></p>";
    echo "<p><a href='?action=test_comment'>Créer un commentaire de test</a></p>";
    echo "<p><a href='?action=create_tables'>Créer les tables manquantes</a></p>";
    echo "<p><a href='/livreor/'>Retour au site</a></p>";

    // Actions de test
    $action = $_GET['action'] ?? '';

    if ($action === 'create_tables') {
        echo "<h3>🏗️ Création des tables...</h3>";

        // Créer table utilisateurs
        $sql = "CREATE TABLE IF NOT EXISTS utilisateurs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            login VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
        echo "<p style='color: green;'>✅ Table 'utilisateurs' créée</p>";

        // Créer table commentaires
        $sql = "CREATE TABLE IF NOT EXISTS commentaires (
            id INT AUTO_INCREMENT PRIMARY KEY,
            commentaire TEXT NOT NULL,
            id_utilisateur INT NOT NULL,
            date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
        )";
        $db->exec($sql);
        echo "<p style='color: green;'>✅ Table 'commentaires' créée</p>";

        echo "<p><a href='?'>Rafraîchir</a></p>";
    }

    if ($action === 'test_user') {
        // Créer un utilisateur de test
        $login = 'test_user';
        $password = password_hash('123456', PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT IGNORE INTO utilisateurs (login, password) VALUES (?, ?)");
        if ($stmt->execute([$login, $password])) {
            echo "<p style='color: green;'>✅ Utilisateur de test créé (login: test_user, password: 123456)</p>";

            // Connecter automatiquement l'utilisateur
            $stmt = $db->prepare("SELECT id, login FROM utilisateurs WHERE login = ?");
            $stmt->execute([$login]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login'] = $user['login'];
                echo "<p style='color: green;'>✅ Utilisateur connecté automatiquement !</p>";
            }
        }
        echo "<p><a href='?'>Rafraîchir</a></p>";
    }

    if ($action === 'test_comment') {
        if (!empty($_SESSION['user_id'])) {
            $stmt = $db->prepare("INSERT INTO commentaires (commentaire, id_utilisateur) VALUES (?, ?)");
            if ($stmt->execute(['Ceci est un commentaire de test ! 🎉', $_SESSION['user_id']])) {
                echo "<p style='color: green;'>✅ Commentaire de test créé !</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Vous devez être connecté pour créer un commentaire</p>";
        }
        echo "<p><a href='?'>Rafraîchir</a></p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
