<?php
session_start();
include_once('../../bdd/bdd.php');
include_once('../../model/Article.php');

$articleModel = new Article($bdd);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'ajouter':
            ajouterArticle($articleModel);
            break;

        case 'modifier':
            modifierArticle($articleModel);
            break;

        case 'supprimer':
            supprimerArticle($articleModel);
            break;
    }
}

// ✅ Ajouter un article
function ajouterArticle($articleModel)
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../index.php?page=connexion');
        exit;
    }

    $requiredFields = ['nom', 'type', 'marque', 'prix'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Tous les champs sont obligatoires.";
            header('Location: ../../index.php?page=ajouter_article_utilisateur');
            exit;
        }
    }

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    $articleModel->ajouterArticle(
        $_SESSION['user_id'],
        $_POST['nom'],
        $_POST['type'],
        $_POST['marque'],
        $_POST['prix'],
        $image
    );

    $_SESSION['success'] = "Article ajouté avec succès.";
    header('Location: ../../index.php?page=article_utilisateur');
    exit;
}

// ✏️ Modifier un article
function modifierArticle($articleModel)
{
    if (!isset($_SESSION['user_id'], $_POST['article_id'])) {
        $_SESSION['error'] = "Accès non autorisé.";
        header('Location: ../../index.php?page=article_utilisateur');
        exit;
    }

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    $articleModel->modifierArticle(
        $_POST['article_id'],
        $_POST['nom'],
        $_POST['type'],
        $_POST['marque'],
        $_POST['prix'],
        $image
    );

    $_SESSION['success'] = "Article modifié avec succès.";
    header('Location: ../../index.php?page=article_utilisateur');
    exit;
}

// ❌ Supprimer un article
function supprimerArticle($articleModel)
{
    if (!isset($_SESSION['user_id'], $_POST['article_id'])) {
        $_SESSION['error'] = "Données manquantes.";
        header('Location: ../../index.php?page=article_utilisateur');
        exit;
    }

    $article = $articleModel->getArticleById($_POST['article_id']);
    if (!$article || $article['proprio'] != $_SESSION['user_id']) {
        $_SESSION['error'] = "Accès interdit.";
        header('Location: ../../index.php?page=article_utilisateur');
        exit;
    }

    $articleModel->supprimerArticle($_POST['article_id']);
    $_SESSION['success'] = "Article supprimé avec succès.";
    header('Location: ../../index.php?page=article_utilisateur');
    exit;
}
