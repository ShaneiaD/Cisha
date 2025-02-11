-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2025 at 04:35 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `learning_ms`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `class_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `course_id`, `class_name`, `description`, `instructor_id`, `created_at`) VALUES
(1, 1, 'BSIT', 'info tech', 3, '2025-02-08 06:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `classschedule`
--

CREATE TABLE `classschedule` (
  `schedule_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `meeting_details` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classschedule`
--

INSERT INTO `classschedule` (`schedule_id`, `course_id`, `date`, `time`, `instructor_id`, `meeting_details`) VALUES
(12, 1, '2025-02-10', '13:00:15', 3, 'gmeet\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `coursecontent`
--

CREATE TABLE `coursecontent` (
  `content_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `topic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coursecontent`
--

INSERT INTO `coursecontent` (`content_id`, `course_id`, `instructor_id`, `file_name`, `file_path`, `file_type`, `uploaded_at`, `topic_id`) VALUES
(6, 2, NULL, 'OUTPUT_CINDY_ABSTRACTION.png', 'uploads/1738461127_OUTPUT_CINDY_ABSTRACTION.png', 'png', '2025-02-02 01:52:07', 1),
(7, 2, NULL, 'Screenshot (137).png', 'uploads/1738489630_Screenshot (137).png', 'png', '2025-02-02 09:47:10', 1),
(10, 1, NULL, 'OUTPUT SS_DACUBA_CINDY.png', 'uploads/1738831249_OUTPUT SS_DACUBA_CINDY.png', 'png', '2025-02-06 08:40:49', 4);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `school_year` varchar(20) NOT NULL,
  `course_code` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `description`, `instructor_id`, `school_year`, `course_code`) VALUES
(1, 'Introduction to Computing', 'intro to computers', 3, '2024-2025', 'ITC123'),
(2, 'Programing 1', 'Process of creating a set of instructions that tell a computer how to perform a task or solve a problem.', 3, '2024-2025', 'ProG1'),
(3, 'Programming 2', 'dfdgf', 3, '2024-2025', 'ProG2'),
(4, 'Application Development', 'App Dev', 3, '2024-2025', 'ApdeV'),
(5, 'Platform Technology', 'You will learn the key concepts, architectures, and tools that enable the development of scalable and flexible platforms.', NULL, '', ''),
(6, 'Platform Technology', 'You will learn the key concepts, architectures, and tools that enable the development of scalable and flexible platforms. ', 4, '2024-2025', 'PT123');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `course_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(6, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `event_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizquestions`
--

CREATE TABLE `quizquestions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('MCQ','true/false','short_answer') NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizquestions`
--

INSERT INTO `quizquestions` (`question_id`, `quiz_id`, `question_text`, `question_type`, `options`, `answer`, `created_at`) VALUES
(15, 7, 'Who developed the theory of relativity?', 'short_answer', '[\"\",\"\",\"\",\"\"]', 'Albert Einstein', '2025-02-06 12:44:08'),
(16, 8, 'The Sun is a star.', '', '[\"TRUE\",\"FALSE\"]', 'TRUE', '2025-02-07 05:43:27'),
(17, 8, 'What is the capital of Japan?', 'short_answer', '[\"\",\"\",\"\",\"\"]', 'Tokyo', '2025-02-07 05:43:27'),
(18, 9, 'What is the capital of Japan?', 'MCQ', '[\"Paris\",\"Madrid\",\"Tokyo\",\"France\"]', 'Tokyo', '2025-02-07 06:27:32'),
(19, 10, 'Java is easy.', '', '[\"TRUE\",\"FALSE\"]', 'FALSE', '2025-02-10 08:52:03'),
(20, 10, 'Who is the father of java?', 'short_answer', '[\"\",\"\",\"\",\"\"]', 'James Gosling', '2025-02-10 08:52:03'),
(21, 11, 'What is the capital of France?', 'MCQ', '[\"Berlin\",\"Madrid\",\"Paris\",\"Lisbon\"]', 'Paris', '2025-02-11 01:20:09'),
(22, 11, 'The sun is a planet.', '', '[\"True\",\"False\"]', 'False', '2025-02-11 01:20:09'),
(23, 11, 'Who wrote the play Romeo and Juliet?', 'short_answer', '[\"\",\"\",\"\",\"\"]', 'William Shakespeare', '2025-02-11 01:20:09'),
(24, 12, 'What is the capital of France?', 'MCQ', '[\"Paris\",\"Manila\",\"Lisbon\",\"Madrid\"]', 'Paris', '2025-02-11 02:39:15'),
(25, 12, 'The sun is a planet. (True or False)', '', '[\"True\",\"False\"]', 'False', '2025-02-11 02:39:15'),
(26, 12, 'The process by which plants make their own food is called __________.', 'short_answer', '[\"\",\"\",\"\",\"\"]', 'Photosynthesis', '2025-02-11 02:39:15'),
(27, 13, 'The sun is a planet. (yes or no)', 'short_answer', '[\"\",\"\",\"\",\"\"]', 'no', '2025-02-11 03:07:54'),
(28, 14, 'What is the capital of the Philippines?', 'MCQ', '[\"Paris\",\"Berlin\",\"Manila\",\"Tokyo\"]', 'Manila', '2025-02-11 03:16:06');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `quiz_title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_points` int(11) DEFAULT NULL,
  `passing_score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `course_id`, `quiz_title`, `created_at`, `total_points`, `passing_score`) VALUES
