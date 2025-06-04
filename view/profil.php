<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=connexion');
    exit;
}

$user_image = $_SESSION['user_image'] ?? './public/images/default-avatar.png';
$user_nom = $_SESSION['user_nom'] ?? 'Nom inconnu';
$user_prenom = $_SESSION['user_prenom'] ?? 'Prénom inconnu';
$user_email = $_SESSION['user_email'] ?? 'Email inconnu';
$user_ville = $_SESSION['user_ville'] ?? 'Ville inconnue';
$user_adresse = $_SESSION['user_adresse'] ?? 'Adresse inconnue';
$user_code_postal = $_SESSION['user_code_postal'] ?? 'Code postal inconnu';
$user_phone = $_SESSION['user_phone'] ?? 'Numéro inconnu';
$user_role = $_SESSION['user_role'] ?? 'Rôle inconnu';
?>
<link rel="stylesheet" href="public/css/profil.css">


<main>
    <div class="profil-container">
        <h1>Mon Profil</h1>
        <div class="profil-info">
            <img src="<?= $user_image ?>" alt="Image de profil" class="profil-image">
            <div class="profil-details">
                <p><strong>Nom:</strong> <?= htmlspecialchars($user_nom) ?></p>
                <p><strong>Prénom:</strong> <?= htmlspecialchars($user_prenom) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user_email) ?></p>
                <p><strong>Adresse:</strong> <?= htmlspecialchars($user_ville . ', ' . $user_adresse) ?></p>
                <p><strong>Code Postal:</strong> <?= htmlspecialchars($user_code_postal) ?></p>
                <p><strong>Téléphone:</strong> <?= htmlspecialchars($user_phone) ?></p>
            </div>
        </div>

        <div class="profil-actions">
            <a href="index.php?page=modifprofil">Modifier le profil</a>
            <a href="index.php?page=deconnexion">Se déconnecter</a>
        </div>
    </div>

    <div class="extra-sections">
        <h2>Mes Articles</h2>
        <a href="index.php?page=ajouter_article_utilisateur">Ajouter un article</a>
        <a href="index.php?page=article_utilisateur">Regarder mes articles</a>

        <h2>Mes ventes</h2>
        <a href="index.php?page=mes_ventes">Voir mes ventes</a>
        <a href="index.php?page=valider_vente">Valider une vente</a>

        <?php
        if ($user_role === 'client') {
            include('./bdd/bdd.php');
            include_once('./model/Commande.php');

            $commandeModel = new Commande($bdd);
            $notifications = $commandeModel->getCommandesNonVuesPourVendeur($_SESSION['user_id']);

            if (count($notifications) > 0) {
                echo '<div style="background-color: #fffae6; padding: 10px; margin: 15px 0; color: #d35400; border: 1px solid #f39c12;">
                    🔔 Vous avez ' . count($notifications) . ' nouvelle(s) commande(s) à valider ! <a href="index.php?page=valider_vente">Voir</a>
                </div>';
            }
        }
        ?>

        <h2>Mes commandes</h2>
        <a href="index.php?page=mes_commandes">Voir mes commandes</a>
    </div>

    <?php if ($user_role === 'admin' || $user_role === 'SuperAdmin'): ?>
        <div class="admin-section extra-sections">
            <h2><a href="index.php?page=admin">Page d'administration</a></h2>
        </div>
    <?php endif; ?>
</main>