<?php
require_once __DIR__ . '/../model/Utilisateur.php';

/**
 * ============================================================
 * NutriSmart - Controleur Utilisateur
 * ============================================================
 * Cours PHP Chapitre 4 - Architecture MVC :
 *   - Controleur = lien entre le Modele et la Vue
 *   - Recoit les requetes POST
 *   - Appelle le Modele (Utilisateur.php)
 *   - Retourne une reponse JSON vers la Vue (script.js)
 * ============================================================
 */
class UtilisateurController {

    private $model;

    public function __construct() {
        // Instanciation du modele
        // Cours : $user = new Utilisateur();
        $this->model = new Utilisateur();
    }

    public function __destruct() {
        unset($this->model);
    }

    /**
     * Point d'entree : lit l'action et dispatch
     */
    public function handle() {
        header('Content-Type: application/json; charset=utf-8');

        $action = isset($_POST['action']) ? $_POST['action'] : '';

        try {
            if ($action === 'liste')     { $this->liste();     }
            elseif ($action === 'ajouter')   { $this->ajouter();   }
            elseif ($action === 'modifier')  { $this->modifier();  }
            elseif ($action === 'supprimer') { $this->supprimer(); }
            elseif ($action === 'stats')     { $this->stats();     }
            else {
                $this->json(['succes' => false, 'message' => 'Action inconnue.']);
            }
        } catch (Exception $e) {
            $this->json(['succes' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
        }
    }

    // Retourner tous les utilisateurs
    private function liste() {
        $this->json([
            'succes'       => true,
            'utilisateurs' => $this->model->findAll()
        ]);
    }

    // Ajouter un utilisateur
    private function ajouter() {
        $data = $this->lirePost(['nom','prenom','email','mot_de_passe','role','provider_login']);

        $erreurs = $this->valider($data, false);
        if (!empty($erreurs)) {
            $this->json(['succes' => false, 'erreurs' => $erreurs]);
            return;
        }

        if ($this->model->emailExiste($data['email'])) {
            $this->json(['succes' => false, 'message' => 'Cet email est deja utilise.']);
            return;
        }

        if ($this->model->create($data)) {
            $this->json(['succes' => true, 'message' => 'Utilisateur ajoute avec succes.']);
        } else {
            $this->json(['succes' => false, 'message' => 'Erreur lors de l\'ajout.']);
        }
    }

    // Modifier un utilisateur
    private function modifier() {
        $id   = isset($_POST['id_user']) ? (int) $_POST['id_user'] : 0;
        $data = $this->lirePost(['nom','prenom','email','mot_de_passe','role','provider_login']);

        if ($id <= 0) {
            $this->json(['succes' => false, 'message' => 'ID invalide.']);
            return;
        }

        $erreurs = $this->valider($data, true);
        if (!empty($erreurs)) {
            $this->json(['succes' => false, 'erreurs' => $erreurs]);
            return;
        }

        if ($this->model->emailExiste($data['email'], $id)) {
            $this->json(['succes' => false, 'message' => 'Cet email est deja utilise.']);
            return;
        }

        if ($this->model->update($id, $data)) {
            $this->json(['succes' => true, 'message' => 'Utilisateur modifie avec succes.']);
        } else {
            $this->json(['succes' => false, 'message' => 'Erreur lors de la modification.']);
        }
    }

    // Supprimer un utilisateur
    private function supprimer() {
        $id = isset($_POST['id_user']) ? (int) $_POST['id_user'] : 0;

        if ($id <= 0) {
            $this->json(['succes' => false, 'message' => 'ID invalide.']);
            return;
        }

        if ($this->model->delete($id)) {
            $this->json(['succes' => true, 'message' => 'Utilisateur supprime.']);
        } else {
            $this->json(['succes' => false, 'message' => 'Erreur lors de la suppression.']);
        }
    }

    // Statistiques pour le dashboard
    private function stats() {
        $this->json([
            'succes'  => true,
            'total'   => $this->model->count(),
            'parRole' => $this->model->countByRole()
        ]);
    }

    // Lire et nettoyer les champs POST
    private function lirePost($champs) {
        $data = [];
        foreach ($champs as $champ) {
            $data[$champ] = isset($_POST[$champ]) ? trim($_POST[$champ]) : '';
        }
        return $data;
    }

    /**
     * Validation des donnees cote serveur (PHP)
     * $optionalMdp = true en modification (mot de passe optionnel)
     */
    private function valider($data, $optionalMdp = false) {
        $erreurs = [];

        // Validation du nom
        if (empty($data['nom'])) {
            $erreurs['nom'] = 'Le nom est obligatoire.';
        } elseif (strlen($data['nom']) < 2 || strlen($data['nom']) > 50) {
            $erreurs['nom'] = 'Le nom doit contenir entre 2 et 50 caracteres.';
        } elseif (!preg_match('/^[A-Za-z\x{00C0}-\x{024F}\s\-\']+$/u', $data['nom'])) {
            $erreurs['nom'] = 'Le nom ne doit contenir que des lettres.';
        }

        // Validation du prenom
        if (empty($data['prenom'])) {
            $erreurs['prenom'] = 'Le prenom est obligatoire.';
        } elseif (strlen($data['prenom']) < 2 || strlen($data['prenom']) > 50) {
            $erreurs['prenom'] = 'Le prenom doit contenir entre 2 et 50 caracteres.';
        } elseif (!preg_match('/^[A-Za-z\x{00C0}-\x{024F}\s\-\']+$/u', $data['prenom'])) {
            $erreurs['prenom'] = 'Le prenom ne doit contenir que des lettres.';
        }

        // Validation de l'email
        if (empty($data['email'])) {
            $erreurs['email'] = 'L\'email est obligatoire.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'L\'adresse email n\'est pas valide.';
        }

        // Validation du mot de passe
        if (!$optionalMdp || !empty($data['mot_de_passe'])) {
            if (empty($data['mot_de_passe'])) {
                $erreurs['mot_de_passe'] = 'Le mot de passe est obligatoire.';
            } elseif (strlen($data['mot_de_passe']) < 8) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 8 caracteres.';
            } elseif (!preg_match('/[A-Z]/', $data['mot_de_passe'])) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins une majuscule.';
            } elseif (!preg_match('/[0-9]/', $data['mot_de_passe'])) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins un chiffre.';
            }
        }

        // Validation du role
        $rolesValides = ['admin', 'nutritionniste', 'client'];
        if (empty($data['role']) || !in_array($data['role'], $rolesValides)) {
            $erreurs['role'] = 'Veuillez choisir un role valide.';
        }

        // Validation du provider
        $providersValides = ['local', 'google', 'facebook'];
        if (empty($data['provider_login']) || !in_array($data['provider_login'], $providersValides)) {
            $erreurs['provider_login'] = 'Veuillez choisir un provider valide.';
        }

        return $erreurs;
    }

    // Envoyer la reponse en JSON
    private function json($data) {
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Instanciation et lancement du controleur
$controller = new UtilisateurController();
$controller->handle();
