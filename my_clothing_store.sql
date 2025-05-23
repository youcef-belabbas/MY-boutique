-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 21 mai 2025 à 04:31
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `my_clothing_store`
--

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `event_requests`
--

CREATE TABLE `event_requests` (
  `id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `time` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `event_requests`
--

INSERT INTO `event_requests` (`id`, `title`, `image`, `description`, `event_date`, `time`, `location`, `status`, `created_by`, `created_at`) VALUES
('682d378772f2a', 'blakc day', 'https://media2.maison-thuret.com/6230-superlarge_default/gilet-enfant-en-peau-de-mouton-naturelle.webp', 'happygood', '2025-05-22', '6.00-9.00 pm', 'djelfa', 'approved', NULL, '2025-05-21 04:16:39'),
('682d39654a069', 'free dayyy', 'https://s.yimg.com/ny/api/res/1.2/NBYHVZ4EuSALuaDyUH5pQg--/YXBwaWQ9aGlnaGxhbmRlcjt3PTk2MDtoPTE0NDA-/https://media.zenfs.com/en/fashionista_850/4931b6fe9ffe30c5c184a4ffebefdc27', 'ezzz', '2025-05-22', '06am-09pm', 'djelfa', 'approved', NULL, '2025-05-21 04:24:37'),
('event1', 'Summer Fashion Show 2025', 'https://d1csarkz8obe9u.cloudfront.net/posterpreviews/salon-beauty-fashion-service-promo-instagram-design-template-485064e368deeda7177fbed2885aabca_screen.jpg?ts=1611486316', 'Join us for our exclusive summer collection reveal!', '2025-06-15', '6:00 PM - 9:00 PM', 'MY Boutique Main Store', 'approved', 'admin', '2025-05-21 03:11:40');

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('Chemises','Pantalons','Vestes','Chaussures') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `sales` int(11) DEFAULT 0,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `stock`, `sales`, `image`) VALUES
('682d21505e00b', 'vest', 'Vestes', 500.00, 4, 1, 'https://kiabi.dz/cdn/shop/products/AZO63_2_ZC1.jpg?v=1738241932&width=1946'),
('682d238509c7e', 'kebch vest', 'Vestes', 10000.00, 1, 1, 'https://media2.maison-thuret.com/6230-superlarge_default/gilet-enfant-en-peau-de-mouton-naturelle.webp');

-- --------------------------------------------------------

--
-- Structure de la table `product_comments`
--

CREATE TABLE `product_comments` (
  `id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `rating` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `product_comments`
--

INSERT INTO `product_comments` (`id`, `product_id`, `user_name`, `comment`, `rating`) VALUES
('682d26c50b18e', '682d238509c7e', 'djelfa', 'ez', 1),
('682d2c410127f', '682d238509c7e', 'djelfa', 'wow', 5);

-- --------------------------------------------------------

--
-- Structure de la table `product_requests`
--

CREATE TABLE `product_requests` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `request_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `product_requests`
--

INSERT INTO `product_requests` (`id`, `name`, `category`, `price`, `stock`, `image`, `user_id`, `status`, `request_date`) VALUES
('682d238509c7e', 'kebch vest', 'Vestes', 10000.00, 2, 'https://media2.maison-thuret.com/6230-superlarge_default/gilet-enfant-en-peau-de-mouton-naturelle.webp', '682d1e70a79bc', 'approved', '2025-05-21 01:51:17');

-- --------------------------------------------------------

--
-- Structure de la table `purchases`
--

CREATE TABLE `purchases` (
  `id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `purchase_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `purchases`
--

INSERT INTO `purchases` (`id`, `user_id`, `purchase_date`) VALUES
('682d1eca46bb7', '682d1e70a79bc', '2025-05-21 02:31:06'),
('682d1ecb3c614', '682d1e70a79bc', '2025-05-21 02:31:07'),
('682d1ecba2335', '682d1e70a79bc', '2025-05-21 02:31:07'),
('682d263ae3902', '682d1e4411511', '2025-05-21 03:02:50'),
('682d264084098', '682d1e4411511', '2025-05-21 03:02:56'),
('682d26b7d066f', '682d1e4411511', '2025-05-21 03:04:55'),
('682d2866be6d7', '682d1e4411511', '2025-05-21 03:12:06'),
('682d286a532de', '682d1e4411511', '2025-05-21 03:12:10');

-- --------------------------------------------------------

--
-- Structure de la table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` varchar(50) NOT NULL,
  `purchase_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `quantity`) VALUES
('682d263ae4726', '682d263ae3902', '682d21505e00b', 1),
('682d264084d22', '682d264084098', '682d238509c7e', 1),
('682d26b7d14ca', '682d26b7d066f', '682d21505e00b', 1),
('682d2866bff91', '682d2866be6d7', '682d21505e00b', 1),
('682d286a59cc5', '682d286a532de', '682d238509c7e', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `card` varchar(50) DEFAULT NULL,
  `role` enum('client','it','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `email`, `password`, `address`, `card`, `role`) VALUES
('682d183dd4773', 'djelfa', 'youcef', 'gg@gmail.com', '123', 'djelfa', '5+63', 'admin'),
('682d1e4411511', 'djelfa', 'youcef', 'gg2@gmail.com', '2', 'djelfa', '95455', 'client'),
('682d1e70a79bc', 'djelfa3', '213', 'gg3@gmail.com', '1', 'djelfa', '485746', 'it');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `event_requests`
--
ALTER TABLE `event_requests`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `product_comments`
--
ALTER TABLE `product_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product_requests`
--
ALTER TABLE `product_requests`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `product_comments`
--
ALTER TABLE `product_comments`
  ADD CONSTRAINT `product_comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Contraintes pour la table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
  ADD CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
