<?php
require_once __DIR__ . '/../config.php';

class Utilisateur
{
    // Proprietes privees (Chapitre 4 - Encapsulation)
    private $id_user;
    private $nom;
    private $prenom;
    private $email;
    private $mot_de_passe;
    private $role;

    // Constructeur (Chapitre 4)
    public function __construct()
    {
        $this->id_user      = 0;
        $this->nom          = '';
        $this->prenom       = '';
        $this->email        = '';
        $this->mot_de_passe = '';
        $this->role         = 'client';
    }

    // Destructeur (Chapitre 4)
    public function __destruct()
    {
        unset($this->id_user);
    }

    // Getters (Chapitre 4)
    public function getIdUser()      { return $this->id_user; }
    public function getNom()         { return $this->nom; }
    public function getPrenom()      { return $this->prenom; }
    public function getEmail()       { return $this->email; }
    public function getMotDePasse()  { return $this->mot_de_passe; }
    public function getRole()        { return $this->role; }

    // Setters (Chapitre 4)
    public function setIdUser($id)      { $this->id_user = $id; }
    public function setNom($nom)        { $this->nom = $nom; }
    public function setPrenom($prenom)  { $this->prenom = $prenom; }
    public function setEmail($email)    { $this->email = $email; }
    public function setMotDePasse($mdp) { $this->mot_de_passe = $mdp; }
    public function setRole($role)      { $this->role = $role; }

    // CRUD - Read All (Chapitre 5 - slide 17)
    public function findAll()
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT * FROM utilisateur ORDER BY id_user DESC");
        $query->execute();
        return $query->fetchAll();
    }

    // CRUD - Read One (Chapitre 5)
    public function findById($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
        $query->execute([':id' => $id]);
        return $query->fetch();
    }

    // CRUD - Create (Chapitre 5 - slide 14)
    public function create($data)
    {
        $this->setNom($data['nom']);
        $this->setPrenom($data['prenom']);
        $this->setEmail($data['email']);
        $this->setMotDePasse($data['mot_de_passe']);
        $this->setRole($data['role']);

        $db    = config::getConnexion();
        $query = $db->prepare(
            "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role)
             VALUES (:nom, :prenom, :email, :mdp, :role)"
        );
        return $query->execute([
            ':nom'    => $this->getNom(),
            ':prenom' => $this->getPrenom(),
            ':email'  => $this->getEmail(),
            ':mdp'    => $this->getMotDePasse(),
            ':role'   => $this->getRole()
        ]);
    }

    // CRUD - Update (Chapitre 5 - slide 18)
    public function update($id, $data)
    {
        $this->setIdUser($id);
        $this->setNom($data['nom']);
        $this->setPrenom($data['prenom']);
        $this->setEmail($data['email']);
        $this->setRole($data['role']);

        $db    = config::getConnexion();
        $query = $db->prepare(
            "UPDATE utilisateur
             SET nom=:nom, prenom=:prenom, email=:email, role=:role
             WHERE id_user=:id"
        );
        return $query->execute([
            ':nom'    => $this->getNom(),
            ':prenom' => $this->getPrenom(),
            ':email'  => $this->getEmail(),
            ':role'   => $this->getRole(),
            ':id'     => $this->getIdUser()
        ]);
    }

    // CRUD - Delete (Chapitre 5 - slide 19)
    public function delete($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("DELETE FROM utilisateur WHERE id_user = :id");
        $query->execute([':id' => $id]);
    }

    // Login - comparaison directe (Chapitre 3 - operateurs comparaison, Chapitre 5 - PDO)
    public function login($email, $mot_de_passe)
    {
        $db    = config::getConnexion();
        $query = $db->prepare(
            "SELECT * FROM utilisateur WHERE email = :email LIMIT 1"
        );
        $query->execute([':email' => $email]);
        $user = $query->fetch();

        if (!$user) {
            return null;
        }
        // Comparaison directe - Chapitre 3 operateurs
        if ($mot_de_passe !== $user['mot_de_passe']) {
            return null;
        }
        return $user;
    }

    // Compter tous les utilisateurs (Chapitre 5 - PDO)
    public function count()
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT COUNT(*) FROM utilisateur");
        $query->execute();
        return (int) $query->fetchColumn();
    }

    // Compter par role - utilise foreach (Chapitre 3 - foreach)
    public function countByRole()
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT role, COUNT(*) as total FROM utilisateur GROUP BY role");
        $query->execute();
        $result = ['admin' => 0, 'nutritionniste' => 0, 'client' => 0];
        foreach ($query->fetchAll() as $row) {
            $result[$row['role']] = (int) $row['total'];
        }
        return $result;
    }

    // Verifier si email existe deja (Chapitre 5 - PDO)
    public function emailExiste($email, $excludeId = 0)
    {
        $db    = config::getConnexion();
        $query = $db->prepare(
            "SELECT COUNT(*) FROM utilisateur WHERE email = :email AND id_user != :id"
        );
        $query->execute([':email' => $email, ':id' => $excludeId]);
        return $query->fetchColumn() > 0;
    }
}
?>
