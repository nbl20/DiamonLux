<?php
session_start();
include('../../bdd/bdd.php');
include('../../model/panier.php');

$panier = new Panier($bdd);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $articleId = $_POST['article_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId || !$articleId) {
        echo "Erreur : données manquantes.";
        exit;
    }

    switch ($action) {
        case 'ajouter':
            if ($panier->ajouterAuPanier($userId, $articleId)) {
                header('Location: ../../index.php?page=panier');
                exit;
            } else {
                echo "Erreur lors de l'ajout au panier.";
            }
            break;

        case 'retirer':
            if ($panier->retirerDuPanier($userId, $articleId)) {
                header('Location: ../../index.php?page=panier');
                exit;
            } else {
                echo "Erreur lors de la suppression.";
            }
            break;

        case 'achat_direct':
            // Vider le panier pour qu'il ne contienne que l'article acheté
            $bdd->prepare("DELETE FROM panier WHERE id_utilisateur = ?")->execute([$userId]);

            // Ajouter l'article au panier
            $panier->ajouterAuPanier($userId, $articleId);

            // Rediriger vers validation du panier
            header('Location: ../../index.php?page=validation_panier');
            exit;
            break;

        default:
            echo "Action non reconnue.";
            break;
    }
}
