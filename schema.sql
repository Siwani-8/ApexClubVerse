-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2026 at 10:20 AM
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
-- Database: `apex_club_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `boa_members`
--

CREATE TABLE `boa_members` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `expertise` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `boa_members`
--

INSERT INTO `boa_members` (`id`, `club_id`, `name`, `title`, `expertise`, `photo`) VALUES
(18, 2, 'Samsher Rawal', 'Immediate Past President', 'Former President of the Sports & Leadership Club', 'samsher.jpeg'),
(19, 2, 'Kushal Basyal', 'Immediate Past Vice President', 'Former Vice President of the Sports & Leadership Club', 'kushal.jpeg'),
(20, 3, 'Peshal Bhatta', 'Immediate Past President', 'Former President of the Travel & Tourism Club', 'peshal.jpeg'),
(21, 3, 'Shivam Kumar Shah', 'Immediate Past Vice President', 'Former Vice President of the Travel & Tourism Club', 'shivam.jpeg'),
(22, 3, 'Prince Shah', 'Immediate Past Treasurer', 'Former Treasurer of the Travel & Tourism Club', 'prince.jpeg'),
(23, 3, 'Pariwesh Shrestha', 'Immediate Past General Secretary', 'Former General Secretary of the Travel & Tourism Club', 'pariwesh.jpeg'),
(24, 3, 'Abhishek Mandal', 'Immediate Past Operations Head', 'Former Operations Head of the Travel & Tourism Club', 'abhishek.jpeg'),
(25, 4, 'Eisha Acharya', 'Immediate Past President', 'Former President of the Media & Marketing Club', 'eisha.jpeg'),
(26, 4, 'Arya Suvan Regmi', 'Immediate Past Vice President', 'Former Vice President of the Media & Marketing Club', 'arya.jpeg'),
(27, 4, 'Sujan Bimali', 'Immediate Past Treasurer', 'Former Treasurer of the Media & Marketing Club', 'sujan.jpeg'),
(28, 4, 'Pratibha Pokhrel', 'Immediate Past General Secretary', 'Former General Secretary of the Media & Marketing Club', 'pratibha.jpeg'),
(29, 4, 'Milan Neupane', 'Immediate Past Operations Head', 'Former Operations Head of the Media & Marketing Club', 'milan.jpeg'),
(30, 5, 'Pranshu Raj Sharma', 'Immediate Past President', 'Former President of the IT Club', 'pranshu.jpeg'),
(31, 5, 'Nikhil Karna', 'Immediate Past Vice President', 'Former Vice President of the IT Club', 'nikhil.jpeg'),
(32, 5, 'Nischal Maharjan', 'Immediate Past Treasurer', 'Former Treasurer of the IT Club', 'nischal_maharjan.jpeg'),
(33, 5, 'Samikshya Baral', 'Immediate Past General Secretary', 'Former General Secretary of the IT Club', 'samikshya.jpeg'),
(34, 6, 'Samit Rajbhandari', 'Immediate Past President', 'Former President of HEAT Club', 'samit.jpeg'),
(35, 6, 'Avani Aryal', 'Immediate Past Vice President', 'Former Vice President of HEAT Club', 'avani.jpeg'),
(36, 6, 'Amrata Shrestha', 'Immediate Past Treasurer', 'Former Treasurer of HEAT Club', 'amrata.jpeg'),
(37, 6, 'Shristi Bhusal', 'Immediate Past General Secretary', 'Former General Secretary of HEAT Club', 'shristi.jpeg'),
(38, 6, 'Aman Singh', 'Immediate Past Operations Head', 'Former Operations Head of HEAT Club', 'aman.jpeg'),
(44, 1, 'Shubham Gautam', 'Immediate Past President', 'Former President of APAC', 'shubham.jpeg'),
(49, 1, 'Aaditya Gupta', 'Immediate Past Vice President', 'Former Vice President of APAC', 'aaditya.jpeg'),
(50, 1, 'Sameer Kandel', 'Immediate Past Treasurer', 'Former Treasurer of APAC', 'sameer.jpeg'),
(51, 1, 'Nischal Gurung', 'Immediate Past General Secretary', 'Former General Secretary of APAC', 'nischal.jpeg'),
(53, 1, 'Aayush B.K', 'Immediate Past Operations Head', 'Former Operations Head of APAC', 'aayush.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `bod_members`
--

CREATE TABLE `bod_members` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bod_members`
--

