CREATE TABLE produit(
idProd numeric(10) PRIMARY KEY,
libelle varchar(255),
prix numeric(10),
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

INSERT INTO produit (idProd, libelle, prix, descriptif, image, vignette) VALUES
(1, 'Henry', 15,'Découvrez Henry, laspirateur qui redéfinit le nettoyage. Conçu pour offrir une performance inégalée, Henry est le choix idéal pour les ménages et les professionnels en quête defficacité.', 'Images/images.jpg', ''),
(2, 'Noo Noo', 500,'Noo-Noo est un aspirateur de couleur bleu vif avec un long tuyau flexible qui se termine par un embout en forme de trompe. Son corps est arrondi et il possède des yeux expressifs qui lui donnent une personnalité amicale et curieuse.', 'Images/Noonoo_1-300x224.jpg', ''),
(3, 'MegaAspi3000x', 2499,'Le MegaAspi3000x est le choix idéal pour ceux qui recherchent un aspirateur puissant, efficace et facile à utiliser. Que ce soit pour un nettoyage quotidien ou des tâches plus lourdes, il répondra à toutes vos attentes en matière de propreté.', 'Images/megaaspi3000x.jpg', '')