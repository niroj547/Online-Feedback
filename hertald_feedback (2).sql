-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 13, 2025 at 07:25 AM
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
-- Database: `hertald_feedback`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`) VALUES
(1, 'HertaldAdmin428935@gmail.com', 'HertaldAdmin');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('active','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `name`, `status`) VALUES
(1, 'Collaborative Development', 'suspended'),
(2, 'Human-Computer Interaction', 'active'),
(3, 'Distributed and Cloud Systems Programming', 'active'),
(4, 'AI expertise', 'active'),
(5, 'Math ', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `feedback_date` date DEFAULT NULL,
  `section` varchar(20) DEFAULT NULL,
  `anonymous_mode` tinyint(1) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `lecturer_rating` int(11) DEFAULT NULL CHECK (`lecturer_rating` between 1 and 5),
  `tutor_rating` int(11) DEFAULT NULL CHECK (`tutor_rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `lecturer_id` int(11) DEFAULT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `lecturer_comment` text DEFAULT NULL,
  `tutor_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `student_name`, `academic_year`, `semester`, `feedback_date`, `section`, `anonymous_mode`, `course_id`, `lecturer_rating`, `tutor_rating`, `comment`, `verified`, `lecturer_id`, `tutor_id`, `lecturer_comment`, `tutor_comment`) VALUES
(13, '', NULL, NULL, '2025-04-07', NULL, 1, 1, 2, 1, NULL, 0, 1, 1, 'low confidence and no proper way of explaining.', 'Cant guide properly weak explanation'),
(14, '', NULL, NULL, '2025-04-07', NULL, 0, 1, 5, 5, NULL, 0, 1, 1, '', ''),
(15, 'Anup', NULL, NULL, '2025-04-07', NULL, 0, 1, 5, 5, NULL, 0, 1, 1, '', ''),
(16, '', NULL, NULL, '2025-04-07', NULL, 0, 1, 5, 5, NULL, 0, 1, 1, '', ''),
(17, '', NULL, NULL, '2025-04-07', NULL, 0, 2, 5, 5, NULL, 0, 2, 2, '', ''),
(18, '', NULL, NULL, '2025-04-07', NULL, 0, 1, 5, 5, NULL, 0, 1, 1, '', ''),
(19, '', NULL, NULL, '2025-04-07', NULL, 1, 4, 5, 5, NULL, 0, 4, 4, '', ''),
(20, '', NULL, NULL, '2025-04-07', NULL, 1, 1, 5, 5, NULL, 0, 1, 1, '', ''),
(21, '', NULL, NULL, '2025-04-07', NULL, 1, 2, 5, 5, NULL, 0, 2, 2, '', ''),
(22, '', NULL, NULL, '2025-04-07', NULL, 1, 2, 5, 5, NULL, 1, 2, 2, '', ''),
(24, 'ram', NULL, NULL, '2025-04-07', NULL, 0, 1, 4, 4, NULL, 0, 1, 1, '', ''),
(25, '', NULL, NULL, '2025-05-06', NULL, 1, 4, 4, 4, NULL, 1, 4, 4, 'nice', 'bad'),
(27, 'Aakansha Acharya', NULL, NULL, '2025-05-07', NULL, 0, 4, 3, 3, NULL, 0, 4, 4, 'nice', 'nice'),
(29, 'Aakansha Acharya', NULL, NULL, '2025-05-10', NULL, 0, 2, 5, 4, NULL, 0, 2, 2, 'nice', 'mid'),
(31, 'Aakansha Acharya', NULL, NULL, '2025-05-10', NULL, 0, 5, 5, 5, NULL, 0, 5, 5, '', ''),
(32, 'Aakansha Acharya', NULL, NULL, '2025-05-10', NULL, 0, 4, 5, 5, NULL, 0, 4, 4, '', ''),
(33, 'Anup Maharjan', NULL, NULL, '2025-05-10', NULL, 0, 5, 5, 5, NULL, 0, 5, 5, '', ''),
(34, 'Anup Maharjan', NULL, NULL, '2025-05-10', NULL, 0, 3, 5, 4, NULL, 0, 3, 3, '', ''),
(35, 'Aarushi Sharma', NULL, NULL, '2025-05-11', NULL, 0, 4, 5, 5, NULL, 1, 4, 4, '', ''),
(36, 'Aarushi Sharma', NULL, NULL, '2025-05-11', NULL, 0, 3, 4, 4, NULL, 0, 3, 3, '', ''),
(37, 'Bibek Karki', NULL, NULL, '2025-05-11', NULL, 0, 5, 5, 5, NULL, 1, 5, 5, 'nice', 'nice');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

CREATE TABLE `lecturer` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `status` enum('active','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`id`, `name`, `course_id`, `status`) VALUES
(1, 'Ram Shah', 1, 'suspended'),
(2, 'Johny Williams', 2, 'active'),
(3, 'Amar Singh', 3, 'active'),
(4, 'Samira shah', 4, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `semester` varchar(10) DEFAULT NULL,
  `section` varchar(20) DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `status` enum('active','suspended') DEFAULT 'active',
  `last_feedback_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `full_name`, `email`, `password`, `student_id`, `academic_year`, `semester`, `section`, `program`, `status`, `last_feedback_time`) VALUES