(7, 1, 'Quiz 1', '2025-02-06 12:44:08', NULL, NULL),
(8, 1, 'Quiz 2', '2025-02-07 05:43:27', NULL, NULL),
(9, 1, 'Quiz 3', '2025-02-07 06:27:32', NULL, NULL),
(10, 1, 'Quiz 4', '2025-02-10 08:52:03', NULL, NULL),
(11, 1, 'Quiz 5', '2025-02-11 01:20:09', NULL, NULL),
(12, 3, 'Quiz 6', '2025-02-11 02:39:15', NULL, NULL),
(13, 2, 'Quiz 1', '2025-02-11 03:07:54', NULL, NULL),
(14, 3, 'Quiz 1', '2025-02-11 03:16:06', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `attempt_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('in_progress','completed','failed') NOT NULL DEFAULT 'completed',
  `attempt_number` int(11) NOT NULL DEFAULT 1,
  `attempt_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`attempt_id`, `student_id`, `quiz_id`, `score`, `total_questions`, `attempt_time`, `status`, `attempt_number`, `attempt_date`) VALUES
(1, 1, 11, 2, 3, '2025-02-11 01:22:27', 'completed', 1, '2025-02-11 02:51:14'),
(2, 1, 11, 2, 3, '2025-02-11 01:27:26', 'completed', 1, '2025-02-11 02:51:14'),
(3, 1, 11, 2, 3, '2025-02-11 01:28:46', 'completed', 1, '2025-02-11 02:51:14'),
(4, 1, 11, 2, 3, '2025-02-11 01:32:07', 'completed', 1, '2025-02-11 02:51:14'),
(5, 1, 11, 2, 3, '2025-02-11 01:32:37', 'completed', 1, '2025-02-11 02:51:14'),
(6, 1, 11, 2, 3, '2025-02-11 01:41:15', 'completed', 1, '2025-02-11 02:51:14'),
(7, 1, 11, 2, 3, '2025-02-11 01:41:45', 'completed', 1, '2025-02-11 02:51:14'),
(8, 1, 11, 2, 3, '2025-02-11 01:41:49', 'completed', 1, '2025-02-11 02:51:14'),
(9, 1, 11, 2, 3, '2025-02-11 01:42:13', 'completed', 1, '2025-02-11 02:51:14'),
(10, 1, 11, 2, 3, '2025-02-11 01:42:30', 'completed', 1, '2025-02-11 02:51:14'),
(11, 1, 8, 0, 0, '2025-02-11 02:04:40', 'completed', 1, '2025-02-11 02:51:14'),
(12, 1, 11, 1, 3, '2025-02-11 02:05:31', 'completed', 1, '2025-02-11 02:51:14'),
(13, 1, 7, 0, 1, '2025-02-11 02:33:10', 'completed', 1, '2025-02-11 02:51:14'),
(14, 1, 12, 0, 3, '2025-02-11 02:40:29', 'completed', 1, '2025-02-11 02:51:14'),
(15, 1, 10, 1, 0, '2025-02-11 02:52:50', 'completed', 1, '2025-02-11 02:52:50'),
(16, 1, 9, 0, 0, '2025-02-11 02:53:57', 'completed', 1, '2025-02-11 02:53:57'),
(17, 1, 13, 1, 0, '2025-02-11 03:08:15', 'completed', 1, '2025-02-11 03:08:15'),
(18, 1, 14, 1, 0, '2025-02-11 03:16:46', 'completed', 1, '2025-02-11 03:16:46');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_scores`
--

CREATE TABLE `quiz_scores` (
  `score_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `date_taken` timestamp NOT NULL DEFAULT current_timestamp(),
  `answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_scores`
