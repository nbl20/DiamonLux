<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=connexion');
    exit;
}

$userId = $_SESSION['user_id'];

include('./bdd/bdd.php');
include('./model/Panier.php');
$panierModel = new Panier($bdd);
$articles = $panierModel->getArticles($userId);

// Debug (à commenter ou supprimer en prod)
// echo "<pre>";
// echo "User ID : " . $userId . "\n";
// var_dump($articles);
// echo "</pre>";
?>

<link rel="stylesheet" href="public/css/panier.css?v=2">


<div class="panier-container">
    <h1>🛒 Mon Panie</h1>

    <!-- ✅ Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-msg"><?= $_SESSION['success'];
                                    unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-msg"><?= $_SESSION['error'];
                                unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- ✅ Contenu -->
    <?php if (empty($articles)): ?>
        <p class="empty-msg">Votre panier est vide.</p>
    <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <?php if (!empty($article['image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($article['image']) ?>" alt="Image article">
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($article['nom']) ?> - <?= htmlspecialchars($article['marque']) ?></h3>
                    <p><strong>Type :</strong> <?= htmlspecialchars($article['type']) ?></p>
                    <p><strong>Prix :</strong> <?= htmlspecialchars($article['prix']) ?> €</p>

                    <form method="POST" action="controller/panier/panierController.php">
                        <input type="hidden" name="action" value="retirer">
                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                        <button type="submit" class="btn-retirer">🗑 Retirer</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="validate-btn-container">
            <a href="index.php?page=validation_panier" class="btn-valider">✅ Valider mon panier</a>
        </div>
    <?php endif; ?>
</div>