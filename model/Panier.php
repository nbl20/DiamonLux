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
        $req = $this->bdd->prepare("
        INSERT INTO panier (id_utilisateur, id_article)
        VALUES (?, ?)
    ");

        $result = $req->execute([$userId, $articleId]);

        if (!$result) {
            echo "<pre>";
            print_r($req->errorInfo());
            echo "</pre>";
            exit;
        }

        return $result;
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
