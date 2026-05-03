-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2025 at 09:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erms`
--

-- --------------------------------------------------------

--
-- Table structure for table `emergencies`
--

CREATE TABLE `emergencies` (
  `Emergency_Id` int(11) NOT NULL,
  `User_Id` int(11) NOT NULL,
  `Emergency_Name` varchar(100) NOT NULL,
  `Description` varchar(500) NOT NULL,
  `Location` varchar(200) NOT NULL,
  `Emergency_Status` varchar(50) DEFAULT 'Pending',
  `Incident_Time` datetime NOT NULL,
  `Reported_Time` timestamp NOT NULL DEFAULT current_timestamp(),
  `Emergency_Department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergencies`
--

INSERT INTO `emergencies` (`Emergency_Id`, `User_Id`, `Emergency_Name`, `Description`, `Location`, `Emergency_Status`, `Incident_Time`, `Reported_Time`, `Emergency_Department`) VALUES
(1, 2, 'Road Accident', 'A severe road accident occurred near Larkana bypass involving two cars. One person is injured and needs medical attention immediately.', 'Larkana Bypass, Near PSO Petrol Pump', 'Viewed', '2025-10-27 15:45:00', '2025-10-27 10:54:43', 'Medical'),
(2, 2, 'Fire Outbreak', 'A fire broke out in a small shop; flames spreading rapidly to nearby stalls.', 'Main Bazar, Larkana', 'Resolved', '2025-10-28 18:00:00', '2025-10-28 13:49:30', 'Fire'),
(3, 2, 'Medical Emergency', 'A person fainted in the city park; ambulance dispatched for immediate aid.', 'Rescue Chowk, Larkana', 'Resolved', '2025-10-26 20:00:00', '2025-10-26 15:05:17', 'Medical'),
(4, 2, 'Gas Leak', 'A strong gas odor reported in nearby houses; residents are being evacuated.', 'Rescue Chowk, Larkana', 'Assigned', '2025-10-28 19:20:00', '2025-10-28 14:22:36', 'Fire'),
(5, 2, 'Fire in City Mall', 'A fire broke out in the kitchen area of City Mall. Smoke is spreading, and evacuation is needed.\r\n', 'City Mall, Larkana', 'In Progress', '2025-10-31 19:30:00', '2025-10-31 14:45:11', 'Fire'),
(6, 5, 'Heart Attack', 'My brother has heart attack', 'near nazeer kiryana, dari muhulla', 'Resolved', '2025-11-01 16:35:00', '2025-11-01 11:39:29', 'Medical'),
(9, 2, 'Road Accident', 'A bike crash due t high speed', 'VIP road, Larkana', 'Viewed', '2025-11-01 19:30:00', '2025-11-01 14:32:19', 'Medical'),
(10, 8, 'Heart Attack', 'My brother get heart attavk', 'Pakistani Chowk', 'In Progress', '2025-11-03 16:28:00', '2025-11-03 11:29:15', 'Medical');

-- --------------------------------------------------------

--
-- Table structure for table `responders`
--

CREATE TABLE `responders` (
  `Responder_Id` int(11) NOT NULL,
  `User_Id` int(11) NOT NULL,
  `Department` varchar(100) NOT NULL,
  `Responder_Status` varchar(100) DEFAULT 'Available',
  `Location` varchar(255) DEFAULT NULL,
  `Experience` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responders`
--

INSERT INTO `responders` (`Responder_Id`, `User_Id`, `Department`, `Responder_Status`, `Location`, `Experience`) VALUES
(1, 4, 'Fire', 'Busy', 'Nazar Muhalla', '2 Years'),
(2, 6, 'Medical', 'Busy', 'Dari Muhalla', '3-5 years'),
(3, 7, 'Medical', 'Available', 'Pakistani Chowk', '1-3 years');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Pass` varchar(255) NOT NULL,
  `Role` varchar(50) NOT NULL DEFAULT 'citizen',
  `Phone` varchar(20) NOT NULL,
  `Address` varchar(100) DEFAULT NULL,
  `Join_Date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Id`, `Name`, `Email`, `Pass`, `Role`, `Phone`, `Address`, `Join_Date`) VALUES
(1, 'Muhammad Asim', 'sasim4589@gmail.com', '$2y$10$s0eTguN5T8OE.COUzOYO6OgmeII72nMakPW1M8hCaQYbne7ei8vOm', 'admin', '03323230869', 'Dari Muhalla, Larkana', '2025-10-27'),
(2, 'Asim', 'sasim4589@icloud.com', '$2y$10$mgN2U0y2Db.Zq8q.44zx9ui.xJIU5MkOvqkPIFCU3BIQWpuoIKhhi', 'citizen', '+923323230869', 'Dari Muhalla, Larkana', '2025-10-27'),
(4, 'Faseeh', 'faseeh@gmail.com', '$2y$10$xzqoXjrf4SIA.xci0WK0n.oF1WY./0UuZeRDJJe8f4G6O9Gbik9Nq', 'responder', '03183433480', 'Nazar Muhalla', '2025-10-30'),
(5, 'Sahib', 'sahib@gmail.com', '$2y$10$FmrML96f3PShe3lyRo9bMuUS3IydiIG80eKo.NDdK9tkmwedx6M/K', 'citizen', '03194814055', 'address', '2025-11-01'),
(6, 'Furqan', 'furqan@gmail.com', '$2y$10$zc7MoySxdAU3qpuUUS9SyugSvvf/huDvsM35LOxGu7E56yau2JN1a', 'responder', '03228300637', 'address', '2025-11-01'),
(7, 'Bilawal', 'bilawal@gmail.com', '$2y$10$YKwFbRhq.LVPsReK2J1NcuLc6/E0.SdFG9g/5Z4yqPLubjOhGP.fG', 'responder', '03337559726', 'address', '2025-11-03'),
(8, 'Owais', 'owais@gmail.com', '$2y$10$E2v326xQA7YXnS7GN67c0u/N4.0LNr5Kjxuz0DpOpaxaeZ8temnYa', 'citizen', '03336053372', 'address', '2025-11-03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `emergencies`
--
ALTER TABLE `emergencies`
  ADD PRIMARY KEY (`Emergency_Id`),
  ADD KEY `User_id` (`User_Id`);

--
-- Indexes for table `responders`
--
ALTER TABLE `responders`
  ADD PRIMARY KEY (`Responder_Id`),
  ADD KEY `User_Id` (`User_Id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `emergencies`
--
ALTER TABLE `emergencies`
  MODIFY `Emergency_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `responders`
--
ALTER TABLE `responders`
  MODIFY `Responder_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emergencies`
--
ALTER TABLE `emergencies`
  ADD CONSTRAINT `emergencies_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `users` (`Id`);

--
-- Constraints for table `responders`
--
ALTER TABLE `responders`
  ADD CONSTRAINT `responders_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
