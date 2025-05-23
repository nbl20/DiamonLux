<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=connexion');
    exit;
}
?>

<link rel="stylesheet" href="./public/css/modifprofil.css">

<div class="modif-container">
    <h1>📝 Modifier mon profil</h1>

    <form action="controller/users/usersController.php" method="POST" enctype="multipart/form-data" class="modif-form">
        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($_SESSION['user_nom']) ?>" required>
        </div>

        <div class="form-group">
            <label>Prénom</label>
            <input type="text" name="prenom" value="<?= htmlspecialchars($_SESSION['user_prenom']) ?>" required>
        </div>

        <div class="form-group">
            <label>Adresse</label>
            <input type="text" name="adresse" value="<?= htmlspecialchars($_SESSION['user_adresse']) ?>" required>
        </div>

        <div class="form-group">
            <label>Code Postal</label>
            <input type="text" name="code_postal" value="<?= htmlspecialchars($_SESSION['user_code_postal']) ?>" required>
        </div>

        <div class="form-group">
            <label>Téléphone</label>
            <input type="text" name="num_phone" value="<?= htmlspecialchars($_SESSION['user_phone']) ?>" required>
        </div>

        <div class="form-group">
            <label>Nouvelle image de profil</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <input type="hidden" name="action" value="update">

        <div class="form-actions">
            <button type="submit">💾 Enregistrer les modifications</button>
        </div>
    </form>
</div>