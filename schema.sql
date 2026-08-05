-- =====================================================================
-- ApexClubVerse - Database Schema
-- Database: apex_club_db
-- Generated: 2026-08-05
--
-- Usage:
--   1. CREATE DATABASE apex_club_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
--   2. mysql -u root apex_club_db < schema.sql   (or import via phpMyAdmin)
--
-- Notes:
--   * Seed data is included for public content (clubs, boards, events, galleries).
--   * User accounts, registrations and poll data are NOT included; they are
--     created at runtime. See the commented example at the bottom of this file
--     for creating an initial club-admin account.
-- =====================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- ---------------------------------------------------------------------
-- Table: clubs
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `clubs`;
CREATE TABLE `clubs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  `intake_open` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `clubs` (`id`, `name`, `description`, `page_url`, `intake_open`) VALUES
('1', 'Apex Performing Arts Club', 'A vibrant community for students passionate about theatre, dance, music, and creative expression.', 'club_performing_arts.php', '1'),
('2', 'Apex Sports and Leadership Club', 'Developing future leaders through sports, teamwork, and physical excellence.', 'club_sports.php', '1'),
('3', 'Apex Travel and Tourism Club', 'Explore the world through travel programs, cultural exchange, and tourism awareness.', 'club_travel.php', '1'),
('4', 'Apex Media and Marketing Club', 'Honing skills in media production, digital marketing, journalism, and content creation.', 'club_media.php', '1'),
('5', 'Apex IT Club', 'A hub for tech enthusiasts to collaborate on coding, cybersecurity, and innovation.', 'club_it.php', '1'),
('6', 'Apex Health Education and Awareness Team (HEAT)', 'Promoting health literacy, wellness programs, and community health awareness.', 'club_heat.php', '1');

-- ---------------------------------------------------------------------
-- Table: users
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `club_id` int(11) DEFAULT NULL,
  `club_name` varchar(100) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_club` (`club_id`),
  CONSTRAINT `fk_users_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table: bod_members
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `bod_members`;
CREATE TABLE `bod_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'default.jpg',
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  CONSTRAINT `bod_members_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bod_members` (`id`, `club_id`, `name`, `position`, `bio`, `photo`) VALUES
('15', '1', 'Kajal Awasthi', 'President', 'Leading the Performing Arts Club with a passion for theatre and creative direction.', 'kajal.jpeg'),
('16', '1', 'Shradha Baniya', 'Vice President', 'Coordinates events and manages inter-club collaborations.', 'shradha.jpeg'),
('17', '1', 'Avishi Rajkarnikar', 'Treasurer', 'Manages the club budget and financial planning.', 'avishi.jpeg'),
('18', '1', 'Sneha Prasai', 'General Secretary', 'Handles communications and maintains club records.', 'sneha_prasai.jpeg'),
('19', '1', 'Kritika Khadka', 'Operations Head', 'Oversees day-to-day operations and event logistics.', 'kritika.jpeg'),
('20', '2', 'Sambhavya Shrestha', 'President', 'Champions student leadership through athletics and team building.', 'sambhavya.jpeg'),
('21', '2', 'Debesh Chaudhary', 'Vice President', 'Organizes sports tournaments and leadership workshops.', 'debesh.jpeg'),
('22', '2', 'Vision Shrestha', 'Treasurer', 'Manages budgets for sports events and equipment.', 'vision.jpeg'),
('23', '2', 'Ritesh Yadav', 'General Secretary', 'Maintains records and coordinates club communications.', 'ritesh.jpeg'),
('24', '2', 'Nirmal Kumar Chaudhary', 'Operations Head', 'Handles logistics for tournaments and leadership programs.', 'nirmal.jpeg'),
('25', '3', 'Rashmi Nakarmi', 'President', 'Passionate about exploring Nepal and beyond through tourism.', 'rashmi.jpeg'),
('26', '3', 'Jatin Bhandari', 'Vice President', 'Plans travel itineraries and cultural exchange programs.', 'jatin.jpeg'),
('27', '3', 'Samar Shrestha', 'Treasurer', 'Handles travel budgets and financial planning.', 'samar.jpeg'),
('28', '3', 'Sanju Maharjan', 'General Secretary', 'Manages documentation and club communications.', 'sanju.jpeg'),
('29', '3', 'Prashna Bajracharya', 'Operations Head', 'Coordinates logistics for trips and tourism events.', 'prashna.jpeg'),
('30', '4', 'Suma Pokharel', 'President', 'Drives media campaigns and student marketing initiatives.', 'suma.jpeg'),
('31', '4', 'Aabash Ranjan', 'Vice President', 'Leads content creation and social media strategy.', 'aabash.jpeg'),
('32', '4', 'Ashmita Ghimire', 'Treasurer', 'Manages media production budgets and sponsorships.', 'ashmita.jpeg'),
('33', '4', 'Prasanna Bhattachan', 'General Secretary', 'Handles club records and internal communications.', 'prasanna.jpeg'),
('34', '4', 'Jenisha Timalsina', 'Operations Head', 'Oversees production schedules and campaign execution.', 'jenisha.jpeg'),
('35', '5', 'Sneha Ghimire', 'President', 'Tech enthusiast driving innovation and coding culture at Apex.', 'sneha_ghimire.jpeg'),
('36', '5', 'Richa Wagle', 'Vice President', 'Organizes hackathons, workshops, and IT awareness programs.', 'richa.jpeg'),
('37', '5', 'Shadhaiva Kunwar', 'Treasurer', 'Manages budgets for tech events and equipment procurement.', 'shadhaiva.jpeg'),
('38', '5', 'Bebika Poudel', 'General Secretary', 'Handles documentation and club communications.', 'bebika.jpeg'),
('39', '5', 'Prajina Bhattarai', 'Operations Head', 'Coordinates technical operations and event management.', 'prajina.jpeg'),
('40', '6', 'Puja Adhikari', 'President', 'Dedicated to spreading health literacy across the campus community.', 'micksha.jpeg'),
('41', '6', 'Rajan Silwal', 'Vice President', 'Plans awareness drives and health camps.', 'garima.jpeg'),
('42', '6', 'Sunita Maharjan', 'Treasurer', 'Manages health program budgets and resources.', 'praptee.jpeg'),
('43', '6', 'Bibek Thapa', 'General Secretary', 'Handles records and coordinates health awareness communications.', 'abhishek.jpeg'),
('44', '6', 'Anita Rai', 'Operations Head', 'Oversees logistics for health camps and wellness programs.', 'shreeja.jpeg');

