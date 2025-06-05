<?php
if (!isset($_GET['id'])) {
    echo "Article introuvable.";
    exit;
}

include('./bdd/bdd.php');
include('./model/Article.php');
include('./model/Users.php');

$articleModel = new Article($bdd);
$userModel = new User($bdd);

$articleId = (int) $_GET['id'];
$article = $articleModel->getArticleById($articleId);

if (!$article) {
    echo "Article non trouvé.";
    exit;
}

$vendeur = $userModel->getUserById($article['proprio']);
?>

<link rel="stylesheet" href="public/css/article.css">

<div class="article-container">
    <h1><?= htmlspecialchars($article['nom']) ?></h1>

    <div class="article-content">
        <!-- Image -->
        <div class="article-image">
            <?php if (!empty($article['image'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($article['image']) ?>" alt="Image article">
            <?php endif; ?>
        </div>

        <!-- Détails -->
        <div class="article-details">
            <p><strong>Type :</strong> <?= htmlspecialchars($article['type']) ?></p>
            <p><strong>Marque :</strong> <?= htmlspecialchars($article['marque']) ?></p>
            <p><strong>Prix :</strong> <span class="prix"><?= htmlspecialchars($article['prix']) ?> €</span></p>
            <p><strong>État :</strong> <?= htmlspecialchars($article['etat']) ?></p>
            <p><strong>Date de mise en vente :</strong> <?= htmlspecialchars($article['date_vente']) ?></p>
            <p><strong>Vendu par :</strong> <?= htmlspecialchars($vendeur['prenom'] . ' ' . $vendeur['nom']) ?></p>

            <div class="article-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Ajouter au panier -->
                    <form method="POST" action="controller/panier/panierController.php">
                        <input type="hidden" name="action" value="ajouter">
                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                        <button type="submit" class="btn-panier">🛒 Ajouter au panier</button>
                    </form>

                    <!-- Acheter maintenant -->
                    <form method="POST" action="controller/panier/panierController.php">
                        <input type="hidden" name="action" value="achat_direct">
                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                        <button type="submit" class="btn-acheter">💳 Acheter maintenant</button>
                    </form>
                <?php else: ?>
                    <a href="index.php?page=connexion" class="btn-panier">🛒 Connectez-vous pour acheter</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
</div>