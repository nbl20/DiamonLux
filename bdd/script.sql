CREATE DATABASE diamonlux2;
USE diamonlux2;

-- Table des utilisateurs
CREATE TABLE user (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nom VARCHAR(35) NOT NULL,
  prenom VARCHAR(35) NOT NULL,
  ville VARCHAR(50) NOT NULL,
  adresse VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL, -- Hashé avec bcrypt
  email VARCHAR(50) NOT NULL UNIQUE,
  num_phone VARCHAR(15) NOT NULL,
  date_naissance DATE NOT NULL,
  image VARCHAR(200) DEFAULT NULL,
  statut ENUM('actif', 'banni') NOT NULL DEFAULT 'actif',
  droits ENUM('client', 'admin') NOT NULL DEFAULT 'client',
  code_postal VARCHAR(10) DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Table des articles
CREATE TABLE article (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nom VARCHAR(35) NOT NULL,
  type VARCHAR(35) NOT NULL,
  marque VARCHAR(35) NOT NULL,
  prix DECIMAL(10,2) NOT NULL,
  date_vente DATE NOT NULL DEFAULT (CURRENT_DATE),
  secteur VARCHAR(50) NOT NULL,
  proprio INT(11) NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  etat ENUM('en_vente', 'vendu', 'réservé') NOT NULL DEFAULT 'en_vente',
  PRIMARY KEY (id),
  FOREIGN KEY (proprio) REFERENCES user(id) ON DELETE CASCADE
);

-- Table des commentaires sur les articles
CREATE TABLE commentaire_article (
  id INT(11) NOT NULL AUTO_INCREMENT,
  id_utilisateur INT(11) NOT NULL,
  id_article INT(11) NOT NULL,
  commentaire TEXT NOT NULL,
  date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (id_utilisateur) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (id_article) REFERENCES article(id) ON DELETE CASCADE
);

-- Table des commentaires entre utilisateurs
CREATE TABLE commentaire_utilisateur (
  id INT(11) NOT NULL AUTO_INCREMENT,
  id_utilisateur_commentateur INT(11) NOT NULL,
  id_utilisateur_commenter INT(11) NOT NULL,
  commentaire TEXT NOT NULL,
  date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (id_utilisateur_commentateur) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur_commenter) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des événements (ventes privées, promotions)
CREATE TABLE evenement (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nom VARCHAR(35) NOT NULL,
  date_debut DATE NOT NULL,
  date_fin DATE NOT NULL,
  description TEXT NOT NULL,
  ville VARCHAR(35) NOT NULL,
  image TEXT DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Table des nouveautés (nouveaux produits disponibles)
CREATE TABLE nouveaute (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nom VARCHAR(35) NOT NULL,
  marque VARCHAR(35) NOT NULL,
  type VARCHAR(35) NOT NULL,
  prix DECIMAL(10,2) NOT NULL,
  date_sortie DATE NOT NULL,
  image TEXT DEFAULT NULL,
  PRIMARY KEY (id)
);

-- Table du panier
CREATE TABLE panier (
  id INT(11) NOT NULL AUTO_INCREMENT,
  id_utilisateur INT(11) NOT NULL,
  id_article INT(11) NOT NULL,
  date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (id_utilisateur) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (id_article) REFERENCES article(id) ON DELETE CASCADE
);

-- Table de participation aux événements
CREATE TABLE participants (
  id INT(11) NOT NULL AUTO_INCREMENT,
  id_utilisateur INT(11) NOT NULL,
  id_evenement INT(11) NOT NULL,
  nom VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_utilisateur) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (id_evenement) REFERENCES evenement(id) ON DELETE CASCADE
);

-- Table des transactions (achats finalisés)
CREATE TABLE transactions (
  id INT(11) NOT NULL AUTO_INCREMENT,
  id_acheteur INT(11) NOT NULL,
  id_article INT(11) NOT NULL,
  date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  montant DECIMAL(10,2) NOT NULL,
  statut ENUM('en_attente', 'payé', 'annulé') NOT NULL DEFAULT 'en_attente',
  PRIMARY KEY (id),
  FOREIGN KEY (id_acheteur) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (id_article) REFERENCES article(id) ON DELETE CASCADE
);
CREATE TABLE commande (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_article INT(11) NOT NULL,
    id_acheteur INT(11) NOT NULL,
    date_achat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'valider', 'expedier', 'livrée') NOT NULL DEFAULT 'en_attente',
    PRIMARY KEY (id),
    FOREIGN KEY (id_article) REFERENCES article(id) ON DELETE CASCADE,
    FOREIGN KEY (id_acheteur) REFERENCES user(id) ON DELETE CASCADE
);