(1, 'AAKANSHA ACHARYA', 'aakansha.acharya@l5cg22.edu', 'student123', 'L5CG22-01', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', '2025-05-10 08:43:35'),
(2, 'AARUSHI SHARMA', 'aarushi.sharma@l5cg22.edu', 'student123', 'L5CG22-02', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', '2025-05-11 06:59:54'),
(3, 'ANUP MAHARJAN', 'anup.maharjan@l5cg22.edu', 'student123', 'L5CG22-03', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', '2025-05-11 08:02:43'),
(4, 'BIBEK KARKI', 'bibek.karki@l5cg22.edu', 'student123', 'L5CG22-04', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', '2025-05-11 07:57:25'),
(5, 'BINUJA SUBEDI', 'binuja.subedi@l5cg22.edu', 'student123', 'L5CG22-05', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(6, 'BRISHIKA GHALAN', 'brishika.ghalan@l5cg22.edu', 'student123', 'L5CG22-06', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(7, 'CHUJAN TAMANG', 'chujan.tamang@l5cg22.edu', 'student123', 'L5CG22-07', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(8, 'DUBKSHEN TAMANG', 'dubkshen.tamang@l5cg22.edu', 'student123', 'L5CG22-08', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(9, 'KRISHIKA KHADKA', 'krishika.khadka@l5cg22.edu', 'student123', 'L5CG22-09', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(10, 'NALINA RAI', 'nalina.rai@l5cg22.edu', 'student123', 'L5CG22-10', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(11, 'NAMUNA REGMI', 'namuna.regmi@l5cg22.edu', 'student123', 'L5CG22-11', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(12, 'NIROJ KHATRI', 'niroj.khatri@l5cg22.edu', 'student123', 'L5CG22-12', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(13, 'PEMA GYALGEN TAMANG', 'pema.tamang@l5cg22.edu', 'student123', 'L5CG22-13', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(14, 'PHERISH KHYAJU', 'pherish.khyaju@l5cg22.edu', 'student123', 'L5CG22-14', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(15, 'PRABIN CHAUDHARY', 'prabin.chaudhary@l5cg22.edu', 'student123', 'L5CG22-15', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(16, 'PRASHANSA HAMAL', 'prashansa.hamal@l5cg22.edu', 'student123', 'L5CG22-16', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(17, 'PRASHNA PRAJAPATI', 'prashna.prajapati@l5cg22.edu', 'student123', 'L5CG22-17', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(18, 'PRASHRAYA CHALISE', 'prashraya.chalise@l5cg22.edu', 'student123', 'L5CG22-18', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(19, 'PUJA RAI', 'puja.rai@l5cg22.edu', 'student123', 'L5CG22-19', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(20, 'RAJAN RAJ SAH', 'rajan.sah@l5cg22.edu', 'student123', 'L5CG22-20', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(21, 'RUBINA CHHAHARI', 'rubina.chhahari@l5cg22.edu', 'student123', 'L5CG22-21', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(22, 'SAKSHYAM SING BHANDARI', 'sakshyam.bhandari@l5cg22.edu', 'student123', 'L5CG22-22', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(23, 'SAMIR GURUNG', 'samir.gurung@l5cg22.edu', 'student123', 'L5CG22-23', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(24, 'SARANA SHAKYA', 'sarana.shakya@l5cg22.edu', 'student123', 'L5CG22-24', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(25, 'SIMRAN SHRESTHA', 'simran.shrestha@l5cg22.edu', 'student123', 'L5CG22-25', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(26, 'SUSHMA BASNET', 'sushma.basnet@l5cg22.edu', 'student123', 'L5CG22-26', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(27, 'SWEEKRITI DIWAKAR', 'sweekriti.diwakar@l5cg22.edu', 'student123', 'L5CG22-27', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(28, 'SWETA THAPA MAGAR', 'sweta.thapa@l5cg22.edu', 'student123', 'L5CG22-28', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(29, 'UGESH KC', 'ugesh.kc@l5cg22.edu', 'student123', 'L5CG22-29', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL),
(30, 'Alen', 'alen.kn@l5cg22.edu', 'student123', 'L5CG22-30', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tutor`
--

CREATE TABLE `tutor` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `status` enum('active','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutor`
--

INSERT INTO `tutor` (`id`, `name`, `course_id`, `status`) VALUES
(1, 'Shyam Magar', 1, 'active'),
(2, 'Rajana Raj', 2, 'active'),
(3, 'Anupa Maharjan', 3, 'active'),
(4, 'suman shrestha', 4, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `tutor`
--
ALTER TABLE `tutor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tutor`
--
ALTER TABLE `tutor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD CONSTRAINT `lecturer_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`);

--
-- Constraints for table `tutor`
--
ALTER TABLE `tutor`
  ADD CONSTRAINT `tutor_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
