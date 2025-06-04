<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../../model/Users.php');
include('../../bdd/bdd.php');

if (isset($_POST['action'])) {
    $userController = new UserController($bdd);

    switch ($_POST['action']) {
        case 'inscription':
            $userController->create();
            break;
        case 'login':
            $userController->login();
            break;
        case 'update':
            $userController->update();
            break;
    }
}

class UserController
{
    private $user;

    function __construct($bdd)
    {
        $this->user = new User($bdd);
    }

    /**
     * Inscription d'un utilisateur
     */
    public function create()
    {
        // Vérification des champs obligatoires
        if (!isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['password'], $_POST['num_phone'], $_POST['date_naissance'], $_POST['ville'], $_POST['adresse'])) {
            die("Tous les champs sont obligatoires.");
        }

        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $mdp = $_POST['password'];
        $num_phone = $_POST['num_phone'];
        $date_naissance = $_POST['date_naissance'];
        $ville = $_POST['ville'];
        $adresse = $_POST['adresse'];
        $code_postal = $_POST['code_postal'] ?? null;
        $statut = 'actif';
        $droits = 'client'; // Rôle par défaut

        // Gestion de l’image (stockage en BLOB)
        $imageBlob = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $imageTmpName = $_FILES['image']['tmp_name'];
            $imageBlob = file_get_contents($imageTmpName); // Stockage en BLOB
        }

        // Ajouter l'utilisateur
        $this->user->ajouterUtilisateur($nom, $prenom, $email, $mdp, $num_phone, $date_naissance, $ville, $adresse, $code_postal, $imageBlob, $statut, $droits);

        // Redirection après l'inscription
        header("Location: /index.php?page=connexion");
        exit();
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login()
    {
        if (!isset($_POST['email'], $_POST['password'])) {
            die("Veuillez remplir le formulaire.");
        }

        // Vérification de l'utilisateur
        $user = $this->user->checklogin($_POST['email'], $_POST['password']);

        if ($user) {
            // Stockage propre des données utilisateur en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'] ?? 'Nom inconnu';
            $_SESSION['user_prenom'] = $user['prenom'] ?? 'Prénom inconnu';
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_ville'] = $user['ville'] ?? 'Ville inconnue';
            $_SESSION['user_adresse'] = $user['adresse'] ?? 'Adresse inconnue';
            $_SESSION['user_code_postal'] = $user['code_postal'] ?? 'Code postal inconnu';
            $_SESSION['user_phone'] = $user['num_phone'] ?? 'Numéro inconnu';
            $_SESSION['user_role'] = $user['droits'];

            // Récupérer l'image en BLOB depuis la base de données et convertir en Base64
            $imageBlob = $this->user->getUserImage($user['id']);

            if ($imageBlob) {
                $_SESSION['user_image'] = 'data:image/jpeg;base64,' . base64_encode($imageBlob);
            } else {
                $_SESSION['user_image'] = './public/images/default-avatar.png'; // Image par défaut
            }

            //var_dump($_SESSION);
            //die;
            // Rediriger vers la page profil
            header('Location: ../../index.php');
            exit;
        } else {
            die("Email ou mot de passe incorrect.");
        }
    }
    /**
     * Modifier les informations d'un utilisateur
     */
    public function update()
    {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            die("Utilisateur non connecté.");
        }

        // Vérification des champs requis
        if (!isset($_POST['nom'], $_POST['prenom'], $_POST['adresse'], $_POST['code_postal'], $_POST['num_phone'])) {
            die("Tous les champs sont obligatoires.");
        }

        // Récupérer les données du formulaire
        $user_id = $_SESSION['user_id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse = $_POST['adresse'];
        $code_postal = $_POST['code_postal'];
        $num_phone = $_POST['num_phone'];
        $imageBlob = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $imageTmpName = $_FILES['image']['tmp_name'];
            $imageBlob = file_get_contents($imageTmpName);
        }

        // Mise à jour des informations de l'utilisateur
        $success = $this->user->modifierUtilisateur($user_id, $nom, $prenom, $adresse, $code_postal, $num_phone, $imageBlob);


        if ($success) {
            // Mettre à jour les données en session
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_adresse'] = $adresse;
            $_SESSION['user_code_postal'] = $code_postal;
            $_SESSION['user_phone'] = $num_phone;

            // Si l'image a été modifiée, on la met à jour aussi
            if ($imageBlob) {
                $_SESSION['user_image'] = 'data:image/jpeg;base64,' . base64_encode($imageBlob);
            }

            $_SESSION['success'] = "Profil mis à jour avec succès.";
            header('Location: ../../index.php?page=profil');
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour.";
            header('Location: ../../index.php?page=modifprofil');
            exit;
        }
    }
}
