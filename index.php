<?php
ob_start(); // Démarre la mise en mémoire tampon
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Toujours définir $page AVANT de l'utiliser
$page = isset($_GET['page']) ? $_GET['page'] : 'acceuil';

// Déconnexion immédiate (avant d'afficher quoi que ce soit)
if ($page === 'deconnexion') {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
    header('Location: index.php?page=acceuil');
    exit();
}

// Ne pas afficher le header sur certaines pages
if (!in_array($page, ['connexion', 'inscription'])) {
    include('./view/commun/header.php');
}

switch ($page) {
    // Authentification
    case 'connexion':
        include('view/authentification/connexion.php');
        break;
    case 'inscription':
        include('view/authentification/inscription.php');
        break;

    // Pages publiques protégées par erreur ? (à corriger si besoin)
    case 'evenement':
    case 'nouveaute':
    case 'message':

    case 'commentaire':
        //page panier
    case 'panier':
        include('view/panier.php');
        break;
    case 'validation_panier':
        include('view/validation_panier.php');
        break;
    case 'valider_commande':
        include('controller/commande/commandeController.php');
        break;
    case 'mes_commandes':
        include('view/mes_commandes.php');
        break;
    case 'mes_ventes':
        include('view/mes_ventes.php');
        break;
    case 'valider_vente':
        include('view/valider_vente.php');
        break;



    //page article
    case 'ajouter_article_utilisateur':
        include('view/ajouter_article_utilisateur.php');
        break;
    case 'article_utilisateur':
        include('view/article_utilisateur.php');
        break;
    case 'modifier_article':
        include('view/modifier_article.php');
        break;
    case 'article':
        include('view/article.php');
        break;




    // Pages profil
    case 'profil':
        include('view/profil.php');
        break;
    case 'modifprofil':
        include('view/modifprofil.php');
        break;

    // Page par défaut
    default:
        include('view/accueil.php');
        break;
}

// Footer sauf sur certaines pages
if (!in_array($page, ['connexion', 'inscription'])) {
    include('view/commun/footer.php');
}
ob_end_flush(); //Envoie tout le contenu tamponné au navigateur