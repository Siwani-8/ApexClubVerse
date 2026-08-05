-- =====================================================================
-- ApexClubVerse - Complete Database Schema + Seed Data
-- Database: apex_club_db
-- Compatible with: MySQL 5.7+ / MariaDB 10.3+ (XAMPP & ProFreeHost)
--
-- Usage (fresh install):
--   1. CREATE DATABASE apex_club_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
--   2. Import this file via phpMyAdmin, or:
--        mysql -u root apex_club_db < database/schema.sql
--
-- On ProFreeHost: create the DB in the control panel, then import this file
-- into that database (do NOT run the CREATE DATABASE line if the panel
-- already created an empty database for you).
--
-- Tables (11):
--   clubs, users, bod_members, boa_members, events, event_editions,
--   event_gallery, polls, poll_options, poll_votes, registrations
--
-- Seed notes:
--   * Public content (clubs, boards, events, editions, galleries) is included.
--   * User accounts, registrations and poll votes are NOT seeded.
--   * See the commented admin INSERT at the bottom to create a club admin.
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- ---------------------------------------------------------------------
-- Drop in dependency order (children first)
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `poll_votes`;
DROP TABLE IF EXISTS `poll_options`;
DROP TABLE IF EXISTS `polls`;
DROP TABLE IF EXISTS `event_gallery`;
DROP TABLE IF EXISTS `event_editions`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `bod_members`;
DROP TABLE IF EXISTS `boa_members`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `clubs`;
DROP TABLE IF EXISTS `club_events`;

-- ---------------------------------------------------------------------
-- Table: clubs
-- page_url points at the real club detail route (club_detail.php?id=N)
-- ---------------------------------------------------------------------

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  `intake_open` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `clubs` (`id`, `name`, `description`, `page_url`, `intake_open`) VALUES
(1, 'Apex Performing Arts Club', 'A vibrant community for students passionate about theatre, dance, music, and creative expression.', 'club_detail.php?id=1', 1),
(2, 'Apex Sports and Leadership Club', 'Developing future leaders through sports, teamwork, and physical excellence.', 'club_detail.php?id=2', 1),
(3, 'Apex Travel and Tourism Club', 'Explore the world through travel programs, cultural exchange, and tourism awareness.', 'club_detail.php?id=3', 1),
(4, 'Apex Media and Marketing Club', 'Honing skills in media production, digital marketing, journalism, and content creation.', 'club_detail.php?id=4', 1),
(5, 'Apex IT Club', 'A hub for tech enthusiasts to collaborate on coding, cybersecurity, and innovation.', 'club_detail.php?id=5', 1),
(6, 'Apex Health Education and Awareness Team (HEAT)', 'Promoting health literacy, wellness programs, and community health awareness.', 'club_detail.php?id=6', 1);

-- ---------------------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  `club_id` int(11) DEFAULT NULL,
  `club_name` varchar(100) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_club` (`club_id`),
  CONSTRAINT `fk_users_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table: bod_members
-- ---------------------------------------------------------------------

CREATE TABLE `bod_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'logo.png',
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  CONSTRAINT `bod_members_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(40, 6, 'Puja Adhikari', 'President', 'Dedicated to spreading health literacy across the campus community.', 'micksha.jpeg'),
(41, 6, 'Rajan Silwal', 'Vice President', 'Plans awareness drives and health camps.', 'garima.jpeg'),
(42, 6, 'Sunita Maharjan', 'Treasurer', 'Manages health program budgets and resources.', 'praptee.jpeg'),
(43, 6, 'Bibek Thapa', 'General Secretary', 'Handles records and coordinates health awareness communications.', 'abhishek.jpeg'),
(44, 6, 'Anita Rai', 'Operations Head', 'Oversees logistics for health camps and wellness programs.', 'shreeja.jpeg');

-- ---------------------------------------------------------------------
-- Table: boa_members
-- ---------------------------------------------------------------------

