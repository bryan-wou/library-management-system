-- --------------------------------------------------------
-- Host:                         10.1.255.195
-- Server version:               10.5.19-MariaDB-0+deb11u2 - Debian 11
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for lms
CREATE DATABASE IF NOT EXISTS `lms` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `lms`;

-- Dumping structure for table lms.Biblio
CREATE TABLE IF NOT EXISTS `Biblio` (
  `biblioID` int(11) NOT NULL AUTO_INCREMENT,
  `biblioTitle` varchar(256) DEFAULT NULL,
  `biblioCallNumber` varchar(256) DEFAULT NULL,
  `biblioAuthor` varchar(256) DEFAULT NULL,
  `biblioPublisher` varchar(256) DEFAULT NULL,
  `biblioISBN` varchar(256) DEFAULT NULL,
  `isRestricted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`biblioID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.Biblio: ~0 rows (approximately)
DELETE FROM `Biblio`;

-- Dumping structure for table lms.BiblioItem
CREATE TABLE IF NOT EXISTS `BiblioItem` (
  `biblioItemID` int(11) NOT NULL AUTO_INCREMENT,
  `biblioID` int(11) NOT NULL,
  `biblioItemLocation` varchar(256) DEFAULT NULL,
  `biblioItemPrice` varchar(256) DEFAULT NULL,
  `biblioItemStatus` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`biblioItemID`),
  KEY `Index 2` (`biblioID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.BiblioItem: ~0 rows (approximately)
DELETE FROM `BiblioItem`;

-- Dumping structure for table lms.BiblioItemBarcode
CREATE TABLE IF NOT EXISTS `BiblioItemBarcode` (
  `biblioItemBarcode` varchar(256) NOT NULL,
  `biblioItemID` int(11) NOT NULL,
  PRIMARY KEY (`biblioItemBarcode`),
  KEY `Index 2` (`biblioItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.BiblioItemBarcode: ~0 rows (approximately)
DELETE FROM `BiblioItemBarcode`;

-- Dumping structure for table lms.Fine
CREATE TABLE IF NOT EXISTS `Fine` (
  `fineID` int(11) NOT NULL AUTO_INCREMENT,
  `transactionID` int(11) NOT NULL,
  `fineType` varchar(256) NOT NULL,
  `fineAmount` decimal(10,2) NOT NULL,
  `waivedAmount` decimal(10,2) NOT NULL,
  `receivedAmount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`fineID`),
  KEY `Index 2` (`transactionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.Fine: ~0 rows (approximately)
DELETE FROM `Fine`;

-- Dumping structure for table lms.Librarian
CREATE TABLE IF NOT EXISTS `Librarian` (
  `librarianID` int(11) NOT NULL AUTO_INCREMENT,
  `librarianName` varchar(256) NOT NULL DEFAULT '',
  `username` varchar(256) NOT NULL DEFAULT '',
  `password` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`librarianID`),
  UNIQUE KEY `Index 2` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.Librarian: ~1 rows (approximately)
DELETE FROM `Librarian`;
INSERT INTO `Librarian` (`librarianID`, `librarianName`, `username`, `password`) VALUES
	(1, 'Admin', 'admin', '$2y$10$uiYRTuS8eWAvLR955jgW2uR3jfHt3F8OCxDhUhbHmCkzxM097k53O');

-- Dumping structure for table lms.LibrarianPrivilege
CREATE TABLE IF NOT EXISTS `LibrarianPrivilege` (
  `librarianPrivilegeID` int(11) NOT NULL AUTO_INCREMENT,
  `librarianPrivilegeTypeID` int(11) NOT NULL,
  `librarianID` int(11) NOT NULL,
  PRIMARY KEY (`librarianPrivilegeID`),
  KEY `Index 2` (`librarianPrivilegeTypeID`),
  KEY `Index 3` (`librarianID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.LibrarianPrivilege: ~10 rows (approximately)
DELETE FROM `LibrarianPrivilege`;
INSERT INTO `LibrarianPrivilege` (`librarianPrivilegeID`, `librarianPrivilegeTypeID`, `librarianID`) VALUES
	(1, 1, 1),
	(2, 2, 1),
	(3, 3, 1),
	(4, 4, 1),
	(5, 5, 1),
	(6, 6, 1),
	(7, 7, 1),
	(8, 8, 1),
	(20, 9, 1),
	(21, 10, 1);

-- Dumping structure for table lms.LibrarianPrivilegeType
CREATE TABLE IF NOT EXISTS `LibrarianPrivilegeType` (
  `librarianPrivilegeTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `librarianPrivilegeTypeKeyword` varchar(256) NOT NULL,
  `librarianPrivilegeTypeName` varchar(256) NOT NULL,
  PRIMARY KEY (`librarianPrivilegeTypeID`),
  UNIQUE KEY `Index 2` (`librarianPrivilegeTypeKeyword`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.LibrarianPrivilegeType: ~10 rows (approximately)
DELETE FROM `LibrarianPrivilegeType`;
INSERT INTO `LibrarianPrivilegeType` (`librarianPrivilegeTypeID`, `librarianPrivilegeTypeKeyword`, `librarianPrivilegeTypeName`) VALUES
	(1, 'add_biblioitem_record', 'Add Biblio/Item Record'),
	(2, 'search_biblioitem_record', 'Search Biblio/Item Record'),
	(3, 'add_patron_record', 'Add Patron Record'),
	(4, 'search_patron_record', 'Search Patron Record'),
	(5, 'circ_checkout', 'Check Out Items'),
	(6, 'circ_checkin', 'Check In Items'),
	(7, 'circ_renew', 'Renew Items'),
	(8, 'manage_librarians', 'Manage Librarians'),
	(9, 'manage_settings', 'Manage Settings'),
	(10, 'manage_patron_categories', 'Manage Patron Categories');

-- Dumping structure for table lms.Patron
CREATE TABLE IF NOT EXISTS `Patron` (
  `patronID` int(11) NOT NULL AUTO_INCREMENT,
  `patronName` varchar(256) NOT NULL,
  `patronContact` varchar(256) DEFAULT NULL,
  `patronCategoryID` int(11) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`patronID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.Patron: ~0 rows (approximately)
DELETE FROM `Patron`;

-- Dumping structure for table lms.PatronBarcode
CREATE TABLE IF NOT EXISTS `PatronBarcode` (
  `patronBarcode` varchar(256) NOT NULL DEFAULT '',
  `patronID` int(11) NOT NULL,
  PRIMARY KEY (`patronBarcode`),
  KEY `Index 2` (`patronID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.PatronBarcode: ~0 rows (approximately)
DELETE FROM `PatronBarcode`;

-- Dumping structure for table lms.PatronCategory
CREATE TABLE IF NOT EXISTS `PatronCategory` (
  `patronCategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `patronCategoryName` varchar(256) NOT NULL,
  `itemCheckOutDays` int(11) NOT NULL,
  `itemCheckOutLimit` int(11) NOT NULL,
  `itemRenewLimit` int(11) NOT NULL,
  PRIMARY KEY (`patronCategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.PatronCategory: ~2 rows (approximately)
DELETE FROM `PatronCategory`;
INSERT INTO `PatronCategory` (`patronCategoryID`, `patronCategoryName`, `itemCheckOutDays`, `itemCheckOutLimit`, `itemRenewLimit`) VALUES
	(1, 'Student', 14, 2, 1),
	(2, 'Staff', 28, 20, 1);

-- Dumping structure for table lms.Settings
CREATE TABLE IF NOT EXISTS `Settings` (
  `settingsName` varchar(64) NOT NULL,
  `settingsValue` text DEFAULT NULL,
  PRIMARY KEY (`settingsName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.Settings: ~6 rows (approximately)
DELETE FROM `Settings`;
INSERT INTO `Settings` (`settingsName`, `settingsValue`) VALUES
	('allow_opac', 'true'),
	('allow_opac_patron_login', 'true'),
	('allow_opac_patron_renew', 'true'),
	('api_key', NULL),
	('library_code', '0000'),
	('library_name', '');

-- Dumping structure for table lms.Transaction
CREATE TABLE IF NOT EXISTS `Transaction` (
  `transactionID` int(11) NOT NULL AUTO_INCREMENT,
  `biblioItemID` int(11) NOT NULL,
  `patronID` int(11) NOT NULL,
  `checkOutDate` date NOT NULL,
  `expectedCheckInDate` date NOT NULL,
  `actualCheckInDate` date DEFAULT NULL,
  `renewCount` int(11) NOT NULL,
  `void` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transactionID`),
  KEY `Index 2` (`biblioItemID`),
  KEY `Index 3` (`patronID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table lms.Transaction: ~0 rows (approximately)
DELETE FROM `Transaction`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
