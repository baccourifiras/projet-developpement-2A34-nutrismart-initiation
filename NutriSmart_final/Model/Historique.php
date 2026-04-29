<?php
require_once __DIR__ . '/../config.php';

class Historique
{
    // Proprietes privees (Chapitre 4 - Encapsulation)
    private $id_historique;
    private $id_user;
    private $action;
    private $statut;
    private $email_tente;

    // Constructeur (Chapitre 4)
    public function __construct()
    {
        $this->id_historique = 0;
        $this->id_user       = 0;
        $this->action        = '';
        $this->statut        = '';
        $this->email_tente   = '';
    }

    // Destructeur (Chapitre 4)
    public function __destruct()
    {
        unset($this->id_historique);
    }

    // Getters (Chapitre 4)
    public function getIdHistorique() { return $this->id_historique; }
    public function getIdUser()       { return $this->id_user; }
    public function getAction()       { return $this->action; }
    public function getStatut()       { return $this->statut; }
    public function getEmailTente()   { return $this->email_tente; }

    // Setters (Chapitre 4)
    public function setIdUser($id)     { $this->id_user = $id; }
    public function setAction($a)      { $this->action = $a; }
    public function setStatut($s)      { $this->statut = $s; }
    public function setEmailTente($e)  { $this->email_tente = $e; }

    // CRUD - Create (Chapitre 5 - slide 14)
    public function enregistrer($id_user, $action, $statut, $email_tente)
    {
        $this->setIdUser($id_user);
        $this->setAction($action);
        $this->setStatut($statut);
        $this->setEmailTente($email_tente);

        $db    = config::getConnexion();
        $query = $db->prepare(
            "INSERT INTO historique (id_user, action, statut, email_tente, date_action)
             VALUES (:id_user, :action, :statut, :email_tente, NOW())"
        );
        return $query->execute([
            ':id_user'     => $this->getIdUser(),
            ':action'      => $this->getAction(),
            ':statut'      => $this->getStatut(),
            ':email_tente' => $this->getEmailTente()
        ]);
    }

    // CRUD - Read All avec jointure (Chapitre 5 - slide 17)
    public function findAll()
    {
        $db    = config::getConnexion();
        $query = $db->prepare(
            "SELECT h.id_historique, h.id_user, h.action, h.date_action,
                    h.statut, h.email_tente, u.nom, u.prenom
             FROM historique h
             LEFT JOIN utilisateur u ON h.id_user = u.id_user
             ORDER BY h.date_action DESC"
        );
        $query->execute();
        return $query->fetchAll();
    }

    // CRUD - Delete One (Chapitre 5 - slide 19)
    public function delete($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("DELETE FROM historique WHERE id_historique = :id");
        $query->execute([':id' => $id]);
    }

    // CRUD - Delete All (Chapitre 5 - exec)
    public function viderTout()
    {
        $db = config::getConnexion();
        $db->exec("DELETE FROM historique");
    }

    // Stats - utilise if/else (Chapitre 3) et PDO (Chapitre 5)
    public function stats()
    {
        $db = config::getConnexion();

        $q1 = $db->prepare("SELECT COUNT(*) FROM historique");
        $q1->execute();
        $total = (int) $q1->fetchColumn();

        $q2 = $db->prepare("SELECT COUNT(*) FROM historique WHERE statut = 'succes'");
        $q2->execute();
        $succes = (int) $q2->fetchColumn();

        $q3 = $db->prepare("SELECT COUNT(*) FROM historique WHERE statut = 'echec'");
        $q3->execute();
        $echec = (int) $q3->fetchColumn();

        return ['total' => $total, 'succes' => $succes, 'echec' => $echec];
    }
}
?>
