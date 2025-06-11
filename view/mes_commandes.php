<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=connexion');
    exit;
}

include('./bdd/bdd.php');

$userId = $_SESSION['user_id'];
$req = $bdd->prepare("
    SELECT c.*, a.nom AS article_nom, a.marque, a.prix, a.image
    FROM commande c
    JOIN article a ON a.id = c.id_article
    WHERE c.id_acheteur = ?
    ORDER BY c.date_achat DESC
");
$req->execute([$userId]);
$commandes = $req->fetchAll(PDO::FETCH_ASSOC);

$totalDepense = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link rel="stylesheet" href="public/css/mes_commandes.css">
    <style>
        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.03);
        }

        .commande-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .commande-card {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            width: 300px;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
        }

        .commande-card img {
            max-width: 100%;
            border-radius: 4px;
        }

        .total-depense {
            margin-top: 30px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>📦 Mes Achats</h1>

    <!-- 📄 Bouton de génération PDF -->
    <a href="rapport_mensuel.php" class="btn">📄 Télécharger le rapport PDF</a>

    <?php if (empty($commandes)): ?>
        <p>Vous n'avez encore rien acheté.</p>
    <?php else: ?>
        <div class="commande-grid">
            <?php foreach ($commandes as $commande): ?>
                <?php $totalDepense += $commande['prix']; ?>
                <div class="commande-card">
                    <?php if (!empty($commande['image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($commande['image']) ?>" alt="image article">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($commande['article_nom']) ?></h3>
                    <p><strong>Marque :</strong> <?= htmlspecialchars($commande['marque']) ?></p>
                    <p><strong>Prix :</strong> <?= htmlspecialchars($commande['prix']) ?> €</p>
                    <p><strong>Acheté le :</strong> <?= date('d/m/Y', strtotime($commande['date_achat'])) ?></p>
                    <p><strong>Statut :</strong> <?= htmlspecialchars($commande['statut']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="total-depense">💰 Total dépensé : <?= number_format($totalDepense, 2) ?> €</h2>
    <?php endif; ?>

</body>
</html>
