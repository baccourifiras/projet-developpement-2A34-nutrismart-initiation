<?php
require_once __DIR__ . '/../Model/Utilisateur.php';
require_once __DIR__ . '/../Model/Historique.php';
require_once __DIR__ . '/../config.php';

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

        if      ($action === 'ajouter')        { $this->ajouter();        }
        elseif  ($action === 'modifier')       { $this->modifier();       }
        elseif  ($action === 'supprimer')      { $this->supprimer();      }
        elseif  ($action === 'login')          { $this->login();          }
        elseif  ($action === 'deconnexion')    { $this->deconnexion();    }
        elseif  ($action === 'face_register')  { $this->faceRegister();   }
        elseif  ($action === 'face_login')     { $this->faceLogin();      }
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

        // Récupérer l'ID du nouvel utilisateur pour l'étape Face ID
        $db    = config::getConnexion();
        $newId = (int) $db->lastInsertId();

        if ($viaAdmin) {
            header('Location: index.php?page=dashboard&onglet=utilisateurs&succes=ajoute');
        } else {
            header('Location: index.php?page=inscription&succes=1&user_id=' . $newId);
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

    /**
     * Enregistre le descripteur facial d'un utilisateur après inscription.
     * Appelé en AJAX (JSON).
     */
    private function faceRegister()
    {
        // Supprimer tout output parasite (notices, warnings PHP) avant le JSON
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $id_user    = isset($_POST['id_user'])    ? (int) $_POST['id_user']    : 0;
        $descriptor = isset($_POST['descriptor']) ? $_POST['descriptor']       : '';

        if ($id_user <= 0 || empty($descriptor)) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
            exit;
        }

        // Valider que c'est bien un JSON de tableau de nombres
        $arr = json_decode($descriptor, true);
        if (!is_array($arr) || count($arr) < 10) {
            echo json_encode(['success' => false, 'message' => 'Descripteur invalide.']);
            exit;
        }

        try {
            $ok = $this->model->saveFaceDescriptor($id_user, $descriptor);
            echo json_encode(['success' => (bool) $ok]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur BD : ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Authentification par reconnaissance faciale.
     * Reçoit un descripteur en POST, compare avec tous les utilisateurs enregistrés.
     * Appelé en AJAX (JSON).
     */
    private function faceLogin()
    {
        // Supprimer tout output parasite (notices, warnings PHP) avant le JSON
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $descriptor = isset($_POST['descriptor']) ? $_POST['descriptor'] : '';

        if (empty($descriptor)) {
            echo json_encode(['success' => false, 'message' => 'Descripteur manquant.']);
            exit;
        }

        $incoming = json_decode($descriptor, true);
        if (!is_array($incoming) || count($incoming) < 10) {
            echo json_encode(['success' => false, 'message' => 'Descripteur invalide.']);
            exit;
        }

        try {
            $users = $this->model->getAllWithFaceDescriptor();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur BD : ' . $e->getMessage()]);
            exit;
        }

        $bestMatch    = null;
        $bestDistance = PHP_FLOAT_MAX;
        $threshold    = 0.55;

        foreach ($users as $u) {
            $stored = json_decode($u['face_descriptor'], true);
            if (!is_array($stored) || count($stored) !== count($incoming)) continue;

            // Distance euclidienne
            $sum = 0.0;
            for ($i = 0; $i < count($incoming); $i++) {
                $diff = (float)$incoming[$i] - (float)$stored[$i];
                $sum += $diff * $diff;
            }
            $distance = sqrt($sum);

            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $bestMatch    = $u;
            }
        }

        if ($bestMatch && $bestDistance <= $threshold) {
            // Connexion réussie
            try {
                $historique = new Historique();
                $historique->enregistrer($bestMatch['id_user'], 'connexion_faceid', 'succes', $bestMatch['email']);
            } catch (Exception $e) { /* non bloquant */ }

            $_SESSION['user_id']     = $bestMatch['id_user'];
            $_SESSION['user_nom']    = $bestMatch['nom'];
            $_SESSION['user_prenom'] = $bestMatch['prenom'];
            $_SESSION['user_email']  = $bestMatch['email'];
            $_SESSION['user_role']   = $bestMatch['role'];

            $redirect = 'index.php?page=accueil';
            if      ($bestMatch['role'] === 'admin')          { $redirect = 'index.php?page=dashboard';             }
            elseif  ($bestMatch['role'] === 'nutritionniste') { $redirect = 'index.php?page=espace_nutritionniste'; }
            elseif  ($bestMatch['role'] === 'client')         { $redirect = 'index.php?page=espace_client';         }

            echo json_encode([
                'success'  => true,
                'redirect' => $redirect,
                'prenom'   => $bestMatch['prenom'],
                'distance' => round($bestDistance, 4)
            ]);
        } else {
            try {
                $historique = new Historique();
                $historique->enregistrer(null, 'connexion_faceid', 'echec', 'face_id_attempt');
            } catch (Exception $e) { /* non bloquant */ }
            echo json_encode(['success' => false, 'message' => 'Visage non reconnu. Veuillez réessayer ou utiliser votre mot de passe.']);
        }
        exit;
    }
}
?>