-- ---------------------------------------------------------------------
-- Table: boa_members
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `boa_members`;
CREATE TABLE `boa_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `expertise` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'default.jpg',
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  CONSTRAINT `boa_members_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `boa_members` (`id`, `club_id`, `name`, `title`, `expertise`, `photo`) VALUES
('46', '1', 'Shubham Gautam', 'Immediate Past President', 'Former President of APAC', 'shubham.jpeg'),
('47', '1', 'Aaditya Gupta', 'Immediate Past Vice President', 'Former Vice President of APAC', 'aaditya.jpeg'),
('48', '1', 'Sameer Kandel', 'Immediate Past Treasurer', 'Former Treasurer of APAC', 'sameer.jpeg'),
('49', '1', 'Nischal Gurung', 'Immediate Past General Secretary', 'Former General Secretary of APAC', 'nischal.jpeg'),
('50', '1', 'Aayush B.K', 'Immediate Past Operations Head', 'Former Operations Head of APAC', 'aayush.jpeg'),
('51', '2', 'Samsher Rawal', 'Immediate Past President', 'Former President of the Sports & Leadership Club', 'samsher.jpeg'),
('52', '2', 'Kushal Basyal', 'Immediate Past Vice President', 'Former Vice President of the Sports & Leadership Club', 'kushal.jpeg'),
('53', '3', 'Peshal Bhatta', 'Immediate Past President', 'Former President of the Travel & Tourism Club', 'peshal.jpeg'),
('54', '3', 'Shivam Kumar Shah', 'Immediate Past Vice President', 'Former Vice President of the Travel & Tourism Club', 'shivam.jpeg'),
('55', '3', 'Prince Shah', 'Immediate Past Treasurer', 'Former Treasurer of the Travel & Tourism Club', 'prince.jpeg'),
('56', '3', 'Pariwesh Shrestha', 'Immediate Past General Secretary', 'Former General Secretary of the Travel & Tourism Club', 'pariwesh.jpeg'),
('57', '3', 'Abhishek Mandal', 'Immediate Past Operations Head', 'Former Operations Head of the Travel & Tourism Club', 'abhishek_mandal.jpeg'),
('58', '4', 'Eisha Acharya', 'Immediate Past President', 'Former President of the Media & Marketing Club', 'eisha.jpeg'),
('59', '4', 'Aryan Suvan Regmi', 'Immediate Past Vice President', 'Former Vice President of the Media & Marketing Club', 'aryan.jpeg'),
('60', '4', 'Sujan Bimali', 'Immediate Past Treasurer', 'Former Treasurer of the Media & Marketing Club', 'sujan.jpeg'),
('61', '4', 'Pratibha Pokhrel', 'Immediate Past General Secretary', 'Former General Secretary of the Media & Marketing Club', 'pratibha.jpeg'),
('62', '4', 'Milan Neupane', 'Immediate Past Operations Head', 'Former Operations Head of the Media & Marketing Club', 'milan.jpeg'),
('63', '5', 'Pranshu Raj Sharma', 'Immediate Past President', 'Former President of the IT Club', 'pranshu.jpeg'),
('64', '5', 'Nikhil Karna', 'Immediate Past Vice President', 'Former Vice President of the IT Club', 'nikhil.jpeg'),
('65', '5', 'Nischal Maharjan', 'Immediate Past Treasurer', 'Former Treasurer of the IT Club', 'nischal_maharjan.jpeg'),
('66', '5', 'Samikshya Baral', 'Immediate Past General Secretary', 'Former General Secretary of the IT Club', 'samikshya.jpeg'),
('67', '6', 'Samit Rajbhandari', 'Immediate Past President', 'Former President of HEAT Club', 'samit.jpeg'),
('68', '6', 'Avani Aryal', 'Immediate Past Vice President', 'Former Vice President of HEAT Club', 'avani.jpeg'),
('69', '6', 'Amrata Shrestha', 'Immediate Past Treasurer', 'Former Treasurer of HEAT Club', 'amrata.jpeg'),
('70', '6', 'Shristi Bhusal', 'Immediate Past General Secretary', 'Former General Secretary of HEAT Club', 'shristi.jpeg'),
('71', '6', 'Aman Singh', 'Immediate Past Operations Head', 'Former Operations Head of HEAT Club', 'aman.jpeg');

