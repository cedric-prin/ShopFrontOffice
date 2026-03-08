-- ============================================
-- Fichier SQL nettoyé et compatible Aiven/MariaDB
-- Base de données : prin_boutique
-- Compatible : MariaDB strict, Aiven MySQL
-- ============================================

-- Forcer l'encodage UTF-8 pour l'import
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_results = utf8mb4;
SET collation_connection = utf8mb4_unicode_ci;

-- Désactiver les vérifications de clés étrangères pendant l'import
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- ÉTAPE 1 : DROP TABLES (dans l'ordre inverse des dépendances)
-- ============================================

DROP TABLE IF EXISTS `log_commandes`;
DROP TABLE IF EXISTS `lignedecommande`;
DROP TABLE IF EXISTS `commande`;
DROP TABLE IF EXISTS `livraison`;
DROP TABLE IF EXISTS `domicile`;
DROP TABLE IF EXISTS `pointrelais`;
DROP TABLE IF EXISTS `panier_produit`;
DROP TABLE IF EXISTS `panier`;
DROP TABLE IF EXISTS `paiement`;
DROP TABLE IF EXISTS `produit`;
DROP TABLE IF EXISTS `categorie`;
DROP TABLE IF EXISTS `fournisseur`;
DROP TABLE IF EXISTS `client`;
DROP TABLE IF EXISTS `utilisateur`;

-- ============================================
-- ÉTAPE 2 : DROP PROCEDURES, FUNCTIONS, TRIGGERS
-- ============================================

DROP PROCEDURE IF EXISTS `AddCommande`;
DROP PROCEDURE IF EXISTS `GetAllClients`;
DROP PROCEDURE IF EXISTS `ReapprovisionnerProduit`;
DROP PROCEDURE IF EXISTS `ReapprovisionnerProduit10`;
DROP PROCEDURE IF EXISTS `ViderPanierClient`;
DROP PROCEDURE IF EXISTS `_deleteProduitById`;
DROP PROCEDURE IF EXISTS `_selectClients`;
DROP PROCEDURE IF EXISTS `_selectDetailsProduits`;
DROP PROCEDURE IF EXISTS `_selectNbTuplesByTable`;

DROP FUNCTION IF EXISTS `CalculerTotalCommande`;
DROP FUNCTION IF EXISTS `_selectNbCommandesByClient`;

DROP TRIGGER IF EXISTS `LogAjoutCommande`;
DROP TRIGGER IF EXISTS `maj_stock_apres_vente`;
DROP TRIGGER IF EXISTS `reappro_automatique_seuil`;

-- ============================================
-- ÉTAPE 3 : CRÉATION DES TABLES (sans FOREIGN KEY)
-- ============================================

CREATE TABLE `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `client` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `rue` varchar(255) NOT NULL,
  `codePostal` int NOT NULL,
  `ville` varchar(255) NOT NULL,
  `tel` varchar(10) NOT NULL,
  `email` varchar(30) NOT NULL,
  `mdp` varchar(255) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fournisseur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `rue` varchar(255) NOT NULL,
  `codePostal` int NOT NULL,
  `ville` varchar(255) NOT NULL,
  `tel` varchar(10) NOT NULL,
  `email` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `prix` decimal(6,2) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `idCategorie` int DEFAULT NULL,
  `idFournisseur` int DEFAULT NULL,
  `QteStockProduit` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idCategorie` (`idCategorie`),
  KEY `idFournisseur` (`idFournisseur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `commande` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` varchar(50) DEFAULT NULL,
  `idClient` int DEFAULT NULL,
  `sousTotal` decimal(10,2) DEFAULT NULL,
  `moyPaiement` varchar(32) DEFAULT NULL,
  `idLivraison` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idClient` (`idClient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `domicile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `domRue` varchar(255) NOT NULL,
  `domCodePostal` varchar(10) NOT NULL,
  `domVille` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pointrelais` (
  `id` int NOT NULL AUTO_INCREMENT,
  `prNom` varchar(255) NOT NULL,
  `prRue` varchar(255) NOT NULL,
  `prCodePostal` varchar(10) NOT NULL,
  `prVille` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `livraison` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idDomicile` int DEFAULT NULL,
  `idRelais` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_livraison_domicile` (`idDomicile`),
  KEY `fk_livraison_relais` (`idRelais`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lignedecommande` (
  `idCommande` int NOT NULL,
  `idProduit` int NOT NULL,
  `quantite` int NOT NULL,
  `prixUnitaire` decimal(10,2) NOT NULL,
  `sousTotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idCommande`,`idProduit`),
  KEY `idProduit` (`idProduit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `log_commandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idCommande` int DEFAULT NULL,
  `dateAjout` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `paiement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numeroCarte` varchar(20) NOT NULL,
  `nomCarte` varchar(100) NOT NULL,
  `dateExpiration` date NOT NULL,
  `codeConfidentiel` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `panier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idClient` int NOT NULL,
  `date_modif` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idClient` (`idClient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `panier_produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idPanier` int NOT NULL,
  `idProduit` int NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idPanier` (`idPanier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(100) DEFAULT NULL,
  `passe` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `isAdmin` tinyint(1) DEFAULT NULL,
  `token_cookie` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ÉTAPE 4 : INSERTION DES DONNÉES
-- ============================================

INSERT INTO `categorie` (`id`, `libelle`) VALUES
(1, 'Boîtier'),
(2, 'Alimentation'),
(3, 'Disque dur'),
(4, 'Disque SSD'),
(5, 'Carte mère'),
(6, 'Carte graphique'),
(7, 'Mémoire'),
(8, 'Processeur'),
(9, 'Refroidissement');

INSERT INTO `client` (`id`, `nom`, `prenom`, `rue`, `codePostal`, `ville`, `tel`, `email`, `mdp`, `date_naissance`) VALUES
(19, 'Roussel', 'Antoine', 'Place de la Concorde', 44001, 'Nantes', '0102345678', 'antoine.roussel@example.com', NULL, NULL),
(20, 'Vincent', 'Valentin', 'Avenue des Roses', 34001, 'Montpellier', '0113456789', 'valentin.vincent@example.com', NULL, NULL),
(129, 'cece', 'cece', 'qdfqsfqs', 34000, 'mtp', '0654214532', 'a@gmail.com', '$2y$10$7jOpB7TSTvUTRhGxwS52WO6qnAOkacgfT46f1l/G0bLVKutsU.0sC', NULL),
(130, 'Prin', 'Cédric', '', 0, '', '', 'cedric34@gmail.com', '$2y$10$XAwnuXN94Cq3.Zd.9Z0kGu6zxSvcMjO5Z0kSTSZE4oHLNuApC/L3q', '2005-08-19'),
(131, 'Vauthier', 'Noemie', '66 avenue des Lilas', 34000, 'Montpellier', '0654214532', 'gugus@gmail.com', '$2y$10$/TL6ivAFHtDvLjPEqcWTe.Yzi/FSZ9OJB01y4xonS8f3B5Ah0lwyq', '2005-12-07'),
(132, 'Salut', 'Salut', '66 rue de rrr', 34000, 'montpellier', '0649378375', 'salut@gmail.com', '$2y$10$VtEYtKg1bgrZKdnQ9O8d0.UulwPDoRlZzj9Yb6yaMMx9CsSr/81Hq', '2005-11-15'),
(133, 'Salezf', 'Salutezfzf', '66 rzfezfze', 34000, 'montpellier', '0649378376', 'zfzf@gmail.com', NULL, NULL),
(134, 'aaa', 'aaa', '', 0, '', '', 'aaa@gmail.com', '$2y$10$.LlFV.pY.buehLxR75LFwOEaTXZi1ZfQQvRA15tvuXIg/PrDJCivC', '2005-02-14');

INSERT INTO `fournisseur` (`id`, `nom`, `rue`, `codePostal`, `ville`, `tel`, `email`) VALUES
(1, 'La Maison du Fournisseur', 'Rue de la République', 75001, 'Paris', '0123456789', 'fournisseur1@example.com'),
(2, 'Fournitures Express', 'Avenue des Champs-Élysées', 75008, 'Paris', '0234567890', 'fournisseur2@example.com'),
(3, 'Étoile des Fournisseurs', 'Rue du Faubourg Saint-Honoré', 75008, 'Paris', '0345678901', 'fournisseur3@example.com'),
(4, 'Fournisseur Incroyable', 'Place de la Bastille', 75011, 'Paris', '0456789012', 'fournisseur4@example.com'),
(5, 'Alliance des Fournisseurs', 'Boulevard Saint-Germain', 75006, 'Paris', '0567890123', 'fournisseur5@example.com');

INSERT INTO `produit` (`id`, `nom`, `description`, `prix`, `image`, `idCategorie`, `idFournisseur`, `QteStockProduit`) VALUES
(1, 'NZXT H510', 'Boîtier PC Moyen Tour - ATX / Micro ATX / Mini ITX - Verre trempé', 79.99, 'nzxt_h510.jpg', 1, 1, 12),
(2, 'Fractale Design Meshify C', 'Boîtier PC Moyen Tour - ATX / Micro ATX / Mini ITX - Verre trempé', 89.00, 'fractal_design_meshify_c.jpg', 1, 1, 35),
(3, 'Corsair iCUE 220T RGB', 'Boîtier PC Moyen Tour - ATX / Micro ATX / Mini ITX - Verre trempé', 99.99, 'corsair_icue_220t_rgb.jpg', 1, 1, 26),
(4, 'Corsair RM750x - 750W', 'Alimentation 750W ATX 12V 2.52 - Modulaire - 80 PLUS Gold', 109.99, 'corsair_rm750x_750w.jpg', 2, 1, 51),
(5, 'Seasonic Focus GX-650 - 650W', 'Alimentation 650W ATX 12V 2.52 - Modulaire - 80 PLUS Gold', 99.99, 'seasonic_focus_gx650_650w.jpg', 2, 1, 88),
(6, 'EVGA SuperNOVA 850 G3 - 850W', 'Alimentation 850W ATX 12V 2.52 - Modulaire - 80 PLUS Gold', 129.99, 'evga_supernova_850_g3_850w.jpg', 2, 1, 67),
(7, 'Seagate Barracuda 2 To', 'Disque dur interne 3.5" - 7200 tr/min - Cache 256 Mo - SATA 6Gb/s', 59.99, 'seagate_barracuda_2to.jpg', 3, 1, 94),
(8, 'Western Digital WD Blue 4 To', 'Disque dur interne 3.5" - 5400 tr/min - Cache 64 Mo - SATA 6Gb/s', 89.99, 'western_digital_wd_blue_4to.jpg', 3, 1, 77),
(9, 'Toshiba P300 1 To', 'Disque dur interne 3.5" - 7200 tr/min - Cache 64 Mo - SATA 6Gb/s', 44.99, 'toshiba_p300_1to.jpg', 3, 1, 61),
(10, 'Samsung 970 EVO Plus 1 To', 'SSD M.2 NVMe PCIe 3.0 x4 - Lecture 3500 Mo/s - Ecriture 3300 Mo/s - Mémoire TLC 3D V-NAND', 149.99, 'samsung_970_evo_plus_1to.jpg', 4, 2, 52),
(11, 'Crucial P2 500 Go', 'SSD M.2 NVMe PCIe 3.0 x4 - Lecture 2400 Mo/s - Ecriture 1800 Mo/s - Mémoire QLC 3D NAND', 59.99, 'crucial_p2_500go.jpg', 4, 2, 36),
(12, 'Western Digital Blue SN550 2 To', 'SSD M.2 NVMe PCIe 3.0 x4 - Lecture 2400 Mo/s - Ecriture 1950 Mo/s - Mémoire TLC 3D NAND', 199.99, 'western_digital_blue_sn550_2to.jpg', 4, 2, 9),
(13, 'MSI B450 TOMAHAWK MAX', 'Carte mère ATX AMD B450 - Socket AM4 - DDR4 - SATA 6Gb/s - M.2 - USB 3.1 - 2x PCI-Express 3.0', 119.99, 'msi_b450_tomahawk_max.jpg', 5, 2, 50),
(14, 'ASUS ROG Strix B550-F Gaming', 'Carte mère ATX AMD B550 - Socket AM4 - DDR4 - SATA 6Gb/s - M.2 - USB 3.2 - 2x PCI-Express 4.0', 189.99, 'asus_rog_strix_b550_f_gaming.jpg', 5, 2, 59),
(15, 'GIGABYTE Z590 AORUS PRO AX', 'Carte mère ATX Intel Z590 - Socket LGA1200 - DDR4 - SATA 6Gb/s - M.2 - USB 3.2 - 3x PCI-Express 4.0', 279.99, 'gigabyte_z590_aorus_pro_ax.jpg', 5, 2, 70),
(16, 'NVIDIA GeForce RTX 3060 Ti', 'Carte graphique NVIDIA GeForce RTX 3060 Ti - 8 Go GDDR6 - Interface PCI-Express 4.0', 399.99, 'nvidia_geforce_rtx_3060_ti.jpg', 6, 3, 95),
(17, 'AMD Radeon RX 6800 XT', 'Carte graphique AMD Radeon RX 6800 XT - 16 Go GDDR6 - Interface PCI-Express 4.0', 699.99, 'amd_radeon_rx_6800_xt.jpg', 6, 3, 120),
(18, 'NVIDIA GeForce RTX 3090', 'Carte graphique NVIDIA GeForce RTX 3090 - 24 Go GDDR6X - Interface PCI-Express 4.0', 1499.99, 'nvidia_geforce_rtx_3090.jpg', 6, 3, 100),
(19, 'Corsair Vengeance LPX 16 Go (2 x 8 Go) DDR4 3200 M', 'Kit mémoire DDR4 PC4-25600 - CMK16GX4M2B3200C16 (garantie à vie par Corsair)', 89.99, 'corsair_vengeance_lpx_16go_ddr4_3200mhz.jpg', 7, 4, 110),
(20, 'G.Skill Ripjaws V Series 32 Go (2 x 16 Go) DDR4 36', 'Kit mémoire DDR4 PC4-28800 - F4-3600C16D-32GVKC (garantie à vie par G.Skill)', 159.99, 'gskill_ripjaws_v_series_32go_ddr4_3600mhz.jpg', 7, 4, 130),
(21, 'Crucial Ballistix RGB 64 Go (2 x 32 Go) DDR4 3600 ', 'Kit mémoire DDR4 PC4-28800 - BL2K32G36C16U4BL (garantie à vie par Crucial)', 249.99, 'crucial_ballistix_rgb_64go_ddr4_3600mhz.jpg', 7, 4, 127),
(22, 'AMD Ryzen 5 5600X', 'Processeur 6 coeurs/12 threads - Fréquence de base 3.7 GHz - Fréquence Turbo 4.6 GHz - Cache L3 32 M', 299.99, 'amd_ryzen_5_5600x.jpg', 8, 5, 123),
(23, 'Intel Core i7-11700K', 'Processeur 8 coeurs/16 threads - Fréquence de base 3.6 GHz - Fréquence Turbo 5.0 GHz - Cache L3 16 M', 399.99, 'intel_core_i7_11700k.jpg', 8, 5, 149),
(24, 'AMD Ryzen 9 5900X', 'Processeur 12 coeurs/24 threads - Fréquence de base 3.7 GHz - Fréquence Turbo 4.8 GHz - Cache L3 64 ', 549.99, 'amd_ryzen_9_5900x.jpg', 8, 5, 156),
(25, 'Noctua NH-D15', 'Ventirad double tour - Socket AMD AM4 / Intel LGA1200 / LGA1151', 99.99, 'noctua_nh_d15.jpg', 9, 5, 167),
(26, 'Cooler Master Hyper 212 Black Edition', 'Ventirad tour simple - Socket AMD AM4 / Intel LGA1200 / LGA1151', 49.99, 'cooler_master_hyper_212_black_edition.jpg', 9, 5, 190),
(27, 'Corsair Hydro Series H100i RGB PLATINUM SE', 'Watercooling tout-en-un - Kit de refroidissement liquide - Socket AMD AM4 / Intel LGA1200 / LGA1151', 159.99, 'corsair_hydro_series_h100i_rgb_platinum_se.jpg', 9, 5, 187);

INSERT INTO `domicile` (`id`, `domRue`, `domCodePostal`, `domVille`) VALUES
(13, '66 rue de rrr', '34000', 'montpellier'),
(14, '66 rue de rrr', '34000', 'montpellier'),
(15, '66 rue de rrr', '34000', 'montpellier'),
(16, '66 rue de rue', '34000', 'montpellier'),
(17, '66 rue de rrr', '34000', 'montpellier'),
(18, '66 rue de rrr', '34000', 'montpellier'),
(19, '66 rue de rrr', '34000', 'montpellier'),
(20, '66 rue de rrr', '34000', 'montpellier'),
(21, '66 rue de rrr', '34000', 'montpellier'),
(22, '66 rue de rrr', '34000', 'montpellier'),
(23, '66 rue de rrr', '34000', 'montpellier'),
(24, '66 rue de rrr', '34000', 'montpellier'),
(25, '66 rue de rrr', '34000', 'montpellier'),
(26, '66 rue de rrr', '34000', 'montpellier'),
(27, '66 rue de rrr', '34000', 'montpellier'),
(28, '66 rue de rrr', '34000', 'montpellier'),
(29, '66 rue de rrr', '34000', 'montpellier'),
(30, '66 rue de rrr', '34000', 'montpellier'),
(31, '66 rue de rrr', '34000', 'montpellier'),
(32, '66 rue de rrr', '34000', 'montpellier'),
(33, '66 rue de rrr', '34000', 'montpellier'),
(34, '66 rue de rrr', '34000', 'montpellier'),
(35, '66 rue de rrr', '34000', 'montpellier'),
(36, '66 rue de rrr', '34000', 'montpellier'),
(37, '66 rue de rrr', '34000', 'montpellier'),
(38, '66 rue de rrr', '34000', 'montpellier'),
(39, '66 rue', '34980', 'Saint-Gély-du-Fesc'),
(40, '1', '34000', 'Montpellier'),
(41, '66', '34980', 'Saint-Gély-du-Fesc'),
(42, '60', '34000', 'Montpellier');

INSERT INTO `pointrelais` (`id`, `prNom`, `prRue`, `prCodePostal`, `prVille`) VALUES
(6, 'Librairie Sauramps', 'Le Triangle', '34000', 'Montpellier'),
(7, 'Librairie Sauramps', 'Le Triangle', '34000', 'Montpellier'),
(8, 'Tabac du Polygone', 'Centre Commercial Polygone', '34000', 'Montpellier'),
(9, 'Librairie Sauramps', 'Le Triangle', '34000', 'Montpellier'),
(10, 'Tabac Antigone', '45 Place du Nombre d\'Or', '34000', 'Montpellier'),
(11, 'Tabac-Presse du Marché', '3 Place du Marché', '34500', 'Ville (34500)'),
(12, 'Tabac du Polygone', 'Centre Commercial Polygone', '34000', 'Montpellier'),
(13, 'Point Relais Centre-Ville', '15 Rue Principale', '34410', 'Ville (34410)'),
(14, 'Relay Gare Saint-Roch', 'Gare SNCF', '34000', 'Montpellier'),
(15, 'Librairie Sauramps', 'Le Triangle', '34000', 'Montpellier'),
(16, 'Relay Gare Saint-Roch', 'Gare SNCF', '34000', 'Montpellier'),
(17, 'Tabac Comédie', '12 Place de la Comédie', '34000', 'Montpellier'),
(18, 'Tabac du Polygone', 'Centre Commercial Polygone', '34000', 'Montpellier'),
(19, 'Tabac Comédie', '12 Place de la Comédie', '34000', 'Montpellier'),
(20, 'Tabac Antigone', '45 Place du Nombre d\'Or', '34000', 'Montpellier'),
(21, 'Tabac du Polygone', 'Centre Commercial Polygone', '34000', 'Montpellier'),
(22, 'Tabac du Polygone', 'Centre Commercial Polygone', '34000', 'Montpellier');

INSERT INTO `livraison` (`id`, `idDomicile`, `idRelais`) VALUES
(28, 13, NULL),
(29, NULL, 6),
(30, 14, NULL),
(31, NULL, 7),
(32, NULL, 8),
(33, 15, NULL),
(34, 16, NULL),
(35, NULL, 9),
(36, 17, NULL),
(37, NULL, 10),
(38, NULL, 11),
(39, NULL, 12),
(40, 18, NULL),
(41, NULL, 13),
(42, 19, NULL),
(43, NULL, 14),
(44, NULL, 15),
(45, NULL, 16),
(46, NULL, 17),
(47, NULL, 18),
(48, NULL, 19),
(49, 20, NULL),
(50, 21, NULL),
(51, 22, NULL),
(52, NULL, 20),
(53, 23, NULL),
(54, 24, NULL),
(55, 25, NULL),
(56, 26, NULL),
(57, 27, NULL),
(58, 28, NULL),
(59, 29, NULL),
(60, 30, NULL),
(61, 31, NULL),
(62, 32, NULL),
(63, 33, NULL),
(64, 34, NULL),
(65, 35, NULL),
(66, 36, NULL),
(67, 37, NULL),
(68, 38, NULL),
(69, NULL, 21),
(70, 39, NULL),
(71, 40, NULL),
(72, NULL, 22),
(73, 41, NULL),
(74, 42, NULL);

INSERT INTO `commande` (`id`, `date`, `idClient`, `sousTotal`, `moyPaiement`, `idLivraison`) VALUES
(102, '2025-05-02 19:20:54', 132, 169.98, 'CB', 28),
(106, '2025-05-04 18:34:42', 132, 2414.92, 'CB', 34),
(107, '2025-05-04 18:37:48', 132, 59.99, 'CB', 35),
(108, '2025-05-04 18:40:07', 132, 59.99, 'CB', 36),
(109, '2025-05-05 12:05:46', 132, 859.94, 'CB', 37),
(110, '2025-05-05 12:14:07', 132, 939.93, 'CB', 38),
(111, '2025-05-20 07:57:46', 132, 1269.90, 'CB', 43),
(112, '2025-05-20 08:34:20', 132, 1269.90, 'CB', 44),
(113, '2025-05-20 08:36:01', 132, 1269.90, 'CB', 45),
(114, '2025-05-27 06:45:21', 132, 1269.90, 'CB', 46),
(115, '2025-05-27 07:04:58', 132, 1349.89, 'CB', 47),
(116, '2025-05-27 09:13:10', 132, 1349.89, 'CB', 48),
(117, '2025-05-27 18:53:21', 132, 1459.88, 'CB', 51),
(118, '2025-05-27 19:10:36', 132, 1459.88, 'CB', 59),
(121, '2025-05-28 14:20:46', 132, 1099.90, 'CB', 63),
(122, '2025-05-28 14:24:13', 132, 1188.90, 'CB', 64),
(123, '2025-05-28 14:26:00', 132, 1288.89, 'CB', 65),
(124, '2025-05-28 14:38:07', 132, 89.00, 'CB', 66),
(125, '2025-05-28 20:12:02', 129, 359.96, 'CB', NULL),
(126, '2025-05-28 20:12:08', 129, 359.96, 'CB', NULL),
(127, '2025-05-28 20:12:11', 129, 359.96, 'CB', NULL),
(128, '2025-05-28 20:15:05', 129, 239.96, 'CB', NULL),
(129, '2025-05-28 20:22:52', 19, 479.94, 'CB', NULL),
(130, '2025-05-28 20:25:12', 130, 649.95, 'CB', NULL),
(131, '2025-05-28 20:25:29', 131, 1039.92, 'CB', NULL),
(132, '2025-05-28 20:26:05', 131, 1099.98, 'CB', NULL),
(133, '2025-05-28 20:26:42', 129, 1099.98, 'CB', NULL),
(134, '2025-05-28 20:28:00', 19, 159.98, 'CB', NULL),
(136, '2025-05-28 20:29:22', 131, 359.94, 'CB', NULL),
(138, '04/06/2025 18:34:42', 131, NULL, NULL, NULL),
(139, '02/05/2023 19:20:54', 129, NULL, NULL, NULL),
(140, '04/05/2022 18:34:42', 132, NULL, NULL, NULL),
(141, '2025-06-02 14:21:37', 132, 878.93, 'CB', 67),
(142, '2025-06-02 14:42:57', 132, 169.98, 'CB', 68),
(143, '2025-06-02 14:44:18', 132, 199.99, 'CB', 69),
(144, '2025-12-02 14:01:38', 134, 259.97, 'CB', 74);

INSERT INTO `lignedecommande` (`idCommande`, `idProduit`, `quantite`, `prixUnitaire`, `sousTotal`) VALUES
(106, 1, 1, 23.22, 0.00),
(106, 9, 1, 44.99, 44.99),
(106, 10, 1, 149.99, 149.99),
(106, 13, 1, 119.99, 119.99),
(106, 18, 1, 1499.99, 1499.99),
(106, 22, 1, 299.99, 299.99),
(106, 27, 1, 159.99, 159.99),
(108, 7, 1, 59.99, 59.99),
(109, 1, 2, 79.99, 159.98),
(109, 10, 3, 149.99, 449.97),
(109, 21, 1, 249.99, 249.99),
(110, 1, 3, 79.99, 239.97),
(110, 10, 3, 149.99, 449.97),
(110, 21, 1, 249.99, 249.99),
(111, 1, 4, 79.99, 319.96),
(111, 3, 1, 99.99, 99.99),
(111, 10, 4, 149.99, 599.96),
(111, 21, 1, 249.99, 249.99),
(112, 1, 4, 79.99, 319.96),
(112, 3, 1, 99.99, 99.99),
(112, 10, 4, 149.99, 599.96),
(112, 21, 1, 249.99, 249.99),
(113, 1, 4, 79.99, 319.96),
(113, 3, 1, 99.99, 99.99),
(113, 10, 4, 149.99, 599.96),
(113, 21, 1, 249.99, 249.99),
(114, 1, 4, 79.99, 319.96),
(114, 3, 1, 99.99, 99.99),
(114, 10, 4, 149.99, 599.96),
(114, 21, 1, 249.99, 249.99),
(115, 1, 5, 79.99, 399.95),
(115, 3, 1, 99.99, 99.99),
(115, 10, 4, 149.99, 599.96),
(115, 21, 1, 249.99, 249.99),
(116, 1, 5, 79.99, 399.95),
(116, 3, 1, 99.99, 99.99),
(116, 10, 4, 149.99, 599.96),
(116, 21, 1, 249.99, 249.99),
(117, 1, 5, 79.99, 399.95),
(117, 3, 1, 99.99, 99.99),
(117, 4, 1, 109.99, 109.99),
(117, 10, 4, 149.99, 599.96),
(117, 21, 1, 249.99, 249.99),
(118, 1, 5, 79.99, 399.95),
(118, 3, 1, 99.99, 99.99),
(118, 4, 1, 109.99, 109.99),
(118, 10, 4, 149.99, 599.96),
(118, 21, 1, 249.99, 249.99),
(121, 1, 6, 79.99, 479.94),
(121, 3, 1, 99.99, 99.99),
(121, 4, 1, 109.99, 109.99),
(121, 21, 1, 249.99, 249.99),
(121, 27, 1, 159.99, 159.99),
(122, 1, 6, 79.99, 479.94),
(122, 2, 1, 89.00, 89.00),
(122, 3, 1, 99.99, 99.99),
(122, 4, 1, 109.99, 109.99),
(122, 21, 1, 249.99, 249.99),
(122, 27, 1, 159.99, 159.99),
(123, 1, 6, 79.99, 479.94),
(123, 2, 1, 89.00, 89.00),
(123, 3, 1, 99.99, 99.99),
(123, 4, 1, 109.99, 109.99),
(123, 5, 1, 99.99, 99.99),
(123, 21, 1, 249.99, 249.99),
(123, 27, 1, 159.99, 159.99),
(124, 2, 1, 89.00, 89.00),
(125, 8, 4, 89.99, 0.00),
(126, 8, 4, 89.99, 0.00),
(127, 8, 4, 89.99, 0.00),
(128, 7, 4, 59.99, 0.00),
(129, 1, 6, 79.99, 0.00),
(130, 6, 5, 129.99, 0.00),
(131, 6, 8, 129.99, 0.00),
(132, 24, 2, 549.99, 0.00),
(133, 24, 2, 549.99, 0.00),
(134, 1, 2, 79.99, 0.00),
(136, 11, 6, 59.99, 0.00),
(141, 1, 2, 79.99, 159.98),
(141, 2, 1, 89.00, 89.00),
(141, 4, 1, 109.99, 109.99),
(141, 7, 1, 59.99, 59.99),
(141, 10, 2, 149.99, 299.98),
(141, 20, 1, 159.99, 159.99),
(142, 1, 1, 79.99, 79.99),
(142, 8, 1, 89.99, 89.99),
(143, 12, 1, 199.99, 199.99),
(144, 1, 2, 79.99, 159.98),
(144, 5, 1, 99.99, 99.99);

INSERT INTO `log_commandes` (`id`, `idCommande`, `dateAjout`) VALUES
(1, 138, '2025-05-28 20:55:26'),
(2, 139, '2025-05-28 20:55:58'),
(3, 140, '2025-05-28 21:05:03'),
(4, 141, '2025-06-02 16:21:37'),
(5, 142, '2025-06-02 16:42:57'),
(6, 143, '2025-06-02 16:44:18'),
(7, 144, '2025-12-02 15:01:38');

INSERT INTO `paiement` (`id`, `numeroCarte`, `nomCarte`, `dateExpiration`, `codeConfidentiel`) VALUES
(63, '86*86*6*58735435', 'fjrtyje', '2006-03-01', '645'),
(62, '423*3*3', 'dyhzyh', '2004-03-01', '143'),
(61, '23*3*3', 'huktyk', '2014-05-01', '421'),
(60, '534343464', 'drgsgsg', '2016-04-01', '142'),
(59, '254165893526', 'gvuv', '2015-02-01', '541'),
(58, '2534125685214763', 'video', '2025-03-01', '352'),
(57, '6065165116', 'bjhugvg', '2015-06-01', '147'),
(55, '45345343434', 'fchdhj', '2028-03-01', '142'),
(56, '45353*', 'ghukfk', '2029-06-01', '426'),
(54, '5383243493744', 'qfqzf', '2026-03-01', '325'),
(53, '5235432531', 'fggbfghdbd', '2017-06-01', '841'),
(52, '2422222', 'wdfgsrdhs', '2025-03-01', '142'),
(51, '587*3*837', 'gyyuktyk', '2025-03-01', '142'),
(50, '2*823*832*', 'tltlt', '2014-06-01', '241'),
(49, '4527327*37', 'gjtfkjrtj', '2017-06-01', '141'),
(48, '453333', 'htdjthjd', '2014-08-01', '640'),
(47, '42333*', 'fyjydjsrfhj', '2024-06-01', '515'),
(46, '46368**', 'fyyuk,rk,', '2002-02-01', '984'),
(45, '752*3*', 'ykyt', '2001-01-01', '111'),
(43, '45363*7+', 'wsgergeg', '2008-08-01', '888'),
(44, '47d', 'drjj', '2008-08-01', '888'),
(64, '4663*63*63', 'drgesh', '2004-06-01', '468'),
(65, '5373*3', 'fthrh', '2009-03-01', '145'),
(66, '47+7+7', 'hili', '2019-02-01', '152'),
(67, '5169196264845', 'sgsv', '2015-02-01', '124'),
(68, '45272387324', 'errzfz', '2016-04-01', '652'),
(69, '151188', 'hvt', '2004-03-01', '142');

INSERT INTO `panier` (`id`, `idClient`, `date_modif`) VALUES
(1, 132, '2025-05-05 13:12:01'),
(2, 134, '2025-11-13 09:42:10');

INSERT INTO `utilisateur` (`id`, `login`, `passe`, `email`, `isAdmin`, `token_cookie`) VALUES
(1, 'Chef', 'prin34', 'princhef@sio.net', 1, NULL),
(2, 'prinRandom', 'prin34980', 'prinrandom@sio.net', 0, NULL);

-- ============================================
-- ÉTAPE 5 : AJOUT DES FOREIGN KEYS
-- ============================================

ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`idClient`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`idLivraison`) REFERENCES `livraison` (`id`) ON DELETE SET NULL;

ALTER TABLE `lignedecommande`
  ADD CONSTRAINT `lignedecommande_ibfk_1` FOREIGN KEY (`idCommande`) REFERENCES `commande` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lignedecommande_ibfk_2` FOREIGN KEY (`idProduit`) REFERENCES `produit` (`id`) ON DELETE RESTRICT;

ALTER TABLE `produit`
  ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`idCategorie`) REFERENCES `categorie` (`id`),
  ADD CONSTRAINT `produit_ibfk_2` FOREIGN KEY (`idFournisseur`) REFERENCES `fournisseur` (`id`);

ALTER TABLE `livraison`
  ADD CONSTRAINT `fk_livraison_domicile` FOREIGN KEY (`idDomicile`) REFERENCES `domicile` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_livraison_relais` FOREIGN KEY (`idRelais`) REFERENCES `pointrelais` (`id`) ON DELETE SET NULL;

ALTER TABLE `panier`
  ADD CONSTRAINT `fk_panier_client` FOREIGN KEY (`idClient`) REFERENCES `client` (`id`) ON DELETE CASCADE;

ALTER TABLE `panier_produit`
  ADD CONSTRAINT `fk_panier_produit_panier` FOREIGN KEY (`idPanier`) REFERENCES `panier` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_panier_produit_produit` FOREIGN KEY (`idProduit`) REFERENCES `produit` (`id`) ON DELETE CASCADE;

-- ============================================
-- ÉTAPE 6 : CRÉATION DES PROCÉDURES
-- ============================================

DELIMITER $$

CREATE PROCEDURE `AddCommande` (IN `p_date` DATETIME, IN `p_idClient` INT, IN `p_sousTotal` DECIMAL(10,2), IN `p_moyPaiement` VARCHAR(20), IN `p_idLivraison` INT)
BEGIN
    INSERT INTO commande (date, idClient, sousTotal, moyPaiement, idLivraison)
    VALUES (p_date, p_idClient, p_sousTotal, p_moyPaiement, p_idLivraison);
END$$

CREATE PROCEDURE `GetAllClients` ()
BEGIN
    SELECT * FROM client;
END$$

CREATE PROCEDURE `ReapprovisionnerProduit` (IN `p_idProduit` INT, IN `p_quantite` INT)
BEGIN
    UPDATE produit
    SET QteStockProduit = QteStockProduit + p_quantite
    WHERE id = p_idProduit;
END$$

CREATE PROCEDURE `ReapprovisionnerProduit10` (IN `p_idProduit` INT)
BEGIN
    UPDATE produit
    SET QteStockProduit = QteStockProduit + 10
    WHERE id = p_idProduit;
END$$

CREATE PROCEDURE `ViderPanierClient` (IN `paramIdClient` INT)
BEGIN
    DELETE FROM panier_produit
    WHERE idPanier IN (SELECT id FROM panier WHERE idClient = paramIdClient);
END$$

CREATE PROCEDURE `_deleteProduitById` (IN `paramIdProduit` INT)
BEGIN
    DELETE FROM produit WHERE id = paramIdProduit;
END$$

CREATE PROCEDURE `_selectClients` ()
BEGIN
    SELECT C.id, CONCAT(C.nom, ' ', C.prenom) AS Patronyme
    FROM client C;
END$$

CREATE PROCEDURE `_selectDetailsProduits` ()
BEGIN
    SELECT P.id, P.nom, P.description, P.prix, P.image, P.idCategorie, P.idFournisseur, P.QteStockProduit, F.nom 
    FROM produit P, categorie C, fournisseur F
    WHERE P.idFournisseur = F.id
    AND P.idCategorie = C.id;
END$$

CREATE PROCEDURE `_selectNbTuplesByTable` (IN `paramNomTable` VARCHAR(50))
BEGIN
    SET @req = CONCAT('SELECT COUNT(*) AS nbTuples FROM ', paramNomTable);
    PREPARE exe FROM @req;
    EXECUTE exe;
    DEALLOCATE PREPARE exe;
END$$

-- ============================================
-- ÉTAPE 7 : CRÉATION DES FONCTIONS
-- ============================================

CREATE FUNCTION `CalculerTotalCommande` (`idCmd` INT) RETURNS DECIMAL(10,2)
BEGIN
    DECLARE total DECIMAL(10,2);
    SELECT SUM(quantite * prixUnitaire) INTO total FROM lignedecommande WHERE idCommande = idCmd;
    RETURN IFNULL(total, 0);
END$$

CREATE FUNCTION `_selectNbCommandesByClient` (`paramIdClient` INT) RETURNS INT NO SQL
BEGIN
    DECLARE nbCommandes INTEGER;
    SET nbCommandes = (SELECT COUNT(id) FROM commande C WHERE C.idClient = paramIdClient);
    RETURN nbCommandes;
END$$

DELIMITER ;

-- ============================================
-- ÉTAPE 8 : CRÉATION DES TRIGGERS
-- ============================================

DELIMITER $$

CREATE TRIGGER `LogAjoutCommande` AFTER INSERT ON `commande` FOR EACH ROW
BEGIN
    INSERT INTO log_commandes (idCommande, dateAjout)
    VALUES (NEW.id, NOW());
END$$

CREATE TRIGGER `maj_stock_apres_vente` AFTER INSERT ON `lignedecommande` FOR EACH ROW
BEGIN
    UPDATE produit
    SET QteStockProduit = QteStockProduit - NEW.quantite
    WHERE id = NEW.idProduit;
END$$

CREATE TRIGGER `reappro_automatique_seuil` AFTER INSERT ON `lignedecommande` FOR EACH ROW
BEGIN
    DECLARE stockActuel INT;

    -- Récupérer le stock actuel après la commande
    SELECT QteStockProduit INTO stockActuel FROM produit WHERE id = NEW.idProduit;

    -- Si le stock passe sous le seuil d'alerte (5), on réapprovisionne de 10
    IF stockActuel < 5 THEN
        UPDATE produit
        SET QteStockProduit = QteStockProduit + 10
        WHERE id = NEW.idProduit;
    END IF;
END$$

DELIMITER ;

-- Réactiver les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- CORRECTION DES CATÉGORIES (pour corriger l'encodage)
-- ============================================
-- Si les catégories sont déjà importées avec un mauvais encodage,
-- ces UPDATE les corrigent directement

UPDATE `categorie` SET `libelle` = 'Boîtier' WHERE `id` = 1;
UPDATE `categorie` SET `libelle` = 'Alimentation' WHERE `id` = 2;
UPDATE `categorie` SET `libelle` = 'Disque dur' WHERE `id` = 3;
UPDATE `categorie` SET `libelle` = 'Disque SSD' WHERE `id` = 4;
UPDATE `categorie` SET `libelle` = 'Carte mère' WHERE `id` = 5;
UPDATE `categorie` SET `libelle` = 'Carte graphique' WHERE `id` = 6;
UPDATE `categorie` SET `libelle` = 'Mémoire' WHERE `id` = 7;
UPDATE `categorie` SET `libelle` = 'Processeur' WHERE `id` = 8;
UPDATE `categorie` SET `libelle` = 'Refroidissement' WHERE `id` = 9;
