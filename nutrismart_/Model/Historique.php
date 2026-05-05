<?php
require_once __DIR__ . '/../config.php';

class Historique
{

    private $id_historique;
    private $id_user;
    private $action;
    private $statut;
    private $email_tente;

    public function __construct()
    {
        $this->id_historique = 0;
        $this->id_user       = 0;
        $this->action        = '';
        $this->statut        = '';
        $this->email_tente   = '';
    }

    public function __destruct()
    {
        unset($this->id_historique);
    }

    public function getIdHistorique() { return $this->id_historique; }
    public function getIdUser()       { return $this->id_user; }
    public function getAction()       { return $this->action; }
    public function getStatut()       { return $this->statut; }
    public function getEmailTente()   { return $this->email_tente; }

    public function setIdUser($id)     { $this->id_user = $id; }
    public function setAction($a)      { $this->action = $a; }
    public function setStatut($s)      { $this->statut = $s; }
    public function setEmailTente($e)  { $this->email_tente = $e; }

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

    public function rechercherParNomEtPrenom($nom, $prenom)
    {
        $db         = config::getConnexion();
        $conditions = [];
        $params     = [];

        if (!empty($nom)) {
            $conditions[] = "u.nom LIKE :nom";
            $params[':nom'] = '%' . $nom . '%';
        }

        if (!empty($prenom)) {
            $conditions[] = "u.prenom LIKE :prenom";
            $params[':prenom'] = '%' . $prenom . '%';
        }

        if (empty($conditions)) {
            return $this->findAll();
        }

        $sql = "SELECT h.id_historique, h.id_user, h.action, h.date_action,
                       h.statut, h.email_tente, u.nom, u.prenom
                FROM historique h
                LEFT JOIN utilisateur u ON h.id_user = u.id_user
                WHERE " . implode(' AND ', $conditions) . "
                ORDER BY h.date_action DESC";

        $query = $db->prepare($sql);
        $query->execute($params);
        return $query->fetchAll();
    }

    public function delete($id)
    {
        $db    = config::getConnexion();
        $query = $db->prepare("DELETE FROM historique WHERE id_historique = :id");
        $query->execute([':id' => $id]);
    }

    public function viderTout()
    {
        $db = config::getConnexion();
        $db->exec("DELETE FROM historique");
    }

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
