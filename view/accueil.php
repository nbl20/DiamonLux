<?php
include('./bdd/bdd.php');
include('./model/Article.php');

$articleModel = new Article($bdd);

// Requête de recherche
$searchType = $_GET['search'] ?? null;

// Pagination
$page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Nombre total d'articles
$totalArticles = $articleModel->countArticles($searchType);
$totalPages = ceil($totalArticles / $limit);

// Récupération des articles à afficher
$userId = $_SESSION['user_id'] ?? null;
$articles = $articleModel->getArticlesPagines($offset, $limit, $searchType, $userId);
?>

<!-- ✅ Lien CSS pour styliser l'accueil -->
<link rel="stylesheet" href="public/css/accueil.css?v=2">

<div class="accueil-container">
    <h1 class="titre-accueil">Articles en vente</h1>

    <!-- 🔍 Barre de recherche -->
    <form method="GET" action="index.php" class="search-form">
        <input type="hidden" name="page" value="acceuil">
        <input type="text" name="search" placeholder="Rechercher par type..." value="<?= htmlspecialchars($searchType ?? '') ?>">
        <button type="submit">🔍</button>
    </form>

    <!-- 🧾 Liste des articles -->
    <?php if (count($articles) === 0): ?>
        <p class="no-result">Aucun article trouvé.</p>
    <?php else: ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <a href="index.php?page=article&id=<?= $article['id'] ?>" class="article-card">
                    <?php if (!empty($article['image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($article['image']) ?>" alt="image article">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($article['nom']) ?> - <?= htmlspecialchars($article['marque']) ?></h3>
                    <p><strong>Type :</strong> <?= htmlspecialchars($article['type']) ?></p>
                    <p><strong>Prix :</strong> <?= htmlspecialchars($article['prix']) ?> €</p>
                    <p><strong>État :</strong> <?= htmlspecialchars($article['etat']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 🔁 Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?page=acceuil&p=<?= $i ?>&search=<?= isset($searchType) ? urlencode($searchType) : '' ?>" class="<?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>