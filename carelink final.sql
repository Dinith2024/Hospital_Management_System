-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2025 at 08:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carelink`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `AppointmentID` int(11) NOT NULL,
  `PatientID` int(11) NOT NULL,
  `DoctorID` int(11) NOT NULL,
  `FacilityID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` varchar(50) NOT NULL,
  `Status` enum('BOOKED','COMPLETED','CANCELLED') DEFAULT 'BOOKED',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`AppointmentID`, `PatientID`, `DoctorID`, `FacilityID`, `Date`, `Time`, `Status`, `CreatedAt`) VALUES
(1, 9, 2, 2, '2025-09-17', '10:30:00-11:00:00', 'CANCELLED', '2025-09-12 17:05:19'),
(2, 9, 2, 2, '2025-09-17', '12:00:00-12:30:00', 'BOOKED', '2025-09-12 17:08:21'),
(3, 9, 2, 2, '2025-09-24', '10:30:00-11:00:00', 'BOOKED', '2025-09-12 17:12:26'),
(4, 9, 2, 2, '2025-09-26', '12:30:00-13:00:00', 'BOOKED', '2025-09-13 06:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'manushalakshani03', 'manushalakshani03@gmail.com', 'Headache', 'I’ve been experiencing frequent headaches and would like to get your advice. Could I schedule a consultation to discuss this further?', '2025-09-12 14:48:04'),
(2, 'manushalakshani03', 'manushalakshani03@gmail.com', 'Updated subject', 'Added new message text ', '2025-09-12 14:48:57'),
(4, 'Pavitha Wickramasinghe', 'pavitha@gmail.com', 'My nearest branch', 'I want to know where the nearest branch to where I live is at since I want to get my tests done ASAP.\r\nMy address is 289/8B, Lake Road, Malabe.', '2025-09-13 06:55:46'),
(5, 'Janudi Ranasinghe', 'rjanudi@gmail.com', 'Get report hardcopies', 'Is it possible to get certified hardcopies of the reports?', '2025-09-13 07:55:22');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `Doctor ID` int(11) NOT NULL,
  `Title` varchar(10) NOT NULL,
  `First Name` varchar(200) NOT NULL,
  `Last Name` varchar(200) NOT NULL,
  `NIC` varchar(20) NOT NULL,
  `Facility Name` varchar(300) NOT NULL,
  `License Number` int(50) NOT NULL,
  `Specialization` varchar(300) NOT NULL,
  `Experience` int(11) NOT NULL,
  `Qualifications` varchar(1000) NOT NULL,
  `Phone Number` varchar(10) NOT NULL,
  `Email Address` varchar(200) NOT NULL,
  `Password` varchar(300) NOT NULL,
  `Profile Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`Doctor ID`, `Title`, `First Name`, `Last Name`, `NIC`, `Facility Name`, `License Number`, `Specialization`, `Experience`, `Qualifications`, `Phone Number`, `Email Address`, `Password`, `Profile Image`) VALUES