INSERT INTO `bod_members` (`id`, `club_id`, `name`, `position`, `bio`, `photo`) VALUES
(15, 1, 'Kajal Awasthi', 'President', 'Leading the Performing Arts Club with a passion for theatre and creative direction.', 'kajal.jpeg'),
(16, 1, 'Shradha Baniya', 'Vice President', 'Coordinates events and manages inter-club collaborations.', 'shradha.jpeg'),
(17, 1, 'Avishi Rajkarnikar', 'Treasurer', 'Manages the club budget and financial planning.', 'avishi.jpeg'),
(18, 1, 'Sneha Prasai', 'General Secretary', 'Handles communications and maintains club records.', 'sneha.jpeg'),
(19, 1, 'Kritika Khadka', 'Operations Head', 'Oversees day-to-day operations and event logistics.', 'kritika.jpeg'),
(20, 2, 'Sambhavya Shrestha', 'President', 'Champions student leadership through athletics and team building.', 'sambhavya.jpeg'),
(21, 2, 'Debesh Chaudhary', 'Vice President', 'Organizes sports tournaments and leadership workshops.', 'debesh.jpeg'),
(22, 2, 'Vision Shrestha', 'Treasurer', 'Manages budgets for sports events and equipment.', 'vision.jpeg'),
(23, 2, 'Ritesh Yadav', 'General Secretary', 'Maintains records and coordinates club communications.', 'ritesh.jpeg'),
(24, 2, 'Nirmal Kumar Chaudhary', 'Operations Head', 'Handles logistics for tournaments and leadership programs.', 'nirmal.jpeg'),
(25, 3, 'Rashmi Nakarmi', 'President', 'Passionate about exploring Nepal and beyond through tourism.', 'rashmi.jpeg'),
(26, 3, 'Jatin Bhandari', 'Vice President', 'Plans travel itineraries and cultural exchange programs.', 'jatin.jpeg'),
(27, 3, 'Samar Shrestha', 'Treasurer', 'Handles travel budgets and financial planning.', 'samar.jpeg'),
(28, 3, 'Sanju Maharjan', 'General Secretary', 'Manages documentation and club communications.', 'sanju.jpeg'),
(29, 3, 'Prashna Bajracharya', 'Operations Head', 'Coordinates logistics for trips and tourism events.', 'prashna.jpeg'),
(30, 4, 'Suma Pokharel', 'President', 'Drives media campaigns and student marketing initiatives.', 'suma.jpeg'),
(31, 4, 'Aabash Ranjan', 'Vice President', 'Leads content creation and social media strategy.', 'aabash.jpeg'),
(32, 4, 'Ashmita Ghimire', 'Treasurer', 'Manages media production budgets and sponsorships.', 'ashmita.jpeg'),
(33, 4, 'Prasanna Bhattachan', 'General Secretary', 'Handles club records and internal communications.', 'prasanna.jpeg'),
(34, 4, 'Jenisha Timalsina', 'Operations Head', 'Oversees production schedules and campaign execution.', 'jenisha.jpeg'),
(35, 5, 'Sneha Ghimire', 'President', 'Tech enthusiast driving innovation and coding culture at Apex.', 'sneha_ghimire.jpeg'),
(36, 5, 'Richa Wagle', 'Vice President', 'Organizes hackathons, workshops, and IT awareness programs.', 'richa.jpeg'),
(37, 5, 'Shadhaiva Kunwar', 'Treasurer', 'Manages budgets for tech events and equipment procurement.', 'shadhaiva.jpeg'),
(38, 5, 'Bebika Poudel', 'General Secretary', 'Handles documentation and club communications.', 'bebika.jpeg'),
(39, 5, 'Prajina Bhattarai', 'Operations Head', 'Coordinates technical operations and event management.', 'prajina.jpeg'),
(40, 6, 'Micksha Damrewa Rai', 'President', 'Dedicated to spreading health literacy across the campus community.', 'micksha.jpeg'),
(41, 6, 'Garima Sharma', 'Vice President', 'Plans awareness drives and health camps.', 'garima.jpeg'),
(42, 6, 'Praptee Dangal', 'Treasurer', 'Manages health program budgets and resources.', 'praptee.jpeg'),
(43, 6, 'Abhishek Gupta', 'General Secretary', 'Handles records and coordinates health awareness communications.', 'abhishekg.jpeg'),
(44, 6, 'Shreeja Joshi', 'Operations Head', 'Oversees logistics for health camps and wellness programs.', 'shreeja.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `intake_open` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `description`, `logo`, `intake_open`) VALUES
(1, 'Apex Performing Arts Club', 'A vibrant community for students passionate about theatre, dance, music, and creative expression.', 'images/apac.png', 1),
(2, 'Apex Sports and Leadership Club', 'Developing future leaders through sports, teamwork, and physical excellence.', 'images/spoirts.png', 1),
(3, 'Apex Travel and Tourism Club', 'Explore the world through travel programs, cultural exchange, and tourism awareness.', 'images/travel.png', 1),
(4, 'Apex Media and Marketing Club', 'Honing skills in media production, digital marketing, journalism, and content creation.', 'images/media.png', 1),
(5, 'Apex IT Club', 'A hub for tech enthusiasts to collaborate on coding, cybersecurity, and innovation.', 'images/it.png', 1),
(6, 'Apex Health Education and Awareness Team (HEAT)', 'Promoting health literacy, wellness programs, and community health awareness.', 'images/heat.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `club_id`, `title`, `description`, `event_date`, `event_time`, `location`, `image`, `status`, `created_at`) VALUES
(1, 1, 'Apex Musical Evening', 'Join us for Apex Musical Evening: a night of live music, energy, and unforgettable memories.', '2026-07-15', '5:00 PM', 'Apex College Auditorium', 'images/events/musical1.jpeg', 'upcoming', '2026-06-30 07:28:16'),
(2, 1, 'Apex Day', 'Join us for an unforgettable celebration of creativity, where music, dance, drama, and more come alive on one stage!', '2026-01-09', '4:00 PM', 'Nepal Academy Hall', 'images/events/apexday1.jpeg', 'upcoming', '2026-06-30 07:28:16'),
(3, 2, 'Summer Cup', 'Annual football tournament between all departments of Apex College competing for the championship trophy.', '2026-07-10', '9:00 AM', 'LA Oval Ground', 'summer_cup.jpg', 'upcoming', '2026-06-30 07:28:16'),
(4, 2, 'Leadership Boot Camp', 'A two-day intensive leadership development program with activities, workshops, and guest speakers.', '2026-08-05', '8:00 AM', 'Conference Hall', 'leadership_bootcamp.jpg', 'upcoming', '2026-06-30 07:28:16'),
(5, 3, 'Adventurous Apex', 'Adventurous Apex offered Apexians a thrilling rafting experience that celebrated adventure, teamwork, and unforgettable memories.', '2026-07-20', '6:00 AM', 'Nagarkot Trail', 'adventurous_apex.jpg', 'upcoming', '2026-06-30 07:28:16'),
(6, 4, 'Apex Smile', 'Campus-wide photography competition with the theme \"Life at Apex\" open to all students.', '2026-07-12', '10:00 AM', 'Media Lab', 'apex_smile.jpg', 'upcoming', '2026-06-30 07:28:16'),
(7, 5, 'Apex Code & Combat', 'Join us for Apex Code and Combat, where brilliant coding minds and fearless gamers compete in an action-packed festival of innovation and esports!', '2026-08-02', '8:00 AM', 'IT Lab, Apex College', 'apex_code_and_combat.jpg', 'upcoming', '2026-06-30 07:28:16'),
(8, 5, 'Apex Gamers Connect', 'Apex Gamers Connect is Nepal\'s premier collegiate e-sports festival, uniting gamers for epic competition and unforgettable experiences.', '2026-07-18', '11:00 AM', 'Computer Lab 2', 'apex_gamers_connect.jpg', 'upcoming', '2026-06-30 07:28:16'),
(9, 6, 'Blood Donation Drive', 'Annual blood donation camp in collaboration with local hospitals to support the community.', '2026-07-08', '9:00 AM', 'Six Sigma', 'blood_donation_drive.jpg', 'upcoming', '2026-06-30 07:28:16'),
(10, 6, 'Apex EcoSprint ', 'A week-long series of talks, workshops, and activities promoting mental wellness among students.', '2026-07-22', '10:00 AM', 'Various Venues', 'apex_ecosprint.jpg', 'upcoming', '2026-06-30 07:28:16');

-- --------------------------------------------------------

--
-- Table structure for table `event_editions`
--

CREATE TABLE `event_editions` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `event_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_editions`
--

INSERT INTO `event_editions` (`id`, `event_id`, `title`, `event_date`, `location`, `description`) VALUES
(1, 2, 'Apex Day 2026', '2026-01-09', 'Nepal Academy Hall', NULL),
(2, 2, 'Apex Day 2023', '2023-07-23', 'Nepal Academy Hall', NULL),
(3, 1, 'Apex Musical Evening 2024', '2024-06-15', 'College Courtyard', 'A night of live music, energy and unforgettable memories.'),
(4, 2, 'Apex Day 2019', '2019-07-14', 'Nepal Academy Hall', 'Apex Day celebration 2019 with performances, competitions and memorable moments.');

-- --------------------------------------------------------

--
-- Table structure for table `event_gallery`
--

CREATE TABLE `event_gallery` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `edition_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_gallery`
--

INSERT INTO `event_gallery` (`id`, `event_id`, `image`, `caption`, `created_at`, `edition_id`) VALUES
(17, 2, 'images/events/apex2023_1.jpg', 'Opening Ceremony', '2026-08-04 18:07:54', 2),
(18, 2, 'images/events/apex2023_2.jpg', 'Chief Guest', '2026-08-04 18:07:54', 2),
(19, 2, 'images/events/apex2023_3.jpg', 'Mr. & Miss Apex', '2026-08-04 18:41:32', 2),
(20, 2, 'images/events/apex2023_4.jpg', 'Mr. & Miss Apex Runner Up', '2026-08-04 18:41:32', 2),
(21, 2, 'images/events/apex2023_5.jpg', 'The Elements', '2026-08-04 18:41:32', 2),
(22, 2, 'images/events/apex2023_6.jpg', 'Talent Round', '2026-08-04 18:41:32', 2),
(24, 2, 'images/events/apexday2019_1.jpg', 'Apex Day 2019', '2026-08-05 06:35:01', 4),
(25, 1, 'images/events/event_1_3_6a72e86980763.jpeg', 'Sanjay Aryal', '2026-08-05 07:38:17', 3),
(26, 1, 'images/events/event_1_3_6a72e8a8853d3.jpeg', 'Audience', '2026-08-05 07:39:20', 3),
(27, 1, 'images/events/event_1_3_6a72e8d2467e7.jpeg', 'Rockheads', '2026-08-05 07:40:02', 3),
(28, 1, 'images/events/event_1_3_6a72e8e751809.jpeg', 'Faculty Performance', '2026-08-05 07:40:23', 3),
(29, 1, 'images/events/event_1_3_6a72e8f956d40.jpeg', 'Student Performance', '2026-08-05 07:40:41', 3),
(30, 1, 'images/events/event_1_3_6a72e92ed93f6.jpeg', 'Organizers', '2026-08-05 07:41:34', 3),
(31, 2, 'images/events/event_2_1_6a72e9b53bb8e.jpeg', 'Opening Ceremony', '2026-08-05 07:43:49', 1),
(32, 2, 'images/events/event_2_1_6a72e9d6e7d52.jpeg', 'Panas Lighting', '2026-08-05 07:44:22', 1),
(33, 2, 'images/events/event_2_1_6a72e9fda18dc.jpeg', 'Contestant', '2026-08-05 07:45:01', 1),
(34, 2, 'images/events/event_2_1_6a72ea1207656.jpeg', 'Award Ceremony', '2026-08-05 07:45:22', 1),
(35, 2, 'images/events/event_2_1_6a72ea243743e.jpeg', 'Audience', '2026-08-05 07:45:40', 1),
(36, 2, 'images/events/event_2_1_6a72ea325c9c2.jpeg', 'Mr and Ms Apex', '2026-08-05 07:45:54', 1),
(37, 2, 'images/events/event_2_1_6a72ea4088dd9.jpeg', 'Sushant and Raga', '2026-08-05 07:46:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_options`
--

CREATE TABLE `poll_options` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `votes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_votes`
--

CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `option_id` int(11) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `student_email` varchar(150) NOT NULL,
  `faculty` varchar(50) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `selected_club` text NOT NULL,
  `interest` text NOT NULL,
  `reasons` text NOT NULL,
  `application_status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `interview_date` date DEFAULT NULL,
  `interview_start_time` time DEFAULT NULL,
  `interview_end_time` time DEFAULT NULL,
  `interview_venue` varchar(255) DEFAULT NULL,
  `is_rescheduled` tinyint(1) NOT NULL DEFAULT 0,
  `interview_status` enum('PENDING','SCHEDULED','RESCHEDULED','COMPLETED') NOT NULL DEFAULT 'PENDING',
  `club_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student',
  `club_id` int(11) DEFAULT NULL,
  `club_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `club_id`, `club_name`) VALUES
(1, 'Apex Performing Arts Admin', 'performing.admin@apexcollege.edu.np', 'admin123', 'admin', 1, 'Performing Arts'),
(2, 'Apex Sports & Leadership Admin', 'sports.admin@apexcollege.edu.np', 'admin123', 'admin', 2, 'Sports & Leadership'),
(3, 'Apex Travel & Tourism Admin', 'travel.admin@apexcollege.edu.np', 'admin123', 'admin', 3, 'Travel & Tourism'),
(4, 'Apex Media & Marketing Admin', 'media.admin@apexcollege.edu.np', 'admin123', 'admin', 4, 'Media & Marketing'),
(5, 'Apex IT Club Admin', 'it.admin@apexcollege.edu.np', 'admin123', 'admin', 5, 'IT Club'),
(6, 'Apex HEAT Admin', 'heat.admin@apexcollege.edu.np', 'admin123', 'admin', 6, 'HEAT');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boa_members`
--
ALTER TABLE `boa_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `bod_members`
--
ALTER TABLE `bod_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `event_editions`
--
ALTER TABLE `event_editions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_gallery`
--
ALTER TABLE `event_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll_id` (`poll_id`);

--
-- Indexes for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `one_vote_per_poll` (`poll_id`,`user_email`),
  ADD KEY `option_id` (`option_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_email` (`student_email`,`selected_club`) USING HASH,
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_club` (`club_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boa_members`
--
ALTER TABLE `boa_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `bod_members`
--
ALTER TABLE `bod_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `event_editions`
--
ALTER TABLE `event_editions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_gallery`
--
ALTER TABLE `event_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `poll_options`
--
ALTER TABLE `poll_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `boa_members`
--
ALTER TABLE `boa_members`
  ADD CONSTRAINT `boa_members_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `boa_members_ibfk_2` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`);

--
-- Constraints for table `bod_members`
--
ALTER TABLE `bod_members`
  ADD CONSTRAINT `bod_members_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `bod_members_ibfk_2` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `fk_events_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `fk_polls_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `polls_ibfk_2` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`);

--
-- Constraints for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD CONSTRAINT `fk_options_poll` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`),
  ADD CONSTRAINT `poll_options_ibfk_2` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`);

--
-- Constraints for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD CONSTRAINT `fk_votes_option` FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_votes_poll` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poll_votes_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`),
  ADD CONSTRAINT `poll_votes_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`id`);

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
