-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : sam. 22 mars 2025 à 14:57
-- Version du serveur : 8.0.41-0ubuntu0.24.04.1
-- Version de PHP : 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `wishlist_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id`) VALUES
(5),
(6);

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

CREATE TABLE `item` (
  `id` int NOT NULL,
  `wishlish_id` int NOT NULL,
  `title` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dtype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `item`
--

INSERT INTO `item` (`id`, `wishlish_id`, `title`, `description`, `price`, `url`, `dtype`) VALUES
(1, 1, 'Imprimante 3d', 'Imprime en 3d', 239, 'https://www.bol.com/be/fr/p/printer-3dandprint-3d-x1-3dandprint-de-construction-technologie-d-impression-fdm-pla/9200000130935184/?bltgh=gd2euj872Zm5Zd0clJNN-w.4_27.28.ProductTitle', ''),
(2, 1, 'Bois', 'Planche sapin petits noeuds, Ep.25 x l.195 mm mm, 2.5 m', 13.9, 'https://www.leroymerlin.fr/produits/planche-sapin-petits-noeuds-ep-25-x-l-195-mm-mm-2-5-m-79322852.html', ''),
(3, 2, 'Alcool', 'LE PHILTRE Organic Vodka\r\n', 49, 'https://www.whisky.fr/le-philtre-organic-vodka.html', ''),
(4, 2, 'T-shirt', 'Bah c un t shirt', 19.99, 'https://www.cache-cache.fr/t-shirt-details-fleurs-rouge-femme-36125346575460486.html', ''),
(5, 2, 'Rattrapage', 'Rattrapage en élec, ATSA, Physique, méca flux et proba\r\n\r\nMon coach Maths 6e avec Nicolas Herla', 8.9, 'https://site.nathan.fr/livres/mon-coach-maths-6e-avec-nicolas-herla-9782095022167.html', '');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `purchased_item`
--

CREATE TABLE `purchased_item` (
  `id` int NOT NULL,
  `buyer_id` int NOT NULL,
  `congratulory_message` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_proof` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `purchased_item`
--

INSERT INTO `purchased_item` (`id`, `buyer_id`, `congratulory_message`, `purchase_proof`) VALUES
(5, 6, 'Bonne chance pour tes rattrapages !', 'LIEN VERS LE FICHIER');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `website_id` int NOT NULL,
  `username` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_locked` tinyint(1) NOT NULL,
  `dtype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `website_id`, `username`, `password`, `email`, `is_locked`, `dtype`) VALUES
(1, 1, 'greg', '0000', 'grec@gmail.com', 0, ''),
(2, 1, 'jose', 'fabio', 'jose@gmail.com', 0, ''),
(3, 1, 'mamou', 'ocaml', 'mamou@gmail.com', 0, ''),
(4, 1, 'elodie', 'rgpd', 'elodie@imt-atlantique.net', 0, ''),
(5, 1, 'maud', 'fablab', 'maud@gmail.com', 0, ''),
(6, 1, 'sylvie', 'fise', 'sylvie@orange.fr', 0, '');

-- --------------------------------------------------------

--
-- Structure de la table `user_contributing_wishlists`
--

CREATE TABLE `user_contributing_wishlists` (
  `user_id` int NOT NULL,
  `wishlist_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_contributing_wishlists`
--

INSERT INTO `user_contributing_wishlists` (`user_id`, `wishlist_id`) VALUES
(1, 2),
(4, 1),
(5, 2),
(6, 1),
(6, 2);

-- --------------------------------------------------------

--
-- Structure de la table `user_invited_wishlists`
--

CREATE TABLE `user_invited_wishlists` (
  `user_id` int NOT NULL,
  `wishlist_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_invited_wishlists`
--

INSERT INTO `user_invited_wishlists` (`user_id`, `wishlist_id`) VALUES
(2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `website`
--

CREATE TABLE `website` (
  `id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `website`
--

INSERT INTO `website` (`id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Structure de la table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int NOT NULL,
  `author_id` int NOT NULL,
  `deadline` date NOT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sharing_url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `wishlist`
--

INSERT INTO `wishlist` (`id`, `author_id`, `deadline`, `name`, `sharing_url`, `display_url`) VALUES
(1, 5, '2026-01-31', 'Fablab', 'URL TO BE ADDED LATER', 'URL TO BE ADDED LATER'),
(2, 3, '2025-08-20', 'Cadeau Elodie', 'URL TO BE ADDED LATER', 'URL TO BE ADDED LATER');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1F1B251EE141BCEA` (`wishlish_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `purchased_item`
--
ALTER TABLE `purchased_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F84821416C755722` (`buyer_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8D93D64918F45C82` (`website_id`);

--
-- Index pour la table `user_contributing_wishlists`
--
ALTER TABLE `user_contributing_wishlists`
  ADD PRIMARY KEY (`user_id`,`wishlist_id`),
  ADD KEY `IDX_9F3DD295A76ED395` (`user_id`),
  ADD KEY `IDX_9F3DD295FB8E54CD` (`wishlist_id`);

--
-- Index pour la table `user_invited_wishlists`
--
ALTER TABLE `user_invited_wishlists`
  ADD PRIMARY KEY (`user_id`,`wishlist_id`),
  ADD KEY `IDX_F2A6939EA76ED395` (`user_id`),
  ADD KEY `IDX_F2A6939EFB8E54CD` (`wishlist_id`);

--
-- Index pour la table `website`
--
ALTER TABLE `website`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9CE12A31F675F31B` (`author_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `item`
--
ALTER TABLE `item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `website`
--
ALTER TABLE `website`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `FK_880E0D76BF396750` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `FK_1F1B251EE141BCEA` FOREIGN KEY (`wishlish_id`) REFERENCES `wishlist` (`id`);

--
-- Contraintes pour la table `purchased_item`
--
ALTER TABLE `purchased_item`
  ADD CONSTRAINT `FK_F84821416C755722` FOREIGN KEY (`buyer_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_F8482141BF396750` FOREIGN KEY (`id`) REFERENCES `item` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D64918F45C82` FOREIGN KEY (`website_id`) REFERENCES `website` (`id`);

--
-- Contraintes pour la table `user_contributing_wishlists`
--
ALTER TABLE `user_contributing_wishlists`
  ADD CONSTRAINT `FK_9F3DD295A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_9F3DD295FB8E54CD` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlist` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_invited_wishlists`
--
ALTER TABLE `user_invited_wishlists`
  ADD CONSTRAINT `FK_F2A6939EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_F2A6939EFB8E54CD` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlist` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `FK_9CE12A31F675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
