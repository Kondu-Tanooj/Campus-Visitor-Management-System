-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 25, 2025 at 04:14 PM
-- Server version: 8.0.41-0ubuntu0.22.04.1
-- PHP Version: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cvms`
--

-- --------------------------------------------------------

--
-- Table structure for table `cam_capture_table`
--

CREATE TABLE `cam_capture_table` (
  `sno` int NOT NULL,
  `regd_id` varchar(50) NOT NULL,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','security','dept') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$pdcaLFnRXiOQ/2biOLBP5.sldwF9X4KZr0upwTzOpFo0KougWi7s6', 'admin'),
(2, 'security', '$2y$10$7Gvcvrow1chKdz9LpYJ8OuXhdauNizqI0FfXPxGhmUVxLGBpTFy.q', 'security'),
(3, 'cse', '$2y$10$79P/ej1Xtj25UkpqjE1hu.U51d0aWZcjPXyRgsBouCIi.Y2KYD2.C', 'dept'),
(4, 'it', '$2y$10$nMj0pmX2UOh4u/YKzWeMt.xpKja7ZidJQjEPwjWqrzvx3c6zGhpTK', 'dept'),
(5, 'de', '$2y$10$Z.u9EF9wiCgpQ2Z/uVWM4ervkDtlfsRQEPoOZgQfqZQ8SlzIj8NJm', 'dept'),
(6, 'ece', '$2y$10$8lZqFbZaiawgeYp2WKx9P.Vlok10fLFIEJfJLwagyI9i5JNOdZjy6', 'dept'),
(7, 'eee', '$2y$10$l34VbTrCeckJsZsAIYbPKOPopKPuodcNfR3phI1YG6z.Oh3ERWGqa', 'dept'),
(8, 'mech', '$2y$10$6jxGool26FespW7.AD44TuTtB/HK8JvqJjp.79emrJfyzedzlIOzO', 'dept'),
(9, 'mba', '$2y$10$AATXjp5jLps9omSJA3bb2eRsleYejsmkLCo9WMO1N0sLNURK1AZwC', 'dept'),
(10, 'civil', '$2y$10$4CNQ49khPaK2lIcsovFRHOrfyiXbhnsLmpEFG1kEuHIFlsRtdGVx.', 'dept'),
(11, 'chem', '$2y$10$pK06g1ofBD9RE0UqTQyxNORqhzYpDtuKtEPeUPRD8s3ZWLlCoC2iC', 'dept');

-- --------------------------------------------------------

--
-- Table structure for table `cse_1_A_subject_timetable`
--

CREATE TABLE `cse_1_A_subject_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT NULL,
  `hour2` varchar(50) DEFAULT NULL,
  `hour3` varchar(50) DEFAULT NULL,
  `hour4` varchar(50) DEFAULT NULL,
  `hour5` varchar(50) DEFAULT NULL,
  `hour6` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_1_A_subject_timetable`
--

INSERT INTO `cse_1_A_subject_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'MVVC', 'PHY', 'LADE', 'LADE(T)', 'LP', 'T&P'),
('Monday', 'LADE', 'T&P', 'HW', 'COUN.HR', 'OTSME', 'OTSME'),
('Saturday', 'Add-on programs', 'Add-on programs', 'Add-on programs', 'Add-on programs', 'Add-on programs', 'Add-on programs'),
('Thursday', 'PHY LAB', 'PHY LAB', 'MVVC', 'LP', 'ES', 'HW'),
('Tuesday', 'PHY', 'COUN.HR', 'MVVC', 'MVVC(T)', 'ES', 'LIBRARY'),
('Wednesday', 'EEW', 'EEW', 'EEW', 'PHY', 'LADE', 'SPORTS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_1_A_timetable`
--

CREATE TABLE `cse_1_A_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_1_A_timetable`
--

INSERT INTO `cse_1_A_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'NO CLASS', 'NO CLASS', 'NO CLASS', 'NO CLASS', 'NO CLASS', 'NO CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'NO CLASS', 'CLASS', 'CLASS', 'NO CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_1_B_timetable`
--

CREATE TABLE `cse_1_B_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_1_B_timetable`
--

INSERT INTO `cse_1_B_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_1_C_subject_timetable`
--

CREATE TABLE `cse_1_C_subject_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT NULL,
  `hour2` varchar(50) DEFAULT NULL,
  `hour3` varchar(50) DEFAULT NULL,
  `hour4` varchar(50) DEFAULT NULL,
  `hour5` varchar(50) DEFAULT NULL,
  `hour6` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_1_C_subject_timetable`
--

INSERT INTO `cse_1_C_subject_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', NULL, NULL, NULL, NULL, NULL, NULL),
('Monday', NULL, NULL, NULL, NULL, NULL, NULL),
('Saturday', NULL, NULL, NULL, NULL, NULL, NULL),
('Thursday', NULL, NULL, NULL, NULL, NULL, NULL),
('Tuesday', NULL, NULL, NULL, NULL, NULL, NULL),
('Wednesday', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cse_1_C_timetable`
--

CREATE TABLE `cse_1_C_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_1_C_timetable`
--

INSERT INTO `cse_1_C_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_1_D_timetable`
--

CREATE TABLE `cse_1_D_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_1_D_timetable`
--

INSERT INTO `cse_1_D_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_1_E_subject_timetable`
--

CREATE TABLE `cse_1_E_subject_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT NULL,
  `hour2` varchar(50) DEFAULT NULL,
  `hour3` varchar(50) DEFAULT NULL,
  `hour4` varchar(50) DEFAULT NULL,
  `hour5` varchar(50) DEFAULT NULL,
  `hour6` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_1_E_subject_timetable`
--

INSERT INTO `cse_1_E_subject_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', NULL, NULL, NULL, NULL, NULL, NULL),
('Monday', NULL, NULL, NULL, NULL, NULL, NULL),
('Saturday', NULL, NULL, NULL, NULL, NULL, NULL),
('Thursday', NULL, NULL, NULL, NULL, NULL, NULL),
('Tuesday', NULL, NULL, NULL, NULL, NULL, NULL),
('Wednesday', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cse_1_E_timetable`
--

CREATE TABLE `cse_1_E_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_1_E_timetable`
--

INSERT INTO `cse_1_E_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_2_A_subject_timetable`
--

CREATE TABLE `cse_2_A_subject_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT NULL,
  `hour2` varchar(50) DEFAULT NULL,
  `hour3` varchar(50) DEFAULT NULL,
  `hour4` varchar(50) DEFAULT NULL,
  `hour5` varchar(50) DEFAULT NULL,
  `hour6` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_2_A_subject_timetable`
--

INSERT INTO `cse_2_A_subject_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', NULL, NULL, NULL, NULL, NULL, NULL),
('Monday', NULL, NULL, NULL, NULL, NULL, NULL),
('Saturday', NULL, NULL, NULL, NULL, NULL, NULL),
('Thursday', NULL, NULL, NULL, NULL, NULL, NULL),
('Tuesday', NULL, NULL, NULL, NULL, NULL, NULL),
('Wednesday', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cse_2_D_subject_timetable`
--

CREATE TABLE `cse_2_D_subject_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT NULL,
  `hour2` varchar(50) DEFAULT NULL,
  `hour3` varchar(50) DEFAULT NULL,
  `hour4` varchar(50) DEFAULT NULL,
  `hour5` varchar(50) DEFAULT NULL,
  `hour6` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_2_D_subject_timetable`
--

INSERT INTO `cse_2_D_subject_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', '', '', '', '', '', ''),
('Monday', '', '', '', '', '', ''),
('Saturday', '', '', '', '', '', ''),
('Thursday', '', '', '', '', '', ''),
('Tuesday', '', '', '', '', '', 'TNP'),
('Wednesday', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `cse_2_D_timetable`
--

CREATE TABLE `cse_2_D_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_2_D_timetable`
--

INSERT INTO `cse_2_D_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_4_A_subject_timetable`
--

CREATE TABLE `cse_4_A_subject_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT NULL,
  `hour2` varchar(50) DEFAULT NULL,
  `hour3` varchar(50) DEFAULT NULL,
  `hour4` varchar(50) DEFAULT NULL,
  `hour5` varchar(50) DEFAULT NULL,
  `hour6` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_4_A_subject_timetable`
--

INSERT INTO `cse_4_A_subject_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'T&P', 'DAA', 'DAA', 'QM', 'PP', 'PLM'),
('Monday', 'DBMS', 'PP', 'CA', 'QM', 'T&P', 'EHV'),
('Saturday', 'Add-on Programs', 'Add-on Programs', 'Add-on Programs', 'Add-on Programs', 'Add-on Programs', 'Add-on Programs'),
('Thursday', 'PP', 'DBMS', 'CA', 'DBMS LAB', 'DBMS LAB', 'DBMS LAB'),
('Tuesday', 'PLM', 'CA', 'QM', 'DAA', 'DBMS', 'EHV'),
('Wednesday', 'FALAB', 'FA LAB', 'PLM', 'PP LAB', 'PP LAB', 'PP LAB');

