<?php
require_once __DIR__ . '/../Model/Utilisateur.php';
require_once __DIR__ . '/../Model/Historique.php';

class UtilisateurController
{

    private $model;

    public function __construct()
    {
        $this->model = new Utilisateur();
    }

    public function __destruct()
    {
        unset($this->model);
    }

    public function handle()
    {
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if      ($action === 'ajouter')     { $this->ajouter();     }
        elseif  ($action === 'modifier')    { $this->modifier();    }
        elseif  ($action === 'supprimer')   { $this->supprimer();   }
        elseif  ($action === 'login')       { $this->login();       }
        elseif  ($action === 'deconnexion') { $this->deconnexion(); }
    }

    private function ajouter()
    {
        // Détecter si la requête vient du dashboard admin ou de la page inscription publique
        $referer    = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $viaAdmin   = (strpos($referer, 'page=dashboard') !== false)
                   || (isset($_POST['source']) && $_POST['source'] === 'dashboard');

        $nom          = isset($_POST['nom'])          ? trim($_POST['nom'])          : '';
        $prenom       = isset($_POST['prenom'])       ? trim($_POST['prenom'])       : '';
        $email        = isset($_POST['email'])        ? trim($_POST['email'])        : '';
        $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe']       : '';
        $role         = isset($_POST['role'])         ? $_POST['role']               : '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($role)) {
            if ($viaAdmin) {
                header('Location: index.php?page=dashboard&onglet=utilisateurs&erreur=champs_vides');
            } else {
                header('Location: index.php?page=inscription&erreur=champs_vides');
            }
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($viaAdmin) {
                header('Location: index.php?page=dashboard&onglet=utilisateurs&erreur=email_invalide');
            } else {
                header('Location: index.php?page=inscription&erreur=email_invalide');
            }
            exit;
        }

        if (strlen($nom) < 2 || strlen($prenom) < 2) {
            if ($viaAdmin) {
                header('Location: index.php?page=dashboard&onglet=utilisateurs&erreur=champs_vides');
            } else {
                header('Location: index.php?page=inscription&erreur=champs_vides');
            }
            exit;
        }

        if (strlen($mot_de_passe) < 4) {
            if ($viaAdmin) {
                header('Location: index.php?page=dashboard&onglet=utilisateurs&erreur=champs_vides');
            } else {
                header('Location: index.php?page=inscription&erreur=champs_vides');
            }
            exit;
        }

        if ($this->model->emailExiste($email)) {
            if ($viaAdmin) {
                header('Location: index.php?page=dashboard&onglet=utilisateurs&erreur=email_existe');
            } else {
                header('Location: index.php?page=inscription&erreur=email_existe');
            }
            exit;
        }

        $data = [
            'nom'          => $nom,
            'prenom'       => $prenom,
            'email'        => $email,
            'mot_de_passe' => $mot_de_passe,
            'role'         => $role
        ];

        $this->model->create($data);

        if ($viaAdmin) {
            header('Location: index.php?page=dashboard&onglet=utilisateurs&succes=ajoute');
        } else {
            header('Location: index.php?page=inscription&succes=1');
        }
        exit;
    }

    private function modifier()
    {
        $id     = isset($_POST['id_user']) ? (int) $_POST['id_user'] : 0;
        $nom    = isset($_POST['nom'])     ? $_POST['nom']           : '';
        $prenom = isset($_POST['prenom'])  ? $_POST['prenom']        : '';
        $email  = isset($_POST['email'])   ? $_POST['email']         : '';
        $role   = isset($_POST['role'])    ? $_POST['role']          : '';

        if ($id <= 0 || empty($nom) || empty($prenom) || empty($email) || empty($role)) {
            header('Location: index.php?page=dashboard&erreur=champs_vides');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: index.php?page=dashboard&erreur=email_invalide');
            exit;
        }

        if (strlen($nom) < 2 || strlen($prenom) < 2) {
            header('Location: index.php?page=dashboard&erreur=champs_vides');
            exit;
        }

        if ($this->model->emailExiste($email, $id)) {
            header('Location: index.php?page=dashboard&erreur=email_existe');
            exit;
        }

        $data = [
            'nom'          => $nom,
            'prenom'       => $prenom,
            'email'        => $email,
            'mot_de_passe' => '',
            'role'         => $role
        ];

        $this->model->update($id, $data);
        header('Location: index.php?page=dashboard&succes=modifie');
        exit;
    }

    private function supprimer()
    {
        $id = isset($_POST['id_user']) ? (int) $_POST['id_user'] : 0;
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: index.php?page=dashboard&succes=supprime');
        exit;
    }

    private function login()
    {
        $email        = isset($_POST['email'])        ? $_POST['email']        : '';
        $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';

        if (empty($email) || empty($mot_de_passe)) {
            header('Location: index.php?page=login&erreur=champs_vides');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: index.php?page=login&erreur=identifiants');
            exit;
        }

        $user = $this->model->login($email, $mot_de_passe);

        $historique = new Historique();

        if (!$user) {
            $historique->enregistrer(null, 'connexion', 'echec', $email);
            header('Location: index.php?page=login&erreur=identifiants');
            exit;
        }

        $historique->enregistrer($user['id_user'], 'connexion', 'succes', $email);

        $_SESSION['user_id']     = $user['id_user'];
        $_SESSION['user_nom']    = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email']  = $user['email'];
        $_SESSION['user_role']   = $user['role'];

        if      ($user['role'] === 'admin')          { header('Location: index.php?page=dashboard');              }
        elseif  ($user['role'] === 'nutritionniste') { header('Location: index.php?page=espace_nutritionniste');  }
        elseif  ($user['role'] === 'client')         { header('Location: index.php?page=espace_client');          }
        else                                         { header('Location: index.php?page=accueil');                }
        exit;
    }

    private function deconnexion()
    {
        if (!empty($_SESSION['user_id'])) {
            $historique = new Historique();
            $historique->enregistrer(
                $_SESSION['user_id'],
                'deconnexion',
                'succes',
                isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''
            );
        }
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}
?>
