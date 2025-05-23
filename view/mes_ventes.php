<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=connexion');
    exit;
}

include('./bdd/bdd.php');
include('./model/Article.php');
include_once('./model/Commande.php');
include('./model/Users.php');

$userId = $_SESSION['user_id'];
$articleModel = new Article($bdd);
$commandeModel = new Commande($bdd);
$userModel = new User($bdd);
$ventes = $commandeModel->getVentesValideesByVendeur($userId);

$totalGagne = 0;
?>

<link rel="stylesheet" href="public/css/mes_ventes.css">

<div class="container-ventes">
    <h1>📊 Mes Ventes Finalisées</h1>

    <?php if (empty($ventes)): ?>
        <p class="empty-msg">Vous n'avez encore vendu aucun article.</p>
    <?php else: ?>
        <table class="vente-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Marque</th>
                    <th>Prix (€)</th>
                    <th>Acheté par</th>
                    <th>Date de vente</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventes as $vente): ?>
                    <?php
                    $totalGagne += $vente['prix'];
                    $acheteur = $userModel->getUserById($vente['id_acheteur']);
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($vente['image'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($vente['image']) ?>" alt="img">
                            <?php else: ?>
                                <span>❌</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($vente['nom']) ?></td>
                        <td><?= htmlspecialchars($vente['marque']) ?></td>
                        <td><?= htmlspecialchars($vente['prix']) ?> €</td>
                        <td><?= htmlspecialchars($acheteur['prenom'] ?? '') . ' ' . htmlspecialchars($acheteur['nom'] ?? '') ?></td>
                        <td><?= htmlspecialchars($vente['date_achat']) ?></td>
                        <td><span class="badge">✅ Vendu</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            💰 Total gagné : <strong><?= number_format($totalGagne, 2) ?> €</strong>
        </div>
    <?php endif; ?>
</div>