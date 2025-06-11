<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=connexion');
    exit;
}

include('./bdd/bdd.php');
include('./model/Article.php');
include('./model/Commande.php'); // 🔹 Ajout pour utiliser la fonction
$articleModel = new Article($bdd);
$commandeModel = new Commande($bdd);

$articles = $articleModel->getArticlesByUser($_SESSION['user_id']);
$mois = date('m');
$annee = date('Y');
?>

<link rel="stylesheet" href="public/css/mes_articles.css">
<main>
    <div class="container-articles">
        <h1>🛍️ Mes articles en vente</h1>

        <?php if (count($articles) === 0): ?>
            <p class="empty-message">Vous n'avez encore publié aucun article.</p>
        <?php else: ?>
            <div class="articles-grid">
                <?php foreach ($articles as $article): ?>
                    <div class="article-card">
                        <?php if (!empty($article['image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($article['image']) ?>" alt="image" class="article-image">
                        <?php endif; ?>

                        <div class="article-info">
                            <h3><?= htmlspecialchars($article['nom']) ?> - <?= htmlspecialchars($article['marque']) ?></h3>
                            <p><strong>Type :</strong> <?= htmlspecialchars($article['type']) ?></p>
                            <p><strong>Prix :</strong> <?= htmlspecialchars($article['prix']) ?> €</p>
                            <p><strong>État :</strong> <?= htmlspecialchars($article['etat']) ?></p>
                        </div>

                        <!-- ✅ Liste des clients ayant acheté cet article ce mois -->
                        <div class="clients-acheteurs">
                            <h4>👥 Acheté ce mois-ci par :</h4>
                            <ul>
                                <?php
                                $clients = $commandeModel->getClientsParArticleEtMois($article['id'], $mois, $annee);
                                if (!empty($clients)):
                                    foreach ($clients as $client): ?>
                                        <li><?= htmlspecialchars($client['prenom']) ?> <?= htmlspecialchars($client['nom']) ?></li>
                                    <?php endforeach;
                                else: ?>
                                    <li>Aucun client ce mois</li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- 🔧 Actions -->
                        <div class="actions">
                            <form action="controller/article/articleController.php" method="POST" onsubmit="return confirm('Confirmer la suppression ?');">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                <button type="submit" class="delete-btn">🗑️ Supprimer</button>
                            </form>

                            <form action="index.php?page=modifier_article" method="POST">
                                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                <button type="submit" class="edit-btn">✏️ Modifier</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
