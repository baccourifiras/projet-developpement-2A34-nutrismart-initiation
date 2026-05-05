<?php
require_once __DIR__ . '/../config.php';

class Utilisateur
{

    private $id_user;
    private $nom;
    private $prenom;
    private $email;
    private $mot_de_passe;
    private $role;

    public function __construct()
    {
        $this->id_user      = 0;
        $this->nom          = '';
        $this->prenom       = '';
        $this->email        = '';
        $this->mot_de_passe = '';
        $this->role         = 'client';
    }

    public function __destruct()
    {
        unset($this->id_user);
    }

    public function getIdUser()      { return $this->id_user; }
    public function getNom()         { return $this->nom; }
    public function getPrenom()      { return $this->prenom; }
    public function getEmail()       { return $this->email; }
    public function getMotDePasse()  { return $this->mot_de_passe; }
    public function getRole()        { return $this->role; }

    public function setIdUser($id)      { $this->id_user = $id; }
    public function setNom($nom)        { $this->nom = $nom; }
    public function setPrenom($prenom)  { $this->prenom = $prenom; }
    public function setEmail($email)    { $this->email = $email; }
    public function setMotDePasse($mdp) { $this->mot_de_passe = $mdp; }
    public function setRole($role)      { $this->role = $role; }

    public function findAll()
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT * FROM utilisateur ORDER BY id_user DESC");
        $query->execute();
        return $query->fetchAll();
    }

    public function findById($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
        $query->execute([':id' => $id]);
        return $query->fetch();
    }

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

    public function delete($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("DELETE FROM utilisateur WHERE id_user = :id");
        $query->execute([':id' => $id]);
    }

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

        if ($mot_de_passe !== $user['mot_de_passe']) {
            return null;
        }
        return $user;
    }

    public function count()
    {
        $db    = config::getConnexion();
        $query = $db->prepare("SELECT COUNT(*) FROM utilisateur");
        $query->execute();
        return (int) $query->fetchColumn();
    }

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

    public function rechercherParNomEtPrenom($nom, $prenom)
    {
        $db         = config::getConnexion();
        $conditions = [];
        $params     = [];

        if (!empty($nom)) {
            $conditions[] = "nom LIKE :nom";
            $params[':nom'] = '%' . $nom . '%';
        }

        if (!empty($prenom)) {
            $conditions[] = "prenom LIKE :prenom";
            $params[':prenom'] = '%' . $prenom . '%';
        }

        if (empty($conditions)) {
            return $this->findAll();
        }

        $sql   = "SELECT * FROM utilisateur WHERE " . implode(' AND ', $conditions) . " ORDER BY id_user DESC";
        $query = $db->prepare($sql);
        $query->execute($params);
        return $query->fetchAll();
    }

    public function rechercherParRole($role)
    {
        $db    = config::getConnexion();
        $query = $db->prepare(
            "SELECT * FROM utilisateur
             WHERE role = :role
             ORDER BY id_user DESC"
        );
        $query->execute([':role' => $role]);
        return $query->fetchAll();
    }

    public function inscriptionsParMois($nbMois = 12)
    {
        $db = config::getConnexion();
        // Récupère le nombre d'inscriptions regroupées par mois sur les N derniers mois
        $query = $db->prepare("
            SELECT
                DATE_FORMAT(date_inscription, '%Y-%m') AS mois,
                COUNT(*) AS total
            FROM utilisateur
            WHERE date_inscription >= DATE_SUB(CURDATE(), INTERVAL :nb MONTH)
            GROUP BY DATE_FORMAT(date_inscription, '%Y-%m')
            ORDER BY mois ASC
        ");
        $query->execute([':nb' => $nbMois]);
        $rows = $query->fetchAll(\PDO::FETCH_ASSOC);

        // Construire un tableau complet avec 0 pour les mois sans inscription
        $result = [];
        for ($i = $nbMois - 1; $i >= 0; $i--) {
            $key = date('Y-m', strtotime("-$i months"));
            $result[$key] = 0;
        }
        foreach ($rows as $row) {
            if (isset($result[$row['mois']])) {
                $result[$row['mois']] = (int)$row['total'];
            }
        }
        return $result;
    }

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
