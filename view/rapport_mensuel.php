<?php
// Inclure la connexion à la base de données
require_once('../../bdd/bdd.php');

// Inclure la bibliothèque FPDF (à placer dans lib/fpdf/fpdf.php)
require_once('../../lib/fpdf/fpdf.php');

// Démarrer la session pour récupérer l'ID client connecté
session_start();

// Vérifier que le client est connecté
if (!isset($_SESSION['id_client'])) {
    die("Accès refusé : veuillez vous connecter.");
}

$id_client = $_SESSION['id_client'];

// Préparer la requête SQL pour récupérer les commandes du client
$sql = "
    SELECT 
        MONTH(c.date_commande) AS mois,
        YEAR(c.date_commande) AS annee,
        c.id_commande,
        a.nom AS article,
        c.statut
    FROM commande c
    JOIN commande_article ca ON c.id_commande = ca.id_commande
    JOIN article a ON ca.id_article = a.id_article
    WHERE c.id_client = ?
    ORDER BY annee DESC, mois DESC, c.date_commande DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_client]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Créer le PDF avec FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Rapport Mensuel des Commandes', 0, 1, 'C');
$pdf->Ln(10);

// Variables pour organiser par mois
$currentMonth = '';
foreach ($commandes as $cmd) {
    $mois = $cmd['mois'] . '/' . $cmd['annee'];
    if ($mois !== $currentMonth) {
        $currentMonth = $mois;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Mois : $mois", 0, 1);
    }
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, "Commande #{$cmd['id_commande']} - Article : {$cmd['article']} - Statut : {$cmd['statut']}", 0, 1);
}

$pdf->Output('D', 'rapport_commandes.pdf');
exit;
