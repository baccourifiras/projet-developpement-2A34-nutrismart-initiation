<?php
require_once __DIR__ . '/../Model/Utilisateur.php';
require_once __DIR__ . '/../Model/Historique.php';

class UtilisateurController
{
    // Propriete privee (Chapitre 4)
    private $model;

    // Constructeur (Chapitre 4)
    public function __construct()
    {
        $this->model = new Utilisateur();
    }

    // Destructeur (Chapitre 4)
    public function __destruct()
    {
        unset($this->model);
    }

    // Dispatch des actions POST (Chapitre 3 - if/elseif)
    public function handle()
    {
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if      ($action === 'ajouter')     { $this->ajouter();     }
        elseif  ($action === 'modifier')    { $this->modifier();    }
        elseif  ($action === 'supprimer')   { $this->supprimer();   }
        elseif  ($action === 'login')       { $this->login();       }
        elseif  ($action === 'deconnexion') { $this->deconnexion(); }
    }

    // Ajouter (Chapitre 3 - empty/isset, Chapitre 5 - CRUD Create)
    private function ajouter()
    {
        $nom          = isset($_POST['nom'])          ? $_POST['nom']          : '';
        $prenom       = isset($_POST['prenom'])       ? $_POST['prenom']       : '';
        $email        = isset($_POST['email'])        ? $_POST['email']        : '';
        $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';
        $role         = isset($_POST['role'])         ? $_POST['role']         : '';

        // Controle de saisie (Chapitre 3 - empty)
        if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($role)) {
            header('Location: index.php?page=inscription&erreur=champs_vides');
            exit;
        }

        if ($this->model->emailExiste($email)) {
            header('Location: index.php?page=inscription&erreur=email_existe');
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
        header('Location: index.php?page=inscription&succes=1');
        exit;
    }

    // Modifier (Chapitre 5 - CRUD Update)
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

    // Supprimer (Chapitre 5 - CRUD Delete)
    private function supprimer()
    {
        $id = isset($_POST['id_user']) ? (int) $_POST['id_user'] : 0;
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: index.php?page=dashboard&succes=supprime');
        exit;
    }

    // Login (Chapitre 3 - if/elseif, Chapitre 5 - PDO)
    private function login()
    {
        $email        = isset($_POST['email'])        ? $_POST['email']        : '';
        $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';

        if (empty($email) || empty($mot_de_passe)) {
            header('Location: index.php?page=login&erreur=champs_vides');
            exit;
        }

        $user = $this->model->login($email, $mot_de_passe);

        // Enregistrement dans l'historique (Chapitre 4 - instanciation)
        $historique = new Historique();

        if (!$user) {
            $historique->enregistrer(null, 'connexion', 'echec', $email);
            header('Location: index.php?page=login&erreur=identifiants');
            exit;
        }

        $historique->enregistrer($user['id_user'], 'connexion', 'succes', $email);

        // Stockage en session
        $_SESSION['user_id']     = $user['id_user'];
        $_SESSION['user_nom']    = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email']  = $user['email'];
        $_SESSION['user_role']   = $user['role'];

        // Redirection selon le role (Chapitre 3 - if/elseif)
        if      ($user['role'] === 'admin')          { header('Location: index.php?page=dashboard');              }
        elseif  ($user['role'] === 'nutritionniste') { header('Location: index.php?page=espace_nutritionniste');  }
        elseif  ($user['role'] === 'client')         { header('Location: index.php?page=espace_client');          }
        else                                         { header('Location: index.php?page=accueil');                }
        exit;
    }

    // Deconnexion
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
