<?php

class Article
{
    private $bdd;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
    }

    // Ajouter un article
    public function ajouterArticle($userId, $nom, $type, $marque, $prix, $image)
    {
        $req = $this->bdd->prepare("
            INSERT INTO article (proprio, nom, type, marque, prix, image, etat, date_vente)
            VALUES (?, ?, ?, ?, ?, ?, 'en_vente', NOW())
        ");
        return $req->execute([$userId, $nom, $type, $marque, $prix, $image]);
    }

    // Modifier un article
    public function modifierArticle($articleId, $nom, $type, $marque, $prix, $image = null)
    {
        if ($image) {
            $req = $this->bdd->prepare("
                UPDATE article 
                SET nom = ?, type = ?, marque = ?, prix = ?, image = ? 
                WHERE id = ?
            ");
            return $req->execute([$nom, $type, $marque, $prix, $image, $articleId]);
        } else {
            $req = $this->bdd->prepare("
                UPDATE article 
                SET nom = ?, type = ?, marque = ?, prix = ?
                WHERE id = ?
            ");
            return $req->execute([$nom, $type, $marque, $prix, $articleId]);
        }
    }

    // Supprimer un article
    public function supprimerArticle($articleId, $userId)
    {
        $req = $this->bdd->prepare("DELETE FROM article WHERE id = ? AND proprio = ?");
        return $req->execute([$articleId, $userId]);
    }

    // Récupérer un article
    public function getArticleById($articleId)
    {
        $req = $this->bdd->prepare("SELECT * FROM article WHERE id = ?");
        $req->execute([$articleId]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    // Articles d’un utilisateur
    public function getArticlesByUser($userId)
    {
        $req = $this->bdd->prepare("SELECT * FROM article WHERE proprio = ?");
        $req->execute([$userId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Articles paginés pour la page d'accueil
    public function getArticlesPagines($offset, $limit, $type = null, $excludeUserId = null)
    {
        $sql = "SELECT * FROM article WHERE etat = 'en_vente'";
        $params = [];

        if ($type) {
            $sql .= " AND type LIKE ?";
            $params[] = "%$type%";
        }

        if ($excludeUserId) {
            $sql .= " AND proprio != ?";
            $params[] = $excludeUserId;
        }

        $sql .= " ORDER BY date_vente DESC LIMIT $offset, $limit";
        $req = $this->bdd->prepare($sql);
        $req->execute($params);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countArticles($type = null)
    {
        $sql = "SELECT COUNT(*) FROM article WHERE etat = 'en_vente'";
        $params = [];

        if ($type) {
            $sql .= " AND type LIKE ?";
            $params[] = "%$type%";
        }

        $req = $this->bdd->prepare($sql);
        $req->execute($params);

        return $req->fetchColumn();
    }

    // Ventes validées
    public function getVentesByUser($userId)
    {
        $sql = "
            SELECT a.*, c.date_achat
            FROM article a
            JOIN commande c ON c.id_article = a.id
            WHERE a.proprio = ?
              AND c.statut = 'valider'
        ";
        $req = $this->bdd->prepare($sql);
        $req->execute([$userId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Articles en attente à valider
    public function getArticlesEnAttenteByProprio($userId)
    {
        $sql = "
            SELECT a.*, c.id AS commande_id, c.date_achat, c.id_acheteur
            FROM article a
            JOIN commande c ON c.id_article = a.id
            WHERE a.proprio = ?
              AND a.etat = 'en_attente'
              AND c.statut = 'en_attente'
        ";
        $req = $this->bdd->prepare($sql);
        $req->execute([$userId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Validation vente
    public function validerVente($articleId, $vendeurId)
    {
        $sql = "SELECT proprio FROM article WHERE id = ?";
        $req = $this->bdd->prepare($sql);
        $req->execute([$articleId]);
        $article = $req->fetch(PDO::FETCH_ASSOC);

        if (!$article || $article['proprio'] != $vendeurId) {
            return false;
        }

        $this->bdd->prepare("UPDATE article SET etat = 'vendu' WHERE id = ?")->execute([$articleId]);
        $this->bdd->prepare("UPDATE commande SET statut = 'valider' WHERE id_article = ? AND statut = 'en_attente'")->execute([$articleId]);

        return true;
    }
}
