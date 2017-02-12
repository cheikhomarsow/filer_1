-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 04 Janvier 2017 à 20:44
-- Version du serveur :  10.1.19-MariaDB
-- Version de PHP :  5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `filer`
--

-- --------------------------------------------------------

--
-- Structure de la table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `files`
--

INSERT INTO `files` (`id`, `file_name`, `file_url`, `id_user`, `date`) VALUES
(14, 'mdr.png', 'files/cosinus/mdr.png', 49, '2017-01-04 20:40:36'),
(15, 'yagami.jpg', 'files/cosinus/yagami.jpg', 49, '2017-01-04 20:40:58'),
(16, 'death-note.jpg', 'files/nathael_arki/death-note.jpg', 50, '2017-01-04 20:41:51'),
(18, 'toto.txt', 'files/nathael_arki/toto.txt', 50, '2017-01-04 20:42:11'),
(20, 'SujetFiler.pdf', 'files/nathael_arki/SujetFiler.pdf', 50, '2017-01-04 20:42:51');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `secret_question` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `secret_answer` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `firstname`, `lastname`, `secret_question`, `secret_answer`) VALUES
(49, 'cosinus', '402cc45b238fedcc8d8f846e05f46d40', 'SOW', 'Cheikh Omar', 'Plat preferee ?', 'mafe'),
(50, 'nathael_arki', '402cc45b238fedcc8d8f846e05f46d40', '', '', 'Footballeur prefere ?', 'Ronaldo');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_user_2` (`id_user`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
