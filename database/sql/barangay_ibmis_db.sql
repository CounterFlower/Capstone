-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2026 at 11:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barangay_ibmis_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `Announcement_ID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Content` text NOT NULL,
  `Date_Posted` datetime DEFAULT current_timestamp(),
  `Posted_By` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `Log_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Action_Performed` varchar(255) NOT NULL,
  `Log_Timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_request`
--

CREATE TABLE `document_request` (
  `Request_ID` int(11) NOT NULL,
  `Resident_ID` int(11) NOT NULL,
  `Date_Requested` datetime DEFAULT current_timestamp(),
  `Years_Stayed` int(11) DEFAULT NULL,
  `Document_Type` varchar(100) NOT NULL,
  `Purpose` varchar(255) DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Pending',
  `Pickup_Schedule` datetime DEFAULT NULL,
  `QR_Hash` varchar(255) DEFAULT NULL,
  `Processed_By` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `Event_ID` int(11) NOT NULL,
  `Event_Name` varchar(255) NOT NULL,
  `Event_Date` datetime NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Available_Slots` int(11) DEFAULT NULL,
  `Created_By` int(11) DEFAULT NULL,
  `Summary` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`Event_ID`, `Event_Name`, `Event_Date`, `Location`, `Available_Slots`, `Created_By`, `Summary`) VALUES
(1, 'Free Rabies Vaccination', '2026-08-31 08:00:00', 'Covered Court', 100, NULL, 'Vaccinate your pets for free');

-- --------------------------------------------------------

--
-- Table structure for table `event_rsvp`
--

CREATE TABLE `event_rsvp` (
  `RSVP_ID` int(11) NOT NULL,
  `Event_ID` int(11) NOT NULL,
  `Resident_ID` int(11) NOT NULL,
  `Date_Registered` datetime DEFAULT current_timestamp(),
  `Attendance_Status` varchar(50) DEFAULT 'Confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest`
--

CREATE TABLE `guest` (
  `Guest_Id` int(11) NOT NULL,
  `First_Name` varchar(225) DEFAULT NULL,
  `Middle_Name` varchar(225) DEFAULT NULL,
  `Last_Name` varchar(225) DEFAULT NULL,
  `Contact_Number` varchar(100) DEFAULT NULL,
  `Address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `household`
--

CREATE TABLE `household` (
  `Household_Index` int(11) NOT NULL,
  `Household_Id` int(11) DEFAULT NULL,
  `House_Number` varchar(50) DEFAULT NULL,
  `Zone_Purok` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `household`
--

INSERT INTO `household` (`Household_Index`, `Household_Id`, `House_Number`, `Zone_Purok`) VALUES
(1, 3321412, '381', 'Purok 4'),
(2, 3312341, '241', '2'),
(3, 4412312, '112', 'Purok 6'),
(4, 1132123, '241', 'Purok 6');

-- --------------------------------------------------------

--
-- Table structure for table `incident_blotter`
--

CREATE TABLE `incident_blotter` (
  `Incident_ID` int(11) NOT NULL,
  `Complainant_Id` int(11) DEFAULT NULL,
  `Respondent_Id` int(11) NOT NULL,
  `Guest_Id` int(11) DEFAULT NULL,
  `Category_Id` int(11) NOT NULL,
  `Description` text NOT NULL,
  `Requested_Relief` text DEFAULT NULL,
  `Date_Reported` datetime DEFAULT current_timestamp(),
  `Date_Filed` datetime DEFAULT NULL,
  `Resolution_Status` enum('Pending','Active','Resolved','Escalated') DEFAULT 'Pending',
  `Latitude` decimal(10,8) DEFAULT NULL,
  `Longitude` decimal(11,8) DEFAULT NULL,
  `Handled_By` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_blotter`
--

INSERT INTO `incident_blotter` (`Incident_ID`, `Complainant_Id`, `Respondent_Id`, `Guest_Id`, `Category_Id`, `Description`, `Requested_Relief`, `Date_Reported`, `Date_Filed`, `Resolution_Status`, `Latitude`, `Longitude`, `Handled_By`) VALUES
(2, 4, 5, NULL, 1, 'Maribukon. Makusugon ang patugtog maski matanga na', 'Tawan kami 5k for mental damages', '2026-08-30 14:55:06', '2026-08-30 14:55:06', 'Pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `incident_types`
--

CREATE TABLE `incident_types` (
  `Category_Id` int(11) NOT NULL,
  `Category` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_types`
--

INSERT INTO `incident_types` (`Category_Id`, `Category`) VALUES
(1, 'Noise Complaint');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_27_000001_create_events_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resident`
--

CREATE TABLE `resident` (
  `Resident_ID` int(11) NOT NULL,
  `Household_Index` int(11) DEFAULT NULL,
  `First_Name` varchar(255) NOT NULL,
  `Middle_Name` varchar(255) DEFAULT NULL,
  `Last_Name` varchar(255) NOT NULL,
  `Date_of_Birth` date DEFAULT NULL,
  `Gender` varchar(20) DEFAULT NULL,
  `Contact_Number` varchar(50) DEFAULT NULL,
  `Is_Verified` tinyint(1) NOT NULL DEFAULT 0,
  `Place_of_Birth` varchar(255) DEFAULT NULL,
  `Civil_Status` enum('Single','Married','Widowed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resident`
--

INSERT INTO `resident` (`Resident_ID`, `Household_Index`, `First_Name`, `Middle_Name`, `Last_Name`, `Date_of_Birth`, `Gender`, `Contact_Number`, `Is_Verified`, `Place_of_Birth`, `Civil_Status`) VALUES
(4, 1, 'Prince Marvin', 'Engay', 'Azul', '2004-08-25', 'Male', '09189231139', 1, 'Legazpi City', 'Single'),
(5, 4, 'Francis Julius', 'Galias', 'Castuera', '2003-05-03', 'Male', '09093241232', 1, 'Daraga', 'Widowed');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_user`
--

CREATE TABLE `system_user` (
  `User_ID` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password_Hash` varchar(255) NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Full_Name` varchar(255) DEFAULT NULL,
  `Is_Active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_user`
--

INSERT INTO `system_user` (`User_ID`, `Username`, `Password_Hash`, `Role`, `Full_Name`, `Is_Active`) VALUES
(1, 'admin', '$2y$12$TxEuhxwAlepnxenerJLUD.kBFYjthmPAk3PCmqnztW0/NJ9jmw6nO', 'admin', 'admin', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`Announcement_ID`),
  ADD KEY `Posted_By` (`Posted_By`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`Log_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `document_request`
--
ALTER TABLE `document_request`
  ADD PRIMARY KEY (`Request_ID`),
  ADD KEY `Resident_ID` (`Resident_ID`),
  ADD KEY `Processed_By` (`Processed_By`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`Event_ID`),
  ADD KEY `Created_By` (`Created_By`);

--
-- Indexes for table `event_rsvp`
--
ALTER TABLE `event_rsvp`
  ADD PRIMARY KEY (`RSVP_ID`),
  ADD KEY `Event_ID` (`Event_ID`),
  ADD KEY `Resident_ID` (`Resident_ID`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `guest`
--
ALTER TABLE `guest`
  ADD PRIMARY KEY (`Guest_Id`);

--
-- Indexes for table `household`
--
ALTER TABLE `household`
  ADD PRIMARY KEY (`Household_Index`);

--
-- Indexes for table `incident_blotter`
--
ALTER TABLE `incident_blotter`
  ADD PRIMARY KEY (`Incident_ID`),
  ADD KEY `Resident_ID` (`Complainant_Id`),
  ADD KEY `Handled_By` (`Handled_By`),
  ADD KEY `Guest_Id` (`Guest_Id`),
  ADD KEY `Incident_Type` (`Category_Id`),
  ADD KEY `Respondent_ID` (`Respondent_Id`);

--
-- Indexes for table `incident_types`
--
ALTER TABLE `incident_types`
  ADD PRIMARY KEY (`Category_Id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `resident`
--
ALTER TABLE `resident`
  ADD PRIMARY KEY (`Resident_ID`),
  ADD KEY `Household_ID` (`Household_Index`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `system_user`
--
ALTER TABLE `system_user`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `Announcement_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `Log_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_request`
--
ALTER TABLE `document_request`
  MODIFY `Request_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `Event_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `event_rsvp`
--
ALTER TABLE `event_rsvp`
  MODIFY `RSVP_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest`
--
ALTER TABLE `guest`
  MODIFY `Guest_Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `household`
--
ALTER TABLE `household`
  MODIFY `Household_Index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `incident_blotter`
--
ALTER TABLE `incident_blotter`
  MODIFY `Incident_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `incident_types`
--
ALTER TABLE `incident_types`
  MODIFY `Category_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `resident`
--
ALTER TABLE `resident`
  MODIFY `Resident_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_user`
--
ALTER TABLE `system_user`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement`
--
ALTER TABLE `announcement`
  ADD CONSTRAINT `announcement_ibfk_1` FOREIGN KEY (`Posted_By`) REFERENCES `system_user` (`User_ID`) ON DELETE SET NULL;

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `system_user` (`User_ID`) ON DELETE SET NULL;

--
-- Constraints for table `document_request`
--
ALTER TABLE `document_request`
  ADD CONSTRAINT `document_request_ibfk_1` FOREIGN KEY (`Resident_ID`) REFERENCES `resident` (`Resident_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_request_ibfk_2` FOREIGN KEY (`Processed_By`) REFERENCES `system_user` (`User_ID`) ON DELETE SET NULL;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`Created_By`) REFERENCES `system_user` (`User_ID`) ON DELETE SET NULL;

--
-- Constraints for table `event_rsvp`
--
ALTER TABLE `event_rsvp`
  ADD CONSTRAINT `event_rsvp_ibfk_1` FOREIGN KEY (`Event_ID`) REFERENCES `event` (`Event_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_rsvp_ibfk_2` FOREIGN KEY (`Resident_ID`) REFERENCES `resident` (`Resident_ID`) ON DELETE CASCADE;

--
-- Constraints for table `incident_blotter`
--
ALTER TABLE `incident_blotter`
  ADD CONSTRAINT `incident_blotter_ibfk_1` FOREIGN KEY (`Complainant_Id`) REFERENCES `resident` (`Resident_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `incident_blotter_ibfk_2` FOREIGN KEY (`Handled_By`) REFERENCES `system_user` (`User_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `incident_blotter_ibfk_3` FOREIGN KEY (`Guest_Id`) REFERENCES `guest` (`Guest_Id`),
  ADD CONSTRAINT `incident_blotter_ibfk_4` FOREIGN KEY (`Category_Id`) REFERENCES `incident_types` (`Category_Id`),
  ADD CONSTRAINT `incident_blotter_ibfk_5` FOREIGN KEY (`Respondent_Id`) REFERENCES `resident` (`Resident_ID`);

--
-- Constraints for table `resident`
--
ALTER TABLE `resident`
  ADD CONSTRAINT `resident_ibfk_1` FOREIGN KEY (`Household_Index`) REFERENCES `household` (`Household_Index`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
