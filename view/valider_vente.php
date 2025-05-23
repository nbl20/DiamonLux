<?php
include('./bdd/bdd.php');
include_once('./model/Commande.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=connexion');
    exit;
}

$userId = $_SESSION['user_id'];
$commandeModel = new Commande($bdd);
$commandes = $commandeModel->getCommandesNonVuesPourVendeur($userId);
?>

<link rel="stylesheet" href="public/css/valider_vente.css">

<div class="container-valider">
    <h1>✅ Valider les ventes en attente</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success"><?= $_SESSION['success'];
                                        unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error"><?= $_SESSION['error'];
                                    unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (empty($commandes)): ?>
        <p class="no-result">Aucune vente à valider pour le moment.</p>
    <?php else: ?>
        <div class="cards-wrapper">
            <?php foreach ($commandes as $commande): ?>
                <div class="commande-card">
                    <?php if (!empty($commande['image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($commande['image']) ?>" alt="image article">
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($commande['article_nom']) ?></h3>
                    <p><strong>Prix :</strong> <?= htmlspecialchars($commande['prix']) ?> €</p>
                    <p><strong>Commandé par :</strong> <?= htmlspecialchars($commande['acheteur_prenom'] . ' ' . $commande['acheteur_nom']) ?></p>
                    <p><strong>Date :</strong> <?= htmlspecialchars($commande['date_achat']) ?></p>

                    <form method="POST" action="controller/commande/commandeController.php">
                        <input type="hidden" name="action" value="valider_commande">
                        <input type="hidden" name="commande_id" value="<?= $commande['id'] ?>">
                        <input type="hidden" name="article_id" value="<?= $commande['id_article'] ?>">
                        <button type="submit" class="btn-valider">✅ Valider la vente</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>