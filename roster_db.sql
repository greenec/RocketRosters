-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 22, 2017 at 01:06 PM
-- Server version: 5.7.19-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `roster_github`
--

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE `codes` (
  `code` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `joinKey` int(10) UNSIGNED NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(60) NOT NULL,
  `role` varchar(9) NOT NULL,
  `id` varchar(6) NOT NULL,
  `registered` tinyint(1) NOT NULL DEFAULT '0',
  `lastChange` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `joinKey` int(10) UNSIGNED NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `playerID` varchar(6) NOT NULL,
  `dob` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recruiters`
--

CREATE TABLE `recruiters` (
  `recruiterID` int(10) UNSIGNED NOT NULL,
  `tournamentID` int(10) UNSIGNED NOT NULL,
  `joinDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(32) NOT NULL,
  `access` int(11) DEFAULT NULL,
  `data` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `teamPlayers`
--

CREATE TABLE `teamPlayers` (
  `playerID` int(10) UNSIGNED NOT NULL,
  `teamID` int(10) UNSIGNED NOT NULL,
  `parentEmail` varchar(100) NOT NULL,
  `playerEmail` varchar(100) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `jersey` varchar(3) NOT NULL,
  `position` varchar(15) NOT NULL,
  `address` varchar(100) NOT NULL,
  `city` varchar(35) NOT NULL,
  `state` varchar(25) NOT NULL,
  `zip` varchar(5) NOT NULL,
  `graduating` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `joinKey` int(10) UNSIGNED NOT NULL,
  `teamName` varchar(75) NOT NULL,
  `coachID` int(10) UNSIGNED NOT NULL,
  `teamID` varchar(6) NOT NULL,
  `creationDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tournamentPlayers`
--

CREATE TABLE `tournamentPlayers` (
  `tournamentID` int(10) UNSIGNED NOT NULL,
  `teamID` int(10) UNSIGNED NOT NULL,
  `playerID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tournaments`
--

CREATE TABLE `tournaments` (
  `joinKey` int(10) UNSIGNED NOT NULL,
  `tournamentName` varchar(100) NOT NULL,
  `directorID` int(10) UNSIGNED NOT NULL,
  `tournamentID` varchar(6) NOT NULL,
  `creationDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tournamentTeams`
--

CREATE TABLE `tournamentTeams` (
  `tournamentID` int(10) UNSIGNED NOT NULL,
  `teamID` int(10) UNSIGNED NOT NULL,
  `joinDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `codes`
--
ALTER TABLE `codes`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`joinKey`),
  ADD UNIQUE KEY `login` (`email`,`registered`) USING BTREE,
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`joinKey`),
  ADD UNIQUE KEY `playerID` (`playerID`) USING BTREE,
  ADD UNIQUE KEY `players` (`dob`,`lastName`,`firstName`) USING BTREE;

--
-- Indexes for table `recruiters`
--
ALTER TABLE `recruiters`
  ADD UNIQUE KEY `recruiters` (`recruiterID`,`tournamentID`) USING BTREE,
  ADD KEY `tournamentID` (`tournamentID`),
  ADD KEY `recruiterID` (`recruiterID`) USING BTREE;

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teamPlayers`
--
ALTER TABLE `teamPlayers`
  ADD UNIQUE KEY `teamPlayers` (`teamID`,`playerID`) USING BTREE,
  ADD KEY `playerID` (`playerID`),
  ADD KEY `teamID` (`teamID`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`joinKey`),
  ADD UNIQUE KEY `teamID` (`teamID`) USING BTREE,
  ADD UNIQUE KEY `ownership` (`joinKey`,`coachID`) USING BTREE;

--
-- Indexes for table `tournamentPlayers`
--
ALTER TABLE `tournamentPlayers`
  ADD UNIQUE KEY `playerInTournament` (`tournamentID`,`teamID`,`playerID`) USING BTREE,
  ADD KEY `teamID` (`teamID`),
  ADD KEY `playerID` (`playerID`),
  ADD KEY `tournamentID` (`tournamentID`) USING BTREE,
  ADD KEY `tournamentPlayers` (`tournamentID`,`teamID`) USING BTREE;

--
-- Indexes for table `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`joinKey`),
  ADD UNIQUE KEY `tournamentID` (`tournamentID`) USING BTREE,
  ADD UNIQUE KEY `ownership` (`joinKey`,`directorID`) USING BTREE;

--
-- Indexes for table `tournamentTeams`
--
ALTER TABLE `tournamentTeams`
  ADD UNIQUE KEY `tournamentTeams` (`tournamentID`,`teamID`) USING BTREE,
  ADD KEY `teamID` (`teamID`),
  ADD KEY `tournamentID` (`tournamentID`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `joinKey` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `joinKey` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `joinKey` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `joinKey` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `recruiters`
--
ALTER TABLE `recruiters`
  ADD CONSTRAINT `recruiters_ibfk_1` FOREIGN KEY (`tournamentID`) REFERENCES `tournaments` (`joinKey`) ON DELETE CASCADE;

--
-- Constraints for table `teamPlayers`
--
ALTER TABLE `teamPlayers`
  ADD CONSTRAINT `teamPlayers_ibfk_1` FOREIGN KEY (`teamID`) REFERENCES `teams` (`joinKey`) ON DELETE CASCADE;

--
-- Constraints for table `tournamentPlayers`
--
ALTER TABLE `tournamentPlayers`
  ADD CONSTRAINT `tournamentPlayers_ibfk_2` FOREIGN KEY (`tournamentID`) REFERENCES `tournaments` (`joinKey`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournamentPlayers_ibfk_3` FOREIGN KEY (`playerID`) REFERENCES `teamPlayers` (`playerID`) ON DELETE CASCADE;

--
-- Constraints for table `tournamentTeams`
--
ALTER TABLE `tournamentTeams`
  ADD CONSTRAINT `tournamentTeams_ibfk_1` FOREIGN KEY (`tournamentID`) REFERENCES `tournaments` (`joinKey`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournamentTeams_ibfk_2` FOREIGN KEY (`teamID`) REFERENCES `teams` (`joinKey`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