CREATE TABLE `boa_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `expertise` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'logo.png',
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  CONSTRAINT `boa_members_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `boa_members` (`id`, `club_id`, `name`, `title`, `expertise`, `photo`) VALUES
(46, 1, 'Shubham Gautam', 'Immediate Past President', 'Former President of APAC', 'shubham.jpeg'),
(47, 1, 'Aaditya Gupta', 'Immediate Past Vice President', 'Former Vice President of APAC', 'aaditya.jpeg'),
(48, 1, 'Sameer Kandel', 'Immediate Past Treasurer', 'Former Treasurer of APAC', 'sameer.jpeg'),
(49, 1, 'Nischal Gurung', 'Immediate Past General Secretary', 'Former General Secretary of APAC', 'nischal.jpeg'),
(50, 1, 'Aayush B.K', 'Immediate Past Operations Head', 'Former Operations Head of APAC', 'aayush.jpeg'),
(51, 2, 'Samsher Rawal', 'Immediate Past President', 'Former President of the Sports & Leadership Club', 'samsher.jpeg'),
(52, 2, 'Kushal Basyal', 'Immediate Past Vice President', 'Former Vice President of the Sports & Leadership Club', 'kushal.jpeg'),
(53, 3, 'Peshal Bhatta', 'Immediate Past President', 'Former President of the Travel & Tourism Club', 'peshal.jpeg'),
(54, 3, 'Shivam Kumar Shah', 'Immediate Past Vice President', 'Former Vice President of the Travel & Tourism Club', 'shivam.jpeg'),
(55, 3, 'Prince Shah', 'Immediate Past Treasurer', 'Former Treasurer of the Travel & Tourism Club', 'prince.jpeg'),
(56, 3, 'Pariwesh Shrestha', 'Immediate Past General Secretary', 'Former General Secretary of the Travel & Tourism Club', 'pariwesh.jpeg'),
(57, 3, 'Abhishek Mandal', 'Immediate Past Operations Head', 'Former Operations Head of the Travel & Tourism Club', 'abhishekg.jpeg'),
(58, 4, 'Eisha Acharya', 'Immediate Past President', 'Former President of the Media & Marketing Club', 'eisha.jpeg'),
(59, 4, 'Aryan Suvan Regmi', 'Immediate Past Vice President', 'Former Vice President of the Media & Marketing Club', 'arya.jpeg'),
(60, 4, 'Sujan Bimali', 'Immediate Past Treasurer', 'Former Treasurer of the Media & Marketing Club', 'sujan.jpeg'),
(61, 4, 'Pratibha Pokhrel', 'Immediate Past General Secretary', 'Former General Secretary of the Media & Marketing Club', 'pratibha.jpeg'),
(62, 4, 'Milan Neupane', 'Immediate Past Operations Head', 'Former Operations Head of the Media & Marketing Club', 'milan.jpeg'),
(63, 5, 'Pranshu Raj Sharma', 'Immediate Past President', 'Former President of the IT Club', 'pranshu.jpeg'),
(64, 5, 'Nikhil Karna', 'Immediate Past Vice President', 'Former Vice President of the IT Club', 'nikhil.jpeg'),
(65, 5, 'Nischal Maharjan', 'Immediate Past Treasurer', 'Former Treasurer of the IT Club', 'nischal_maharjan.jpeg'),
(66, 5, 'Samikshya Baral', 'Immediate Past General Secretary', 'Former General Secretary of the IT Club', 'samikshya.jpeg'),
(67, 6, 'Samit Rajbhandari', 'Immediate Past President', 'Former President of HEAT Club', 'samit.jpeg'),
(68, 6, 'Avani Aryal', 'Immediate Past Vice President', 'Former Vice President of HEAT Club', 'avani.jpeg'),
(69, 6, 'Amrata Shrestha', 'Immediate Past Treasurer', 'Former Treasurer of HEAT Club', 'amrata.jpeg'),
(70, 6, 'Shristi Bhusal', 'Immediate Past General Secretary', 'Former General Secretary of HEAT Club', 'shristi.jpeg'),
(71, 6, 'Aman Singh', 'Immediate Past Operations Head', 'Former Operations Head of HEAT Club', 'aman.jpeg');

-- ---------------------------------------------------------------------
-- Table: events
-- image values are project-relative paths that exist on disk
-- ---------------------------------------------------------------------

CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed') NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `events` (`id`, `club_id`, `title`, `description`, `event_date`, `event_time`, `location`, `image`, `status`, `created_at`) VALUES
(1, 1, 'Musical Night', 'One of the most fun and anticipated evenings at Apex — live performances from students and faculty.', '2025-12-15', '5:00 PM', 'Apex College Premises', 'images/musical.jpg', 'completed', '2026-07-04 14:40:07'),
(2, 1, 'Apex Day', 'The annual college celebration featuring performances, competitions, and Mr. & Miss Apex.', '2026-02-20', '9:00 AM', 'Apex College Premises', 'images/clubs-stage.jpeg', 'completed', '2026-07-04 14:40:07'),
(3, 2, 'Summer Cup', 'Annual football tournament between all departments of Apex College competing for the championship trophy.', '2026-07-10', '9:00 AM', 'LA Oval Ground', 'images/football.jpeg', 'upcoming', '2026-07-04 14:40:07'),
(4, 2, 'Apex Sports Week', 'Join us for Apex Sports Week, where teamwork, determination, and sportsmanship come together in an exciting celebration of athletic excellence!', '2026-08-05', '8:00 AM', 'Conference Hall', 'images/sports.jpg', 'upcoming', '2026-07-04 14:40:07'),
(5, 3, 'Adventurous Apex', 'Adventurous Apex offered Apexians a thrilling rafting experience that celebrated adventure, teamwork, and unforgettable memories.', '2026-07-20', '6:00 AM', 'Nagarkot Trail', 'images/hero-group.jpeg', 'upcoming', '2026-07-04 14:40:07'),
(6, 4, 'Apex Smile', 'Campus-wide photography competition with the theme "Life at Apex" open to all students.', '2026-07-12', '10:00 AM', 'Media Lab', 'images/pitch.jpg', 'upcoming', '2026-07-04 14:40:07'),
(7, 5, 'Apex Code & Combat', 'Join us for Apex Code and Combat, where brilliant coding minds and fearless gamers compete in an action-packed festival of innovation and esports!', '2026-08-02', '8:00 AM', 'IT Lab, Apex College', 'uploads/events/1785562131_hackathon.jpg', 'upcoming', '2026-07-04 14:40:07'),
(8, 5, 'Apex Gamers Connect', 'Apex Gamers Connect is Nepal''s premier collegiate e-sports festival, uniting gamers for epic competition and unforgettable experiences.', '2026-07-18', '11:00 AM', 'Computer Lab 2', 'images/events/gamersconnect1.jpg', 'upcoming', '2026-07-04 14:40:07'),
(9, 6, 'Blood Donation Drive', 'Annual blood donation camp in collaboration with local hospitals to support the community.', '2026-07-08', '9:00 AM', 'Six Sigma', 'uploads/events/1785691817_Giving Blood (2).jpeg', 'upcoming', '2026-07-04 14:40:07'),
(10, 4, 'Apex Pitch & Pop', 'Join Apex Pitch & Pop, where creative minds compete to craft innovative marketing campaigns, engaging media content, and winning business pitches!', '2026-08-10', '5:00 PM', 'Apex College Premises', 'images/pitch.jpg', 'upcoming', '2026-07-05 19:45:31'),
(11, 6, 'HEAT Wellness Session', 'Campus wellness and sound-bath session promoting mental and physical health awareness.', '2026-08-12', '3:00 PM', 'Six Sigma Hall', 'images/soundbath.jpeg', 'upcoming', '2026-08-02 22:10:12');

-- ---------------------------------------------------------------------
-- Table: event_editions  (yearly/seasonal runs of a recurring event)
-- ---------------------------------------------------------------------