(2, 'Mr', 'Arjun', 'Singh', '199098596699', '2', 105660003, 'Cardiologist', 3, 'Bachelor of Medicine, Bachelor of Surgery', '0716554379', 'rjanudi@gmail.com', '$2y$10$jFTuTB6Gto47kHm5k/1cGeiyDr36Q5wthMoAPAr2aao1MmZgYBqXO', 'Images/Doctors/doctor_2_1757657580.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule`
--

CREATE TABLE `doctor_schedule` (
  `Day` varchar(20) NOT NULL,
  `Start Time` varchar(10) NOT NULL,
  `End Time` varchar(10) NOT NULL,
  `Schedule ID` int(11) NOT NULL,
  `Slot Duration` int(11) NOT NULL,
  `Number of Slots` int(11) NOT NULL,
  `Doctor ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedule`
--

INSERT INTO `doctor_schedule` (`Day`, `Start Time`, `End Time`, `Schedule ID`, `Slot Duration`, `Number of Slots`, `Doctor ID`) VALUES
('Monday', '08:00', '12:00', 6, 30, 8, 2),
('Tuesday', '09:00', '12:00', 19, 30, 6, 2),
('Wednesday', '09:00', '18:00', 20, 30, 18, 2),
('Friday', '12:00', '15:00', 21, 30, 6, 2);

--
-- Triggers `doctor_schedule`
--
DELIMITER $$
CREATE TRIGGER `after_schedule_insert` AFTER INSERT ON `doctor_schedule` FOR EACH ROW BEGIN
    DECLARE slot_start TIME;
    DECLARE slot_end TIME;
    DECLARE counter INT DEFAULT 0;
    DECLARE day_value VARCHAR(20);

    SET slot_start = NEW.`Start Time`;
    SET day_value  = NEW.`Day`;

    WHILE counter < NEW.`Number of Slots` DO
        SET slot_end = ADDTIME(slot_start, SEC_TO_TIME( NEW.`Slot Duration` * 60 ));

        -- adjust column names in this INSERT to match doctor_slots exactly
        INSERT INTO `doctor_slots` (`ScheduleID`, `DoctorID`, `Day`, `StartTime`, `EndTime`, `Status`)
        VALUES (NEW.`Schedule ID`, NEW.`Doctor ID`, day_value, slot_start, slot_end, 'AVAILABLE');

        SET slot_start = slot_end;
        SET counter = counter + 1;
    END WHILE;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_slots`
--

CREATE TABLE `doctor_slots` (
  `SlotID` int(11) NOT NULL,
  `ScheduleID` int(11) DEFAULT NULL,
  `DoctorID` int(11) DEFAULT NULL,
  `Day` varchar(100) DEFAULT NULL,
  `StartTime` varchar(100) DEFAULT NULL,
  `EndTime` varchar(100) DEFAULT NULL,
  `Status` enum('AVAILABLE','UNAVAILABLE') DEFAULT 'AVAILABLE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_slots`
--

INSERT INTO `doctor_slots` (`SlotID`, `ScheduleID`, `DoctorID`, `Day`, `StartTime`, `EndTime`, `Status`) VALUES
(1, 20, 2, 'Wednesday', '09:00:00', '09:30:00', 'AVAILABLE'),
(2, 20, 2, 'Wednesday', '09:30:00', '10:00:00', 'AVAILABLE'),
(3, 20, 2, 'Wednesday', '10:00:00', '10:30:00', 'AVAILABLE'),
(4, 20, 2, 'Wednesday', '10:30:00', '11:00:00', 'AVAILABLE'),
(5, 20, 2, 'Wednesday', '11:00:00', '11:30:00', 'AVAILABLE'),
(6, 20, 2, 'Wednesday', '11:30:00', '12:00:00', 'AVAILABLE'),
(7, 20, 2, 'Wednesday', '12:00:00', '12:30:00', 'UNAVAILABLE'),
(8, 20, 2, 'Wednesday', '12:30:00', '13:00:00', 'AVAILABLE'),
(9, 20, 2, 'Wednesday', '13:00:00', '13:30:00', 'AVAILABLE'),
(10, 20, 2, 'Wednesday', '13:30:00', '14:00:00', 'AVAILABLE'),
(11, 20, 2, 'Wednesday', '14:00:00', '14:30:00', 'AVAILABLE'),
(12, 20, 2, 'Wednesday', '14:30:00', '15:00:00', 'AVAILABLE'),
(13, 20, 2, 'Wednesday', '15:00:00', '15:30:00', 'AVAILABLE'),
(14, 20, 2, 'Wednesday', '15:30:00', '16:00:00', 'AVAILABLE'),
(15, 20, 2, 'Wednesday', '16:00:00', '16:30:00', 'AVAILABLE'),
(16, 20, 2, 'Wednesday', '16:30:00', '17:00:00', 'AVAILABLE'),
(17, 20, 2, 'Wednesday', '17:00:00', '17:30:00', 'AVAILABLE'),
(18, 20, 2, 'Wednesday', '17:30:00', '18:00:00', 'AVAILABLE'),
(19, 21, 2, 'Friday', '12:00:00', '12:30:00', 'AVAILABLE'),
(20, 21, 2, 'Friday', '12:30:00', '13:00:00', 'AVAILABLE'),
(21, 21, 2, 'Friday', '13:00:00', '13:30:00', 'AVAILABLE'),
(22, 21, 2, 'Friday', '13:30:00', '14:00:00', 'AVAILABLE'),
(23, 21, 2, 'Friday', '14:00:00', '14:30:00', 'AVAILABLE'),
(24, 21, 2, 'Friday', '14:30:00', '15:00:00', 'AVAILABLE');

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

CREATE TABLE `facility` (
  `Facility ID` int(11) NOT NULL,
  `Facility Type` varchar(100) NOT NULL,
  `Facility Name` varchar(500) NOT NULL,
  `Sector` varchar(200) NOT NULL,
  `MOH_PHSRC NUMBER` varchar(50) NOT NULL,
  `Address` varchar(500) NOT NULL,
  `Phone Number` varchar(10) NOT NULL,
  `Email Address` varchar(200) NOT NULL,
  `Password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility`
--

INSERT INTO `facility` (`Facility ID`, `Facility Type`, `Facility Name`, `Sector`, `MOH_PHSRC NUMBER`, `Address`, `Phone Number`, `Email Address`, `Password`) VALUES
(2, 'Clinic', 'Sunshine Medical Clinic', 'Private', '2020V67MC', '123 Main Street, Colombo', '0771234567', 'info@sunshineclinic.lk', '$2y$10$.HEjHLhvVTsMFGLhMHQTZ.br0uB5RmB20xetN95QyA2sI0kiSXvWi');

-- --------------------------------------------------------

--
-- Table structure for table `medical_reports`
--

CREATE TABLE `medical_reports` (
  `PatientID` int(11) NOT NULL,
  `DoctorID` int(11) NOT NULL,
  `TestType` varchar(500) NOT NULL,
  `TestDate` date NOT NULL,
  `Priority` varchar(100) NOT NULL,
  `Notes` varchar(2000) NOT NULL,
  `Attachments` varchar(1000) NOT NULL,
  `ReportID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_reports`
--

INSERT INTO `medical_reports` (`PatientID`, `DoctorID`, `TestType`, `TestDate`, `Priority`, `Notes`, `Attachments`, `ReportID`) VALUES
(9, 2, 'Blood Test', '2025-09-12', 'Urgent', 'Patient fasted for 10 hours before the test.', '1757772943_Blood Report.pdf', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_type` enum('patient','doctor') NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_type`, `user_id`, `token_hash`, `expires_at`, `used`, `created_at`) VALUES
(1, 'patient', 9, '272e72c242c5059a74aac70fd09c8dee2c4bb69676a069c9991dd7e4098b8617', '2025-09-13 19:36:16', 0, '2025-09-13 22:06:16'),
(22, 'patient', 7, '$2y$10$lSV7U1fLCfPZlI8SuorOo.cCt.WsdydEyez69MKcbYV/.X7nc5f4O', '2025-09-14 00:16:54', 0, '2025-09-13 23:16:54'),
(23, 'patient', 7, '$2y$10$tZDXvUH.n9SwBSv3IjWcNeja7ALKZZ2jZut8w7PQu67es0g2RDi5q', '2025-09-14 00:23:56', 0, '2025-09-13 23:23:56'),
(24, 'patient', 7, '$2y$10$QnMRnEvPFFjkMZFd1UQBs.NTLw2YlUi1dmgBzoMxU7yt8qmhNY1ki', '2025-09-14 00:24:01', 0, '2025-09-13 23:24:01'),
(25, 'patient', 7, '$2y$10$35xLHYK9fd1owqF.8WXP4OS6mpBGHny022EjLSIMSW8bRSp40gb6S', '2025-09-14 00:24:04', 0, '2025-09-13 23:24:04'),
(26, 'patient', 7, '$2y$10$Uty2GdovxDLXMZaIZGEWh.y6vXy3APugvDQ8TH1tJNOqWAt1SdLdy', '2025-09-14 00:24:08', 0, '2025-09-13 23:24:08'),
(27, 'patient', 7, '$2y$10$H6kj5vQL0JMa4jc4Ovkav.CIEO0.bjtajBs6AOQCWHAqc5PqRSXiK', '2025-09-14 00:24:11', 0, '2025-09-13 23:24:11'),
(28, 'patient', 7, '$2y$10$3hTax/w0P3rBTgtn9OF3Ye2/URLAC9b2orDGHEtCdH32atQyvu/Ga', '2025-09-14 00:24:15', 0, '2025-09-13 23:24:15'),
(29, 'patient', 7, '$2y$10$5K.s.dJUUVQLSmsWWUB.HOBFekRtZe9dQfcwTh1U/43t.Pl0vgYT6', '2025-09-14 00:24:19', 0, '2025-09-13 23:24:19'),
(30, 'patient', 7, '$2y$10$n8v4qIYaIXwU7v7mu8pBXeyOhxYjG0mBtFjBfaranLsgzvBRO4cgS', '2025-09-14 00:25:46', 0, '2025-09-13 23:25:46'),
(31, 'patient', 7, '$2y$10$nT3Bylwc9kmBsp1kuK5QtObHN2K7YaTdbSWyh6CLaGqAeqM0pu4PS', '2025-09-14 00:25:50', 0, '2025-09-13 23:25:50'),
(32, 'patient', 5, '$2y$10$Qqq2eJ6C1kR0PO.XG8p0B.1PRFI9PoAli5yQcRqzkdDRm6fwKaOdW', '2025-09-14 00:26:41', 0, '2025-09-13 23:26:41'),
(33, '', 2, '$2y$10$sii1kIZ/kSjiDss4bcQnj.Crrclx60g9IU/GLMh1im5NesdWLI.qq', '2025-09-14 00:42:39', 0, '2025-09-13 23:42:39'),
(35, 'patient', 7, '$2y$10$YGjhhnqTKqNlw6wYIACZ0eNcYvcVJADEymYv.lBjdXMrS0cXqhFQC', '2025-09-14 00:46:20', 0, '2025-09-13 23:46:20'),
(36, 'patient', 5, '$2y$10$LpUiLCIRvNDpjCyHnieABu63v4I/f7yKJyDM2EfoRIpEg5KR.HC0e', '2025-09-14 00:46:45', 0, '2025-09-13 23:46:45'),
(39, 'doctor', 2, '$2y$10$jmxBZFTB.vufBDDvO/cl8eDCGkwjPWhNJkIm0i5RKjFEOZXLxrszq', '2025-09-14 01:01:28', 0, '2025-09-14 00:01:28'),
(40, 'patient', 5, '$2y$10$bmu0JzeGFXnE05BKSgyHr.40Uu51ibUtwIWE4snI7n.cKrdKMK.Zq', '2025-09-14 01:07:25', 0, '2025-09-14 00:07:25');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `NIC` varchar(12) NOT NULL,
  `Password` varchar(1000) NOT NULL,
  `PatientID` int(11) NOT NULL,
  `Title` varchar(5) NOT NULL,
  `First Name` varchar(200) NOT NULL,
  `Last Name` varchar(200) NOT NULL,
  `Phone Number` varchar(10) NOT NULL,
  `Email Address` varchar(200) NOT NULL,
  `Created Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Blood Type` varchar(10) DEFAULT NULL,
  `Allergies` text DEFAULT NULL,
  `Medical History` text DEFAULT NULL,
  `Date of Birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`NIC`, `Password`, `PatientID`, `Title`, `First Name`, `Last Name`, `Phone Number`, `Email Address`, `Created Date`, `Blood Type`, `Allergies`, `Medical History`, `Date of Birth`) VALUES
('200265205004', '$2y$10$cqtkDe3aAsAgcZp/FSEp4.oFqHT5jchMcwGGXiWNY2N', 5, 'Ms', 'Janudi', 'Ranasinghe', '0776223279', 'rjanudi@gmail.com', '2025-09-11 09:20:15', NULL, NULL, NULL, NULL),
('200209786543', '$2y$10$2k3341al94pXj1OGdpVu1OCcNhx/x.DRO/4EM1QK2JY', 6, 'Ms', 'Pavitha', 'Wickramasinghe', '0468426034', 'pavitha@gmail.com', '2025-09-11 18:16:23', NULL, NULL, NULL, NULL),
('200109890021', '$2y$10$MkQ8ODg4fo9gu/7lExaAJ.4CszO1UCqT/WT2gziABuRgFr5gVvV32', 7, 'Mr', 'Dinith', 'Sasanga', '0778676782', 'dinithsasanga@gmail.com', '2025-09-11 18:26:16', NULL, NULL, NULL, NULL),
('199560784003', '$2y$10$80Wphcp6ZwnjaWBlS26GuOpRUc2HgPTAlb1jIbGtTCAPNjzVt6h4O', 8, 'Dr', 'Janet', 'Perera', '0776223255', 'janet@gmail.com', '2025-09-11 18:34:03', NULL, NULL, NULL, NULL),
('199520400356', '$2y$10$1APEIpCMKLrOpRo1wv8QwO.V27TT2ASpyQj2W6hKLxM892VuXf.Ha', 9, 'Mr', 'Joe', 'Perera', '0776223245', 'joey@gmail.com', '2025-09-12 03:30:16', 'A+', 'None', '', '2001-02-21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`AppointmentID`),
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `DoctorID` (`DoctorID`),
  ADD KEY `FacilityID` (`FacilityID`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`Doctor ID`);

--
-- Indexes for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD PRIMARY KEY (`Schedule ID`),
  ADD KEY `fk_doctor_schedule_doctor` (`Doctor ID`);

--
-- Indexes for table `doctor_slots`
--
ALTER TABLE `doctor_slots`
  ADD PRIMARY KEY (`SlotID`),
  ADD KEY `ScheduleID` (`ScheduleID`),
  ADD KEY `DoctorID` (`DoctorID`);

--
-- Indexes for table `facility`
--
ALTER TABLE `facility`
  ADD PRIMARY KEY (`Facility ID`);

--
-- Indexes for table `medical_reports`
--
ALTER TABLE `medical_reports`
  ADD PRIMARY KEY (`ReportID`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`PatientID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `Doctor ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  MODIFY `Schedule ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `doctor_slots`
--
ALTER TABLE `doctor_slots`
  MODIFY `SlotID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `facility`
--
ALTER TABLE `facility`
  MODIFY `Facility ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medical_reports`
--
ALTER TABLE `medical_reports`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `PatientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `doctors` (`Doctor ID`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`FacilityID`) REFERENCES `facility` (`Facility ID`);

--
-- Constraints for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD CONSTRAINT `fk_doctor_schedule_doctor` FOREIGN KEY (`Doctor ID`) REFERENCES `doctors` (`Doctor ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doctor_slots`
--
ALTER TABLE `doctor_slots`
  ADD CONSTRAINT `doctor_slots_ibfk_1` FOREIGN KEY (`ScheduleID`) REFERENCES `doctor_schedule` (`Schedule ID`),
  ADD CONSTRAINT `doctor_slots_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `doctor_schedule` (`Doctor ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
