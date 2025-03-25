--
-- Déchargement des données de la table `website`
--

INSERT INTO `website` (`id`) VALUES
(1);


--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `website_id`, `username`, `password`, `email`, `is_locked`, `dtype`) VALUES
(1, 1, 'greg', '$2y$10$gRFH1ERnj1UsnlN.0/KYY.jgS1/QkUDZNx5hJLR8bywcLYIcxIYzG', 'grec@gmail.com', 0, 'user'),
(2, 1, 'jose', '$2y$10$Ytwopd0EIFHtx3EQs2kugOAcVMrFQGQ7g0p/hzUH0ffiQl1JOfgSG', 'jose@gmail.com', 0, 'user'),
(3, 1, 'mamou', '$2y$10$57cJecyuHOZmnCRJqlucye81phwnurU.NBGqCVCNb2z1qkq/UhlbS', 'mamou@gmail.com', 0, 'user'),
(4, 1, 'elodie', '$2y$10$f0HuXoAHWq7s7CG95iBvi.qxBpHExfyUrHPwQSRJSg1usgSTqsmJK', 'elodie@imt-atlantique.net', 0, 'user'),
(5, 1, 'maud', '$2y$10$vIjemuoT/dCRV5HGPOHzSel5RCt2bfh5dTps.7d4nzWld2uUYvDoW', 'maud@gmail.com', 0, 'admin'),
(6, 1, 'sylvie', '$2y$10$Zv8zi58sQXFupkInOiaa5u5HKKuwHDzFp8o/oK26NWWezSNyUhaB6', 'sylvie@orange.fr', 0, 'admin');


--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id`) VALUES
(5),
(6);


--
-- Déchargement des données de la table `wishlist`
--

INSERT INTO `wishlist` (`id`, `author_id`, `deadline`, `name`, `sharing_url`, `display_url`) VALUES
(1, 5, '2026-01-31', 'Fablab', 'invite/efc5582c-c6ca-455d-be4c-e93499b111c6', '1c5a7c2f-31fd-4aa0-8301-d1bfdffd88f8'),
(2, 3, '2025-08-20', 'Cadeau Elodie', 'invite/efc5582c-c6ca-455d-be4c-e93499b111c5', '1c5a7c2f-31fd-4aa0-8301-d1bfdffd88f7');


--
-- Déchargement des données de la table `item`
--

INSERT INTO `item` (`id`, `wishlist_id`, `title`, `description`, `price`, `url`, `dtype`) VALUES
(1, 1, 'Imprimante 3d', 'Imprime en 3d', 239, 'https://www.bol.com/be/fr/p/printer-3dandprint-3d-x1-3dandprint-de-construction-technologie-d-impression-fdm-pla/9200000130935184/?bltgh=gd2euj872Zm5Zd0clJNN-w.4_27.28.ProductTitle', 'item'),
(2, 1, 'Bois', 'Planche sapin petits noeuds, Ep.25 x l.195 mm mm, 2.5 m', 13.9, 'https://www.leroymerlin.fr/produits/planche-sapin-petits-noeuds-ep-25-x-l-195-mm-mm-2-5-m-79322852.html', 'item'),
(3, 2, 'Alcool', 'LE PHILTRE Organic Vodka\r\n', 49, 'https://www.whisky.fr/le-philtre-organic-vodka.html', 'item'),
(4, 2, 'T-shirt', 'Bah c un t shirt', 19.99, 'https://www.cache-cache.fr/t-shirt-details-fleurs-rouge-femme-36125346575460486.html', 'item'),
(5, 2, 'Rattrapage', 'Rattrapage en élec, ATSA, Physique, méca flux et proba\r\n\r\nMon coach Maths 6e avec Nicolas Herla', 8.9, 'https://site.nathan.fr/livres/mon-coach-maths-6e-avec-nicolas-herla-9782095022167.html', 'purchasedItem');

--
-- Déchargement des données de la table `purchased_item`
--

INSERT INTO `purchased_item` (`id`, `buyer`, `congratulory_message`, `purchase_proof`) VALUES
(5, 'sylvie', 'Bonne chance pour tes rattrapages !', 'LIEN VERS LE FICHIER');


--
-- Déchargement des données de la table `user_contributing_wishlists`
--

INSERT INTO `user_contributing_wishlists` (`user_id`, `wishlist_id`) VALUES
(1, 2),
(4, 1),
(5, 2),
(6, 1),
(6, 2);

--
-- Déchargement des données de la table `user_invited_wishlists`
--

INSERT INTO `user_invited_wishlists` (`user_id`, `wishlist_id`) VALUES
(2, 1);
