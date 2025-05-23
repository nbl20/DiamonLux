<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'inscription</title>
    <link rel="stylesheet" href="./public/css/inscription.css">
</head>

<body>
    <div>
        <h1>Inscription</h1>

        <!-- Formulaire modifié avec l'attribut enctype pour l'upload d'image -->
        <form action="controller/users/usersController.php" method="POST" enctype="multipart/form-data">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required><br>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required><br>

            <label for="date_naissance">Date de naissance</label>
            <input type="date" id="date_naissance" name="date_naissance" required><br>

            <label for="ville">Ville</label>
            <input type="text" id="ville" name="ville" required><br>

            <label for="adresse">Adresse</label>
            <input type="text" id="adresse" name="adresse" required><br>

            <label for="code_postal">Code postal </label>
            <input type="text" id="code_postal" name="code_postal" required><br>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required><br>

            <label for="num_phone">Numéro de téléphone</label>
            <input type="text" id="num_phone" name="num_phone" required><br>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required><br>

            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*" required><br>

            <input type="hidden" name="role" value="client">
            <input type="hidden" name="action" value="inscription">
            <input type="submit" value="S'inscrire">
        </form>
    </div>
</body>

</html>