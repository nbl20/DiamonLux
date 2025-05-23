<?php
session_start();
include(__DIR__ . '/../../bdd/bdd.php');
include(__DIR__ . '/../../model/Commande.php');
include(__DIR__ . '/../../model/Panier.php');

$commandeModel = new Commande($bdd);
$panier = new Panier($bdd);

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: ../../index.php?page=connexion');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'valider_panier':
            $articles = $panier->getArticles($userId);

            if (empty($articles)) {
                $_SESSION['error'] = "Votre panier est vide.";
                header('Location: ../../index.php?page=panier');
                exit;
            }

            foreach ($articles as $article) {
                $commandeModel->creerCommande($article['id'], $userId);

                // Mettre l'article en "en_attente"
                $update = $bdd->prepare("UPDATE article SET etat = 'en_attente' WHERE id = ?");
                $update->execute([$article['id']]);

                $panier->retirerDuPanier($userId, $article['id']);
            }

            $_SESSION['success'] = "Commande envoyée. En attente de validation par le vendeur.";
            header('Location: ../../index.php?page=profil');
            exit;

        case 'achat_direct':
            if (!isset($_POST['article_id'])) {
                $_SESSION['error'] = "Article manquant pour l'achat.";
                header('Location: ../../index.php?page=acceuil');
                exit;
            }

            $articleId = (int) $_POST['article_id'];

            // Vérifier l'article
            $check = $bdd->prepare("SELECT proprio, etat FROM article WHERE id = ?");
            $check->execute([$articleId]);
            $article = $check->fetch(PDO::FETCH_ASSOC);

            if (!$article || $article['etat'] !== 'en_vente') {
                $_SESSION['error'] = "Article indisponible.";
                header('Location: ../../index.php?page=acceuil');
                exit;
            }

            if ($article['proprio'] == $userId) {
                $_SESSION['error'] = "Tu ne peux pas acheter ton propre article 😅";
                header('Location: ../../index.php?page=acceuil');
                exit;
            }

            $commandeModel->creerCommande($articleId, $userId);

            // Mettre l'article en "en_attente"
            $bdd->prepare("UPDATE article SET etat = 'en_attente' WHERE id = ?")
                ->execute([$articleId]);

            $panier->retirerDuPanier($userId, $articleId);

            $_SESSION['success'] = "Commande envoyée. En attente de validation par le vendeur.";
            header('Location: ../../index.php?page=profil');
            exit;

        case 'accepter_commande':
            $commandeId = $_POST['commande_id'] ?? null;
            $articleId = $_POST['article_id'] ?? null;

            if ($commandeId && $articleId) {
                $commandeModel->validerCommande($commandeId);
                $commandeModel->marquerArticleCommeVendu($articleId);
                $_SESSION['success'] = "Commande acceptée avec succès.";
            } else {
                $_SESSION['error'] = "Données manquantes.";
            }

            header('Location: ../../index.php?page=valider_vente');
            exit;

        case 'valider_commande':
            $commandeId = $_POST['commande_id'] ?? null;
            $articleId = $_POST['article_id'] ?? null;

            if (!$commandeId || !$articleId) {
                $_SESSION['error'] = "Données manquantes.";
                header('Location: ../../index.php?page=valider_vente');
                exit;
            }

            $commandeModel->validerCommande($commandeId);
            $commandeModel->marquerArticleCommeVendu($articleId);

            $_SESSION['success'] = "Vente validée avec succès ✅";
            header('Location: ../../index.php?page=valider_vente');
            exit;

        default:
            $_SESSION['error'] = "Action inconnue.";
            header('Location: ../../index.php?page=acceuil');
            exit;
    }
}
