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