-- ---------------------------------------------------------------------
-- Table: events
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `events` (`id`, `club_id`, `title`, `description`, `event_date`, `event_time`, `location`, `image`, `status`, `created_at`) VALUES
('3', '2', 'Summer Cup', 'Annual football tournament between all departments of Apex College competing for the championship trophy.', '2026-07-10', '9:00 AM', 'LA Oval Ground', 'summer_cup.jpg', 'upcoming', '2026-07-04 14:40:07'),
('4', '2', 'Apex Sports Week', 'Join us for Apex Sports Week, where teamwork, determination, and sportsmanship come together in an exciting celebration of athletic excellence!', '2026-08-05', '8:00 AM', 'Conference Hall', 'leadership_bootcamp.jpg', 'upcoming', '2026-07-04 14:40:07'),
('5', '3', 'Adventurous Apex', 'Adventurous Apex offered Apexians a thrilling rafting experience that celebrated adventure, teamwork, and unforgettable memories.', '2026-07-20', '6:00 AM', 'Nagarkot Trail', 'adventurous_apex.jpg', 'upcoming', '2026-07-04 14:40:07'),
('6', '4', 'Apex Smile', 'Campus-wide photography competition with the theme \"Life at Apex\" open to all students.', '2026-07-12', '10:00 AM', 'Media Lab', 'apex_smile.jpg', 'upcoming', '2026-07-04 14:40:07'),
('7', '5', 'Apex Code & Combat', 'Join us for Apex Code and Combat, where brilliant coding minds and fearless gamers compete in an action-packed festival of innovation and esports!', '2026-08-02', '8:00 AM', 'IT Lab, Apex College', 'apex_code_and_combat.jpg', 'upcoming', '2026-07-04 14:40:07'),
('8', '5', 'Apex Gamers Connect', 'Apex Gamers Connect is Nepal\'s premier collegiate e-sports festival, uniting gamers for epic competition and unforgettable experiences.', '2026-07-18', '11:00 AM', 'Computer Lab 2', 'apex_gamers_connect.jpg', 'upcoming', '2026-07-04 14:40:07'),
('9', '6', 'Blood Donation Drive', 'Annual blood donation camp in collaboration with local hospitals to support the community.', '2026-07-08', '9:00 AM', 'Six Sigma', 'blood_donation_drive.jpg', 'upcoming', '2026-07-04 14:40:07'),
('11', '4', 'Apex Pitch & Pop', 'Join Apex Pitch & Pop, where creative minds compete to craft innovative marketing campaigns, engaging media content, and winning business pitches!', '2026-08-10', '5:00 PM', 'Apex College Premises', NULL, 'upcoming', '2026-07-05 19:45:31'),
('15', '3', 'Musical Event', 'This is one of the most fun and anticipated event in our college.', '2026-08-03', '11:30', 'Apex College', 'uploads/events/1785577570_musical.jpg', 'upcoming', '2026-08-01 15:31:10'),
('18', '1', 'HEllo', 'Event', '2026-08-03', '10:09', 'Apex College', 'uploads/events/1785687912_heat.png', 'upcoming', '2026-08-02 22:10:12');

