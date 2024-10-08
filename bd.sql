CREATE TABLE produit(
idProd numeric(10) PRIMARY KEY,
libelle varchar(255),
descriptif varchar(1024),
image varchar(2048),
vignette varchar(2048));

CREATE TABLE panier(
idPanier numeric(10) PRIMARY KEY);

CREATE TABLE contient(
idProduit numeric(10),
idPanier numeric(10),
quantite numeric(3),
PRIMARY KEY(idProduit, idPanier));

CREATE TABLE administrateur(
identifiant varchar(16) PRIMARY KEY,
mdp varchar(16));

INSERT INTO produit (idProd, libelle, descriptif, image, vignette) VALUES
(2, 'Noo Noo', 'Noo-Noo est un aspirateur de couleur bleu vif avec un long tuyau flexible qui se termine par un embout en forme de trompe. Son corps est arrondi et il possède des yeux expressifs qui lui donnent une personnalité amicale et curieuse.', 'Images/Noonoo_1-300x224.jpg', '')
