-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 02:57 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `seid_ac_hepi`
--

-- --------------------------------------------------------

--
-- Table structure for table `history_lot`
--

CREATE TABLE `history_lot` (
  `id_history_evap` int(255) NOT NULL,
  `coupon` varchar(255) NOT NULL,
  `tgl_lot` datetime NOT NULL,
  `shift` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `status` enum('IN','OUT') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lot`
--

CREATE TABLE `lot` (
  `coupon` varchar(255) NOT NULL,
  `tgl_lot` datetime NOT NULL,
  `shift` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `status` enum('Available','Used') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piping_history_lot`
--

CREATE TABLE `piping_history_lot` (
  `id_history` int(255) NOT NULL,
  `coupon` varchar(20) NOT NULL,
  `tgl_lot` datetime NOT NULL,
  `shift` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `status` enum('IN','OUT') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piping_lot`
--

CREATE TABLE `piping_lot` (
  `coupon` varchar(255) NOT NULL,
  `tgl_lot` datetime NOT NULL,
  `shift` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `status` enum('Available','Used') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piping_prod_report`
--

CREATE TABLE `piping_prod_report` (
  `id` int(11) NOT NULL,
  `tgl_lot` date NOT NULL,
  `shift` int(11) NOT NULL,
  `ALL_IDU_IN` int(11) DEFAULT 0,
  `ALL_IDU_OUT` int(11) DEFAULT 0,
  `CAP_5K2_IN` int(11) DEFAULT 0,
  `CAP_5K2_OUT` int(11) DEFAULT 0,
  `CAP_7K1_IN` int(11) DEFAULT 0,
  `CAP_7K1_OUT` int(11) DEFAULT 0,
  `CAP_9K2_IN` int(11) DEFAULT 0,
  `CAP_9K2_OUT` int(11) DEFAULT 0,
  `CAP_68K_IN` int(11) DEFAULT 0,
  `CAP_68K_OUT` int(11) DEFAULT 0,
  `CAP_10K_IN` int(11) DEFAULT 0,
  `CAP_10K_OUT` int(11) DEFAULT 0,
  `CAP_13K_IN` int(11) DEFAULT 0,
  `CAP_13K_OUT` int(11) DEFAULT 0,
  `CAP_9CY_IN` int(11) DEFAULT 0,
  `CAP_9CY_OUT` int(11) DEFAULT 0,
  `SUC_5K2_IN` int(11) DEFAULT 0,
  `SUC_5K2_OUT` int(11) DEFAULT 0,
  `SUC_7K1_IN` int(11) DEFAULT 0,
  `SUC_7K1_OUT` int(11) DEFAULT 0,
  `SUC_9K2_IN` int(11) DEFAULT 0,
  `SUC_9K2_OUT` int(11) DEFAULT 0,
  `SUC_68K_IN` int(11) DEFAULT 0,
  `SUC_68K_OUT` int(11) DEFAULT 0,
  `SUC_10K_IN` int(11) DEFAULT 0,
  `SUC_10K_OUT` int(11) DEFAULT 0,
  `SUC_13K_IN` int(11) DEFAULT 0,
  `SUC_13K_OUT` int(11) DEFAULT 0,
  `SUC_9CY_IN` int(11) DEFAULT 0,
  `SUC_9CY_OUT` int(11) DEFAULT 0,
  `DIS_5K2_IN` int(11) DEFAULT 0,
  `DIS_5K2_OUT` int(11) DEFAULT 0,
  `DIS_7K1_IN` int(11) DEFAULT 0,
  `DIS_7K1_OUT` int(11) DEFAULT 0,
  `DIS_9K2_IN` int(11) DEFAULT 0,
  `DIS_9K2_OUT` int(11) DEFAULT 0,
  `DIS_68K_IN` int(11) DEFAULT 0,
  `DIS_68K_OUT` int(11) DEFAULT 0,
  `DIS_10K_IN` int(11) DEFAULT 0,
  `DIS_10K_OUT` int(11) DEFAULT 0,
  `DIS_13K_IN` int(11) DEFAULT 0,
  `DIS_13K_OUT` int(11) DEFAULT 0,
  `DIS_9CY_IN` int(11) DEFAULT 0,
  `DIS_9CY_OUT` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piping_stok`
--

CREATE TABLE `piping_stok` (
  `part_code` varchar(20) NOT NULL,
  `nama_part` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `piping_stok`
--

INSERT INTO `piping_stok` (`part_code`, `nama_part`, `qty`) VALUES
('ALL-IDU', 'Tube Assy', 0),
('CAP-10K', 'Capillary 10K', 0),
('CAP-13K', 'Capillary 13K', 0),
('CAP-5K2', 'Capillary 5K2', 0),
('CAP-68K', 'Capillary 6K & 8K', 0),
('CAP-7K1', 'Capillary 7K', 0),
('CAP-9CY', 'Capillary 9CAY', 0),
('CAP-9K2', 'Capillary 9K2', 0),
('DIS-10K', 'Discharge 10K', 0),
('DIS-13K', 'Discharge 13K', 0),
('DIS-5K2', 'Discharge 5K2', 0),
('DIS-68K', 'Discharge 6K & 8K', 0),
('DIS-7K1', 'Discharge 7K', 0),
('DIS-9CY', 'Discharge 9CAY', 0),
('DIS-9K2', 'Discharge 9K2', 0),
('SUC-10K', 'Suction 10K', 0),
('SUC-13K', 'Suction 13K', 0),
('SUC-5K2', 'Suction 5K2', 0),
('SUC-68K', 'Suction 6K & 8K', 0),
('SUC-7K1', 'Suction 7K', 0),
('SUC-9CY', 'Suction 9CAY', 0),
('SUC-9K2', 'Suction 9K2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `production_plan`
--

CREATE TABLE `production_plan` (
  `id` int(11) NOT NULL,
  `model` varchar(255) NOT NULL,
  `plan_date` date NOT NULL,
  `shift1` int(11) NOT NULL,
  `shift2` int(11) NOT NULL,
  `shift3` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prod_report`
--

CREATE TABLE `prod_report` (
  `id` int(11) NOT NULL,
  `tgl_lot` date NOT NULL,
  `shift` int(11) NOT NULL,
  `EVA_IN` int(11) DEFAULT 0,
  `CSR_IN` int(11) DEFAULT 0,
  `CDR_IN` int(11) DEFAULT 0,
  `SRI_IN` int(11) DEFAULT 0,
  `DRI_IN` int(11) DEFAULT 0,
  `EVA_OUT` int(11) DEFAULT 0,
  `CSR_OUT` int(11) DEFAULT 0,
  `CDR_OUT` int(11) DEFAULT 0,
  `SRI_OUT` int(11) DEFAULT 0,
  `DRI_OUT` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prod_report`
--

INSERT INTO `prod_report` (`id`, `tgl_lot`, `shift`, `EVA_IN`, `CSR_IN`, `CDR_IN`, `SRI_IN`, `DRI_IN`, `EVA_OUT`, `CSR_OUT`, `CDR_OUT`, `SRI_OUT`, `DRI_OUT`) VALUES
(18, '2025-03-27', 1, 300, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, '2025-03-27', 2, 2700, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(20, '2025-04-08', 1, 3300, 0, 0, 0, 0, 400, 0, 0, 0, 0),
(21, '2025-04-08', 2, 0, 0, 0, 0, 0, 1200, 0, 0, 0, 0),
(22, '2025-04-08', 3, 1200, 0, 0, 0, 0, 900, 0, 0, 0, 0),
(23, '2025-04-09', 1, 2000, 0, 0, 0, 0, 1500, 0, 0, 0, 0),
(24, '2025-04-09', 2, 0, 0, 0, 0, 0, 1100, 0, 0, 0, 0),
(25, '2025-04-09', 3, 1400, 0, 0, 0, 0, 1100, 0, 0, 0, 0),
(26, '2025-04-10', 1, 1500, 0, 0, 0, 0, 1000, 0, 0, 0, 0),
(27, '2025-04-10', 2, 0, 0, 0, 0, 0, 1000, 0, 0, 0, 0),
(28, '2025-04-10', 3, 1400, 0, 0, 0, 0, 1100, 0, 0, 0, 0),
(29, '2025-04-11', 1, 1400, 0, 0, 0, 0, 1000, 0, 0, 0, 0),
(30, '2025-04-11', 2, 0, 0, 0, 0, 0, 1200, 0, 0, 0, 0),
(31, '2025-04-11', 3, 1000, 0, 0, 0, 0, 1100, 0, 0, 0, 0),
(32, '2025-04-12', 1, 1300, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(33, '2025-04-12', 3, 1100, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(34, '2025-04-14', 1, 1100, 0, 0, 0, 0, 900, 0, 0, 0, 0),
(35, '2025-04-14', 2, 0, 0, 0, 0, 0, 1100, 0, 0, 0, 0),
(36, '2025-04-14', 3, 1300, 0, 0, 0, 0, 1100, 0, 0, 0, 0),
(37, '2025-04-15', 1, 1600, 0, 0, 0, 0, 1100, 0, 0, 0, 0),
(38, '2025-04-15', 2, 0, 0, 0, 0, 0, 1100, 0, 0, 0, 0),
(39, '2025-04-15', 3, 1300, 0, 0, 0, 0, 1100, 0, 200, 0, 0),
(40, '2025-04-16', 1, 1500, 0, 2000, 0, 0, 1100, 0, 0, 0, 0),
(41, '2025-04-16', 2, 0, 0, 0, 0, 0, 1100, 0, 900, 0, 0),
(42, '2025-04-16', 3, 1400, 0, 3000, 0, 0, 1200, 0, 1400, 0, 0),
(43, '2025-04-17', 1, 1600, 0, 0, 0, 0, 1200, 0, 600, 0, 0),
(44, '2025-04-17', 2, 0, 0, 0, 0, 0, 1100, 0, 700, 0, 0),
(45, '2025-04-17', 3, 1400, 0, 0, 0, 0, 1000, 0, 900, 0, 0),
(46, '2025-04-18', 2, 0, 0, 0, 0, 0, 0, 0, 100, 0, 0),
(47, '2025-04-20', 3, 0, 0, 100, 0, 0, 0, 0, 200, 0, 0),
(48, '2025-04-21', 1, 1400, 0, 500, 0, 0, 1100, 0, 0, 0, 0),
(49, '2025-04-21', 2, 0, 0, 500, 0, 0, 800, 0, 800, 0, 0),
(50, '2025-04-21', 3, 1400, 0, 500, 0, 0, 1000, 0, 400, 0, 0),
(51, '2025-04-22', 1, 1600, 0, 200, 0, 0, 1200, 0, 0, 0, 0),
(52, '2025-04-22', 2, 0, 0, 100, 0, 0, 1400, 0, 0, 0, 0),
(53, '2025-04-22', 3, 1400, 0, 100, 0, 0, 1200, 0, 200, 0, 0),
(54, '2025-04-23', 1, 1600, 0, 0, 0, 0, 1200, 0, 0, 0, 0),
(55, '2025-04-23', 2, 100, 0, 600, 0, 0, 600, 0, 0, 0, 0),
(56, '2025-04-23', 3, 1400, 0, 600, 0, 0, 1300, 0, 300, 0, 0),
(57, '2025-04-24', 1, 1600, 0, 1100, 0, 0, 1300, 0, 900, 0, 0),
(58, '2025-04-24', 2, 200, 0, 600, 0, 0, 700, 0, 900, 0, 0),
(59, '2025-04-24', 3, 1400, 0, 300, 0, 0, 1300, 0, 1200, 0, 0),
(60, '2025-04-25', 1, 1700, 0, 600, 0, 0, 1400, 0, 1200, 0, 0),
(61, '2025-04-25', 2, 200, 0, 600, 0, 0, 600, 0, 200, 0, 0),
(62, '2025-04-25', 3, 100, 0, 500, 0, 0, 100, 0, 700, 0, 0),
(63, '2025-04-28', 1, 1500, 0, 200, 0, 0, 1300, 0, 400, 0, 0),
(64, '2025-04-28', 2, 1400, 0, 0, 0, 0, 1100, 0, 0, 0, 0),
(65, '2025-04-28', 3, 300, 0, 0, 0, 0, 800, 0, 0, 0, 0),
(66, '2025-04-29', 1, 1300, 0, 0, 0, 0, 1000, 0, 0, 0, 0),
(67, '2025-04-29', 2, 1200, 0, 0, 0, 0, 300, 0, 0, 0, 0),
(68, '2025-04-29', 3, 0, 0, 0, 0, 0, 300, 0, 0, 0, 0),
(69, '2025-04-30', 1, 1600, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(70, '2025-04-30', 2, 1400, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(71, '2025-05-02', 1, 1400, 0, 300, 0, 0, 0, 0, 0, 0, 0),
(72, '2025-05-02', 2, 700, 5400, 700, 200, 0, 0, 0, 0, 0, 0),
(73, '2025-05-02', 3, 0, 0, 600, 0, 0, 0, 0, 0, 0, 0),
(81, '2025-05-05', 1, 2300, 0, 700, 0, 0, 1200, 1200, 0, 200, 0),
(82, '2025-05-05', 2, 0, 0, 700, 0, 0, 1200, 1200, 0, 0, 0),
(83, '2025-05-05', 3, 1400, 0, 500, 0, 0, 1200, 1000, 0, 0, 0),
(84, '2025-05-06', 1, 1600, 0, 0, 0, 0, 1000, 1000, 0, 0, 0),
(1506, '2025-05-06', 2, 0, 1200, 0, 0, 0, 1100, 1000, 0, 0, 0),
(2756, '2025-05-06', 3, 1300, 1400, 0, 0, 0, 800, 1000, 0, 0, 0),
(2788, '2025-05-07', 1, 1700, 1600, 0, 0, 0, 1100, 1200, 0, 0, 0),
(4168, '2025-05-07', 2, 0, 1200, 0, 0, 0, 1100, 1000, 0, 0, 0),
(5506, '2025-05-07', 3, 1300, 1200, 0, 0, 0, 1100, 1000, 0, 0, 0),
(5525, '2025-05-08', 1, 1600, 1400, 500, 0, 0, 1100, 1000, 100, 0, 0),
(6827, '2025-05-08', 2, 0, 200, 0, 0, 600, 600, 200, 800, 0, 0),
(7819, '2025-05-08', 3, 900, 0, 0, 0, 500, 1100, 0, 600, 0, 300),
(7838, '2025-05-09', 1, 1400, 0, 100, 1200, 0, 1300, 0, 100, 0, 700),
(8995, '2025-05-09', 2, 100, 0, 800, 0, 0, 1200, 0, 1100, 0, 0),
(9827, '2025-05-13', 1, 1000, 0, 800, 0, 0, 1200, 200, 0, 1000, 0),
(10897, '2025-05-13', 2, 0, 0, 500, 0, 0, 1200, 1000, 0, 0, 0),
(11161, '2025-05-13', 3, 1400, 0, 600, 0, 0, 1100, 1000, 0, 0, 0),
(11194, '2025-05-14', 1, 1600, 1000, 200, 0, 0, 1300, 1400, 0, 0, 0),
(13160, '2025-05-14', 2, 0, 1400, 0, 0, 0, 1100, 1000, 0, 0, 0),
(13782, '2025-05-14', 3, 1400, 1200, 0, 0, 0, 400, 1000, 0, 0, 0),
(13800, '2025-05-15', 1, 1600, 1600, 0, 0, 0, 2000, 1400, 0, 0, 0),
(15982, '2025-05-15', 2, 0, 1200, 0, 0, 0, 1100, 1000, 0, 0, 0),
(16544, '2025-05-15', 3, 1600, 1200, 0, 0, 0, 1000, 0, 1000, 0, 0),
(16558, '2025-05-09', 3, 1400, 0, 1200, 0, 0, 900, 0, 1600, 0, 0),
(16565, '2025-05-16', 1, 1600, 1400, 0, 0, 0, 1100, 0, 1200, 0, 0),
(18354, '2025-05-16', 2, 0, 1200, 0, 0, 0, 1100, 0, 1000, 0, 0),
(19274, '2025-05-19', 1, 1800, 1400, 0, 200, 0, 1200, 0, 0, 1000, 0),
(19823, '2025-05-19', 2, 0, 400, 100, 0, 0, 900, 400, 0, 0, 0),
(20327, '2025-05-19', 3, 1400, 0, 600, 0, 0, 1000, 1200, 0, 0, 0),
(20376, '2025-05-20', 1, 1300, 0, 900, 0, 0, 1200, 1200, 0, 0, 0),
(21352, '2025-05-20', 2, 0, 600, 400, 0, 100, 1000, 1200, 0, 0, 0),
(21994, '2025-05-20', 3, 800, 1200, 0, 0, 0, 1100, 1000, 0, 0, 0),
(22074, '2025-05-21', 1, 1400, 1000, 300, 0, 0, 1200, 1000, 0, 0, 0),
(23514, '2025-05-21', 2, 0, 0, 700, 0, 0, 1100, 1000, 0, 0, 0),
(24514, '2025-05-21', 3, 1500, 0, 400, 0, 500, 1100, 1200, 0, 0, 0),
(24571, '2025-05-22', 1, 1600, 1400, 0, 0, 200, 1200, 0, 0, 0, 800),
(25213, '2025-05-16', 3, 1500, 400, 0, 800, 0, 900, 400, 600, 0, 0),
(25588, '2025-05-22', 2, 0, 1400, 0, 0, 0, 1000, 1200, 0, 0, 0),
(26226, '2025-05-22', 3, 1600, 1400, 0, 0, 0, 1100, 200, 800, 0, 0),
(26254, '2025-05-23', 1, 1600, 1400, 0, 0, 0, 1000, 0, 900, 0, 0),
(27150, '2025-05-23', 2, 0, 1200, 0, 0, 0, 1000, 0, 1100, 0, 0),
(27794, '2025-05-23', 3, 1500, 600, 0, 200, 0, 1100, 600, 600, 0, 0),
(27859, '2025-05-24', 1, 1400, 1000, 0, 600, 0, 1400, 1600, 0, 0, 0),
(29447, '2025-05-24', 2, 0, 0, 300, 0, 0, 900, 800, 0, 0, 0),
(30299, '2025-05-26', 1, 1800, 0, 800, 0, 0, 1200, 200, 0, 600, 0),
(30727, '2025-05-24', 3, 2600, 0, 1200, 0, 0, 1800, 1600, 0, 400, 0),
(30955, '2025-05-26', 2, 0, 0, 600, 0, 0, 1100, 800, 0, 0, 0),
(33598, '2025-05-26', 3, 1700, 0, 400, 0, 0, 1300, 1000, 0, 0, 0),
(34156, '2025-05-27', 1, 1700, 0, 800, 0, 0, 1000, 400, 500, 0, 0),
(34498, '2025-05-27', 2, 0, 1400, 100, 0, 0, 1000, 0, 900, 0, 0),
(35043, '2025-05-27', 3, 1500, 1200, 0, 0, 0, 1200, 0, 800, 0, 0),
(35524, '2025-05-28', 1, 1700, 1600, 0, 0, 0, 600, 0, 1100, 0, 0),
(36277, '2025-05-28', 2, 100, 1200, 0, 0, 0, 200, 600, 300, 0, 0),
(36729, '2025-05-28', 3, 1000, 0, 0, 1200, 0, 0, 0, 0, 0, 0),
(37007, '2025-06-01', 3, 1000, 0, 0, 0, 400, 0, 0, 0, 0, 0),
(37360, '2025-06-02', 1, 1800, 0, 0, 0, 700, 1100, 1000, 0, 0, 0),
(38382, '2025-06-02', 2, 0, 0, 500, 0, 0, 1200, 1200, 0, 0, 0),
(38742, '2025-06-02', 3, 1600, 0, 600, 0, 0, 900, 1000, 0, 0, 0),
(39271, '2025-06-03', 1, 1700, 0, 700, 0, 0, 1300, 600, 0, 0, 0),
(40274, '2025-06-03', 2, 0, 200, 300, 0, 0, 1200, 1000, 0, 0, 0),
(40574, '2025-06-03', 3, 1400, 800, 0, 0, 0, 1000, 1000, 0, 0, 0),
(41006, '2025-06-04', 1, 1600, 1600, 0, 0, 0, 1100, 1200, 0, 0, 0),
(41698, '2025-06-04', 2, 0, 1200, 0, 0, 0, 1200, 1000, 0, 0, 0),
(42038, '2025-06-04', 3, 1500, 600, 0, 0, 0, 1100, 200, 700, 0, 0),
(42410, '2025-06-09', 1, 1800, 600, 400, 0, 0, 1100, 0, 0, 0, 1000),
(42905, '2025-06-09', 2, 0, 0, 900, 0, 0, 800, 0, 800, 0, 100),
(43377, '2025-06-09', 3, 1600, 0, 800, 0, 0, 1100, 0, 900, 0, 0),
(44186, '2025-06-10', 1, 1800, 0, 800, 0, 0, 1200, 0, 1100, 0, 0),
(44615, '2025-06-10', 2, 0, 0, 900, 0, 0, 900, 0, 800, 0, 0),
(45045, '2025-06-10', 3, 1600, 1000, 300, 0, 0, 1000, 0, 700, 200, 0),
(45728, '2025-06-11', 1, 1800, 1800, 0, 0, 0, 1300, 0, 0, 1200, 0),
(46296, '2025-06-11', 2, 0, 1400, 0, 0, 0, 1000, 200, 700, 0, 0),
(46520, '2025-06-11', 3, 1600, 800, 0, 0, 0, 1000, 400, 400, 0, 0),
(47339, '2025-06-12', 1, 1600, 1000, 0, 0, 0, 1200, 1200, 0, 0, 0),
(47789, '2025-06-12', 2, 0, 200, 0, 1200, 0, 1000, 1200, 0, 0, 0),
(48082, '2025-06-12', 3, 1200, 0, 900, 0, 0, 1100, 1200, 0, 0, 0),
(48702, '2025-06-13', 1, 1600, 600, 900, 0, 0, 1300, 1200, 0, 0, 0),
(49258, '2025-06-13', 2, 0, 0, 600, 0, 0, 1000, 1000, 0, 0, 0),
(49523, '2025-06-13', 3, 1200, 0, 700, 0, 0, 900, 600, 100, 0, 0),
(50887, '2025-06-16', 1, 1400, 200, 800, 0, 0, 1200, 0, 900, 0, 0),
(51399, '2025-06-16', 2, 0, 1400, 0, 0, 0, 1000, 0, 1100, 0, 0),
(51662, '2025-06-16', 3, 1600, 1400, 0, 0, 0, 1200, 0, 1000, 0, 0),
(52493, '2025-06-17', 1, 1800, 1400, 0, 0, 0, 1100, 0, 0, 800, 0),
(53061, '2025-06-17', 2, 0, 0, 0, 0, 0, 1000, 0, 600, 200, 0),
(53193, '2025-06-17', 3, 1500, 1200, 0, 1000, 0, 1100, 1000, 0, 0, 0),
(53920, '2025-06-18', 1, 1800, 0, 0, 1000, 0, 1300, 1400, 0, 0, 0),
(54497, '2025-06-18', 2, 0, 2400, 0, 0, 0, 1000, 1200, 0, 0, 0),
(54846, '2025-06-18', 3, 1300, 1000, 0, 0, 0, 1100, 1200, 0, 0, 0),
(55628, '2025-06-19', 1, 1600, 1600, 0, 0, 400, 1300, 1400, 0, 0, 0),
(56283, '2025-06-19', 2, 0, 0, 0, 0, 800, 1000, 1200, 0, 0, 0),
(56617, '2025-06-19', 3, 1600, 0, 300, 0, 400, 1100, 1000, 0, 0, 0),
(57088, '2025-06-20', 1, 1600, 0, 800, 0, 0, 1300, 1200, 0, 0, 0),
(57687, '2025-06-20', 2, 0, 0, 700, 0, 0, 1000, 1200, 0, 0, 0),
(57967, '2025-06-20', 3, 1000, 0, 700, 0, 0, 1100, 600, 200, 400, 0),
(58396, '2025-06-21', 1, 1400, 0, 1100, 0, 0, 1300, 800, 200, 400, 0),
(58801, '2025-06-21', 2, 200, 0, 300, 0, 0, 1000, 200, 900, 0, 0),
(59118, '2025-06-21', 3, 1200, 400, 500, 0, 0, 900, 0, 700, 200, 0),
(61221, '2025-06-23', 1, 1600, 1000, 0, 0, 0, 1200, 0, 0, 800, 0),
(61527, '2025-06-23', 2, 0, 1400, 0, 0, 0, 1100, 0, 800, 200, 0),
(62279, '2025-06-23', 3, 1000, 1000, 0, 0, 0, 1200, 800, 300, 0, 100),
(62773, '2025-06-24', 1, 1300, 1200, 0, 0, 0, 1200, 400, 0, 0, 700),
(63018, '2025-06-24', 2, 1400, 400, 0, 0, 0, 1100, 1000, 0, 0, 0),
(63488, '2025-06-24', 3, 1000, 1400, 0, 0, 0, 1100, 800, 0, 0, 100),
(63819, '2025-06-25', 1, 1600, 1200, 0, 0, 0, 1100, 600, 0, 0, 700),
(64168, '2025-06-25', 2, 1400, 0, 0, 0, 0, 1000, 1000, 0, 0, 0),
(64704, '2025-06-25', 3, 1100, 1000, 0, 0, 0, 1200, 400, 500, 0, 0),
(65127, '2025-06-26', 1, 1700, 1400, 0, 200, 0, 800, 0, 1000, 0, 0),
(65926, '2025-06-26', 2, 0, 0, 0, 800, 0, 0, 600, 100, 0, 0),
(66805, '2025-06-26', 3, 1000, 0, 0, 1000, 0, 100, 0, 0, 0, 0),
(67427, '2025-07-01', 1, 900, 1400, 0, 0, 0, 1200, 1400, 0, 200, 0),
(68492, '2025-07-01', 2, 0, 1200, 0, 0, 0, 1200, 1000, 0, 0, 0),
(68795, '2025-07-01', 3, 1300, 1400, 0, 0, 0, 1000, 1200, 0, 0, 0),
(69123, '2025-07-02', 1, 1800, 1200, 0, 0, 0, 1200, 1400, 0, 0, 0),
(69988, '2025-07-02', 2, 0, 1400, 0, 0, 0, 1200, 1000, 0, 0, 0),
(70316, '2025-07-02', 3, 1600, 1000, 0, 0, 0, 900, 1200, 0, 0, 0),
(70892, '2025-07-03', 1, 1800, 600, 500, 0, 0, 1200, 1200, 0, 0, 0),
(71860, '2025-07-03', 2, 0, 0, 800, 0, 0, 1200, 800, 300, 0, 0),
(72289, '2025-07-03', 3, 1600, 0, 700, 0, 0, 900, 0, 1000, 200, 0),
(72760, '2025-07-07', 1, 1800, 0, 600, 0, 0, 1200, 0, 0, 1000, 0),
(73238, '2025-07-07', 2, 0, 0, 700, 0, 0, 1000, 0, 800, 0, 0),
(73698, '2025-07-07', 3, 1600, 0, 0, 0, 600, 1100, 0, 900, 0, 200),
(74858, '2025-07-21', 1, 700, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `stok`
--

CREATE TABLE `stok` (
  `part_code` varchar(20) NOT NULL,
  `nama_part` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stok`
--

INSERT INTO `stok` (`part_code`, `nama_part`, `qty`) VALUES
('DCON-B070JBEZ', 'Condensor SR', 0),
('DCON-B074JBEZ', 'Condensor SR INV', 0),
('DCON-B075JBEZ', 'Condensor DR INV', 0),
('DCON-B105JBEZ', 'Condensor DR', 0),
('PEVA-B243JBEZ', 'Evaporator', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `role` enum('Manager','Leader-HE','Leader-ML') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `password`, `nama`, `role`) VALUES
('he01', 'SeidMail01', 'Dafhis', 'Leader-HE'),
('he02', 'SeidMail01', 'Edwin', 'Leader-HE'),
('he03', 'SeidMail01', 'Ilyas', 'Leader-HE'),
('idu01', 'idu01', 'Irfan & Dewa', 'Leader-ML'),
('idu02', 'idu02', 'Haris & Rohman', 'Leader-ML'),
('idu03', 'idu03', 'Group C', 'Leader-ML'),
('leader_he1', '123', 'Leader HE', 'Leader-HE'),
('leader_ml1', '123', 'Leader ML', 'Leader-ML'),
('manager1', 'superadmin01', 'Tommy', 'Manager'),
('odu01', 'odu01', 'Irwanto, Adi & Nugroho', 'Leader-ML'),
('odu02', 'odu02', 'Romly, Mufti & Nurdin', 'Leader-ML'),
('odu03', 'odu03', 'Group C', 'Leader-ML');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `history_lot`
--
ALTER TABLE `history_lot`
  ADD PRIMARY KEY (`id_history_evap`);

--
-- Indexes for table `lot`
--
ALTER TABLE `lot`
  ADD PRIMARY KEY (`coupon`);

--
-- Indexes for table `piping_history_lot`
--
ALTER TABLE `piping_history_lot`
  ADD PRIMARY KEY (`id_history`);

--
-- Indexes for table `piping_lot`
--
ALTER TABLE `piping_lot`
  ADD PRIMARY KEY (`coupon`);

--
-- Indexes for table `piping_prod_report`
--
ALTER TABLE `piping_prod_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE_COLUMN` (`tgl_lot`,`shift`);

--
-- Indexes for table `piping_stok`
--
ALTER TABLE `piping_stok`
  ADD PRIMARY KEY (`part_code`);

--
-- Indexes for table `production_plan`
--
ALTER TABLE `production_plan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE_COLUMN` (`model`,`plan_date`) USING BTREE;

--
-- Indexes for table `prod_report`
--
ALTER TABLE `prod_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tgl_shift` (`tgl_lot`,`shift`);

--
-- Indexes for table `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`part_code`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `history_lot`
--
ALTER TABLE `history_lot`
  MODIFY `id_history_evap` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `piping_history_lot`
--
ALTER TABLE `piping_history_lot`
  MODIFY `id_history` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `piping_prod_report`
--
ALTER TABLE `piping_prod_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5595;

--
-- AUTO_INCREMENT for table `production_plan`
--
ALTER TABLE `production_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5401;

--
-- AUTO_INCREMENT for table `prod_report`
--
ALTER TABLE `prod_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77108;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