--

INSERT INTO `quiz_scores` (`score_id`, `user_id`, `quiz_id`, `score`, `date_taken`, `answer`) VALUES
(21, 1, 7, 1, '2025-02-06 14:06:52', '{\"question_id\":15,\"answer\":\"Albert Einstein\"}'),
(39, 1, 7, 1, '2025-02-07 04:24:37', NULL),
(40, 1, 8, 1, '2025-02-07 06:21:14', NULL),
(41, 1, 9, 0, '2025-02-07 06:28:04', NULL),
(42, 1, 9, 0, '2025-02-07 07:07:14', NULL),
(43, 1, 9, 0, '2025-02-07 07:09:04', NULL),
(44, 1, 9, 0, '2025-02-07 07:13:16', NULL),
(45, 1, 9, 0, '2025-02-07 07:13:27', NULL),
(46, 1, 10, 0, '2025-02-10 09:31:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `studentprogress`
--

CREATE TABLE `studentprogress` (
  `progress_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `status` enum('not_started','in_progress','completed','failed','pending_review','past_due') NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentprogress`
--

INSERT INTO `studentprogress` (`progress_id`, `student_id`, `course_id`, `score`, `status`, `date_updated`) VALUES
(7, 1, 1, 0.00, 'completed', '2025-02-11 02:53:57'),
(8, 1, 1, 0.00, 'completed', '2025-02-11 02:53:57'),
(9, 1, 1, 0.00, 'completed', '2025-02-11 02:53:57'),
(10, 1, 3, 1.00, 'completed', '2025-02-11 03:16:46');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `topic_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `topic_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`topic_id`, `course_id`, `topic_name`) VALUES
(1, 2, 'OOP LECTURES'),
(2, 2, 'MACHINE PROBLEM'),
(4, 1, 'EXAMPLE');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('instructor','student') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `email`, `password`, `role`) VALUES
(1, 'Cindy Dacuba', 'cindy', 'cindy@gmail.com', '12345', 'student'),
(2, 'Marian Costales', 'marian', 'marian@gmail.com', '12345', 'student'),
(3, 'Marilag', 'marilag', 'marilag@gmail.com', 'marilag123', 'instructor'),
(4, 'Zab Agsunod', 'jerry', 'jerry@gmail.com', 'zab123', 'instructor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `classschedule`
--
ALTER TABLE `classschedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `fk_instructor` (`instructor_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `coursecontent`
--
ALTER TABLE `coursecontent`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `fk_course_id` (`course_id`),
  ADD KEY `fk_instructor_id` (`instructor_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quizquestions`
--
ALTER TABLE `quizquestions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `user_id` (`student_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_scores`
--
ALTER TABLE `quiz_scores`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `studentprogress`
--
ALTER TABLE `studentprogress`
  ADD PRIMARY KEY (`progress_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`topic_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `classschedule`
--
ALTER TABLE `classschedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `coursecontent`
--
ALTER TABLE `coursecontent`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizquestions`
--
ALTER TABLE `quizquestions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `quiz_scores`
--
ALTER TABLE `quiz_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `studentprogress`
--
ALTER TABLE `studentprogress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `classschedule`
--
ALTER TABLE `classschedule`
  ADD CONSTRAINT `classschedule_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `coursecontent`
--
ALTER TABLE `coursecontent`
  ADD CONSTRAINT `coursecontent_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`topic_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_instructor_id` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `quizquestions`
--
ALTER TABLE `quizquestions`
  ADD CONSTRAINT `quizquestions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_scores`
--
ALTER TABLE `quiz_scores`
  ADD CONSTRAINT `quiz_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_scores_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `studentprogress`
--
ALTER TABLE `studentprogress`
  ADD CONSTRAINT `studentprogress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `studentprogress_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
