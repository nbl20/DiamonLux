<?php
if (!isset($_POST['article_id'], $_SESSION['user_id'])) {
    header('Location: index.php?page=article_utilisateur');
    exit;
}

include('./bdd/bdd.php');
include('./model/Article.php');

$articleModel = new Article($bdd);
$article = $articleModel->getArticleById($_POST['article_id']);

if ($article['proprio'] != $_SESSION['user_id']) {
    header('Location: index.php?page=article_utilisateur');
    exit;
}
?>

<link rel="stylesheet" href="public/css/modifier_article.css">

<div class="modif-container">
    <h1>✏️ Modifier l'article</h1>

    <form action="controller/article/articleController.php" method="POST" enctype="multipart/form-data" class="modif-form">
        <input type="hidden" name="action" value="modifier">
        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">

        <div class="form-group">
            <label>Nom :</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($article['nom']) ?>" required>
        </div>

        <div class="form-group">
            <label>Type :</label>
            <input type="text" name="type" value="<?= htmlspecialchars($article['type']) ?>" required>
        </div>

        <div class="form-group">
            <label>Marque :</label>
            <input type="text" name="marque" value="<?= htmlspecialchars($article['marque']) ?>" required>
        </div>

        <div class="form-group">
            <label>Prix (€) :</label>
            <input type="number" name="prix" step="0.01" value="<?= $article['prix'] ?>" required>
        </div>

        <div class="form-group">
            <label>Image (laisser vide si inchangée) :</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <div class="form-actions">
            <button type="submit">💾 Enregistrer les modifications</button>
        </div>
    </form>
</div>