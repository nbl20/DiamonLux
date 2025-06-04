<?php

class Panier
{
    private $bdd;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
    }

    // Ajouter un article au panier
    public function ajouterAuPanier($userId, $articleId)
    {
        $sql = "INSERT INTO panier (id_utilisateur, id_article) VALUES (?, ?)";
        $req = $this->bdd->prepare($sql);

        if (!$req->execute([$userId, $articleId])) {
            var_dump("Erreur d'insertion : ", $req->errorInfo());
            exit;
        }

        echo "Ajout réussi dans la BDD";
        return true;
    }



    public function retirerDuPanier($id_utilisateur, $id_article)
    {
        $sql = "DELETE FROM panier WHERE id_utilisateur = :id_utilisateur AND id_article = :id_article";
        $stmt = $this->bdd->prepare($sql);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Récupérer les articles du panier
    public function getArticles($userId)
    {
        $req = $this->bdd->prepare("
            SELECT a.*
            FROM article a
            JOIN panier p ON p.id_article = a.id
            WHERE p.id_utilisateur = ?
        ");
        $req->execute([$userId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