-- --------------------------------------------------------

--
-- Table structure for table `cse_4_A_timetable`
--

CREATE TABLE `cse_4_A_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_4_A_timetable`
--

INSERT INTO `cse_4_A_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_4_B_subject_timetable`
--

CREATE TABLE `cse_4_B_subject_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT NULL,
  `hour2` varchar(50) DEFAULT NULL,
  `hour3` varchar(50) DEFAULT NULL,
  `hour4` varchar(50) DEFAULT NULL,
  `hour5` varchar(50) DEFAULT NULL,
  `hour6` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_4_B_subject_timetable`
--

INSERT INTO `cse_4_B_subject_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'PP', 'DBMS', 'CA', 'DAA', 'T&P', 'PLM'),
('Monday', 'DAA', 'DBMS', 'PP', 'PLM', 'CA', 'QM'),
('Saturday', 'Add-on Programs', 'Add-on Programs', 'Add-on Programs', 'Add-on Programs', 'Add-on Programs', 'Add-on Programs'),
('Thursday', 'CA', 'DAA', 'EHV', 'PP LAB', 'PP LAB', 'PP LAB'),
('Tuesday', 'FA LAB', 'FA LAB', 'QM', 'DBMS', 'EHV', 'T&P'),
('Wednesday', 'QM', 'PP', 'PLM', 'DBMS LAB', 'DBMS LAB', 'DBMS LAB');

-- --------------------------------------------------------

--
-- Table structure for table `cse_4_B_timetable`
--

CREATE TABLE `cse_4_B_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_4_B_timetable`
--

INSERT INTO `cse_4_B_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_4_C_timetable`
--

CREATE TABLE `cse_4_C_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_4_C_timetable`
--

INSERT INTO `cse_4_C_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_5_A_timetable`
--

CREATE TABLE `cse_5_A_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_5_A_timetable`
--

INSERT INTO `cse_5_A_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_5_B_timetable`
--

CREATE TABLE `cse_5_B_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_5_B_timetable`
--

INSERT INTO `cse_5_B_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_5_C_timetable`
--

CREATE TABLE `cse_5_C_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_5_C_timetable`
--

INSERT INTO `cse_5_C_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_6_A_subject_timetable`
--

CREATE TABLE `cse_6_A_subject_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT NULL,
  `hour2` varchar(50) DEFAULT NULL,
  `hour3` varchar(50) DEFAULT NULL,
  `hour4` varchar(50) DEFAULT NULL,
  `hour5` varchar(50) DEFAULT NULL,
  `hour6` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_6_A_subject_timetable`
--

INSERT INTO `cse_6_A_subject_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', NULL, NULL, NULL, NULL, NULL, NULL),
('Monday', NULL, NULL, NULL, NULL, NULL, NULL),
('Saturday', NULL, NULL, NULL, NULL, NULL, NULL),
('Thursday', NULL, NULL, NULL, NULL, NULL, NULL),
('Tuesday', NULL, NULL, NULL, NULL, NULL, NULL),
('Wednesday', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cse_6_A_timetable`
--

CREATE TABLE `cse_6_A_timetable` (
  `day` varchar(10) NOT NULL,
  `hour1` varchar(50) DEFAULT 'CLASS',
  `hour2` varchar(50) DEFAULT 'CLASS',
  `hour3` varchar(50) DEFAULT 'CLASS',
  `hour4` varchar(50) DEFAULT 'CLASS',
  `hour5` varchar(50) DEFAULT 'CLASS',
  `hour6` varchar(50) DEFAULT 'CLASS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cse_6_A_timetable`
--

INSERT INTO `cse_6_A_timetable` (`day`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('Friday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Monday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Saturday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Thursday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Tuesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS'),
('Wednesday', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS', 'CLASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse_subjects`
--

CREATE TABLE `cse_subjects` (
  `id` int NOT NULL,
  `semester` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_sections`
--

CREATE TABLE `department_sections` (
  `dept_id` int NOT NULL,
  `department` varchar(50) NOT NULL,
  `section` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `department_sections`
--

INSERT INTO `department_sections` (`dept_id`, `department`, `section`) VALUES
(1, 'cse', 'A'),
(2, 'cse', 'B'),
(3, 'cse', 'C'),
(4, 'cse', 'D'),
(5, 'cse', 'E'),
(6, 'de', 'CSM'),
(7, 'de', 'CSD'),
(8, 'de', 'CIC'),
(9, 'it', 'A'),
(10, 'it', 'B'),
(11, 'it', 'CSIT'),
(12, 'ece', 'A'),
(13, 'ece', 'B'),
(14, 'ece', 'C'),
(15, 'eee', 'A'),
(16, 'eee', 'B'),
(17, 'mech', 'A'),
(18, 'mech', 'B'),
(19, 'mba', 'A'),
(20, 'mba', 'B'),
(21, 'civil', 'A'),
(22, 'civil', 'B'),
(23, 'chem', 'A'),
(24, 'chem', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `devices_table`
--

CREATE TABLE `devices_table` (
  `dev_ID` varchar(50) NOT NULL,
  `type` enum('Main','Hand') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `devices_table`
--

INSERT INTO `devices_table` (`dev_ID`, `type`) VALUES
('dev_1', 'Main'),
('dev_2', 'Hand');

-- --------------------------------------------------------

--
-- Table structure for table `guest_id_status`
--

CREATE TABLE `guest_id_status` (
  `sno` int NOT NULL,
  `guest_id` varchar(30) NOT NULL,
  `available` enum('Yes','No') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guest_id_status`
--

INSERT INTO `guest_id_status` (`sno`, `guest_id`, `available`) VALUES
(1, 'Guest_1', 'Yes'),
(2, 'Guest_2', 'Yes'),
(4, 'Guest_3', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `guest_info`
--

CREATE TABLE `guest_info` (
  `sno` int NOT NULL,
  `guest_id` varchar(15) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `vehicle` enum('Yes','No') NOT NULL,
  `vehicle_number` varchar(20) DEFAULT NULL,
  `no_of_companions` int DEFAULT '0',
  `names_of_companions` text,
  `entry_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `exit_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `invalid_id_table`
--

CREATE TABLE `invalid_id_table` (
  `id` int NOT NULL,
  `rf_id` varchar(50) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `main_entry_table`
--

CREATE TABLE `main_entry_table` (
  `sno` int NOT NULL,
  `regd_no` varchar(20) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `semester` int DEFAULT NULL,
  `batch` varchar(20) DEFAULT NULL,
  `in_time` datetime DEFAULT NULL,
  `out_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `permission_table`
--

CREATE TABLE `permission_table` (
  `sno` int NOT NULL,
  `regd_id` varchar(20) NOT NULL,
  `dept` varchar(100) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `by_user` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `raw_id_dev_1_table`
--

CREATE TABLE `raw_id_dev_1_table` (
  `sno` int NOT NULL,
  `rf_id` varchar(50) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `raw_id_dev_2_table`
--

CREATE TABLE `raw_id_dev_2_table` (
  `sno` int NOT NULL,
  `rf_id` varchar(50) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `raw_id_table`
--

CREATE TABLE `raw_id_table` (
  `Sno` int NOT NULL,
  `rf_id` varchar(50) NOT NULL,
  `id_name` varchar(255) DEFAULT NULL,
  `regd_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- --------------------------------------------------------

--
-- Table structure for table `report_dev_entry_table`
--

CREATE TABLE `report_dev_entry_table` (
  `sno` int NOT NULL,
  `regd_id` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `section` varchar(10) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- --------------------------------------------------------

--
-- Table structure for table `semester_hour`
--

CREATE TABLE `semester_hour` (
  `semester` varchar(2) NOT NULL,
  `hour1` varchar(20) DEFAULT NULL,
  `hour2` varchar(20) DEFAULT NULL,
  `hour3` varchar(20) DEFAULT NULL,
  `hour4` varchar(20) DEFAULT NULL,
  `hour5` varchar(20) DEFAULT NULL,
  `hour6` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `semester_hour`
--

INSERT INTO `semester_hour` (`semester`, `hour1`, `hour2`, `hour3`, `hour4`, `hour5`, `hour6`) VALUES
('1', '09:00 to 10:00', '10:00 to 11:00', '11:15 to 12:15', '12:15 to 13:15', '14:00 to 15:00', '15:00 to 16:00'),
('2', '09:00 to 10:00', '10:00 to 11:00', '11:15 to 12:15', '12:15 to 13:15', '14:00 to 15:00', '15:00 to 16:00'),
('3', '09:00 to 10:00', '10:00 to 11:00', '12:00 to 12:45', '12:45 to 13:45', '13:45 to 14:45', '14:45 to 15:45'),
('4', '09:00 to 10:00', '10:00 to 11:00', '12:00 to 12:45', '12:45 to 13:45', '13:45 to 14:45', '14:45 to 15:45'),
('5', '09:00 to 10:00', '10:00 to 11:00', '12:00 to 12:45', '12:45 to 13:45', '13:45 to 14:45', '14:45 to 15:45'),
('6', '09:00 to 10:00', '10:00 to 11:00', '12:00 to 12:45', '12:45 to 13:45', '13:45 to 14:45', '14:45 to 15:45'),
('7', '09:00 to 10:00', '10:00 to 11:00', '11:15 to 12:15', '12:15 to 13:15', '14:00 to 15:00', '15:00 to 16:00'),
('8', '09:00 to 10:00', '10:00 to 11:00', '11:00 to 12:00', '12:45 to 13:45', '13:45 to 14:45', '14:45 to 15:45');

-- --------------------------------------------------------

--
-- Table structure for table `student_details`
--

CREATE TABLE `student_details` (
  `regd_id` varchar(50) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `allowed` enum('YES','NO') NOT NULL DEFAULT 'YES'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_details`
--

INSERT INTO `student_details` (`regd_id`, `name`, `batch_no`, `department`, `semester`, `section`, `allowed`) VALUES
('12355', 'Shiva', '3', 'Cse', '5', 'B', 'YES'),
('21331A0589', 'K SIVA PRASANNA', '2021', 'cse', '4', 'B', 'YES'),
('21331A0592', 'KONDU TANOOJ', '2021-2025', 'cse', '1', 'A', 'YES'),
('21331A05A9', 'M BHARAT KUMAR', '2021-2025', 'cse', '4', 'A', 'YES'),
('21331A05C0', 'M MOOSA SAIT', '2021-2025', 'cse', '1', 'A', 'YES'),
('22331A0523', 'B SIDDIKHA', '2022-2026', 'CSE', '6', 'A', 'YES'),
('24331A05L4', 'P Poojith', '2024-2028', 'cse', '2', 'D', 'YES');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cam_capture_table`
--
ALTER TABLE `cam_capture_table`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cse_1_A_subject_timetable`
--
ALTER TABLE `cse_1_A_subject_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_1_A_timetable`
--
ALTER TABLE `cse_1_A_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_1_B_timetable`
--
ALTER TABLE `cse_1_B_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_1_C_subject_timetable`
--
ALTER TABLE `cse_1_C_subject_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_1_C_timetable`
--
ALTER TABLE `cse_1_C_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_1_D_timetable`
--
ALTER TABLE `cse_1_D_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_1_E_subject_timetable`
--
ALTER TABLE `cse_1_E_subject_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_1_E_timetable`
--
ALTER TABLE `cse_1_E_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_2_A_subject_timetable`
--
ALTER TABLE `cse_2_A_subject_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_2_D_subject_timetable`
--
ALTER TABLE `cse_2_D_subject_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_2_D_timetable`
--
ALTER TABLE `cse_2_D_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_4_A_subject_timetable`
--
ALTER TABLE `cse_4_A_subject_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_4_A_timetable`
--
ALTER TABLE `cse_4_A_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_4_B_subject_timetable`
--
ALTER TABLE `cse_4_B_subject_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_4_B_timetable`
--
ALTER TABLE `cse_4_B_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_4_C_timetable`
--
ALTER TABLE `cse_4_C_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_5_A_timetable`
--
ALTER TABLE `cse_5_A_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_5_B_timetable`
--
ALTER TABLE `cse_5_B_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_5_C_timetable`
--
ALTER TABLE `cse_5_C_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_6_A_subject_timetable`
--
ALTER TABLE `cse_6_A_subject_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_6_A_timetable`
--
ALTER TABLE `cse_6_A_timetable`
  ADD PRIMARY KEY (`day`);

--
-- Indexes for table `cse_subjects`
--
ALTER TABLE `cse_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department_sections`
--
ALTER TABLE `department_sections`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `devices_table`
--
ALTER TABLE `devices_table`
  ADD PRIMARY KEY (`dev_ID`);

--
-- Indexes for table `guest_id_status`
--
ALTER TABLE `guest_id_status`
  ADD PRIMARY KEY (`sno`),
  ADD UNIQUE KEY `guest_id` (`guest_id`);

--
-- Indexes for table `guest_info`
--
ALTER TABLE `guest_info`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `invalid_id_table`
--
ALTER TABLE `invalid_id_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `main_entry_table`
--
ALTER TABLE `main_entry_table`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `permission_table`
--
ALTER TABLE `permission_table`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `raw_id_dev_1_table`
--
ALTER TABLE `raw_id_dev_1_table`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `raw_id_dev_2_table`
--
ALTER TABLE `raw_id_dev_2_table`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `raw_id_table`
--
ALTER TABLE `raw_id_table`
  ADD PRIMARY KEY (`Sno`),
  ADD UNIQUE KEY `rf_id` (`rf_id`);

--
-- Indexes for table `report_dev_entry_table`
--
ALTER TABLE `report_dev_entry_table`
  ADD PRIMARY KEY (`sno`);

--
-- Indexes for table `semester_hour`
--
ALTER TABLE `semester_hour`
  ADD PRIMARY KEY (`semester`);

--
-- Indexes for table `student_details`
--
ALTER TABLE `student_details`
  ADD PRIMARY KEY (`regd_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cam_capture_table`
--
ALTER TABLE `cam_capture_table`
  MODIFY `sno` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2900;

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cse_subjects`
--
ALTER TABLE `cse_subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_sections`
--
ALTER TABLE `department_sections`
  MODIFY `dept_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `guest_id_status`
--
ALTER TABLE `guest_id_status`
  MODIFY `sno` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `guest_info`
--
ALTER TABLE `guest_info`
  MODIFY `sno` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invalid_id_table`
--
ALTER TABLE `invalid_id_table`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `main_entry_table`
--
ALTER TABLE `main_entry_table`
  MODIFY `sno` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `permission_table`
--
ALTER TABLE `permission_table`
  MODIFY `sno` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `raw_id_dev_1_table`
--
ALTER TABLE `raw_id_dev_1_table`
  MODIFY `sno` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `raw_id_dev_2_table`
--
ALTER TABLE `raw_id_dev_2_table`
  MODIFY `sno` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `raw_id_table`
--
ALTER TABLE `raw_id_table`
  MODIFY `Sno` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `report_dev_entry_table`
--
ALTER TABLE `report_dev_entry_table`
  MODIFY `sno` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
