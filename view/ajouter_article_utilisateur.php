<?php
// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=connexion');
    exit;
}
?>

<link rel="stylesheet" href="public/css/ajouter_article.css">

<div class="ajout-container">
    <h1>🛍 Ajouter un article</h1>

    <form action="controller/article/articleController.php" method="POST" enctype="multipart/form-data" class="ajout-form">
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" name="nom" required>
        </div>

        <div class="form-group">
            <label for="type">Type</label>
            <input type="text" name="type" required>
        </div>

        <div class="form-group">
            <label for="marque">Marque</label>
            <input type="text" name="marque" required>
        </div>

        <div class="form-group">
            <label for="prix">Prix (€)</label>
            <input type="number" name="prix" step="0.01" required>
        </div>


        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" name="image" accept="image/*" required>
        </div>

        <input type="hidden" name="action" value="ajouter">

        <div class="form-actions">
            <button type="submit">✅ Ajouter l'article</button>
        </div>
    </form>
</div>