CREATE TABLE `event_editions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `event_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `event_editions_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `event_editions` (`id`, `event_id`, `title`, `event_date`, `location`, `description`) VALUES
(1, 2, 'Apex Day 2026', '2026-02-20', 'Apex College Premises', 'Opening ceremony, panas lighting, performances, and Mr. & Miss Apex.'),
(2, 2, 'Apex Day 2023', '2023-02-18', 'Apex College Premises', 'Highlights from Apex Day 2023.'),
(3, 1, 'Musical Night 2025', '2025-12-15', 'Apex College Premises', 'Student bands, faculty performances, and special guests.'),
(4, 8, 'Gamers Connect 2025', '2025-07-18', 'Computer Lab 2', 'Collegiate esports tournament and community hangout.');

-- ---------------------------------------------------------------------
-- Table: event_gallery
-- ---------------------------------------------------------------------

CREATE TABLE `event_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `edition_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `edition_id` (`edition_id`),
  CONSTRAINT `event_gallery_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `event_gallery_ibfk_2` FOREIGN KEY (`edition_id`) REFERENCES `event_editions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `event_gallery` (`id`, `event_id`, `edition_id`, `image`, `caption`, `created_at`) VALUES
(4, 2, 1, 'images/events/apexday1.jpeg', 'Opening Ceremony', '2026-08-05 00:26:28'),
(5, 2, 1, 'images/events/apexday2.jpeg', 'Panas Lighting', '2026-08-05 00:26:28'),
(6, 2, 1, 'images/events/apexday3.jpeg', 'Panas Lighting', '2026-08-05 00:26:28'),
(7, 2, 1, 'images/events/apexday4.jpeg', '', '2026-08-05 00:26:28'),
(8, 2, 1, 'images/events/apexday5.jpeg', '', '2026-08-05 00:26:28'),
(9, 2, 1, 'images/events/apexday6.jpeg', 'Mr. & Miss Apex', '2026-08-05 00:26:28'),
(10, 2, 1, 'images/events/apexday7.jpeg', 'Mr. & Miss Apex Runner-Ups', '2026-08-05 00:26:28'),
(11, 2, 1, 'images/events/organizers.jpg', 'Organizing Committee', '2026-08-05 00:26:28'),
(12, 1, 3, 'images/events/musical1.jpeg', 'Sanjay Aryal', '2026-08-05 00:27:21'),
(13, 1, 3, 'images/events/musical2.jpeg', '', '2026-08-05 00:27:21'),
(14, 1, 3, 'images/events/musical3.jpeg', 'ROCKHEADS', '2026-08-05 00:27:21'),
(15, 1, 3, 'images/events/musical4.jpeg', 'Faculty performance', '2026-08-05 00:27:21'),
(16, 1, 3, 'images/events/musical5.jpeg', '', '2026-08-05 00:27:21'),
(17, 1, 3, 'images/events/event_1_3_6a72e86980763.jpeg', '', '2026-08-05 00:30:00'),
(18, 1, 3, 'images/events/event_1_3_6a72e8a8853d3.jpeg', '', '2026-08-05 00:30:00'),
(19, 1, 3, 'images/events/event_1_3_6a72e8d2467e7.jpeg', '', '2026-08-05 00:30:00'),
(20, 1, 3, 'images/events/event_1_3_6a72e8e751809.jpeg', '', '2026-08-05 00:30:00'),
(21, 1, 3, 'images/events/event_1_3_6a72e8f956d40.jpeg', '', '2026-08-05 00:30:00'),
(22, 1, 3, 'images/events/event_1_3_6a72e92ed93f6.jpeg', '', '2026-08-05 00:30:00'),
(23, 2, 1, 'images/events/event_2_1_6a72e9b53bb8e.jpeg', '', '2026-08-05 00:31:00'),
(24, 2, 1, 'images/events/event_2_1_6a72e9d6e7d52.jpeg', '', '2026-08-05 00:31:00'),
(25, 2, 1, 'images/events/event_2_1_6a72e9fda18dc.jpeg', '', '2026-08-05 00:31:00'),
(26, 2, 1, 'images/events/event_2_1_6a72ea1207656.jpeg', '', '2026-08-05 00:31:00'),
(27, 2, 1, 'images/events/event_2_1_6a72ea243743e.jpeg', '', '2026-08-05 00:31:00'),
(28, 2, 1, 'images/events/event_2_1_6a72ea325c9c2.jpeg', '', '2026-08-05 00:31:00'),
(29, 2, 1, 'images/events/event_2_1_6a72ea4088dd9.jpeg', '', '2026-08-05 00:31:00'),
(30, 2, 2, 'images/events/apex2023_1.jpg', 'Apex Day 2023', '2026-08-05 00:32:00'),
(31, 2, 2, 'images/events/apexday2019_1.jpg', 'Apex Day archive', '2026-08-05 00:32:00'),
(32, 8, 4, 'images/events/gamersconnect1.jpg', 'Gamers Connect', '2026-08-05 00:33:00'),
(33, 8, 4, 'images/events/gamersconnect2.jpg', '', '2026-08-05 00:33:00'),
(34, 8, 4, 'images/events/gamersconnect4.jpg', '', '2026-08-05 00:33:00'),
(35, 8, 4, 'images/events/gamersconnect5.jpg', '', '2026-08-05 00:33:00'),
(36, 8, 4, 'images/events/gamersconnect13.jpg', '', '2026-08-05 00:33:00');

-- ---------------------------------------------------------------------
-- Table: polls
-- ---------------------------------------------------------------------

CREATE TABLE `polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table: poll_options
-- ---------------------------------------------------------------------

