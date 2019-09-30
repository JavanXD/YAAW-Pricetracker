-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 30. Sep 2019 um 19:52
-- Server-Version: 5.5.64-MariaDB
-- PHP-Version: 7.1.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `yaaw`
--
CREATE DATABASE IF NOT EXISTS `yaaw` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `yaaw`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Prices`
--

CREATE TABLE `Prices` (
  `PriceID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Price` float NOT NULL,
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Products`
--

CREATE TABLE `Products` (
  `ProductID` int(11) NOT NULL,
  `ASIN` char(15) NOT NULL,
  `Region` char(5) NOT NULL,
  `ProductCode` char(5) NOT NULL,
  `ProductTitle` char(255) NOT NULL,
  `ProductUrl` char(255) NOT NULL,
  `ProductImage` char(255) NOT NULL,
  `ObservationSince` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `LastUpdate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Tracks`
--

CREATE TABLE `Tracks` (
  `TrackID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `IsFavorite` tinyint(1) NOT NULL DEFAULT '0',
  `IsMuted` tinyint(1) NOT NULL DEFAULT '0',
  `PriceStarted` float NOT NULL,
  `PriceAlarm` float NOT NULL,
  `LastEmail` int(11) NOT NULL,
  `LastEmailPrice` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Users`
--

CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL,
  `Email` char(255) NOT NULL,
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Prices`
--
ALTER TABLE `Prices`
  ADD PRIMARY KEY (`PriceID`);

--
-- Indizes für die Tabelle `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`ProductID`),
  ADD UNIQUE KEY `ASIN` (`ASIN`,`Region`) USING BTREE;

--
-- Indizes für die Tabelle `Tracks`
--
ALTER TABLE `Tracks`
  ADD PRIMARY KEY (`TrackID`);

--
-- Indizes für die Tabelle `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Prices`
--
ALTER TABLE `Prices`
  MODIFY `PriceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Products`
--
ALTER TABLE `Products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Tracks`
--
ALTER TABLE `Tracks`
  MODIFY `TrackID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
