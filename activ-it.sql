-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: Št 16.Feb 2023, 00:26
-- Verzia serveru: 10.4.24-MariaDB
-- Verzia PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `activ-it`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `city`
--

CREATE TABLE `city` (
  `city_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Sťahujem dáta pre tabuľku `city`
--

INSERT INTO `city` (`city_id`, `name`, `latitude`, `longitude`) VALUES
(3, 'Banská Bystrica', 48.7385, 19.1309),
(2, 'Bratislava', 48.1467, 17.1228),
(1, 'Čimhová', 49.3615, 19.6947);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `controller`
--

CREATE TABLE `controller` (
  `controller_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `controller_path` varchar(255) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Sťahujem dáta pre tabuľku `controller`
--

INSERT INTO `controller` (`controller_id`, `title`, `url`, `description`, `controller_path`) VALUES
(1, 'Weather', 'weather', 'Displaying the weather in the given city', 'WeatherModul\\Controller\\Weather'),
(2, 'City Administration', 'city', 'Administrative menu, list, adding and deleting city records.', 'CityModul\\Controller\\City'),
(3, 'Multi-step PHP form\r\n', 'multiple-form', 'Display and processing Multi-step PHP form', 'MultipleFormModul\\Controller\\MultipleForm');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user_data`
--

CREATE TABLE `user_data` (
  `user_data_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `register_number` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `post_code` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `iban` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Sťahujem dáta pre tabuľku `user_data`
--

INSERT INTO `user_data` (`user_data_id`, `name`, `last_name`, `street`, `register_number`, `post_code`, `city`, `iban`) VALUES
(1, 'Michal', 'Šimaľa', 'Čimhová', '109', '02712', 'Čimhová', 'DE89370400440532013000'),
(2, 'Jožko', 'Mrkvička', 'Zahradková', '1', '00000', 'Zem', 'AL35202111090000000001234567');

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`city_id`),
  ADD UNIQUE KEY `uniq_name` (`name`),
  ADD UNIQUE KEY `uniq` (`latitude`,`longitude`),
  ADD UNIQUE KEY `uniq_entry` (`name`,`latitude`,`longitude`);

--
-- Indexy pre tabuľku `controller`
--
ALTER TABLE `controller`
  ADD PRIMARY KEY (`controller_id`),
  ADD UNIQUE KEY `url` (`url`);

--
-- Indexy pre tabuľku `user_data`
--
ALTER TABLE `user_data`
  ADD PRIMARY KEY (`user_data_id`),
  ADD UNIQUE KEY `name_iban` (`name`,`last_name`,`iban`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `city`
--
ALTER TABLE `city`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pre tabuľku `controller`
--
ALTER TABLE `controller`
  MODIFY `controller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pre tabuľku `user_data`
--
ALTER TABLE `user_data`
  MODIFY `user_data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