-- ---------------------------------------------------------------------
-- Table: event_gallery
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `event_gallery`;
CREATE TABLE `event_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `event_gallery` (`id`, `event_id`, `image`, `caption`, `created_at`) VALUES
('4', '2', 'images/events/apexday1.jpeg', 'Opening Ceremony', '2026-08-05 00:26:28'),
('5', '2', 'images/events/apexday2.jpeg', 'Panas Lighting', '2026-08-05 00:26:28'),
('6', '2', 'images/events/apexday3.jpeg', 'Panas Lighting', '2026-08-05 00:26:28'),
('7', '2', 'images/events/apexday4.jpeg', '', '2026-08-05 00:26:28'),
('8', '2', 'images/events/apexday5.jpeg', '', '2026-08-05 00:26:28'),
('9', '2', 'images/events/apexday6.jpeg', 'Mr. & Miss Apex', '2026-08-05 00:26:28'),
('10', '2', 'images/events/apexday7.jpeg', 'Mr. & Miss Apex Runner-Ups', '2026-08-05 00:26:28'),
('11', '2', 'images/events/apexday8.jpeg', 'Sushant & Raga Performance', '2026-08-05 00:26:28'),
('12', '1', 'images/events/musical1.jpeg', 'Sanjay Aryal', '2026-08-05 00:27:21'),
('13', '1', 'images/events/musical2.jpeg', ' ', '2026-08-05 00:27:21'),
('14', '1', 'images/events/musical3.jpeg', 'ROCKHEADS', '2026-08-05 00:27:21'),
('15', '1', 'images/events/musical4.jpeg', 'Faculty performance ', '2026-08-05 00:27:21'),
('16', '1', 'images/events/musical5.jpeg', ' ', '2026-08-05 00:27:21');

-- ---------------------------------------------------------------------
-- Table: polls
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `polls`;
CREATE TABLE `polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table: poll_options
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `poll_options`;
CREATE TABLE `poll_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `votes` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`),
  CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table: poll_votes
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `poll_votes`;
CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `option_id` int(11) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `one_vote_per_poll` (`poll_id`,`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table: registrations
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `registrations`;
CREATE TABLE `registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_email` (`student_email`,`selected_club`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Table: club_events
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `club_events`;
CREATE TABLE `club_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  CONSTRAINT `club_events_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ---------------------------------------------------------------------
-- Optional: create an initial club-admin account.
-- Generate the password hash in PHP with:
--   php -r "echo password_hash('your-password', PASSWORD_DEFAULT);"
-- ---------------------------------------------------------------------
-- INSERT INTO `users` (name, email, password, role, club_id, club_name, email_verified)
-- VALUES ('IT Club Admin', 'it.admin@apexcollege.edu.np', '<bcrypt-hash>', 'admin', 5, 'IT Club', 1);
