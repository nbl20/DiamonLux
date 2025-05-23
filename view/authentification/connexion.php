<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de connexion</title>
    <link rel="stylesheet" href="./public/css/connexion.css">

</head>

<body>
    <div class="login-container">
        <h1>Connexion</h1>

        <form action="controller/users/usersController.php" method="POST" enctype="multipart/form-data" onsubmit="return verifierCaptcha();">

            <label for="email">Email</label>
            <input type="email" name="email" required><br>

            <label for="password">Mot de passe</label>
            <input type="password" name="password" required><br>

            <h3 class="testBot">Assemblez le logo pour continuer</h3>

            <div class="puzzle-container" id="puzzle">
                <img src="public/images/logo_part_3.png" class="puzzle-piece" draggable="true" id="3">
                <img src="public/images/logo_part_1.png" class="puzzle-piece" draggable="true" id="1">
                <img src="public/images/logo_part_4.png" class="puzzle-piece" draggable="true" id="4">
                <img src="public/images/logo_part_2.png" class="puzzle-piece" draggable="true" id="2">
            </div>

            <p id="captcha-error">Remettez le puzzle dans l'ordre avant de vous connecter.</p>

            <input type="hidden" name="action" value="login">
            <input type="submit" value="Valider">

        </form>

        <div class="inscription">
            <p id="no-account">Pas de compte ?</p>
            <a href="index.php?page=inscription" id="inscription-link">S'inscrire</a>
        </div>
    </div>

    <script>
        const container = document.getElementById("puzzle");
        let dragged;

        container.addEventListener("dragstart", (e) => {
            dragged = e.target;
        });

        container.addEventListener("dragover", (e) => {
            e.preventDefault();
        });

        container.addEventListener("drop", (e) => {
            e.preventDefault();
            if (e.target.classList.contains("puzzle-piece") && e.target !== dragged) {
                const draggedClone = dragged.cloneNode(true);
                const targetClone = e.target.cloneNode(true);

                container.replaceChild(draggedClone, e.target);
                container.replaceChild(targetClone, dragged);
            }
        });

        function verifierCaptcha() {
            const ordreCorrect = ["1", "2", "3", "4"];
            const pieces = Array.from(container.children);
            const ordreActuel = pieces.map(p => p.id);
            if (JSON.stringify(ordreActuel) !== JSON.stringify(ordreCorrect)) {
                document.getElementById("captcha-error").style.display = "block";
                return false;
            }
            return true;
        }
    </script>
</body>

</html>