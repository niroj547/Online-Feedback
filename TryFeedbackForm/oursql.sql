-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Mar 24, 2025 at 02:12 PM
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
-- Database: `feedback_system`
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
(1, 'admin@example.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

CREATE TABLE `lecturer` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `program` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `full_name`, `email`, `password`, `student_id`, `academic_year`, `semester`, `section`, `program`) VALUES
(30, 'AAKANSHA ACHARYA', 'aakansha.acharya@l5cg22.edu', 'student123', 'L5CG22-01', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(31, 'AARUSHI SHARMA', 'aarushi.sharma@l5cg22.edu', 'student123', 'L5CG22-02', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(32, 'ANUP MAHARJAN', 'anup.maharjan@l5cg22.edu', 'student123', 'L5CG22-03', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(33, 'BIBEK KARKI', 'bibek.karki@l5cg22.edu', 'student123', 'L5CG22-04', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(34, 'BINUJA SUBEDI', 'binuja.subedi@l5cg22.edu', 'student123', 'L5CG22-05', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(35, 'BRISHIKA GHALAN', 'brishika.ghalan@l5cg22.edu', 'student123', 'L5CG22-06', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(36, 'CHUJAN TAMANG', 'chujan.tamang@l5cg22.edu', 'student123', 'L5CG22-07', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(37, 'DUBKSHEN TAMANG', 'dubkshen.tamang@l5cg22.edu', 'student123', 'L5CG22-08', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(38, 'KRISHIKA KHADKA', 'krishika.khadka@l5cg22.edu', 'student123', 'L5CG22-09', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(39, 'NALINA RAI', 'nalina.rai@l5cg22.edu', 'student123', 'L5CG22-10', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(40, 'NAMUNA REGMI', 'namuna.regmi@l5cg22.edu', 'student123', 'L5CG22-11', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(41, 'NIROJ KHATRI', 'niroj.khatri@l5cg22.edu', 'student123', 'L5CG22-12', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(42, 'PEMA GYALGEN TAMANG', 'pema.tamang@l5cg22.edu', 'student123', 'L5CG22-13', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(43, 'PHERISH KHYAJU', 'pherish.khyaju@l5cg22.edu', 'student123', 'L5CG22-14', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(44, 'PRABIN CHAUDHARY', 'prabin.chaudhary@l5cg22.edu', 'student123', 'L5CG22-15', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(45, 'PRASHANSA HAMAL', 'prashansa.hamal@l5cg22.edu', 'student123', 'L5CG22-16', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(46, 'PRASHNA PRAJAPATI', 'prashna.prajapati@l5cg22.edu', 'student123', 'L5CG22-17', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(47, 'PRASHRAYA CHALISE', 'prashraya.chalise@l5cg22.edu', 'student123', 'L5CG22-18', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(48, 'PUJA RAI', 'puja.rai@l5cg22.edu', 'student123', 'L5CG22-19', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(49, 'RAJAN RAJ SAH', 'rajan.sah@l5cg22.edu', 'student123', 'L5CG22-20', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(50, 'RUBINA CHHAHARI', 'rubina.chhahari@l5cg22.edu', 'student123', 'L5CG22-21', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(51, 'SAKSHYAM SING BHANDARI', 'sakshyam.bhandari@l5cg22.edu', 'student123', 'L5CG22-22', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(52, 'SAMIR GURUNG', 'samir.gurung@l5cg22.edu', 'student123', 'L5CG22-23', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(53, 'SARANA SHAKYA', 'sarana.shakya@l5cg22.edu', 'student123', 'L5CG22-24', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(54, 'SIMRAN SHRESTHA', 'simran.shrestha@l5cg22.edu', 'student123', 'L5CG22-25', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(55, 'SUSHMA BASNET', 'sushma.basnet@l5cg22.edu', 'student123', 'L5CG22-26', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(56, 'SWEEKRITI DIWAKAR', 'sweekriti.diwakar@l5cg22.edu', 'student123', 'L5CG22-27', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(57, 'SWETA THAPA MAGAR', 'sweta.thapa@l5cg22.edu', 'student123', 'L5CG22-28', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS'),
(58, 'UGESH KC', 'ugesh.kc@l5cg22.edu', 'student123', 'L5CG22-29', 'Year 2', '4th', 'L5CG22', 'BSc(Hons)CS');

-- --------------------------------------------------------

--
-- Table structure for table `tutor`
--

CREATE TABLE `tutor` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `tutor`
--
ALTER TABLE `tutor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
