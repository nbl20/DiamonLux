<?php
class User
{
    private $bdd;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
    }

    public function ajouterUtilisateur($nom, $prenom, $email, $mdp, $num_phone, $date_naissance, $ville, $adresse, $code_postal = null, $image = null, $statut = 'actif', $droits = 'client')
    {
        $hashPassword = password_hash($mdp, PASSWORD_BCRYPT);

        // Modifié pour ne pas inclure 'image_type' si ce n'est pas dans la base de données
        $req = $this->bdd->prepare("
        INSERT INTO `user` (nom, prenom, email, password, num_phone, date_naissance, ville, adresse, code_postal, image, statut, droits)
        VALUES (:nom, :prenom, :email, :mdp, :num_phone, :date_naissance, :ville, :adresse, :code_postal, :image, :statut, :droits)
    ");

        $req->bindParam(':nom', $nom);
        $req->bindParam(':prenom', $prenom);
        $req->bindParam(':email', $email);
        $req->bindParam(':mdp', $hashPassword);
        $req->bindParam(':num_phone', $num_phone);
        $req->bindParam(':date_naissance', $date_naissance);
        $req->bindParam(':ville', $ville);
        $req->bindParam(':adresse', $adresse);
        $req->bindParam(':code_postal', $code_postal);

        if ($image) {
            $req->bindParam(':image', $image, PDO::PARAM_LOB);
        } else {
            $req->bindValue(':image', null, PDO::PARAM_NULL);
        }

        // Suppression de la ligne pour 'image_type', puisque la colonne n'existe pas
        $req->bindParam(':statut', $statut);
        $req->bindParam(':droits', $droits);

        $req->execute();

        return $this->bdd->lastInsertId();
    }

    public function checklogin($email, $password)
    {
        $req = $this->bdd->prepare("SELECT * FROM `user` WHERE email = :email");
        $req->bindParam(':email', $email);
        $req->execute();
        $user = $req->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function getUserImage($userId)
    {
        $req = $this->bdd->prepare("SELECT image FROM `user` WHERE id = :id");
        $req->bindParam(':id', $userId);
        $req->execute();
        $user = $req->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['image'])) {
            return $user['image']; // retourne le BLOB brut, sans base64_encode
        }

        return null;
    }


    public function modifierUtilisateur($user_id, $nom, $prenom, $adresse, $code_postal, $num_phone, $image = null)
    {
        if ($image !== null) {
            $req = $this->bdd->prepare("
                UPDATE `user`
                SET nom = :nom,
                    prenom = :prenom,
                    adresse = :adresse,
                    code_postal = :code_postal,
                    num_phone = :num_phone,
                    image = :image
                WHERE id = :user_id
            ");

            $req->bindParam(':image', $image, PDO::PARAM_LOB);
        } else {
            $req = $this->bdd->prepare("
                UPDATE `user`
                SET nom = :nom,
                    prenom = :prenom,
                    adresse = :adresse,
                    code_postal = :code_postal,
                    num_phone = :num_phone
                WHERE id = :user_id
            ");
        }

        $req->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $req->bindParam(':nom', $nom, PDO::PARAM_STR);
        $req->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $req->bindParam(':adresse', $adresse, PDO::PARAM_STR);
        $req->bindParam(':code_postal', $code_postal, PDO::PARAM_STR);
        $req->bindParam(':num_phone', $num_phone, PDO::PARAM_STR);

        return $req->execute();
    }
    public function getUserById($id)
    {
        $req = $this->bdd->prepare("SELECT nom, prenom FROM user WHERE id = :id");
        $req->execute([':id' => $id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }
}