CREATE TABLE `poll_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`),
  CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table: poll_votes
-- ---------------------------------------------------------------------

CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `option_id` int(11) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `one_vote_per_poll` (`poll_id`, `user_email`(191)),
  KEY `option_id` (`option_id`),
  CONSTRAINT `poll_votes_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `poll_votes_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table: registrations
-- ---------------------------------------------------------------------

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_name` varchar(100) NOT NULL,
  `student_email` varchar(150) NOT NULL,
  `faculty` varchar(50) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `selected_club` text NOT NULL,
  `interest` text NOT NULL,
  `reasons` text NOT NULL,
  `application_status` enum('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `interview_date` date DEFAULT NULL,
  `interview_start_time` time DEFAULT NULL,
  `interview_end_time` time DEFAULT NULL,
  `interview_venue` varchar(255) DEFAULT NULL,
  `is_rescheduled` tinyint(1) NOT NULL DEFAULT 0,
  `interview_status` enum('PENDING','SCHEDULED','RESCHEDULED','COMPLETED') NOT NULL DEFAULT 'PENDING',
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_email_club` (`student_email`, `selected_club`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- Reset AUTO_INCREMENT counters past seeded IDs
ALTER TABLE `clubs` AUTO_INCREMENT = 7;
ALTER TABLE `bod_members` AUTO_INCREMENT = 45;
ALTER TABLE `boa_members` AUTO_INCREMENT = 72;
ALTER TABLE `events` AUTO_INCREMENT = 12;
ALTER TABLE `event_editions` AUTO_INCREMENT = 5;
ALTER TABLE `event_gallery` AUTO_INCREMENT = 37;

-- ---------------------------------------------------------------------
-- Club admin accounts (one per club)
-- Login password for ALL of these accounts: Admin@12345
-- Emails use @apexcollege.edu.np (required by login.php)
-- ---------------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `club_id`, `club_name`, `email_verified`, `verification_token`) VALUES
(1, 'APAC Admin', 'admin.performingarts@apexcollege.edu.np', '$2y$10$TXJujipOOVGjJPFFvRKl1uky3YuBixUqbmRTB7S5bJFLeJtc7sR5a', 'admin', 1, 'Apex Performing Arts Club', 1, NULL),
(2, 'ASLC Admin', 'admin.sports@apexcollege.edu.np', '$2y$10$TXJujipOOVGjJPFFvRKl1uky3YuBixUqbmRTB7S5bJFLeJtc7sR5a', 'admin', 2, 'Apex Sports and Leadership Club', 1, NULL),
(3, 'ATTC Admin', 'admin.travel@apexcollege.edu.np', '$2y$10$TXJujipOOVGjJPFFvRKl1uky3YuBixUqbmRTB7S5bJFLeJtc7sR5a', 'admin', 3, 'Apex Travel and Tourism Club', 1, NULL),
(4, 'AMMC Admin', 'admin.media@apexcollege.edu.np', '$2y$10$TXJujipOOVGjJPFFvRKl1uky3YuBixUqbmRTB7S5bJFLeJtc7sR5a', 'admin', 4, 'Apex Media and Marketing Club', 1, NULL),
(5, 'AITC Admin', 'admin.it@apexcollege.edu.np', '$2y$10$TXJujipOOVGjJPFFvRKl1uky3YuBixUqbmRTB7S5bJFLeJtc7sR5a', 'admin', 5, 'Apex IT Club', 1, NULL),
(6, 'HEAT Admin', 'admin.heat@apexcollege.edu.np', '$2y$10$TXJujipOOVGjJPFFvRKl1uky3YuBixUqbmRTB7S5bJFLeJtc7sR5a', 'admin', 6, 'Apex Health Education and Awareness Team (HEAT)', 1, NULL);

ALTER TABLE `users` AUTO_INCREMENT = 7;
