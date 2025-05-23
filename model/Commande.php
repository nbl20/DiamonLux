<?php
// ===== model/Commande.php =====
class Commande
{
    private $bdd;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
    }

    // Créer une nouvelle commande (en attente)
    public function creerCommande($id_article, $id_acheteur)
    {
        $stmt = $this->bdd->prepare("INSERT INTO commande (id_article, id_acheteur, date_achat, statut) VALUES (?, ?, NOW(), 'en_attente')");
        $stmt->execute([$id_article, $id_acheteur]);

        // Mettre à jour l'article comme en attente
        $this->bdd->prepare("UPDATE article SET etat = 'en_attente' WHERE id = ?")->execute([$id_article]);
    }

    // Récupérer les commandes passées par un utilisateur
    public function getCommandesByAcheteur($userId)
    {
        $sql = "
            SELECT c.*, a.nom AS article_nom, a.image, a.prix, a.marque
            FROM commande c
            JOIN article a ON c.id_article = a.id
            WHERE c.id_acheteur = ?
            ORDER BY c.date_achat DESC
        ";
        $stmt = $this->bdd->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les commandes reçues pour un vendeur
    public function getCommandesNonVuesPourVendeur($vendeurId)
    {
        $sql = "
            SELECT 
                c.*, 
                u.nom AS acheteur_nom, 
                u.prenom AS acheteur_prenom, 
                a.nom AS article_nom,
                a.prix,
                a.image
            FROM commande c
            JOIN article a ON c.id_article = a.id
            JOIN user u ON c.id_acheteur = u.id
            WHERE a.proprio = :vendeurId
              AND c.statut = 'en_attente'
        ";

        $stmt = $this->bdd->prepare($sql);
        $stmt->bindParam(':vendeurId', $vendeurId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Valider une commande (changer son statut)
    public function validerCommande($commandeId)
    {
        $stmt = $this->bdd->prepare("UPDATE commande SET statut = 'valider' WHERE id = ?");
        return $stmt->execute([$commandeId]);
    }

    // Marquer un article comme vendu
    public function marquerArticleCommeVendu($articleId)
    {
        $stmt = $this->bdd->prepare("UPDATE article SET etat = 'vendu' WHERE id = ?");
        return $stmt->execute([$articleId]);
    }
    public function getVentesValideesByVendeur($vendeurId)
    {
        $sql = "
        SELECT c.*, a.nom, a.marque, a.prix, a.image, u.id AS id_acheteur
        FROM commande c
        JOIN article a ON c.id_article = a.id
        JOIN user u ON c.id_acheteur = u.id
        WHERE a.proprio = ? AND c.statut = 'valider'
        ORDER BY c.date_achat DESC
    ";

        $stmt = $this->bdd->prepare($sql);
        $stmt->execute([$vendeurId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
