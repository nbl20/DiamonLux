<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=connexion');
    exit;
}

include('./bdd/bdd.php');
include('./model/Panier.php');

$userId = $_SESSION['user_id'];
$panierModel = new Panier($bdd);
$articles = $panierModel->getArticles($userId);

$total = 0;
?>

<link rel="stylesheet" href="public/css/validation_panier.css">

<div class="validation-container">
    <h1>🧾 Validation du panier</h1>

    <?php if (empty($articles)): ?>
        <p class="empty-msg">Votre panier est vide.</p>
    <?php else: ?>
        <div class="panier-content">
            <!-- 🧾 Liste des articles -->
            <div class="article-list">
                <?php foreach ($articles as $article): ?>
                    <div class="article-item">
                        <?php if (!empty($article['image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($article['image']) ?>" alt="image article">
                        <?php endif; ?>
                        <div>
                            <h3><?= htmlspecialchars($article['nom']) ?> - <?= htmlspecialchars($article['marque']) ?></h3>
                            <p>Type : <?= htmlspecialchars($article['type']) ?></p>
                            <p>Prix : <?= htmlspecialchars($article['prix']) ?> €</p>
                        </div>
                    </div>
                    <?php $total += $article['prix']; ?>
                <?php endforeach; ?>
            </div>

            <!-- 💳 Paiement -->
            <div class="recap-section">
                <h2>Total à payer</h2>
                <p class="total-amount"><?= number_format($total, 2) ?> €</p>

                <form action="controller/commande/commandeController.php" method="POST">
                    <input type="hidden" name="action" value="valider_panier">
                    <button type="submit" class="btn-payer">✅ Payer</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>