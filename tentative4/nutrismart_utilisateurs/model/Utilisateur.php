<?php
require_once __DIR__ . '/config.php';

/**
 * ============================================================
 * NutriSmart - Modele Utilisateur (Version Corrigée)
 * ============================================================
 * Correction : Date d'inscription qui s'affiche correctement
 * ============================================================
 */
class Utilisateur {

    // ----------------------------------------------------------
    // PROPRIETES PRIVEES - Encapsulation
    // ----------------------------------------------------------
    private $id_user;
    private $nom;
    private $prenom;
    private $email;
    private $mot_de_passe;
    private $role;
    private $provider_login;
    private $date_inscription;
    private $pdo;

    // ----------------------------------------------------------
    // CONSTRUCTEUR
    // ----------------------------------------------------------
    public function __construct() {
        $this->pdo              = Database::getConnection();
        $this->id_user          = 0;
        $this->nom              = '';
        $this->prenom           = '';
        $this->email            = '';
        $this->mot_de_passe     = '';
        $this->role             = 'client';
        $this->provider_login   = 'local';
        $this->date_inscription = date('Y-m-d H:i:s');
    }

    // ----------------------------------------------------------
    // DESTRUCTEUR
    // ----------------------------------------------------------
    public function __destruct() {
        unset($this->pdo);
    }

    // ====================== GETTERS ======================
    public function getIdUser()         { return $this->id_user; }
    public function getNom()            { return $this->nom; }
    public function getPrenom()         { return $this->prenom; }
    public function getEmail()          { return $this->email; }
    public function getRole()           { return $this->role; }
    public function getProviderLogin()  { return $this->provider_login; }
    public function getDateInscription(){ return $this->date_inscription; }

    // ====================== SETTERS ======================
    public function setIdUser($id) {
        $this->id_user = $id;
    }

    public function setNom($nom) {
        $this->nom = trim($nom);
    }

    public function setPrenom($prenom) {
        $this->prenom = trim($prenom);
    }

    public function setEmail($email) {
        $this->email = strtolower(trim($email));
    }

    public function setMotDePasse($mdp) {
        $this->mot_de_passe = password_hash($mdp, PASSWORD_BCRYPT);
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function setProviderLogin($provider) {
        $this->provider_login = $provider;
    }

    // ----------------------------------------------------------
    // CRUD - Lire tous les utilisateurs
    // ----------------------------------------------------------
    public function findAll() {
        $stmt = $this->pdo->query(
            'SELECT id_user, nom, prenom, email, role, provider_login, 
                    date_inscription 
             FROM utilisateur 
             ORDER BY date_inscription DESC'
        );
        return $stmt->fetchAll();
    }

    // ----------------------------------------------------------
    // CRUD - Lire un utilisateur par ID
    // ----------------------------------------------------------
    public function findById($id) {
        $stmt = $this->pdo->prepare(
            'SELECT id_user, nom, prenom, email, role, provider_login, date_inscription
             FROM utilisateur 
             WHERE id_user = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ==========================================================
    // CRUD - AJOUTER UN UTILISATEUR (CORRIGÉ)
    // ==========================================================
    public function create($data) {
        $this->setNom($data['nom']);
        $this->setPrenom($data['prenom']);
        $this->setEmail($data['email']);
        $this->setMotDePasse($data['mot_de_passe']);
        $this->setRole($data['role']);
        $this->setProviderLogin($data['provider_login']);

        $stmt = $this->pdo->prepare(
            'INSERT INTO utilisateur 
             (nom, prenom, email, mot_de_passe, role, provider_login, date_inscription) 
             VALUES 
             (:nom, :prenom, :email, :mdp, :role, :provider, NOW())'
        );

        return $stmt->execute([
            ':nom'      => $this->getNom(),
            ':prenom'   => $this->getPrenom(),
            ':email'    => $this->getEmail(),
            ':mdp'      => $this->mot_de_passe,
            ':role'     => $this->getRole(),
            ':provider' => $this->getProviderLogin()
        ]);
    }

    // ----------------------------------------------------------
    // CRUD - Modifier un utilisateur
    // ----------------------------------------------------------
    public function update($id, $data) {
        $this->setIdUser($id);
        $this->setNom($data['nom']);
        $this->setPrenom($data['prenom']);
        $this->setEmail($data['email']);
        $this->setRole($data['role']);
        $this->setProviderLogin($data['provider_login']);

        if (!empty($data['mot_de_passe'])) {
            $this->setMotDePasse($data['mot_de_passe']);
            $stmt = $this->pdo->prepare(
                'UPDATE utilisateur 
                 SET nom=:nom, prenom=:prenom, email=:email,
                     mot_de_passe=:mdp, role=:role, provider_login=:provider
                 WHERE id_user=:id'
            );
            return $stmt->execute([
                ':nom'      => $this->getNom(),
                ':prenom'   => $this->getPrenom(),
                ':email'    => $this->getEmail(),
                ':mdp'      => $this->mot_de_passe,
                ':role'     => $this->getRole(),
                ':provider' => $this->getProviderLogin(),
                ':id'       => $this->getIdUser()
            ]);
        }

        // Mise à jour sans changer le mot de passe
        $stmt = $this->pdo->prepare(
            'UPDATE utilisateur 
             SET nom=:nom, prenom=:prenom, email=:email,
                 role=:role, provider_login=:provider
             WHERE id_user=:id'
        );
        return $stmt->execute([
            ':nom'      => $this->getNom(),
            ':prenom'   => $this->getPrenom(),
            ':email'    => $this->getEmail(),
            ':role'     => $this->getRole(),
            ':provider' => $this->getProviderLogin(),
            ':id'       => $this->getIdUser()
        ]);
    }

    // ----------------------------------------------------------
    // CRUD - Supprimer
    // ----------------------------------------------------------
    public function delete($id) {
        $stmt = $this->pdo->prepare(
            'DELETE FROM utilisateur WHERE id_user = :id'
        );
        return $stmt->execute([':id' => $id]);
    }

    // ----------------------------------------------------------
    // Vérifier si email existe
    // ----------------------------------------------------------
    public function emailExiste($email, $excludeId = 0) {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM utilisateur 
             WHERE email = :email AND id_user != :id'
        );
        $stmt->execute([
            ':email' => strtolower(trim($email)),
            ':id'    => $excludeId
        ]);
        return $stmt->fetchColumn() > 0;
    }

    // ----------------------------------------------------------
    // Statistiques
    // ----------------------------------------------------------
    public function count() {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM utilisateur')->fetchColumn();
    }

    public function countByRole() {
        $stmt = $this->pdo->query(
            'SELECT role, COUNT(*) as total FROM utilisateur GROUP BY role'
        );
        $result = ['admin' => 0, 'nutritionniste' => 0, 'client' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['role']] = (int) $row['total'];
        }
        return $result;
    }
}
