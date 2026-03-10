-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 07, 2026 at 09:54 PM
-- Server version: 11.4.9-MariaDB-log
-- PHP Version: 8.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `b04slom6h9u2_04sl`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'about_us',
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`id`, `category`, `name`, `title`, `image`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'about_us', 'Overview', '04 SOCCER LEAGUE', 'img_693535fe4a2cd.jpg', 'Founded in 2025, 04 Soccer League brings together 10 vibrant clubs across Bikita East (Masvingo Province, Zimbabwe) Ward 24 and Ward 18, namely: Mamutse FC || Gedhe FC  ||  Chapongo FC  || Chilli Boyz FC  || Dukuhwe FC  ||  Six FC  || 5 Stars FC  || 4A Vikings FC  || 4B FC  ||  Chishakwe V3 FC\r\nOur mission is to empower local youth through skills development, physical activity, and community engagement.\r\n04 Soccer League aims to foster friendly relations amongst officials, clubs\' players and all other stakeholders of the League, as well as promote the development of youth football in Bikita East, Zimbabwe.\r\nWe also aim to develop young talents to professional football and fit to compete on the domestic and international stage.\r\nWith a focus on unity and growth, we aim to create a brighter future for our youth.', 0, 1, '2025-12-07 10:08:30', '2025-12-07 10:14:23'),
(2, 'about_us', 'Objectives', 'Our Objectives', 'img_693536db466df.png', 'We strive to register our league with the Zimbabwe Football Association (ZIFA) to provide structured opportunities for our players.\r\nOur goal is to register our players on FIFA Connect, opening doors to global recognition and opportunities.\r\nThrough sports, we aim to foster discipline, teamwork, and leadership among the youth.\r\nWe are dedicated to eradicating drug abuse and preventing early child marriages in our communities.\r\n We also aim to eliminate livestock theft and poaching of wildlife animals from Devuli Game Reserve.', 1, 1, '2025-12-07 10:12:11', '2025-12-07 10:12:11'),
(3, 'about_us', 'Principles', 'Our Principles', 'img_693536fa1fd00.png', 'Ambitious: Playing the local and world’s competitive and compelling football. Striving for excellence in everything we do. Thinking big; not setting any unnecessary limits or barriers.\r\nInspiring: Sharing everyone’s excitement and passion for the game. Creating experiences that excite people and invigorate lives.\r\nConnected: Being for everybody, everywhere. Being easy and open to work with. Listening as well as talking.\r\nFairness: Being objective, responsible and trustworthy. Safeguarding the integrity of the game. Thinking of tomorrow when making decisions for today.\r\nCommitment: We envision a thriving community where every young person can achieve their full potential.', 2, 1, '2025-12-07 10:12:42', '2025-12-07 10:12:42'),
(4, 'about_us', 'Fairness', 'Fairness Campaign', 'img_69353739d0557.png', 'We are fully committed to providing a positive playing environment that offers equal treatment and equal opportunities for all stakeholders involved in the 04 Soccer League and where everyone is treated with respect.\r\nAll 04 Soccer League activities must be performed in a professional manner, free of any form of hatred, favoritism, discrimination or harassment.\r\nAny inappropriate conduct toward others, including but not limited to that based on an individual’s sex, race, color, weight, size, religion, national origin, age, marital or domestic-partnership status, disability, sexual orientation, or gender identity or expression, will not be tolerated.', 3, 1, '2025-12-07 10:13:45', '2025-12-07 10:13:53'),
(5, 'about_us', 'Safeguarding', 'Safeguarding', 'img_693537e516e5c.png', '04 Soccer League places emphasis on the importance of everyone\'s right to enjoy football and participate in our activities in safe and inclusive environments.\r\nWith our limited resources, we still aim to adopt the highest safeguarding standards and expect everyone involved with 04 Soccer League to share this commitment, irrespective of individual roles and responsibilities.\r\nEveryone has the right to protection from abuse and mistreatment in any form from other football players, fans, match officials or any other stakeholders.\r\nDemonstrable top-level commitment, robust governance measures, and embedding a culture of continual learning and shared responsibility for safeguarding is essential.\r\nPrevention is the primary aim of our safeguarding arrangements.\r\nAll behaviors and actions must prioritize the rights, safety and wellbeing of 04 Soccer League stakeholders.', 4, 1, '2025-12-07 10:16:37', '2025-12-07 10:16:37'),
(6, 'about_us', 'Health and Safety', 'Health and Safety', 'img_69353818ac552.png', '04 Soccer League is committed to managing health and safety effectively to protect our players and other stakeholders that we interact with.\r\nWe recognize that we have a legal duty of care towards protecting the health and safety of its players and that managing health and safety is a business-critical function.\r\nWe also aim to raise awareness and empower players and stakeholders and enhance our ability to deliver effective care and protection for any at risk.', 5, 1, '2025-12-07 10:17:28', '2025-12-07 10:17:28'),
(7, 'league_rules', 'Player Rules', 'Player Rules', 'img_69354fe70e846.jpg', '*Number of Players*\r\nA match is played by two teams, each with a maximum of eleven players; one must be the goalkeeper.\r\nA match may not start or continue if either team has fewer than seven players.\r\n*Number of substitutions*\r\nThe number of substitutes, up to a maximum of five, which may be used in any match played in an official competition will be determined by FIFA.\r\nIf a team has not used the maximum number of substitutes any unused substitutes can be used in extra time.\r\n*Substitution procedure*\r\nThe names of the substitutes must be given to the referee before the start of the match.\r\nAny substitute not named by this time must not take part in the match.\r\n*Offence and Discipline*\r\nIf a team official, substitute, substituted or sent-off player or outside agent enters the field of play the referee must:\r\n only stop play if there is interference with play,\r\n have the person removed when play stops\r\n take appropriate disciplinary action\r\n Player outside the field of play\r\nIf a player who requires the referee\'s permission to re-enter the field of play re-enters without the referee\'s permission, the referee must:\r\n stop play - not immediately if the player does not interfere with play or a match official or if the advantage can be applied\r\n caution the player for entering the field of play without permission\r\nIf the referee stops play, it must be restarted:\r\n with a direct free kick from the position of the interference\r\n with an indirect free kick from the position of the ball when play was stopped if there was no interference\r\n*Cautionable Offences*\r\nA player and/or a substitute is cautioned if guilty of:\r\n delaying the restart of play,\r\n dissent by word or action,\r\n entering, re-entering or deliberately leaving the field of play without the referee’s permission,\r\n failing to respect the required distance when play is restarted with a dropped ball, corner kick, free kick or throw-in,\r\n persistent offences (no specific number or pattern of offences constitutes “persistent”),\r\n unsporting behaviour,\r\n entering the referee review area (RRA),\r\n excessively using the \'review\' (TV screen) signal\r\n Sending-Off Offences\r\nA player, substitute who commits any of the following offences is sent off:\r\n denying the opposing team a goal or an obvious goal-scoring opportunity by committing a deliberate handball offence (except a goalkeeper within their penalty area),\r\n denying the opposing team a goal or an obvious goal-scoring opportunity by committing a non-deliberate handball offence outside their own penalty area,\r\n denying a goal or an obvious goal-scoring opportunity to an opponent whose overall movement is towards the offender\'s goal by an offence punishable by a free kick (unless as outlined below),\r\n serious foul play,\r\n biting or spitting at someone,\r\n violent conduct,\r\n using offensive, insulting or abusive language and/or action(s),\r\n receiving a second caution/ yellow card in the same match', 0, 1, '2025-12-07 11:59:03', '2025-12-07 11:59:03'),
(8, 'league_rules', 'Unsporting Behaviors', 'Cautions for Unsporting Behaviors', 'img_69355017470c4.jpg', 'There are different circumstances when a player must be cautioned for unsporting behavior including if a player:\r\n attempts to deceive the referee e.g. by feigning/faking injury or pretending to have been fouled (simulation)\r\n changes places with the goalkeeper during play or without the referee’s permission\r\n commits in a reckless manner a direct free kick offence\r\n handles the ball to interfere with or stop a promising attack, except where the referee awards a penalty kick for a non-deliberate handball offence\r\n denies the opposing team a goal or an obvious goal-scoring opportunity and the referee awards a penalty kick for a non-deliberate handball offence\r\n commits any other offence which interferes with or stops a promising attack except where the referee awards a penalty kick for an offence which was an attempt to play the ball or for a challenge for the ball\r\n denies an opponent an obvious goal-scoring opportunity by committing an offence which was an attempt to play the ball or challenge for the ball and the referee awards a penalty kick\r\n handles the ball in an attempt to score a goal (whether or not the attempt is successful) or in an unsuccessful attempt to prevent a goal\r\n makes unauthorised marks on the field of play\r\n plays the ball when leaving the field of play after being given permission to leave\r\n shows a lack of respect for the game\r\n initiates a deliberate trick for the ball to be passed (including from a free kick or goal kick) to the goalkeeper with the head, chest, knee etc. to circumvent the Law, whether or not the goalkeeper touches the ball with the hands; the goalkeeper is cautioned if responsible for initiating the deliberate trick\r\n verbally distracts an opponent during play or at a restart\r\n no player is allowed to enter the pitch and play while intoxicated/drunk\r\n\r\nReferees must caution players who delay the restart of play by:\r\n appearing to take a throw-in but suddenly leaving it to a team-mate to take\r\n delaying leaving the field of play when being substituted\r\n excessively delaying a restart\r\n kicking or carrying the ball away, or provoking a confrontation by deliberately touching the ball after the referee has stopped play\r\n taking a free kick from the wrong position to force a retake\r\n*Team Officials*\r\nWhere an offence is committed by someone from the technical area (substitute, substituted player, sent-off player or team official) and the offender cannot be identified, the senior team coach present in the technical area will receive the sanction.', 0, 1, '2025-12-07 11:59:51', '2025-12-07 11:59:51'),
(9, 'league_rules', 'Match Official Rules', 'Match Official Rules', 'img_693550ec66bde.jpg', 'Football referees are responsible for enforcing the Laws of the game, ensuring fair play, and maintaining the safety of players, fans, and team officials, while also acting as the game\'s timekeeper.\r\n*KEY RESPONSIBILITIES*\r\n Enforcing the Laws of the Game: referees interpret and apply the laws of the game, ensuring that players adhere to the rules.\r\n Maintaining Fair Play: referees ensure fair play by penalizing fouls and misconduct and cautioning or expelling players as necessary.\r\n Ensuring Player Safety: referees have a duty to protect players from harm, including intervening in dangerous situations and ensuring players are not injured during the game.\r\n Game Timekeeping: referees are responsible for accurately keeping track of the game time and signaling the end of each half.\r\n Maintaining Order: referees maintain order on the field and in the stands, intervening in situations of misconduct or violence.\r\n Communication: referees communicate effectively with players, coaches, and other officials, explaining decisions and ensuring everyone understands the rules.\r\n\r\n*COMMON SCENARIOS AND ACTIONS*\r\n Fouls and Misconduct: referees must identify and penalize fouls and misconduct, including actions like kicking, tripping, pushing, or using excessive force.\r\n Red Cards and Suspension: a red card typically results in an automatic suspension of at least one match, but can extend to multiple matches depending on the severity of the offense\r\n Cautions and Expulsions: referees can issue yellow cards (cautions) for minor offenses and red cards (expulsions) for serious offenses, such as violent conduct or denying a clear goal-scoring opportunity.\r\n Free Kicks and Penalties: referees award free kicks for fouls outside the penalty area and penalties for fouls inside the penalty area.\r\n Substitutions: referees manage the substitution process, ensuring that players enter and leave the field of play according to the rules.\r\n Match Abandonment: In extreme circumstances, such as a threat of continued abuse or violence, or a significant injury, referees can consider abandoning the match.\r\n Restarting Play: referees are responsible for restarting play after stoppages, such as after a goal, a foul, or a substitution.\r\n Injured Players: referees must ensure that injured players are treated on the sidelines and can only re-enter the field of play after play has restarted.\r\n Players\' Equipment: Players must NOT wear anything that is dangerous to themselves or other players, including jewelry and boots/shoes that are designed for football.\r\n Delaying the Restart of Play: referees must caution players who delay the restart of play, such as by kicking or carrying the ball away after play has been stopped.\r\n Assaulting a Referee: any player, coach, team official, or spectator committing or attempting to commit a referee assault is automatically suspended for one (1) year from the time of the assault.\r\nIf serious injuries are inflicted, then the MINIMUM suspension shall be for five (5) years.', 2, 1, '2025-12-07 12:03:24', '2025-12-07 12:03:24'),
(10, 'training', 'Player Training', 'Player Training', 'img_69355c6d93f7d.jpg', 'coming soon...', 0, 1, '2025-12-07 12:52:29', '2025-12-07 14:26:32'),
(11, 'sponsorships', 'Sponsorships', 'League Sponsorships', 'img_693568be2bd09.jpg', 'coming soon...', 0, 1, '2025-12-07 13:45:02', '2025-12-07 14:26:21'),
(12, 'others', 'Awareness', 'Awareness Campaign for the Youth', 'img_693574d98d900.png', '04 Soccer League Football Awareness Campaign for the youth is a dynamic initiative aimed at promoting the positive impact of football among our young people.', 0, 1, '2025-12-07 14:33:52', '2025-12-07 14:36:41'),
(13, 'others', 'The Main Focus', 'The Main Focus', 'img_693574bd29660.png', 'The campaign focuses on raising awareness about the importance of sportsmanship, physical fitness, teamwork, and discipline through the beautiful game of football.', 1, 1, '2025-12-07 14:36:13', '2025-12-07 14:36:13'),
(14, 'others', 'The Engagement', 'The Engagement', 'img_693574f717be0.png', 'It engages youth through workshops, community matches, mentorship programs and educational sessions that highlight how football can empower personal growth and social development.', 2, 1, '2025-12-07 14:37:11', '2025-12-07 14:37:11'),
(15, 'others', 'The Dedication', 'The Dedication', 'img_6935751c91fda.png', 'By creating a supportive and inclusive environment, the campaign not only encourages active participation in sports but also educates young footballers about health, mental well-being, and life skills away from drugs and unlawful activities', 3, 1, '2025-12-07 14:37:48', '2025-12-07 14:37:48'),
(16, 'others', 'The Desire', 'The Desire', 'img_69357540552e0.png', 'It serves as a platform for discovering talent, building confidence, and fostering unity across diverse communities. The campaign aspires to inspire the next generation to lead healthy, active, and purpose-driven lives through football.', 4, 1, '2025-12-07 14:38:24', '2025-12-07 14:38:24'),
(17, 'others', 'The Nurturing', 'The Nurturing', 'img_6935755f31ab0.png', 'By fostering a deeper understanding and love for football, the campaign not only nurtures future athletes but also helps shape well-rounded individuals who value respect, perseverance, and community spirit.', 5, 1, '2025-12-07 14:38:55', '2025-12-07 14:38:55');

-- --------------------------------------------------------

--
-- Table structure for table `assists`
--

CREATE TABLE `assists` (
  `id` int(11) NOT NULL,
  `goal_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assists`
--

INSERT INTO `assists` (`id`, `goal_id`, `player_id`, `created_at`) VALUES
(16, 47, 145, '2025-11-29 09:36:37'),
(17, 51, 145, '2025-11-29 09:46:14'),
(18, 53, 213, '2025-11-29 09:48:39'),
(19, 54, 35, '2025-11-29 09:49:25'),
(20, 56, 189, '2025-11-29 09:54:27'),
(21, 57, 212, '2025-11-29 09:54:44'),
(22, 58, 190, '2025-11-29 09:54:58'),
(23, 60, 27, '2025-11-29 09:57:13'),
(24, 63, 110, '2025-11-29 09:58:28'),
(25, 62, 110, '2025-11-29 09:58:38'),
(26, 65, 170, '2025-11-29 09:59:19'),
(27, 66, 31, '2025-11-29 10:00:44'),
(28, 70, 126, '2025-11-29 10:05:40'),
(29, 72, 108, '2025-11-29 10:05:50'),
(30, 74, 29, '2025-11-29 10:06:21'),
(31, 78, 63, '2025-11-29 10:10:39'),
(32, 83, 102, '2025-11-29 10:12:57'),
(33, 84, 85, '2025-11-29 10:13:10'),
(34, 85, 145, '2025-11-29 10:14:37'),
(35, 86, 145, '2025-11-29 10:14:45'),
(36, 87, 147, '2025-11-29 10:14:59'),
(37, 89, 182, '2025-11-29 10:35:17'),
(38, 90, 182, '2025-11-29 10:35:34'),
(39, 92, 182, '2025-11-29 10:35:43'),
(40, 91, 171, '2025-11-29 10:35:55'),
(41, 97, 171, '2025-11-29 10:36:03'),
(42, 93, 168, '2025-11-29 10:36:24'),
(43, 94, 172, '2025-11-29 10:36:34'),
(44, 95, 185, '2025-11-29 10:36:47'),
(45, 96, 183, '2025-11-29 10:37:04'),
(46, 99, 132, '2025-11-29 10:40:21'),
(47, 106, 209, '2025-11-29 10:48:50'),
(48, 110, 61, '2025-11-29 10:53:35'),
(49, 111, 138, '2025-11-29 10:53:58'),
(50, 107, 145, '2025-11-29 10:54:12'),
(51, 112, 138, '2025-11-29 10:54:27'),
(52, 108, 135, '2025-11-29 10:54:40'),
(53, 113, 145, '2025-11-29 10:54:49'),
(54, 116, 66, '2025-11-29 11:01:48'),
(55, 117, 49, '2025-11-29 11:02:22'),
(56, 118, 30, '2025-11-29 11:02:58'),
(57, 119, 132, '2025-11-29 11:03:50'),
(58, 120, 84, '2025-11-29 11:04:43'),
(59, 122, 112, '2025-11-29 11:05:50'),
(60, 121, 111, '2025-11-29 11:06:03'),
(61, 124, 190, '2025-11-29 11:07:09'),
(62, 133, 169, '2025-11-29 11:14:27'),
(63, 134, 71, '2025-11-29 11:17:41'),
(64, 136, 68, '2025-11-29 11:17:50'),
(65, 137, 67, '2025-11-29 11:18:07'),
(66, 138, 134, '2025-11-29 11:19:38'),
(67, 139, 189, '2025-11-29 11:20:10'),
(68, 140, 121, '2025-11-29 11:21:00'),
(69, 141, 110, '2025-11-29 11:21:08'),
(70, 146, 152, '2025-11-29 11:26:45'),
(71, 143, 216, '2025-11-29 11:27:43'),
(72, 149, 88, '2025-11-29 11:29:37'),
(73, 152, 90, '2025-11-29 11:29:45'),
(74, 150, 91, '2025-11-29 11:29:57'),
(75, 151, 92, '2025-11-29 11:30:07'),
(76, 157, 170, '2025-11-29 11:32:45'),
(77, 158, 189, '2025-11-29 11:33:12'),
(78, 159, 154, '2025-11-29 11:33:57'),
(79, 160, 133, '2025-11-29 13:22:43'),
(80, 163, 195, '2025-11-29 13:25:48'),
(81, 168, 173, '2025-11-29 13:27:20'),
(82, 171, 33, '2025-11-29 13:27:55'),
(83, 172, 69, '2025-11-29 13:29:08'),
(84, 173, 74, '2025-11-29 13:29:17'),
(85, 175, 94, '2025-11-29 13:29:39'),
(86, 176, 154, '2025-11-29 13:31:07'),
(87, 178, 152, '2025-11-29 13:31:45'),
(88, 177, 158, '2025-11-29 13:31:56'),
(89, 179, 152, '2025-11-29 13:32:11'),
(90, 184, 55, '2025-11-29 13:40:43'),
(91, 183, 56, '2025-11-29 13:40:56'),
(92, 185, 30, '2025-11-29 13:41:20'),
(93, 188, 189, '2025-11-29 13:43:16'),
(94, 196, 119, '2025-11-29 13:48:02'),
(95, 202, 76, '2025-11-29 13:52:24'),
(96, 201, 76, '2025-11-29 13:52:31'),
(97, 204, 189, '2025-11-29 13:52:56'),
(98, 207, 179, '2025-11-29 13:53:58'),
(99, 208, 92, '2025-11-29 13:54:56'),
(100, 211, 29, '2025-11-29 13:56:51'),
(101, 209, 36, '2025-11-29 13:57:24'),
(102, 215, 138, '2025-11-29 14:01:31'),
(103, 216, 145, '2025-11-29 14:02:07'),
(104, 217, 138, '2025-11-29 14:02:22'),
(105, 219, 95, '2025-11-29 14:03:51'),
(106, 221, 52, '2025-11-29 14:04:11'),
(107, 222, 166, '2025-11-29 14:05:19'),
(108, 223, 165, '2025-11-29 14:05:40'),
(109, 224, 157, '2025-11-29 14:05:50'),
(110, 229, 205, '2025-11-29 14:08:32'),
(111, 230, 144, '2025-11-29 14:08:56'),
(112, 234, 79, '2025-11-29 14:16:15'),
(113, 235, 72, '2025-11-29 14:16:34'),
(114, 236, 72, '2025-11-29 14:16:44'),
(115, 237, 68, '2025-11-29 14:16:54'),
(116, 233, 68, '2025-11-29 14:17:02'),
(117, 240, 22, '2025-11-29 14:18:03'),
(118, 239, 24, '2025-11-29 14:18:13'),
(119, 241, 164, '2025-11-29 14:20:26'),
(120, 242, 164, '2025-11-29 14:20:34'),
(121, 243, 164, '2025-11-29 14:20:42'),
(122, 244, 163, '2025-11-29 14:21:45'),
(123, 248, 183, '2025-11-29 14:23:42'),
(124, 251, 36, '2025-11-29 14:27:10'),
(125, 256, 38, '2025-11-29 14:27:22'),
(126, 252, 42, '2025-11-29 14:27:36'),
(127, 253, 35, '2025-11-29 14:27:45'),
(128, 254, 35, '2025-11-29 14:27:56'),
(129, 257, 93, '2025-11-29 14:28:48'),
(130, 259, 143, '2025-11-29 14:29:50'),
(131, 261, 111, '2025-11-29 14:33:24'),
(132, 264, 170, '2025-11-29 14:34:52'),
(133, 265, 171, '2025-11-29 14:35:31'),
(134, 266, 171, '2025-11-29 14:35:49'),
(135, 269, 201, '2025-11-29 14:36:58'),
(136, 268, 190, '2025-11-29 14:37:15'),
(137, 267, 190, '2025-11-29 14:37:22'),
(138, 275, 76, '2025-11-29 14:41:34'),
(139, 278, 39, '2025-11-29 14:42:10'),
(140, 280, 111, '2025-11-29 14:43:19'),
(141, 283, 52, '2025-11-29 14:44:27'),
(142, 285, 99, '2025-11-29 14:46:26'),
(143, 284, 100, '2025-11-29 14:46:37'),
(144, 286, 85, '2025-11-29 14:46:50'),
(145, 287, 93, '2025-11-29 14:47:01'),
(146, 295, 189, '2025-11-29 14:52:09'),
(147, 296, 168, '2025-11-29 14:53:28'),
(148, 298, 168, '2025-11-29 14:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `card_type` enum('Yellow','Red') NOT NULL,
  `minute` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`id`, `match_id`, `player_id`, `card_type`, `minute`, `created_at`) VALUES
(14, 106, 47, 'Yellow', 65, '2025-11-29 09:31:48'),
(15, 107, 187, 'Yellow', 54, '2025-11-29 09:38:34'),
(16, 105, 154, 'Yellow', 45, '2025-11-29 09:43:01'),
(17, 105, 110, 'Red', 45, '2025-11-29 09:43:25'),
(18, 104, 150, 'Yellow', 12, '2025-11-29 09:46:38'),
(19, 104, 64, 'Yellow', 78, '2025-11-29 09:46:51'),
(20, 100, 49, 'Yellow', 78, '2025-11-29 09:52:00'),
(21, 99, 211, 'Yellow', 88, '2025-11-29 09:55:37'),
(22, 97, 186, 'Yellow', 15, '2025-11-29 09:59:39'),
(23, 97, 181, 'Yellow', 78, '2025-11-29 09:59:50'),
(24, 94, 115, 'Yellow', 58, '2025-11-29 10:06:41'),
(25, 94, 38, 'Yellow', 85, '2025-11-29 10:07:00'),
(26, 94, 111, 'Red', 86, '2025-11-29 10:07:30'),
(27, 87, 131, 'Yellow', 77, '2025-11-29 10:41:48'),
(28, 87, 146, 'Yellow', 45, '2025-11-29 10:42:00'),
(29, 87, 147, 'Yellow', 85, '2025-11-29 10:42:14'),
(30, 85, 45, 'Yellow', 15, '2025-11-29 10:44:36'),
(31, 85, 44, 'Yellow', 45, '2025-11-29 10:44:48'),
(32, 85, 46, 'Yellow', 78, '2025-11-29 10:45:01'),
(33, 82, 106, 'Yellow', 58, '2025-11-29 10:59:29'),
(34, 25, 151, 'Yellow', 26, '2025-11-29 11:07:37'),
(35, 26, 161, 'Red', 79, '2025-11-29 11:10:24'),
(36, 26, 40, 'Red', 79, '2025-11-29 11:10:44'),
(37, 28, 86, 'Yellow', 69, '2025-11-29 11:14:58'),
(38, 31, 115, 'Yellow', 47, '2025-11-29 11:21:31'),
(39, 31, 116, 'Yellow', 90, '2025-11-29 11:21:42'),
(40, 34, 31, 'Yellow', 58, '2025-11-29 11:31:13'),
(41, 34, 32, 'Yellow', 25, '2025-11-29 11:31:27'),
(42, 34, 136, 'Yellow', 98, '2025-11-29 11:31:44'),
(43, 34, 137, 'Yellow', 66, '2025-11-29 11:32:09'),
(44, 35, 171, 'Yellow', 85, '2025-11-29 11:33:24'),
(46, 37, 87, 'Yellow', 58, '2025-11-29 11:36:39'),
(47, 38, 133, 'Yellow', 89, '2025-11-29 13:23:39'),
(48, 38, 16, 'Yellow', 65, '2025-11-29 13:23:51'),
(49, 43, 18, 'Yellow', 58, '2025-11-29 13:33:21'),
(50, 43, 19, 'Yellow', 69, '2025-11-29 13:33:43'),
(51, 43, 175, 'Yellow', 33, '2025-11-29 13:33:56'),
(52, 43, 176, 'Yellow', 91, '2025-11-29 13:34:19'),
(53, 43, 177, 'Yellow', 54, '2025-11-29 13:34:33'),
(54, 44, 110, 'Yellow', 69, '2025-11-29 13:36:46'),
(55, 44, 118, 'Yellow', 78, '2025-11-29 13:37:16'),
(56, 44, 197, 'Yellow', 59, '2025-11-29 13:38:37'),
(57, 46, 69, 'Yellow', 89, '2025-11-29 13:42:19'),
(58, 48, 49, 'Yellow', 78, '2025-11-29 13:45:38'),
(59, 50, 34, 'Yellow', 87, '2025-11-29 13:48:20'),
(60, 50, 31, 'Yellow', 47, '2025-11-29 13:48:34'),
(61, 50, 120, 'Yellow', 88, '2025-11-29 13:48:48'),
(62, 51, 114, 'Yellow', 87, '2025-11-29 13:50:34'),
(63, 51, 20, 'Yellow', 89, '2025-11-29 13:50:48'),
(64, 51, 22, 'Yellow', 58, '2025-11-29 13:50:58'),
(65, 80, 207, 'Yellow', 78, '2025-11-29 13:59:48'),
(66, 80, 208, 'Yellow', 48, '2025-11-29 13:59:59'),
(67, 80, 184, 'Yellow', 48, '2025-11-29 14:00:12'),
(68, 79, 44, 'Yellow', 88, '2025-11-29 14:02:52'),
(69, 76, 125, 'Yellow', 48, '2025-11-29 14:07:06'),
(70, 76, 76, 'Yellow', 92, '2025-11-29 14:07:20'),
(71, 76, 81, 'Yellow', 25, '2025-11-29 14:07:33'),
(72, 76, 69, 'Yellow', 58, '2025-11-29 14:07:53'),
(73, 75, 206, 'Yellow', 58, '2025-11-29 14:09:43'),
(74, 74, 31, 'Yellow', 55, '2025-11-29 14:10:52'),
(75, 73, 109, 'Yellow', 14, '2025-11-29 14:12:06'),
(76, 73, 108, 'Yellow', 87, '2025-11-29 14:12:21'),
(77, 70, 162, 'Yellow', 11, '2025-11-29 14:22:29'),
(78, 70, 160, 'Yellow', 44, '2025-11-29 14:22:40'),
(79, 70, 151, 'Yellow', 87, '2025-11-29 14:23:01'),
(80, 67, 84, 'Yellow', 44, '2025-11-29 14:30:14'),
(81, 67, 103, 'Yellow', 77, '2025-11-29 14:31:57'),
(82, 65, 141, 'Yellow', 11, '2025-11-29 14:33:37'),
(83, 64, 171, 'Yellow', 77, '2025-11-29 14:35:09'),
(84, 63, 198, 'Yellow', 58, '2025-11-29 14:37:38'),
(85, 60, 151, 'Yellow', 33, '2025-11-29 14:43:46'),
(86, 59, 60, 'Yellow', 15, '2025-11-29 14:44:41'),
(87, 59, 49, 'Yellow', 66, '2025-11-29 14:44:56'),
(88, 58, 98, 'Yellow', 11, '2025-11-29 14:47:15'),
(89, 57, 31, 'Yellow', 45, '2025-11-29 14:52:22'),
(90, 55, 160, 'Yellow', 48, '2025-11-29 14:54:17'),
(91, 55, 57, 'Yellow', 91, '2025-11-29 14:54:58');

-- --------------------------------------------------------

--
-- Table structure for table `clean_sheets`
--

CREATE TABLE `clean_sheets` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `minute` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clean_sheets`
--

INSERT INTO `clean_sheets` (`id`, `match_id`, `player_id`, `minute`, `created_at`) VALUES
(25, 110, 90, 0, '2025-11-29 09:29:09'),
(26, 109, 140, 0, '2025-11-29 09:32:40'),
(27, 105, 114, 0, '2025-11-29 09:43:35'),
(28, 105, 159, 0, '2025-11-29 09:44:22'),
(29, 104, 140, 0, '2025-11-29 09:45:44'),
(30, 102, 90, 0, '2025-11-29 09:49:45'),
(31, 100, 159, 0, '2025-11-29 09:52:20'),
(32, 100, 56, 0, '2025-11-29 09:52:29'),
(33, 97, 174, 0, '2025-11-29 09:59:59'),
(34, 92, 90, 0, '2025-11-29 10:13:19'),
(35, 91, 148, 0, '2025-11-29 10:15:55'),
(36, 90, 56, 0, '2025-11-29 10:29:19'),
(37, 89, 114, 0, '2025-11-29 10:31:23'),
(38, 89, 203, 0, '2025-11-29 10:32:08'),
(39, 87, 148, 0, '2025-11-29 10:42:23'),
(40, 86, 90, 0, '2025-11-29 10:43:14'),
(41, 82, 110, 0, '2025-11-29 11:00:46'),
(42, 24, 114, 0, '2025-11-29 11:06:17'),
(43, 25, 191, 0, '2025-11-29 11:08:24'),
(44, 29, 70, 0, '2025-11-29 11:18:42'),
(45, 31, 114, 0, '2025-11-29 11:21:54'),
(46, 36, 153, 0, '2025-11-29 11:34:34'),
(47, 37, 90, 0, '2025-11-29 11:37:03'),
(48, 37, 117, 0, '2025-11-29 11:37:18'),
(49, 43, 217, 0, '2025-11-29 13:36:00'),
(50, 46, 140, 0, '2025-11-29 13:42:30'),
(51, 48, 56, 0, '2025-11-29 13:45:48'),
(52, 49, 174, 0, '2025-11-29 13:46:26'),
(53, 81, 159, 0, '2025-11-29 13:58:50'),
(54, 80, 174, 0, '2025-11-29 14:00:22'),
(55, 77, 159, 0, '2025-11-29 14:06:01'),
(56, 74, 31, 0, '2025-11-29 14:11:36'),
(57, 73, 56, 0, '2025-11-29 14:12:37'),
(58, 73, 114, 0, '2025-11-29 14:12:48'),
(59, 72, 174, 0, '2025-11-29 14:13:10'),
(60, 72, 104, 0, '2025-11-29 14:13:55'),
(61, 69, 174, 0, '2025-11-29 14:23:52'),
(62, 66, 56, 0, '2025-11-29 14:32:27'),
(63, 65, 124, 0, '2025-11-29 14:33:57'),
(64, 64, 174, 0, '2025-11-29 14:35:57'),
(65, 63, 202, 0, '2025-11-29 14:39:09'),
(66, 59, 56, 0, '2025-11-29 14:45:06'),
(67, 58, 90, 0, '2025-11-29 14:47:49'),
(68, 56, 174, 0, '2025-11-29 14:53:45'),
(69, 55, 159, 0, '2025-11-29 14:55:09'),
(70, 55, 56, 0, '2025-11-29 14:55:18');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stadium` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `logo`, `description`, `stadium`, `created_at`) VALUES
(2, 'Chapongo FC', 'chapongo_fc.png', '', 'Mutsinzwa', '2025-11-16 08:51:46'),
(3, 'Chilli Boyz FC', 'chilli_boyz_fc.png', '', 'Nehanda', '2025-11-16 08:52:37'),
(4, '4B FC', '4b_fc.png', 'Village 4B, Manyuchi, Bikita East', 'Village 4B', '2025-11-16 08:53:06'),
(5, 'Chishakwe V3 FC', 'chishakwe_v3_fc.png', 'home heome', 'Village 3', '2025-11-16 08:53:31'),
(6, 'Gedhe FC', 'gedhe_fc.png', '', 'Tafara', '2025-11-16 08:54:00'),
(7, 'Dukuhwe FC', 'dukuhwe_fc.png', '', 'Nehanda', '2025-11-16 08:54:18'),
(8, '5 Stars FC', '5_stars_fc.png', '', 'Village 5', '2025-11-16 08:54:38'),
(9, 'Mamutse FC', 'mamutse_fc.png', '', 'Mamutse', '2025-11-16 08:54:53'),
(10, '4A Vikings FC', '4a_vikings_fc.png', '', 'Village 4A', '2025-11-16 08:55:13'),
(11, 'Six FC', 'six_fc.png', '', 'Village 6', '2025-11-16 08:55:33'),
(19, 'Loser 1', 'club_1764580620_7041.png', '', 'Village 4B', '2025-12-01 09:13:01'),
(20, 'Loser 2', 'club_1764580666_7895.png', '', 'Village 4B', '2025-12-01 09:13:12'),
(21, 'Winner 1', 'club_1764580711_8281.jpg', '', 'Village 4B', '2025-12-01 09:13:55'),
(22, 'Winner 2', 'club_1764580718_9906.jpg', '', 'Village 4B', '2025-12-01 09:14:00'),
(23, '04 FC', 'club_1770379396_3125.png', '', 'Village 4B', '2026-02-06 11:09:20'),
(24, 'Shainster FC', 'club_1771183033_5162.png', '', 'Chibvumani', '2026-02-15 15:24:17');

-- --------------------------------------------------------

--
-- Table structure for table `competition_seasons`
--

CREATE TABLE `competition_seasons` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `competition_name` varchar(100) NOT NULL,
  `season` year(4) NOT NULL,
  `type` enum('league','cup','international') NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `competition_seasons`
--

INSERT INTO `competition_seasons` (`id`, `name`, `short_name`, `competition_name`, `season`, `type`, `country`, `logo`, `is_current`, `created_at`) VALUES
(1, '04 Soccer League', NULL, '04 Soccer League', '2026', 'league', 'Zimbabwe', '', 0, '2025-11-22 17:44:37'),
(2, 'Terra Firma Super Cup', NULL, 'Terra Firma  Super Cup', '2025', 'cup', 'Zimbabwe', '', 1, '2025-11-22 17:45:34'),
(3, '04 Soccer League', NULL, '04 Soccer League', '2025', 'league', 'Zimbabwe', '', 1, '2025-11-24 18:51:01'),
(4, 'Dhasindo Tournament', NULL, 'Dhasindo Knockout Tournament', '2025', 'cup', 'Zimbabwe', '', 1, '2025-12-08 12:15:48'),
(5, 'MOSA One Day Clash', NULL, 'MOSA One Day Clash', '2026', 'cup', 'Zimbabwe', '', 1, '2025-12-10 21:39:19');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `league_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `google_maps_embed` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--

CREATE TABLE `contributions` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `contributor_type` enum('member','player','management') DEFAULT 'member',
  `contributor_id` int(11) DEFAULT NULL,
  `type_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `description` text DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contributions`
--

INSERT INTO `contributions` (`id`, `member_id`, `contributor_type`, `contributor_id`, `type_id`, `amount`, `quantity`, `description`, `purpose`, `recorded_by`, `recorded_at`) VALUES
(10, 1, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-01-31 18:24:09'),
(11, 3, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-01-31 18:24:03'),
(12, 3, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-02-28 18:23:54'),
(13, 2, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-01-31 18:23:58'),
(14, 2, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-02-28 18:23:48'),
(15, 12, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-01-31 18:13:55'),
(16, 12, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-02-28 18:14:11'),
(22, 13, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-01-31 13:45:30'),
(35, 57, 'player', 170, 5, 3.00, 1, '4B FC', '04FC Registration', 2, '2026-02-06 08:36:33'),
(36, 64, 'management', 8, 5, 3.00, 1, 'Dukuhwe FC', '04FC Registration', 2, '2026-02-06 13:54:53'),
(37, 58, 'player', 168, 5, 3.00, 1, '4B FC', '04FC Registration', 2, '2026-02-06 08:36:46'),
(38, 4, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-01-31 07:26:26'),
(39, 4, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-02-28 07:31:51'),
(40, 8, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-01-31 11:34:39'),
(41, 7, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-01-31 11:34:59'),
(43, 56, 'player', 224, 5, 3.00, 1, 'Dukuhwe FC', '04FC Registration', 2, '2026-02-12 08:36:15'),
(44, 33, 'member', NULL, 1, 10.00, NULL, '', 'D2 Affiliation', 2, '2026-02-11 05:00:53'),
(45, 55, 'player', 121, 5, 3.00, 1, 'Chilli Boyz FC', '04FC Registration', 2, '2026-02-15 08:36:03'),
(46, 6, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-01-31 13:17:15'),
(47, 6, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-02-28 13:17:34'),
(48, 36, 'member', NULL, 1, 20.00, NULL, '', '04SL Affiliation', 2, '2026-02-15 13:27:50'),
(49, 61, 'management', 9, 5, 3.00, 1, 'Chilli Boyz FC', '04FC Registration', 2, '2026-02-15 08:37:58'),
(50, 53, 'player', 159, 5, 3.00, 1, '5 Stars FC', '04FC Registration', 2, '2026-02-16 08:35:44'),
(51, 54, 'player', 157, 5, 3.00, 1, '5 Stars FC', '04FC Registration', 2, '2026-02-16 08:35:51'),
(52, 51, 'player', 70, 5, 3.00, 1, 'Dukuhwe FC', '04FC Registration', 2, '2026-02-20 08:35:21'),
(53, 59, 'player', 231, 5, 3.00, 1, 'Dukuhwe FC', '04FC Registration', 2, '2026-02-20 08:37:05'),
(54, 50, 'player', 110, 5, 3.00, 1, 'Chilli Boyz FC', '04FC Registration', 2, '2026-02-20 08:35:00'),
(55, 52, 'player', 237, 5, 3.00, 1, '5 Stars FC', '04FC Registration', 2, '2026-02-16 08:35:36'),
(56, 60, 'player', 156, 5, 3.00, 1, '5 Stars FC', '04FC Registration', 2, '2026-02-16 08:37:17'),
(57, 62, 'player', 115, 5, 3.00, 1, 'Chilli Boyz FC', '04FC Registration', 2, '2026-02-23 12:32:50'),
(58, 63, 'player', 228, 5, 3.00, 1, 'Chilli Boyz FC', '04FC Registration', 2, '2026-02-23 12:37:27'),
(59, 1, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-02-28 14:10:59'),
(60, 65, 'management', 16, 5, 3.00, 1, '', '04SL Registration', 2, '2026-02-23 14:29:33'),
(61, 66, 'player', 171, 5, 2.00, 1, '', '04SL Registration', 2, '2026-02-23 16:38:33'),
(62, 4, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-03-31 15:35:39'),
(63, 67, 'player', 42, 5, 3.00, 1, 'Mamutse FC (paid to Thomas)', '04FC Registration', 2, '2026-02-27 15:40:17'),
(64, 68, 'member', NULL, 1, 20.00, NULL, '', '04SL Registration', 2, '2026-02-28 15:47:01'),
(65, 69, 'player', 36, 5, 3.00, 1, '', '04FC Registration', 2, '2026-02-28 15:49:32'),
(66, 70, 'player', 49, 5, 3.00, 1, '', '04FC Registration', 2, '2026-02-28 15:50:29'),
(67, 8, 'member', NULL, 1, 15.00, NULL, '', 'D2 Affiliation', 2, '2026-02-28 17:37:40'),
(68, 71, 'player', 222, 5, 3.00, 1, '', '04FC Registration', 2, '2026-03-05 15:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `contribution_expenses`
--

CREATE TABLE `contribution_expenses` (
  `id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `recorded_at` datetime DEFAULT current_timestamp(),
  `custom_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contribution_expenses`
--

INSERT INTO `contribution_expenses` (`id`, `amount`, `type`, `description`, `purpose`, `recorded_by`, `recorded_at`, `custom_date`) VALUES
(2, 15.00, 'Transport', 'Machona B - D2 Workshop, Masvingo', '', 2, '2026-02-06 10:07:01', '0000-00-00'),
(3, 100.00, 'D2 Affiliation Deposit', '', '', 2, '2026-02-05 06:56:29', '0000-00-00'),
(4, 6.00, 'Training Expense', 'Machona B - $3\r\nMaziveyi P - $3\r\nMutsetsi N - $3', '', 2, '2026-02-28 17:53:14', '0000-00-00'),
(5, 14.00, 'Transport', '04 FC Training Session - Nehanda', '', 2, '2026-02-28 17:53:50', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `contribution_purposes`
--

CREATE TABLE `contribution_purposes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contribution_purposes`
--

INSERT INTO `contribution_purposes` (`id`, `name`, `is_active`, `sort_order`) VALUES
(1, '04FC Registration ', 1, 1),
(2, 'Monthly Contributions', 1, 2),
(3, 'League Contributions', 1, 3),
(4, 'Jersey/Balls Donation', 1, 4),
(5, 'Referee Payment', 1, 5),
(6, 'Other', 1, 99),
(7, 'D2 Affiliation', 1, 5),
(8, '04SL Registration ', 1, 1),
(9, '04SL Affiliation', 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `contribution_types`
--

CREATE TABLE `contribution_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_monetary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contribution_types`
--

INSERT INTO `contribution_types` (`id`, `name`, `is_monetary`) VALUES
(1, 'Cash', 1),
(2, 'Balls', 0),
(3, 'Jerseys', 0),
(4, 'Whistles', 0),
(5, 'Other Items', 0),
(6, 'Cash – Player/Management', 1);

-- --------------------------------------------------------

--
-- Table structure for table `fixtures`
--

CREATE TABLE `fixtures` (
  `id` int(11) NOT NULL,
  `matchday` int(11) NOT NULL,
  `home_club_id` int(11) NOT NULL,
  `away_club_id` int(11) NOT NULL,
  `fixture_date` datetime NOT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `status` enum('Scheduled','Played','Postponed') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `competition_season_id` int(11) DEFAULT NULL,
  `referee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fixtures`
--

INSERT INTO `fixtures` (`id`, `matchday`, `home_club_id`, `away_club_id`, `fixture_date`, `venue`, `status`, `created_at`, `competition_season_id`, `referee`) VALUES
(29, 1, 7, 6, '2025-03-15 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 9),
(30, 1, 9, 11, '2025-03-15 14:30:00', 'Mamutse', '', '2025-11-28 17:31:32', 3, 6),
(31, 1, 2, 5, '2025-03-15 14:30:00', 'Mutsinzwa', '', '2025-11-28 17:31:32', 3, 3),
(32, 1, 4, 3, '2025-03-16 14:30:00', 'Village 4B', '', '2025-11-28 17:31:32', 3, 5),
(33, 1, 10, 8, '2025-03-16 14:30:00', 'Village 4A', '', '2025-11-28 17:31:32', 3, 4),
(34, 2, 8, 9, '2025-07-27 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 10),
(35, 2, 3, 6, '2025-06-07 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 10),
(36, 2, 5, 4, '2025-03-29 14:30:00', 'Village 3', '', '2025-11-28 17:31:32', 3, 8),
(37, 2, 11, 7, '2025-03-29 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 2),
(38, 2, 2, 10, '2025-03-30 14:30:00', 'Mutsinzwa', '', '2025-11-28 17:31:32', 3, 11),
(39, 3, 7, 3, '2025-04-05 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 5),
(40, 3, 11, 8, '2025-04-05 14:30:00', 'Village 6', '', '2025-11-28 17:31:32', 3, 4),
(41, 3, 6, 5, '2025-04-12 14:30:00', 'Tafara', '', '2025-11-28 17:31:32', 3, 9),
(42, 3, 9, 2, '2025-04-06 14:30:00', 'Mamutse', '', '2025-11-28 17:31:32', 3, 3),
(43, 3, 4, 10, '2025-04-12 14:30:00', 'v4B', '', '2025-11-28 17:31:32', 3, 8),
(44, 4, 8, 7, '2025-04-19 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 10),
(45, 4, 5, 3, '2025-04-19 14:30:00', 'Village 3', '', '2025-11-28 17:31:32', 3, 2),
(46, 4, 2, 11, '2025-04-19 14:30:00', 'Mutsinzwa', '', '2025-11-28 17:31:32', 3, 3),
(47, 4, 10, 6, '2025-04-20 14:30:00', 'Village 4A', '', '2025-11-28 17:31:32', 3, 5),
(48, 4, 4, 9, '2025-04-19 14:30:00', 'Village 4B', '', '2025-11-28 17:31:32', 3, 7),
(49, 5, 7, 5, '2025-05-03 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 4),
(50, 5, 8, 2, '2025-05-04 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 11),
(51, 5, 11, 4, '2025-05-03 14:30:00', 'Village 6', '', '2025-11-28 17:31:32', 3, 2),
(52, 5, 3, 10, '2025-05-04 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 9),
(53, 5, 6, 9, '2025-05-03 14:30:00', 'Tafara', '', '2025-11-28 17:31:32', 3, 8),
(54, 6, 2, 7, '2025-05-11 14:30:00', 'Mutsinzwa', '', '2025-11-28 17:31:32', 3, 10),
(55, 6, 10, 5, '2025-05-17 14:30:00', 'Village 4A', '', '2025-11-28 17:31:32', 3, 2),
(56, 6, 6, 11, '2025-05-11 14:30:00', 'Tafara', '', '2025-11-28 17:31:32', 3, 9),
(57, 6, 4, 8, '2025-05-17 14:30:00', 'Village 4B', '', '2025-11-28 17:31:32', 3, 3),
(58, 6, 9, 3, '2025-05-11 14:30:00', 'Mamutse', '', '2025-11-28 17:31:32', 3, 5),
(59, 7, 3, 11, '2025-05-25 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 2),
(60, 7, 7, 10, '2025-05-31 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 9),
(61, 7, 2, 4, '2025-05-31 14:30:00', 'Mutsinzwa', '', '2025-11-28 17:31:32', 3, 11),
(62, 7, 5, 9, '2025-05-24 14:30:00', 'Village 3', '', '2025-11-28 17:31:32', 3, 3),
(63, 7, 8, 6, '2025-05-25 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 7),
(64, 8, 4, 7, '2025-06-14 14:30:00', 'Village 4B', '', '2025-11-28 17:31:32', 3, 2),
(65, 8, 9, 10, '2025-06-14 14:30:00', 'Mamutse', '', '2025-11-28 17:31:32', 3, 7),
(66, 8, 11, 5, '2025-06-14 14:30:00', 'Village 6', '', '2025-11-28 17:31:32', 3, 3),
(67, 8, 6, 2, '2025-06-15 14:30:00', 'Tafara', '', '2025-11-28 17:31:32', 3, 9),
(68, 8, 3, 8, '2025-06-14 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 10),
(69, 9, 7, 9, '2025-06-21 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 4),
(70, 9, 5, 8, '2025-06-21 14:30:00', 'Village 3', '', '2025-11-28 17:31:32', 3, 9),
(71, 9, 10, 11, '2025-06-21 14:30:00', 'Village 4A', '', '2025-11-28 17:31:32', 3, 3),
(72, 9, 4, 6, '2025-06-21 14:30:00', 'Village 4B', '', '2025-11-28 17:31:32', 3, 10),
(73, 9, 2, 3, '2025-06-21 14:30:00', 'Mutsinzwa', '', '2025-11-28 17:31:32', 3, 8),
(74, 10, 6, 7, '2025-07-05 14:30:00', 'Tafara', '', '2025-11-28 17:31:32', 3, 8),
(75, 10, 5, 2, '2025-07-05 14:30:00', 'Village 3', '', '2025-11-28 17:31:32', 3, 3),
(76, 10, 11, 9, '2025-07-06 14:30:00', 'Village 6', '', '2025-11-28 17:31:32', 3, 10),
(77, 10, 3, 4, '2025-07-06 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 9),
(78, 10, 8, 10, '2025-07-06 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 7),
(79, 11, 7, 11, '2025-07-26 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 4),
(80, 11, 4, 5, '2025-07-12 14:30:00', 'Village 4B', '', '2025-11-28 17:31:32', 3, 11),
(81, 11, 6, 3, '2025-07-12 14:30:00', 'Tafara', '', '2025-11-28 17:31:32', 3, 2),
(82, 11, 9, 8, '2025-07-13 14:30:00', 'Mamutse', '', '2025-11-28 17:31:32', 3, 3),
(83, 11, 10, 2, '2025-07-26 14:30:00', 'Village 4A', '', '2025-11-28 17:31:32', 3, 5),
(84, 12, 3, 7, '2025-08-03 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 6),
(85, 12, 8, 11, '2025-08-02 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 4),
(86, 12, 5, 6, '2025-07-26 14:30:00', 'Village 3', '', '2025-11-28 17:31:32', 3, 9),
(87, 12, 2, 9, '2025-08-03 14:30:00', 'Mutsinzwa', '', '2025-11-28 17:31:32', 3, 10),
(88, 12, 10, 4, '2025-08-02 14:30:00', 'Village 4A', '', '2025-11-28 17:31:32', 3, 3),
(89, 13, 7, 8, '2025-08-17 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 9),
(90, 13, 3, 5, '2025-08-16 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 10),
(91, 13, 11, 2, '2025-08-16 14:30:00', 'Village 6', '', '2025-11-28 17:31:32', 3, 8),
(92, 13, 6, 10, '2025-08-17 14:30:00', 'Tafara', '', '2025-11-28 17:31:32', 3, 3),
(93, 13, 9, 4, '2025-08-16 14:30:00', 'Mamutse', '', '2025-11-28 17:31:32', 3, 6),
(94, 14, 5, 7, '2025-08-23 14:30:00', 'Village 3', '', '2025-11-28 17:31:32', 3, 10),
(95, 14, 2, 8, '2025-08-31 14:30:00', 'Mutsinzwa', '', '2025-11-28 17:31:32', 3, 7),
(96, 14, 4, 11, '2025-08-30 14:30:00', 'Village 4B', '', '2025-11-28 17:31:32', 3, 10),
(97, 14, 10, 3, '2025-08-24 14:30:00', 'Village 4A', '', '2025-11-28 17:31:32', 3, 5),
(98, 14, 9, 6, '2025-08-24 14:30:00', 'Mamutse', '', '2025-11-28 17:31:32', 3, 2),
(99, 15, 7, 2, '2025-09-07 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 9),
(100, 15, 5, 10, '2025-09-07 14:30:00', 'Village 3', '', '2025-11-28 17:31:32', 3, 4),
(101, 15, 11, 6, '2025-09-07 14:30:00', 'Village 6', '', '2025-11-28 17:31:32', 3, 10),
(102, 15, 3, 9, '2025-09-07 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 6),
(103, 15, 8, 4, '2025-09-07 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 3),
(104, 16, 9, 5, '2025-09-20 14:30:00', 'Mamutse', '', '2025-11-28 17:31:32', 3, 3),
(105, 16, 4, 2, '2025-09-20 14:30:00', 'Village 4B', '', '2025-11-28 17:31:32', 3, 6),
(106, 16, 11, 3, '2025-09-21 14:30:00', 'Village 6', '', '2025-11-28 17:31:32', 3, 8),
(107, 16, 10, 7, '2025-09-21 14:30:00', 'Village 4A', '', '2025-11-28 17:31:32', 3, 9),
(108, 16, 6, 8, '2025-09-21 14:30:00', 'Tafara', '', '2025-11-28 17:31:32', 3, 7),
(109, 17, 7, 4, '2025-09-27 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 2),
(110, 17, 5, 11, '2025-10-11 14:30:00', 'Village 3', '', '2025-11-28 17:31:32', 3, 7),
(111, 17, 10, 9, '2025-10-11 14:30:00', 'Village 4A', '', '2025-11-28 17:31:32', 3, 6),
(112, 17, 2, 6, '2025-10-05 14:30:00', 'Mutsinzwa', '', '2025-11-28 17:31:32', 3, 3),
(113, 17, 8, 3, '2025-10-12 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 10),
(114, 18, 9, 7, '2025-11-02 14:30:00', 'Mamutse', '', '2025-11-28 17:31:32', 3, 3),
(115, 18, 6, 4, '2025-11-02 14:30:00', 'Gedhe', '', '2025-11-28 17:31:32', 3, 2),
(116, 18, 11, 10, '2025-11-02 14:30:00', 'Village 6', '', '2025-11-28 17:31:32', 3, 4),
(117, 18, 3, 2, '2025-11-02 14:30:00', 'Nehanda', '', '2025-11-28 17:31:32', 3, 6),
(118, 18, 8, 5, '2025-11-02 14:30:00', 'Village 5', '', '2025-11-28 17:31:32', 3, 10);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `image`, `uploaded_at`) VALUES
(1, '04', 'thomas', 'gallery_1763913348_4285.jpg', '2025-11-23 17:55:48'),
(2, '02', '', 'gallery_1763913676_1025.jpg', '2025-11-23 18:01:16'),
(3, '01', '01', 'gallery_1763916339_2018.jpg', '2025-11-23 18:45:39'),
(4, '03', '', 'gallery_1763916350_7877.jpg', '2025-11-23 18:45:50'),
(5, 'fans', '', 'gallery_1763916361_7746.jpg', '2025-11-23 18:46:01'),
(6, '09', '', 'gallery_1763916392_4746.png', '2025-11-23 18:46:32'),
(7, 'Gedhe FC vs 4B FC', '06 Dec 2025', 'gallery_1765189041_9780.jpg', '2025-12-08 12:17:21'),
(8, 'Gedhe FC vs 4B FC', '06 Dec 2025', 'gallery_1765189059_8036.jpg', '2025-12-08 12:17:39'),
(9, '04SL Prize Day', '', 'gallery_1766844033_8594.jpeg', '2025-12-27 16:00:33'),
(10, '04SL Prize Day', '', 'gallery_1766844049_9758.jpeg', '2025-12-27 16:00:49'),
(11, '04SL Prize Day', '', 'gallery_1766844069_5365.jpeg', '2025-12-27 16:01:09'),
(12, '04SL Prize Day', '', 'gallery_1766844081_4659.jpeg', '2025-12-27 16:01:21'),
(13, '04SL Prize Day', '', 'gallery_1766844093_3487.jpeg', '2025-12-27 16:01:33'),
(14, '04SL Prize Day', '', 'gallery_1766844109_7197.jpeg', '2025-12-27 16:01:49'),
(15, '04SL Prize Day', '', 'gallery_1766844120_4228.jpeg', '2025-12-27 16:02:00'),
(16, '04SL Prize Day', '', 'gallery_1766844130_1101.jpeg', '2025-12-27 16:02:10'),
(17, '04SL Prize Day', '', 'gallery_1766844162_8889.jpeg', '2025-12-27 16:02:42'),
(18, '04SL Prize Day', '', 'gallery_1766844173_5283.jpeg', '2025-12-27 16:02:53'),
(19, '04SL Prize Day', '', 'gallery_1766844187_8139.jpeg', '2025-12-27 16:03:07'),
(20, '04 Legends', '', 'gallery_1766847170_3527.jpeg', '2025-12-27 16:52:50'),
(21, '04 Legends', '', 'gallery_1766847180_1049.jpeg', '2025-12-27 16:53:00'),
(22, '04 Legends', '', 'gallery_1766847189_8897.jpeg', '2025-12-27 16:53:09');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `minute` int(11) NOT NULL,
  `is_penalty` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`id`, `match_id`, `player_id`, `minute`, `is_penalty`, `created_at`) VALUES
(40, 108, 20, 11, 0, '2025-11-29 09:15:36'),
(41, 108, 20, 54, 0, '2025-11-29 09:15:45'),
(42, 108, 28, 77, 0, '2025-11-29 09:16:08'),
(43, 110, 102, 45, 0, '2025-11-29 09:18:20'),
(44, 106, 70, 32, 0, '2025-11-29 09:30:13'),
(45, 106, 69, 23, 0, '2025-11-29 09:30:33'),
(46, 106, 82, 65, 0, '2025-11-29 09:30:48'),
(47, 109, 61, 87, 0, '2025-11-29 09:35:58'),
(48, 107, 50, 45, 0, '2025-11-29 09:37:43'),
(49, 107, 168, 87, 0, '2025-11-29 09:38:03'),
(50, 107, 172, 25, 0, '2025-11-29 09:38:15'),
(51, 104, 132, 41, 0, '2025-11-29 09:45:37'),
(52, 103, 189, 15, 0, '2025-11-29 09:48:10'),
(53, 103, 204, 55, 0, '2025-11-29 09:48:26'),
(54, 103, 43, 66, 0, '2025-11-29 09:48:58'),
(55, 101, 172, 78, 0, '2025-11-29 09:50:33'),
(56, 99, 198, 54, 0, '2025-11-29 09:53:18'),
(57, 99, 202, 77, 0, '2025-11-29 09:53:36'),
(58, 99, 210, 94, 0, '2025-11-29 09:54:14'),
(59, 99, 65, 25, 0, '2025-11-29 09:55:17'),
(60, 98, 26, 58, 0, '2025-11-29 09:56:59'),
(61, 98, 121, 57, 0, '2025-11-29 09:57:26'),
(62, 98, 129, 25, 0, '2025-11-29 09:57:38'),
(63, 98, 108, 12, 0, '2025-11-29 09:58:00'),
(64, 98, 130, 91, 0, '2025-11-29 09:58:12'),
(65, 97, 185, 88, 0, '2025-11-29 09:59:08'),
(66, 96, 39, 78, 0, '2025-11-29 10:00:25'),
(67, 95, 214, 56, 0, '2025-11-29 10:02:34'),
(68, 95, 167, 87, 0, '2025-11-29 10:02:47'),
(69, 95, 168, 54, 0, '2025-11-29 10:03:09'),
(70, 94, 122, 15, 0, '2025-11-29 10:03:52'),
(71, 94, 127, 45, 0, '2025-11-29 10:04:05'),
(72, 94, 111, 25, 0, '2025-11-29 10:04:19'),
(73, 94, 128, 78, 0, '2025-11-29 10:05:23'),
(74, 94, 33, 56, 0, '2025-11-29 10:06:08'),
(75, 93, 17, 23, 0, '2025-11-29 10:08:17'),
(76, 93, 23, 29, 0, '2025-11-29 10:08:31'),
(77, 93, 26, 87, 0, '2025-11-29 10:08:43'),
(78, 93, 49, 36, 0, '2025-11-29 10:08:58'),
(79, 93, 50, 56, 0, '2025-11-29 10:09:09'),
(80, 93, 54, 69, 0, '2025-11-29 10:09:22'),
(81, 93, 54, 88, 0, '2025-11-29 10:09:30'),
(82, 93, 62, 95, 0, '2025-11-29 10:10:24'),
(83, 92, 107, 25, 0, '2025-11-29 10:12:11'),
(84, 92, 102, 65, 0, '2025-11-29 10:12:26'),
(85, 91, 61, 36, 0, '2025-11-29 10:13:46'),
(86, 91, 149, 39, 0, '2025-11-29 10:14:07'),
(87, 91, 132, 87, 0, '2025-11-29 10:14:18'),
(88, 90, 54, 88, 0, '2025-11-29 10:29:06'),
(89, 88, 171, 4, 0, '2025-11-29 10:33:08'),
(90, 88, 169, 34, 0, '2025-11-29 10:33:25'),
(91, 88, 169, 44, 0, '2025-11-29 10:33:33'),
(92, 88, 169, 36, 0, '2025-11-29 10:33:42'),
(93, 88, 182, 78, 0, '2025-11-29 10:33:54'),
(94, 88, 182, 87, 0, '2025-11-29 10:34:03'),
(95, 88, 172, 91, 0, '2025-11-29 10:34:16'),
(96, 88, 168, 96, 0, '2025-11-29 10:34:26'),
(97, 88, 185, 65, 0, '2025-11-29 10:35:02'),
(98, 88, 25, 88, 0, '2025-11-29 10:37:15'),
(99, 87, 145, 58, 0, '2025-11-29 10:39:49'),
(100, 87, 131, 87, 0, '2025-11-29 10:40:05'),
(101, 85, 38, 25, 0, '2025-11-29 10:44:04'),
(102, 85, 168, 85, 0, '2025-11-29 10:44:18'),
(103, 84, 50, 55, 0, '2025-11-29 10:47:26'),
(104, 84, 50, 78, 0, '2025-11-29 10:47:37'),
(105, 84, 50, 87, 0, '2025-11-29 10:47:55'),
(106, 84, 206, 36, 0, '2025-11-29 10:48:33'),
(107, 83, 138, 34, 0, '2025-11-29 10:51:06'),
(108, 83, 138, 44, 0, '2025-11-29 10:51:17'),
(110, 83, 138, 25, 0, '2025-11-29 10:51:55'),
(111, 83, 61, 49, 0, '2025-11-29 10:52:48'),
(112, 83, 132, 58, 0, '2025-11-29 10:52:57'),
(113, 83, 132, 69, 0, '2025-11-29 10:53:10'),
(114, 83, 145, 87, 0, '2025-11-29 10:53:24'),
(115, 82, 109, 88, 0, '2025-11-29 10:58:32'),
(116, 21, 65, 58, 0, '2025-11-29 11:01:21'),
(117, 21, 48, 51, 0, '2025-11-29 11:02:04'),
(118, 22, 29, 55, 0, '2025-11-29 11:02:48'),
(119, 23, 131, 55, 0, '2025-11-29 11:03:40'),
(120, 23, 83, 91, 0, '2025-11-29 11:04:31'),
(121, 24, 108, 58, 0, '2025-11-29 11:05:06'),
(122, 24, 109, 14, 0, '2025-11-29 11:05:20'),
(123, 24, 110, 78, 0, '2025-11-29 11:05:34'),
(124, 25, 188, 14, 0, '2025-11-29 11:06:44'),
(125, 25, 189, 58, 0, '2025-11-29 11:06:55'),
(126, 26, 157, 25, 0, '2025-11-29 11:09:36'),
(127, 26, 165, 48, 0, '2025-11-29 11:09:48'),
(128, 26, 35, 59, 0, '2025-11-29 11:11:01'),
(129, 27, 123, 25, 0, '2025-11-29 11:12:15'),
(130, 27, 50, 59, 0, '2025-11-29 11:12:28'),
(131, 27, 58, 78, 0, '2025-11-29 11:12:41'),
(132, 28, 85, 25, 0, '2025-11-29 11:14:03'),
(133, 28, 168, 89, 0, '2025-11-29 11:14:16'),
(134, 29, 67, 10, 0, '2025-11-29 11:16:00'),
(135, 29, 67, 51, 0, '2025-11-29 11:16:10'),
(136, 29, 67, 25, 0, '2025-11-29 11:16:19'),
(137, 29, 69, 78, 0, '2025-11-29 11:16:32'),
(138, 30, 132, 11, 0, '2025-11-29 11:19:27'),
(139, 30, 192, 15, 0, '2025-11-29 11:19:58'),
(140, 31, 113, 11, 0, '2025-11-29 11:20:31'),
(141, 31, 113, 58, 0, '2025-11-29 11:20:41'),
(142, 31, 113, 89, 0, '2025-11-29 11:20:48'),
(143, 32, 215, 15, 0, '2025-11-29 11:25:33'),
(144, 32, 17, 88, 0, '2025-11-29 11:25:47'),
(145, 32, 153, 91, 0, '2025-11-29 11:26:19'),
(146, 32, 154, 69, 0, '2025-11-29 11:26:30'),
(147, 33, 50, 23, 0, '2025-11-29 11:28:09'),
(148, 33, 51, 17, 0, '2025-11-29 11:28:22'),
(149, 33, 87, 12, 0, '2025-11-29 11:28:40'),
(150, 33, 85, 57, 0, '2025-11-29 11:28:50'),
(151, 33, 88, 77, 0, '2025-11-29 11:29:02'),
(152, 33, 88, 47, 0, '2025-11-29 11:29:12'),
(153, 33, 89, 85, 0, '2025-11-29 11:29:25'),
(154, 34, 30, 45, 0, '2025-11-29 11:30:34'),
(155, 34, 135, 88, 0, '2025-11-29 11:30:48'),
(156, 34, 136, 78, 0, '2025-11-29 11:30:58'),
(157, 35, 171, 58, 0, '2025-11-29 11:32:28'),
(158, 35, 193, 85, 0, '2025-11-29 11:32:57'),
(159, 36, 155, 91, 0, '2025-11-29 11:33:45'),
(160, 38, 132, 45, 0, '2025-11-29 13:22:11'),
(161, 38, 138, 88, 0, '2025-11-29 13:22:27'),
(162, 38, 17, 48, 0, '2025-11-29 13:23:04'),
(163, 39, 194, 15, 0, '2025-11-29 13:24:34'),
(165, 39, 192, 48, 0, '2025-11-29 13:25:12'),
(166, 39, 194, 78, 0, '2025-11-29 13:25:28'),
(167, 39, 53, 65, 0, '2025-11-29 13:26:02'),
(168, 40, 171, 36, 0, '2025-11-29 13:26:41'),
(169, 40, 172, 58, 0, '2025-11-29 13:26:54'),
(170, 40, 172, 84, 0, '2025-11-29 13:27:02'),
(171, 40, 32, 81, 0, '2025-11-29 13:27:41'),
(172, 41, 72, 33, 0, '2025-11-29 13:28:32'),
(173, 41, 73, 45, 0, '2025-11-29 13:28:48'),
(174, 41, 69, 78, 0, '2025-11-29 13:29:00'),
(175, 41, 93, 88, 0, '2025-11-29 13:29:27'),
(176, 42, 152, 23, 0, '2025-11-29 13:30:16'),
(177, 42, 152, 58, 0, '2025-11-29 13:30:28'),
(178, 42, 156, 65, 0, '2025-11-29 13:30:39'),
(179, 42, 157, 87, 0, '2025-11-29 13:30:57'),
(180, 42, 139, 82, 0, '2025-11-29 13:32:55'),
(181, 44, 113, 55, 0, '2025-11-29 13:36:22'),
(182, 45, 49, 89, 0, '2025-11-29 13:39:04'),
(183, 45, 54, 55, 0, '2025-11-29 13:39:16'),
(184, 45, 54, 35, 0, '2025-11-29 13:39:26'),
(185, 45, 31, 48, 0, '2025-11-29 13:41:07'),
(186, 46, 143, 58, 0, '2025-11-29 13:41:51'),
(187, 47, 189, 84, 0, '2025-11-29 13:42:54'),
(188, 47, 198, 48, 0, '2025-11-29 13:43:06'),
(189, 47, 85, 47, 0, '2025-11-29 13:43:56'),
(190, 47, 96, 59, 0, '2025-11-29 13:44:15'),
(191, 47, 84, 32, 0, '2025-11-29 13:44:29'),
(192, 48, 50, 45, 0, '2025-11-29 13:45:19'),
(193, 49, 168, 48, 0, '2025-11-29 13:46:06'),
(194, 49, 178, 77, 0, '2025-11-29 13:46:15'),
(195, 50, 30, 33, 0, '2025-11-29 13:46:55'),
(196, 50, 113, 56, 0, '2025-11-29 13:47:11'),
(197, 50, 121, 68, 0, '2025-11-29 13:47:23'),
(198, 50, 121, 87, 0, '2025-11-29 13:47:37'),
(199, 51, 122, 69, 0, '2025-11-29 13:49:42'),
(200, 51, 21, 91, 0, '2025-11-29 13:50:00'),
(201, 52, 75, 58, 0, '2025-11-29 13:51:48'),
(202, 52, 73, 25, 0, '2025-11-29 13:52:03'),
(203, 52, 72, 87, 0, '2025-11-29 13:52:13'),
(204, 52, 199, 65, 0, '2025-11-29 13:52:43'),
(205, 53, 132, 45, 0, '2025-11-29 13:53:16'),
(206, 53, 139, 44, 0, '2025-11-29 13:53:28'),
(207, 53, 170, 48, 0, '2025-11-29 13:53:42'),
(208, 54, 84, 48, 0, '2025-11-29 13:54:26'),
(209, 54, 30, 69, 0, '2025-11-29 13:55:38'),
(210, 54, 30, 78, 0, '2025-11-29 13:56:01'),
(211, 54, 35, 33, 0, '2025-11-29 13:56:14'),
(212, 81, 165, 34, 0, '2025-11-29 13:58:27'),
(213, 81, 165, 87, 0, '2025-11-29 13:58:38'),
(214, 80, 185, 87, 0, '2025-11-29 13:59:19'),
(215, 79, 61, 13, 0, '2025-11-29 14:00:56'),
(216, 79, 138, 65, 0, '2025-11-29 14:01:07'),
(217, 79, 145, 56, 0, '2025-11-29 14:01:20'),
(218, 79, 39, 19, 0, '2025-11-29 14:02:34'),
(219, 78, 89, 29, 0, '2025-11-29 14:03:16'),
(220, 78, 105, 22, 0, '2025-11-29 14:03:29'),
(221, 78, 50, 69, 0, '2025-11-29 14:04:03'),
(222, 77, 164, 11, 0, '2025-11-29 14:04:38'),
(223, 77, 164, 23, 0, '2025-11-29 14:04:48'),
(224, 77, 163, 58, 0, '2025-11-29 14:04:59'),
(225, 77, 157, 69, 0, '2025-11-29 14:05:10'),
(226, 76, 110, 29, 0, '2025-11-29 14:06:22'),
(227, 76, 121, 58, 0, '2025-11-29 14:06:34'),
(228, 76, 72, 91, 0, '2025-11-29 14:06:49'),
(229, 75, 204, 32, 0, '2025-11-29 14:08:18'),
(230, 75, 132, 58, 0, '2025-11-29 14:08:46'),
(231, 74, 43, 88, 0, '2025-11-29 14:10:17'),
(232, 74, 43, 36, 0, '2025-11-29 14:10:38'),
(233, 71, 72, 55, 0, '2025-11-29 14:14:45'),
(234, 71, 75, 14, 0, '2025-11-29 14:15:03'),
(235, 71, 65, 58, 0, '2025-11-29 14:15:18'),
(236, 71, 80, 69, 0, '2025-11-29 14:15:37'),
(237, 71, 72, 51, 0, '2025-11-29 14:15:58'),
(238, 71, 23, 45, 0, '2025-11-29 14:17:16'),
(239, 71, 23, 47, 0, '2025-11-29 14:17:25'),
(240, 71, 17, 19, 0, '2025-11-29 14:17:37'),
(241, 70, 162, 15, 0, '2025-11-29 14:18:48'),
(242, 70, 156, 25, 0, '2025-11-29 14:19:07'),
(243, 70, 156, 58, 0, '2025-11-29 14:19:19'),
(244, 70, 161, 69, 0, '2025-11-29 14:19:38'),
(245, 70, 163, 84, 0, '2025-11-29 14:19:58'),
(246, 70, 151, 81, 0, '2025-11-29 14:20:14'),
(247, 70, 203, 51, 0, '2025-11-29 14:22:15'),
(248, 69, 169, 45, 0, '2025-11-29 14:23:26'),
(249, 68, 17, 11, 0, '2025-11-29 14:24:12'),
(250, 68, 21, 89, 0, '2025-11-29 14:24:25'),
(251, 68, 40, 14, 0, '2025-11-29 14:24:35'),
(252, 68, 41, 25, 0, '2025-11-29 14:24:48'),
(253, 68, 41, 45, 0, '2025-11-29 14:26:04'),
(254, 68, 41, 48, 0, '2025-11-29 14:26:15'),
(255, 68, 42, 48, 0, '2025-11-29 14:26:28'),
(256, 68, 41, 19, 0, '2025-11-29 14:26:45'),
(257, 67, 84, 14, 0, '2025-11-29 14:28:36'),
(258, 67, 142, 55, 0, '2025-11-29 14:29:00'),
(259, 67, 138, 18, 0, '2025-11-29 14:29:23'),
(260, 66, 51, 44, 0, '2025-11-29 14:32:19'),
(261, 65, 109, 14, 0, '2025-11-29 14:32:45'),
(262, 65, 108, 19, 0, '2025-11-29 14:33:00'),
(263, 65, 109, 88, 0, '2025-11-29 14:33:12'),
(264, 64, 181, 14, 0, '2025-11-29 14:34:15'),
(265, 64, 168, 58, 0, '2025-11-29 14:34:29'),
(266, 64, 168, 88, 0, '2025-11-29 14:34:41'),
(267, 63, 189, 55, 0, '2025-11-29 14:36:24'),
(268, 63, 189, 45, 0, '2025-11-29 14:36:35'),
(269, 63, 190, 15, 0, '2025-11-29 14:36:49'),
(270, 62, 101, 25, 0, '2025-11-29 14:39:59'),
(271, 62, 102, 14, 0, '2025-11-29 14:40:10'),
(272, 62, 161, 21, 0, '2025-11-29 14:40:24'),
(273, 61, 67, 58, 0, '2025-11-29 14:40:42'),
(274, 61, 74, 48, 0, '2025-11-29 14:40:57'),
(275, 61, 77, 15, 0, '2025-11-29 14:41:11'),
(276, 61, 78, 87, 0, '2025-11-29 14:41:25'),
(277, 61, 30, 91, 0, '2025-11-29 14:41:46'),
(278, 61, 30, 69, 0, '2025-11-29 14:42:01'),
(279, 60, 108, 55, 0, '2025-11-29 14:42:36'),
(280, 60, 123, 51, 0, '2025-11-29 14:42:51'),
(281, 60, 109, 87, 0, '2025-11-29 14:43:07'),
(282, 60, 158, 48, 0, '2025-11-29 14:43:31'),
(283, 59, 59, 44, 0, '2025-11-29 14:44:08'),
(284, 58, 93, 55, 0, '2025-11-29 14:45:30'),
(285, 58, 95, 12, 0, '2025-11-29 14:45:45'),
(286, 58, 100, 58, 0, '2025-11-29 14:45:54'),
(287, 58, 100, 69, 0, '2025-11-29 14:46:11'),
(288, 57, 35, 11, 0, '2025-11-29 14:48:24'),
(289, 57, 35, 16, 0, '2025-11-29 14:48:37'),
(290, 57, 30, 13, 0, '2025-11-29 14:48:53'),
(291, 57, 30, 58, 0, '2025-11-29 14:49:05'),
(292, 57, 37, 85, 0, '2025-11-29 14:49:26'),
(293, 57, 38, 46, 0, '2025-11-29 14:49:41'),
(294, 57, 37, 71, 0, '2025-11-29 14:51:26'),
(295, 57, 200, 69, 0, '2025-11-29 14:51:41'),
(296, 56, 180, 15, 0, '2025-11-29 14:52:44'),
(297, 56, 180, 87, 0, '2025-11-29 14:52:57'),
(298, 56, 169, 48, 0, '2025-11-29 14:53:13');

--
-- Triggers `goals`
--
DELIMITER $$
CREATE TRIGGER `update_player_goals` AFTER INSERT ON `goals` FOR EACH ROW BEGIN
    UPDATE players SET goals = goals + 1 WHERE id = NEW.player_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `logos`
--

CREATE TABLE `logos` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `purpose` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `filename` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logos`
--

INSERT INTO `logos` (`id`, `title`, `purpose`, `is_active`, `filename`, `uploaded_at`) VALUES
(8, '04 Soccer League', 'login_logo', 1, 'img_6921adf2dae04.png', '2025-11-22 14:34:58'),
(9, 'Frontend logo', 'frontend_header', 1, 'img_6921b77c16ccd.png', '2025-11-22 15:15:40'),
(13, 'matchday_18', 'league_shield', 1, 'img_6935775d9e8c2.png', '2025-12-07 14:47:25'),
(16, 'gif', 'footer_logo', 1, 'logo_693d3612740ae.gif', '2025-12-09 20:02:49'),
(20, 'banner', 'sponsor_banner', 1, 'img_695fb5839a04c.png', '2026-01-08 15:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `management`
--

CREATE TABLE `management` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `club_id` int(11) NOT NULL,
  `role` enum('Referee','Linesman','Head Coach','Assistant Coach','Secretary','Treasurer','Committee Member','Medical Aid','Councillor','Chairman','Vice-Chairman') NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `management`
--

INSERT INTO `management` (`id`, `full_name`, `club_id`, `role`, `date_of_birth`, `is_active`, `photo`, `created_at`) VALUES
(1, 'Muvavarirwa Phillip', 5, 'Head Coach', '1972-02-12', 1, 'management_694160bb20da6.jpg', '2025-12-16 10:15:59'),
(3, 'Masunungure Edward', 5, 'Committee Member', '1970-04-10', 1, 'management_697e6bf5afe1a.png', '2026-01-31 20:54:13'),
(4, 'Chahweta Nickson', 5, 'Chairman', '1947-03-07', 1, 'management_697e728e66e83.png', '2026-01-31 20:55:26'),
(5, 'Mangezi Onismores', 5, 'Medical Aid', '1996-04-26', 1, 'management_6980f818728ae.png', '2026-02-02 19:16:40'),
(6, 'Muvavarirwa Alfonce', 5, 'Secretary', '1979-09-09', 1, 'management_6980f8b1c029c.jpeg', '2026-02-02 19:19:13'),
(7, 'Muvavarirwa Costen', 5, 'Vice-Chairman', '1976-09-10', 1, 'management_6980fa07ab642.png', '2026-02-02 19:20:27'),
(8, 'Mutsetsi Nonkululeko', 23, 'Treasurer', '1993-09-10', 1, 'management_6985cc3160ad0.png', '2026-02-06 11:10:41'),
(9, 'Machona Brighton', 23, 'Head Coach', '1983-08-07', 1, 'management_69897e68dd4e3.png', '2026-02-09 06:27:52'),
(10, 'Nikisi Lillian', 23, 'Secretary', '1986-08-08', 1, 'management_69916eadc0ce5.jpg', '2026-02-15 06:58:53'),
(11, 'Maziveyi Patrick', 23, 'Assistant Coach', '1970-02-05', 1, 'management_69917ed47aabd.png', '2026-02-15 08:07:48'),
(12, 'Hore Moses', 3, 'Head Coach', '1979-07-03', 1, 'management_6991959424038.png', '2026-02-15 09:44:00'),
(14, 'Gumireshe Amon', 3, 'Assistant Coach', '1972-07-05', 1, 'management_69921d7bbff0c.png', '2026-02-15 19:21:20'),
(15, 'Charamba Edson', 4, 'Head Coach', '1980-05-07', 1, 'management_6996fcf5c25cb.jpg', '2026-02-19 12:07:17'),
(16, 'Machona James', 24, 'Head Coach', '1985-11-28', 1, 'management_699734e7ec683.png', '2026-02-19 16:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `fixture_id` int(11) NOT NULL,
  `home_score` int(11) DEFAULT 0,
  `away_score` int(11) DEFAULT 0,
  `match_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `fixture_id`, `home_score`, `away_score`, `match_date`, `created_at`) VALUES
(21, 29, 1, 1, '2025-03-15 14:30:00', '2025-11-28 17:31:32'),
(22, 30, 1, 0, '2025-03-15 14:30:00', '2025-11-28 17:31:32'),
(23, 31, 1, 1, '2025-03-15 14:30:00', '2025-11-28 17:31:32'),
(24, 32, 0, 3, '2025-03-16 14:30:00', '2025-11-28 17:31:32'),
(25, 33, 2, 0, '2025-03-16 14:30:00', '2025-11-28 17:31:32'),
(26, 34, 2, 1, '2025-07-27 14:30:00', '2025-11-28 17:31:32'),
(27, 35, 1, 2, '2025-06-07 14:30:00', '2025-11-28 17:31:32'),
(28, 36, 1, 1, '2025-03-29 14:30:00', '2025-11-28 17:31:32'),
(29, 37, 0, 4, '2025-03-29 14:30:00', '2025-11-28 17:31:32'),
(30, 38, 1, 1, '2025-03-30 14:30:00', '2025-11-28 17:31:32'),
(31, 39, 0, 3, '2025-04-05 14:30:00', '2025-11-28 17:31:32'),
(32, 40, 2, 2, '2025-04-05 14:30:00', '2025-11-28 17:31:32'),
(33, 41, 2, 5, '2025-04-12 14:30:00', '2025-11-28 17:31:32'),
(34, 42, 1, 2, '2025-04-06 14:30:00', '2025-11-28 17:31:32'),
(35, 43, 1, 1, '2025-04-12 14:30:00', '2025-11-28 17:31:32'),
(36, 44, 1, 0, '2025-04-19 14:30:00', '2025-11-28 17:31:32'),
(37, 45, 0, 0, '2025-04-19 14:30:00', '2025-11-28 17:31:32'),
(38, 46, 2, 1, '2025-04-19 14:30:00', '2025-11-28 17:31:32'),
(39, 47, 3, 1, '2025-04-20 14:30:00', '2025-11-28 17:31:32'),
(40, 48, 3, 1, '2025-04-19 14:30:00', '2025-11-28 17:31:32'),
(41, 49, 3, 1, '2025-05-03 14:30:00', '2025-11-28 17:31:32'),
(42, 50, 4, 1, '2025-05-04 14:30:00', '2025-11-28 17:31:32'),
(43, 51, 0, 0, '2025-05-03 14:30:00', '2025-11-28 17:31:32'),
(44, 52, 1, 0, '2025-05-04 14:30:00', '2025-11-28 17:31:32'),
(45, 53, 3, 1, '2025-05-03 14:30:00', '2025-11-28 17:31:32'),
(46, 54, 2, 0, '2025-05-11 14:30:00', '2025-11-28 17:31:32'),
(47, 55, 2, 3, '2025-05-17 14:30:00', '2025-11-28 17:31:32'),
(48, 56, 1, 0, '2025-05-11 14:30:00', '2025-11-28 17:31:32'),
(49, 57, 2, 0, '2025-05-17 14:30:00', '2025-11-28 17:31:32'),
(50, 58, 1, 3, '2025-05-11 14:30:00', '2025-11-28 17:31:32'),
(51, 59, 1, 1, '2025-05-25 14:30:00', '2025-11-28 17:31:32'),
(52, 60, 3, 1, '2025-05-31 14:30:00', '2025-11-28 17:31:32'),
(53, 61, 2, 1, '2025-05-31 14:30:00', '2025-11-28 17:31:32'),
(54, 62, 2, 3, '2025-05-24 14:30:00', '2025-11-28 17:31:32'),
(55, 63, 0, 0, '2025-05-25 14:30:00', '2025-11-28 17:31:32'),
(56, 64, 3, 0, '2025-06-14 14:30:00', '2025-11-28 17:31:32'),
(57, 65, 7, 1, '2025-06-14 14:30:00', '2025-11-28 17:31:32'),
(58, 66, 0, 4, '2025-06-14 14:30:00', '2025-11-28 17:31:32'),
(59, 67, 1, 0, '2025-06-15 14:30:00', '2025-11-28 17:31:32'),
(60, 68, 3, 1, '2025-06-14 14:30:00', '2025-11-28 17:31:32'),
(61, 69, 4, 2, '2025-06-21 14:30:00', '2025-11-28 17:31:32'),
(62, 70, 2, 1, '2025-06-21 14:30:00', '2025-11-28 17:31:32'),
(63, 71, 3, 0, '2025-06-21 14:30:00', '2025-11-28 17:31:32'),
(64, 72, 3, 0, '2025-06-21 14:30:00', '2025-11-28 17:31:32'),
(65, 73, 0, 3, '2025-06-21 14:30:00', '2025-11-28 17:31:32'),
(66, 74, 1, 0, '2025-07-05 14:30:00', '2025-11-28 17:31:32'),
(67, 75, 1, 2, '2025-07-05 14:30:00', '2025-11-28 17:31:32'),
(68, 76, 2, 6, '2025-07-06 14:30:00', '2025-11-28 17:31:32'),
(69, 77, 0, 1, '2025-07-06 14:30:00', '2025-11-28 17:31:32'),
(70, 78, 6, 1, '2025-07-06 14:30:00', '2025-11-28 17:31:32'),
(71, 79, 5, 3, '2025-07-26 14:30:00', '2025-11-28 17:31:32'),
(72, 80, 0, 0, '2025-07-12 14:30:00', '2025-11-28 17:31:32'),
(73, 81, 0, 0, '2025-07-12 14:30:00', '2025-11-28 17:31:32'),
(74, 82, 2, 0, '2025-07-13 14:30:00', '2025-11-28 17:31:32'),
(75, 83, 1, 1, '2025-07-26 14:30:00', '2025-11-28 17:31:32'),
(76, 84, 2, 1, '2025-08-03 14:30:00', '2025-11-28 17:31:32'),
(77, 85, 4, 0, '2025-08-02 14:30:00', '2025-11-28 17:31:32'),
(78, 86, 2, 1, '2025-07-26 14:30:00', '2025-11-28 17:31:32'),
(79, 87, 3, 1, '2025-08-03 14:30:00', '2025-11-28 17:31:32'),
(80, 88, 0, 1, '2025-08-02 14:30:00', '2025-11-28 17:31:32'),
(81, 89, 0, 2, '2025-08-17 14:30:00', '2025-11-28 17:31:32'),
(82, 90, 1, 0, '2025-08-16 14:30:00', '2025-11-28 17:31:32'),
(83, 91, 2, 7, '2025-08-16 14:30:00', '2025-11-28 17:31:32'),
(84, 92, 3, 1, '2025-08-17 14:30:00', '2025-11-28 17:31:32'),
(85, 93, 1, 1, '2025-08-16 14:30:00', '2025-11-28 17:31:32'),
(86, 94, 3, 0, '2025-08-23 14:30:00', '2025-11-28 17:31:32'),
(87, 95, 2, 0, '2025-08-31 14:30:00', '2025-11-28 17:31:32'),
(88, 96, 9, 1, '2025-08-30 14:30:00', '2025-11-28 17:31:32'),
(89, 97, 0, 0, '2025-08-24 14:30:00', '2025-11-28 17:31:32'),
(90, 98, 0, 2, '2025-08-24 14:30:00', '2025-11-28 17:31:32'),
(91, 99, 0, 3, '2025-09-07 14:30:00', '2025-11-28 17:31:32'),
(92, 100, 2, 0, '2025-09-07 14:30:00', '2025-11-28 17:31:32'),
(93, 101, 3, 5, '2025-09-07 14:30:00', '2025-11-28 17:31:32'),
(94, 102, 4, 1, '2025-09-07 14:30:00', '2025-11-28 17:31:32'),
(95, 103, 2, 1, '2025-09-07 14:30:00', '2025-11-28 17:31:32'),
(96, 104, 1, 1, '2025-09-20 14:30:00', '2025-11-28 17:31:32'),
(97, 105, 1, 0, '2025-09-20 14:30:00', '2025-11-28 17:31:32'),
(98, 106, 1, 4, '2025-09-21 14:30:00', '2025-11-28 17:31:32'),
(99, 107, 3, 3, '2025-09-21 14:30:00', '2025-11-28 17:31:32'),
(100, 108, 0, 0, '2025-09-21 14:30:00', '2025-11-28 17:31:32'),
(101, 109, 1, 1, '2025-09-27 14:30:00', '2025-11-28 17:31:32'),
(102, 110, 3, 0, '2025-10-11 14:30:00', '2025-11-28 17:31:32'),
(103, 111, 2, 1, '2025-10-11 14:30:00', '2025-11-28 17:31:32'),
(104, 112, 1, 0, '2025-10-05 14:30:00', '2025-11-28 17:31:32'),
(105, 113, 0, 0, '2025-10-12 14:30:00', '2025-11-28 17:31:32'),
(106, 114, 1, 3, '2025-11-02 14:30:00', '2025-11-28 17:31:32'),
(107, 115, 1, 2, '2025-11-02 14:30:00', '2025-11-28 17:31:32'),
(108, 116, 3, 2, '2025-11-02 14:30:00', '2025-11-28 17:31:32'),
(109, 117, 0, 2, '2025-11-02 14:30:00', '2025-11-28 17:31:32'),
(110, 118, 0, 1, '2025-11-02 14:30:00', '2025-11-28 17:31:32');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `is_active`, `created_at`) VALUES
(1, 'gadzairobert', 'gadzairobert@gmail.com', '$2y$12$NExpzBWlaPJZjH5HmgqniOrsf1UXmeU9G3EkKc52llPnrJUZEA0va', 'Gadzai Robert', NULL, 1, '2025-12-17 15:35:39'),
(2, 'gadzairevisonwalter', 'gadzairevisonwalter@gmail.com', '$2y$12$HDFpn3L0NLqe/HE//oo87uG1qfZ9Z9OhsJHyZH.W43RXEQhRVyz.S', 'Gadzai Revison  Walter', NULL, 1, '2025-12-18 07:07:32'),
(3, 'makayathomas', 'makayathomas@gmail.com', '$2y$12$jrvrldtgGutvJjfBDdCe0.f3GZVspsb1d2L8OFiLCDZ6Hlf9zQD8C', 'Makaya Thomas', NULL, 1, '2025-12-18 07:08:49'),
(4, 'nunuraimichael', 'nunuraimichael@gmail.com', '$2y$12$0VzkfYh03NunWD35kWa3PuJa3AmWvEHzaP2hb83kQot8jogFBi43a', 'Nunurai Michael', NULL, 1, '2025-12-18 07:09:54'),
(6, 'isaacpumayi', 'isaacpumayi@gmail.com', '$2y$12$U2x5CaRtu5zQ5dJpVYobge8OmEfI6es.SHi7HOdmLOCLcN3tpV2D2', 'Isaac Pumayi', NULL, 1, '2025-12-18 07:11:10'),
(7, 'muvarirwazephania', 'muvarirwazephania@gmail.com', '$2y$12$EOQ5/0CqO2Z2p0g3c2SA1.xVaa0KUKbuKnpNFWF.iHESgCgbFWpTK', 'Muvarirwa Zephania', NULL, 1, '2025-12-18 07:14:02'),
(8, 'marozvevhuobey', 'marozvevhuobey@gmail.com', '$2y$12$O1LUPS9TZga/kIzsaH9O8.obzlv5MAUjoDcd6HZ8bAs789lLtJpb.', 'Marozvevhu Obey', NULL, 1, '2025-12-18 07:14:40'),
(10, '04sl', 'info@04sl.online', '$2y$12$txLxkG0tGNEeLpp9YSbK3.bn9mqDglmoUcv9LRTs/NzuXlznWi0Xi', '04SL', NULL, 1, '2026-02-02 20:20:17'),
(12, 'zvotoonakudakwashe', 'zvotoonakudakwashe@gmail.com', '$2y$12$KWJYHPLy1hgP5iSt8zMXnOvq2Msq.Dc5URfye98k1Kl.7CixFtcmS', 'Zvotoona Kudakwashe', NULL, 1, '2026-02-05 20:12:00'),
(13, 'muchabarwarichard', 'muchabarwarichard@gmail.com', '$2y$12$U99bYGJ9bJMrNhhJeNSQNeZ5F9RdtR3fw/WicWmIHNlUXrpj7w4sm', 'Muchabarwa Richard', NULL, 1, '2026-02-06 15:45:08'),
(30, 'player_168_1770406828', 'dummy.player_168_1770406828@noemail.local', '', 'Player: Pfumo Chacnois T - 04 FC', NULL, 1, '2026-02-06 19:40:28'),
(31, 'mgmt_8_1770406847', 'dummy.mgmt_8_1770406847@noemail.local', '', 'Management: Mutsetsi Nonkululeko (Treasurer) - 04 FC', NULL, 1, '2026-02-06 19:40:47'),
(32, 'player_170_1770406880', 'dummy.player_170_1770406880@noemail.local', '', 'Player: Chakamba Samuel - 04 FC', NULL, 1, '2026-02-06 19:41:20'),
(33, 'nikisililian', 'nikisililian@gmail.com', '$2y$12$1WuHIq/ZVAJ0d.9sx8MoQuTQmWIltkv/ELcC9eAC57tIaUQuyB/3m', 'Nikisi Lilian', NULL, 1, '2026-02-10 06:27:36'),
(34, 'player_224_1770899133', 'dummy.player_224_1770899133@noemail.local', '', 'Player: Mutsomekwa Atanas - 04 FC', NULL, 1, '2026-02-12 12:25:33'),
(35, 'player_121_1771138892', 'dummy.player_121_1771138892@noemail.local', '', 'Player: Zvaipa Darlington - 04 FC', NULL, 1, '2026-02-15 07:01:32'),
(36, 'shainster', 'shainster@gmail.com', '$2y$12$3onR4hf1Hh.TpWTYxJJH0OGee16APK4qvpCK8dR16wc1e4DXFCKR2', 'Shainster FC', NULL, 1, '2026-02-15 15:25:39'),
(37, 'mgmt_9_1771169545', 'dummy.mgmt_9_1771169545@noemail.local', '', 'Management: Machona Brighton (Head Coach) - 04 FC', NULL, 1, '2026-02-15 15:32:25'),
(38, 'player_159_1771344647', 'dummy.player_159_1771344647@noemail.local', '', 'Player: Sigauke Vitalis Jnr - 04 FC', NULL, 1, '2026-02-17 16:10:47'),
(39, 'player_157_1771399495', 'dummy.player_157_1771399495@noemail.local', '', 'Player: Tazvikonewa Godfrey - 04 FC', NULL, 1, '2026-02-18 07:24:55'),
(40, 'player_70_1771570883', 'dummy.player_70_1771570883@noemail.local', '', 'Player: Chemhaka Given - 04 FC', NULL, 1, '2026-02-20 07:01:23'),
(41, 'player_231_1771570902', 'dummy.player_231_1771570902@noemail.local', '', 'Player: Chenerwi Alexio - 04 FC', NULL, 1, '2026-02-20 07:01:42'),
(42, 'player_110_1771581816', 'dummy.player_110_1771581816@noemail.local', '', 'Player: Tarova Alfred - 04 FC', NULL, 1, '2026-02-20 10:03:36'),
(43, 'player_237_1771614359', 'dummy.player_237_1771614359@noemail.local', '', 'Player: Muchabarwa Andrew - 04 FC', NULL, 1, '2026-02-20 19:05:59'),
(44, 'player_156_1771614378', 'dummy.player_156_1771614378@noemail.local', '', 'Player: Zvotoona Tadiwa - 04 FC', NULL, 1, '2026-02-20 19:06:18'),
(45, 'player_156_1771614440', 'dummy.player_156_1771614440@noemail.local', '', 'Player: Zvotoona Tadiwa - 04 FC', NULL, 1, '2026-02-20 19:07:20'),
(46, 'player_156_1771614457', 'dummy.player_156_1771614457@noemail.local', '', 'Player: Zvotoona Tadiwa - 04 FC', NULL, 1, '2026-02-20 19:07:37'),
(47, 'player_237_1771614507', 'dummy.player_237_1771614507@noemail.local', '', 'Player: Muchabarwa Andrew - 04 FC', NULL, 1, '2026-02-20 19:08:27'),
(48, 'player_237_1771614522', 'dummy.player_237_1771614522@noemail.local', '', 'Player: Muchabarwa Andrew - 04 FC', NULL, 1, '2026-02-20 19:08:42'),
(49, 'player_110_1771756493', 'dummy.player_110_1771756493@noemail.local', '', 'Player: Tarova Alfred - 04 FC', NULL, 1, '2026-02-22 10:34:53'),
(50, 'player_110_1771756500', 'dummy.player_110_1771756500@noemail.local', '', 'Player: Tarova Alfred - 04 FC', NULL, 1, '2026-02-22 10:35:00'),
(51, 'player_70_1771756521', 'dummy.player_70_1771756521@noemail.local', '', 'Player: Chemhaka Given - 04 FC', NULL, 1, '2026-02-22 10:35:21'),
(52, 'player_237_1771756536', 'dummy.player_237_1771756536@noemail.local', '', 'Player: Muchabarwa Andrew - 04 FC', NULL, 1, '2026-02-22 10:35:36'),
(53, 'player_159_1771756544', 'dummy.player_159_1771756544@noemail.local', '', 'Player: Sigauke Prince - 04 FC', NULL, 1, '2026-02-22 10:35:44'),
(54, 'player_157_1771756551', 'dummy.player_157_1771756551@noemail.local', '', 'Player: Tazvikonewa Godfrey - 04 FC', NULL, 1, '2026-02-22 10:35:51'),
(55, 'player_121_1771756563', 'dummy.player_121_1771756563@noemail.local', '', 'Player: Zvaipa Darlington - 04 FC', NULL, 1, '2026-02-22 10:36:03'),
(56, 'player_224_1771756575', 'dummy.player_224_1771756575@noemail.local', '', 'Player: Mutsomekwa Atanas - 04 FC', NULL, 1, '2026-02-22 10:36:15'),
(57, 'player_170_1771756593', 'dummy.player_170_1771756593@noemail.local', '', 'Player: Chakamba Samuel - 04 FC', NULL, 1, '2026-02-22 10:36:33'),
(58, 'player_168_1771756606', 'dummy.player_168_1771756606@noemail.local', '', 'Player: Pfumo Chacnois T - 04 FC', NULL, 1, '2026-02-22 10:36:46'),
(59, 'player_231_1771756625', 'dummy.player_231_1771756625@noemail.local', '', 'Player: Chenerwi Alexio - 04 FC', NULL, 1, '2026-02-22 10:37:05'),
(60, 'player_156_1771756637', 'dummy.player_156_1771756637@noemail.local', '', 'Player: Zvotoona Tadiwa - 04 FC', NULL, 1, '2026-02-22 10:37:17'),
(61, 'mgmt_9_1771756678', 'dummy.mgmt_9_1771756678@noemail.local', '', 'Management: Machona Brighton (Head Coach) - 04 FC', NULL, 1, '2026-02-22 10:37:58'),
(62, 'player_115_1771857170', 'dummy.player_115_1771857170@noemail.local', '', 'Player: Chisiiwa Tawanda - 04 FC', NULL, 1, '2026-02-23 14:32:50'),
(63, 'player_228_1771857447', 'dummy.player_228_1771857447@noemail.local', '', 'Player: Zvaipa Panganai - 04 FC', NULL, 1, '2026-02-23 14:37:27'),
(64, 'mgmt_8_1771862093', 'dummy.mgmt_8_1771862093@noemail.local', '', 'Management: Mutsetsi Nonkululeko (Treasurer) - 04 FC', NULL, 1, '2026-02-23 15:54:53'),
(65, 'mgmt_16_1771864173', 'dummy.mgmt_16_1771864173@noemail.local', '', 'Management: Machona James (Head Coach) - Shainster FC', NULL, 1, '2026-02-23 16:29:33'),
(66, 'player_171_1771871913', 'dummy.player_171_1771871913@noemail.local', '', 'Player: Muchayi Norman - 4B FC', NULL, 1, '2026-02-23 18:38:33'),
(67, 'player_42_1772214017', 'dummy.player_42_1772214017@noemail.local', '', 'Player: Mudzinganyama Cain T - 04 FC', NULL, 1, '2026-02-27 17:40:17'),
(68, 'nehandaunited', 'nehanda@gmail.com', '$2y$12$I7OA2zc73D3OBFffWPI89.7e4XlV126oYZE3mX54SFYdGbJeQlSx6', 'Nehanda United FC', NULL, 1, '2026-02-28 17:46:34'),
(69, 'player_36_1772300972', 'dummy.player_36_1772300972@noemail.local', '', 'Player: Machona Message - 04 FC', NULL, 1, '2026-02-28 17:49:32'),
(70, 'player_49_1772301029', 'dummy.player_49_1772301029@noemail.local', '', 'Player: Gambakwe Lloyd - 04 FC', NULL, 1, '2026-02-28 17:50:29'),
(71, 'player_222_1772730732', 'dummy.player_222_1772730732@noemail.local', '', 'Player: Marozvevhu Nathan - 04 FC', NULL, 1, '2026-03-05 17:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `nav_items`
--

CREATE TABLE `nav_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Menu item name (e.g. "Home", "Matchday")',
  `link` varchar(255) DEFAULT NULL COMMENT 'Page URL (e.g. "index.php", "clubs.php")',
  `is_dropdown` tinyint(1) DEFAULT 0 COMMENT '1 = Has dropdown submenu',
  `parent_id` int(11) DEFAULT 0 COMMENT 'For dropdown items: parent menu ID',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Display order',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 = Visible in navbar',
  `icon_class` varchar(50) DEFAULT NULL COMMENT 'Optional Bootstrap icon class',
  `target_blank` tinyint(1) DEFAULT 0 COMMENT '1 = Open in new tab',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nav_items`
--

INSERT INTO `nav_items` (`id`, `name`, `link`, `is_dropdown`, `parent_id`, `sort_order`, `is_active`, `icon_class`, `target_blank`, `created_at`, `updated_at`) VALUES
(1, 'Home', 'index.php', 0, 0, 0, 1, 'bi-house', 0, '2025-11-22 13:43:09', '2025-12-11 18:35:50'),
(2, 'Matchday', NULL, 1, 0, 2, 1, 'bi-calendar-event', 0, '2025-11-22 13:43:09', '2025-11-22 13:43:09'),
(3, '04SL Clubs', 'clubs.php', 0, 2, 0, 1, 'bi-shield', 0, '2025-11-22 13:43:09', '2025-12-07 10:06:23'),
(4, 'About Us', NULL, 0, 0, 4, 1, 'bi-info-circle', 0, '2025-11-22 13:43:09', '2025-12-07 10:08:02'),
(7, '04SL Fixtures', 'fixtures.php', 0, 2, 1, 1, NULL, 0, '2025-11-22 13:43:09', '2025-12-07 10:06:31'),
(8, '04SL Results', 'results.php', 0, 2, 2, 1, NULL, 0, '2025-11-22 13:43:09', '2025-12-07 10:06:35'),
(9, 'Player Stats', 'player_stats.php', 0, 2, 4, 1, NULL, 0, '2025-11-22 13:43:09', '2025-12-11 18:38:03'),
(10, '04SL Table', 'league.php', 0, 2, 3, 1, NULL, 0, '2025-11-22 13:43:09', '2025-12-11 18:38:00'),
(11, 'News', 'news.php', 0, 0, 3, 1, 'bi bi-newspaper', 0, '2025-11-22 13:52:17', '2025-12-11 18:36:46'),
(12, 'Contact Us', 'contact_us.php', 0, 0, 7, 1, 'bi-info-circle', 0, '2025-11-23 12:52:37', '2025-11-23 12:52:37'),
(13, 'Gallery', 'gallery.php', 0, 2, 5, 1, '', 0, '2025-11-23 15:57:10', '2025-12-11 18:38:41'),
(15, 'Tournaments', 'tournaments.php', 1, 0, 3, 1, '', 0, '2025-12-07 09:37:16', '2025-12-11 18:36:16'),
(16, 'Sponsorships', 'sponsorships.php', 0, 4, 2, 1, '', 0, '2025-12-07 09:37:47', '2025-12-11 18:34:26'),
(17, 'Player Rules', 'league_rules.php', 0, 4, 3, 1, '', 0, '2025-12-07 09:39:00', '2025-12-11 18:34:17'),
(18, 'Overview', 'about-us.php', 0, 4, 0, 1, '', 0, '2025-12-07 10:08:34', '2025-12-07 10:08:34'),
(19, 'Training & Discipline', 'training.php', 0, 4, 1, 1, '', 0, '2025-12-07 10:09:31', '2025-12-07 10:09:31'),
(20, 'Awareness Campaign', 'awareness_campaign.php', 0, 4, 4, 1, '', 0, '2025-12-07 12:34:34', '2025-12-07 12:41:18');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `publish_date` date NOT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image`, `publish_date`, `is_published`, `created_at`) VALUES
(1, 'CHAPONGO CROWNED CHAMPIONS 2025', 'The 2025 Ward 24 Community League reached an electrifying conclusion today as Chapongo FC were crowned Champions after a thrilling Matchweek 18 victory over long-time league leaders Chilli Boyz FC.\r\n\r\nThe 2025 Ward 24 Community League reached an electrifying conclusion today as Chapongo FC were crowned Champions after a thrilling Matchweek 18 victory over long-time league leaders Chilli Boyz FC.\r\n\r\nChilli Boyz FC had dominated the standings throughout the season, sitting comfortably at the top with 33 points heading into the final week. Chapongo FC, trailing closely behind, needed nothing less than a win to claim the title — and they delivered in decisive fashion.\r\n\r\nIn a match filled with intensity and high stakes, Chapongo took the lead through Gomora Tadiwa, who found the back of the net after a clever assist from Muchionei Life. The pressure mounted on Chilli Boyz FC, who were desperate to respond, but a costly own goal sealed their fate and handed Chapongo a vital 2–0 victory.\r\n\r\nThe result pushed Chapongo FC to 36 points, overtaking Chilli Boyz FC at the top of the table and earning them the Ward 24 Community League Championship title for 2025.\r\n\r\nLeague officials have announced that the Ward 24 Community League Awards and Closing Ceremony will take place on 27 December 2025 in Village 4B, where Chapongo FC will officially be crowned champions and recognized for their incredible season.\r\n\r\nCongratulations to Chapongo FC — true champions of Ward 24!', '1762617049_FIXTURES.png', '2025-11-16', 1, '2025-11-16 12:23:41'),
(2, 'THRILLING FRIENDLY MATCH', 'Bikita East, Zimbabwe - In a spirited display of local soccer prowess and social advocacy, 04 FC emerged victorious over host Pamushana FC with a narrow 1-0 win in a friendly match held at Pamushana High School grounds today.\r\n\r\nBikita East, Zimbabwe - In a spirited display of local soccer prowess and social advocacy, 04 FC emerged victorious over host Pamushana FC with a narrow 1-0 win in a friendly match held at Pamushana High School grounds today. The event, attended by enthusiastic community members, school students, and local leaders, served as more than just a game—it was a powerful platform to combat pressing social issues while scouting emerging talents in the region.\r\n\r\nThe lone goal came in the first half, courtesy of 04 FC\'s striker Bhobho Tinotenda, who capitalized on a precise assist from teammate Pfumo Chacnois T. Tinotenda\'s clinical finish from inside the box silenced the home crowd momentarily, but the overall atmosphere remained celebratory, underscoring the match\'s friendly nature. Despite Pamushana FC\'s relentless efforts in the second half, including several close attempts on goal, 04 FC\'s defense held firm to secure the shutout victory.\r\n\r\nOrganized by local community groups in collaboration with the two teams, the match was themed around eliminating drug abuse, livestock theft, wildlife poaching, and early child marriages—challenges that have plagued rural areas in Bikita. \r\n\r\nBeyond the scoreboard, the event doubled as a talent identification initiative. Young athletes from both sides showcased impressive skills, from deft dribbling to tactical awareness, highlighting the untapped potential in Bikita East. \r\n\r\nThis friendly encounter not only provided entertainment but also reinforced community bonds in Bikita East. Organizers hope to make it a regular event, expanding its reach to inspire positive change across Zimbabwe\'s rural districts.', '1760891651_FIXTURES (5).png', '2025-11-01', 1, '2025-11-16 12:40:30'),
(4, 'UPDATED FIXTURE ADJUSTMENTS', 'The Ward 24 Community League Executive would like to inform all participating clubs and stakeholders of the following adjustments to our league fixtures: Matchweek 17: Beginning on 27 September 2025, only one game will be played per weekend.\r\n\r\nThe Ward 24 Community League Executive would like to inform all participating clubs and stakeholders of the following adjustments to our league fixtures:\r\n\r\nMatchweek 17:\r\nBeginning on 27 September 2025, only one game will be played per weekend. This adjustment has been made in consideration of our players who are Grade 7, Form 4, and Form 6 students, as they will be preparing for the upcoming October/November 2025 ZIMSEC examinations.\r\n\r\nFinal Matchweek 18:\r\nOn 2 November 2025, all five scheduled matches will be played on the same day, marking the conclusion of the current season.\r\n\r\nWe recognize the importance of balancing education and sports, and we believe this arrangement will allow our young players to focus on their studies while still participating in the league.\r\n\r\nFurther updates regarding other upcoming programs will be communicated in due course.\r\n\r\nWe sincerely thank you all for your understanding and cooperation. Your continued support is much appreciated.', '1758469624_FIXTURES (2).png', '2025-10-26', 1, '2025-11-16 12:44:09'),
(6, 'Terra Firma Super Cup Kicks Off', 'The much-anticipated Terra Firma Super Cup Tournament officially kicked off on 6 December 2025, bringing together four competitive community clubs in a thrilling football showdown set to run until 26 December 2025.\r\n\r\nThe tournament features 4B FC, Chishakwe V3 FC, Chilli Boyz FC, and Gedhe FC, all competing in a home-and-away round-robin format as they battle for glory, pride, and valuable prizes.\r\n\r\nOpening Match Results\r\nThe opening weekend delivered excitement and competitive football as the first two matches were played on 6 and 7 December:\r\n\r\n4B FC 0–0 Gedhe FC\r\nA tightly contested encounter saw both sides cancel each other out in a goalless draw, with solid defensive performances and disciplined goalkeeping on display.\r\n\r\nChilli Boyz FC 2–0 Chishakwe V3 FC\r\nChilli Boyz FC made an early statement of intent with a convincing victory, showcasing attacking flair and clinical finishing to secure all three points.\r\n\r\nUpcoming Fixtures:\r\nThe tournament continues with the return fixtures scheduled for the coming weekend:\r\nSaturday, 13 December 2025:\r\nChishakwe V3 FC vs Chilli Boyz FC\r\n\r\nSunday, 14 December 2025:\r\nGedhe FC vs 4B FC\r\n\r\nThese matches are expected to play a crucial role in shaping the final standings as teams push for qualification into the decisive stages of the competition.\r\n\r\nTournament Format & Finals Day\r\nAll match results are recorded in a standard league table, with final positions determining progression:\r\n3rd vs 4th place teams will contest the semi-final\r\n1st vs 2nd place teams will face off in the grand final\r\nBoth the semi-final and final will be played on 26 December 2025 at Village 4B, in what promises to be a festive football celebration. Community dignitaries and local leaders will be invited to witness the climax of the tournament.\r\n\r\nPrize Money & Awards:\r\nThe Terra Firma Super Cup offers attractive rewards to recognize both team and individual excellence:\r\nTeam Prizes\r\n🥇 1st Place: $100 + medals + trophy\r\n🥈 2nd Place: $90 + medals\r\n🥉 3rd Place: $70\r\n4️⃣ 4th Place: $50\r\n\r\nIndividual Awards:\r\n🏅 Top Goal Scorer\r\n🏅 Best Player of the Tournament\r\n🏅 Most Clean Sheets\r\n🏅 Best Coach of the Tournament\r\n🏅 All 3 Referees\r\n\r\nWith competitive football, community unity, and exciting rewards on the line, the Terra Firma Super Cup is shaping up to be a memorable tournament that celebrates local talent and sportsmanship while it amplifies the NO TO DRUG abuse campaign\r\nFootball fans are encouraged to come out in numbers and support their teams as the action continues.', 'img_693d62ee5f552.png', '2025-12-13', 1, '2025-12-13 12:34:09'),
(8, '04SL Sports Day Ends 2025 Season in Style', '04 SOCCER LEAGUE:\r\nChapongo FC Crowned League Champions & Chilli Boyz FC Take Runners-Up Spot\r\n\r\nVillage 4B, Manyuchi – The 26th of December 2025 will be remembered as a landmark day for football lovers in Bikita East as Village 4B hosted the grand 04 Soccer League Awards and a thrilling Terra Firma Super Cup showdown. Communities from all corners of the Bikita East gathered in huge numbers, creating a lively atmosphere filled with cheers, colour, excitement and celebration of local football talent.\r\n\r\nChapongo FC walked away as the 2025 Champions of the 04 Soccer League, securing their place in history, while the spirited Chilli Boyz FC claimed the runners-up title after a solid campaign.\r\n\r\nAs part of recognising exceptional talent across participating clubs, Best Player per Team awards were presented, each recipient receiving a brand-new pair of soccer boots. The honoured players were:\r\n\r\n* Bhobho Tinotenda (Chapongo FC)\r\n* Zvaipa Panganai (Chilli Boyz FC)\r\n* Pfumo Chacnois Ticharwa (4B FC)\r\n* Mufundirwa Believe (Chishakwe V3 FC)\r\n* Zvirimo Bernard (Gedhe FC)\r\n* Muchabarwa Theophelus (5 Stars FC)\r\n* Ganya Pardon (Dukuhwe FC)\r\n* Mawere Calvin (Mamutse FC)\r\n* Machaka Tadiwanashe (4A Vikings FC)\r\n* Chebhiri Garikai (Six FC)\r\nEach club also walked away with a brand-new soccer ball, an envelope with some tokens of appreciation as well as a brand-new soccer kit with 15 shirts, 15 shorts and 15 pairs of socks.\r\n\r\n---\r\nTERRA FIRMA SUPER CUP\r\nSponsored by Walter Gadzai of Village 4B, the Terra Firma Super League added intensity to the day with both semi-final and final matches played.\r\n\r\nIn the semi-final, 4B FC battled Chishakwe V3 FC, with Chishakwe securing a 2–1 victory thanks to a brilliant brace from Vengere Lenox.\r\n\r\nBefore the final kicked off, fans were treated to a nostalgic and entertaining Legends Match, where veteran players rolled back the years in a spirited encounter. The game ended 2–1, with the winning Legends team walking away with $30, while the runners-up received $20. The match brought laughter, memories and strong applause from supporters who enjoyed seeing their former heroes back on the field.\r\n\r\nLater, the main final saw Chilli Boyz FC clash with Gedhe FC, ending 1–1 in regulation time. With tension high, Chilli Boyz sealed victory 4–2 on penalties, claiming the Terra Firma Super League title to the delight of their fans.\r\n---\r\nIndividual Brilliance Shines\r\n\r\nAward presentations followed, with Vhengere Lenox of Chishakwe V3 FC becoming the star of the tournament after scooping:\r\n\r\n🏆 Golden Boot\r\n🏆 Best Player of the Tournament\r\n⚽ A hat-trick ball for his group stage performance against Chilli Boyz FC\r\n\r\nChilli Boyz goalkeeper Tarova Alfred earned the Golden Glove after an outstanding run of clean sheets.\r\nThe tournament referees walked away with full officiating uniform, a whistle and a pen and a $20 cash prize\r\nThe tournament winner Chilli Boyz FC walked away with $150, runners-up Gedhe FC $100, number 3 Chishakwe V3 FC $50 while the host 4B FC $30.\r\n\r\nAll honours were presented by Ward 24 Councillor Mr. Steven Chakaamba, who commended the level of talent and discipline shown throughout the league. The ceremony was smoothly managed by Masters of Ceremony Robert Gadzai and Sam Nechipani of 4A Vikings FC.\r\n\r\n\r\nSecurity & Atmosphere\r\n\r\nA special vote of thanks was extended to the Zimbabwe Republic Police Chikuku Branch for maintaining safety and order throughout the sporting event and awards ceremony.\r\n\r\nThe atmosphere was electric—filled with music, ululations, joy and unity as villagers mingled freely, celebrating the fruits of their labour throughout the year.\r\n\r\n\r\nFootball as a Tool for Development\r\n\r\nThe 04 Soccer League continues to play a vital role in social development within Bikita East. Its mission is to:\r\n\r\n* Fight drug and substance abuse\r\n* Reduce early child marriages\r\n* Combat livestock theft and wildlife poaching\r\n* Promote and expose young football talent in the community\r\n---\r\nThe event concluded with smiles, handshakes and celebration under the 4B skies. It was a perfect blend of sport, recognition and community spirit—a fitting end to an unforgettable 2025 football season.', 'img_694fea3f92d28.jpeg', '2025-12-27', 1, '2025-12-27 14:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive','Suspended','Transferred','Banned') DEFAULT 'Active',
  `position` varchar(10) NOT NULL DEFAULT '',
  `jersey_number` int(11) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `goals` int(11) DEFAULT 0,
  `assists` int(11) DEFAULT 0,
  `yellow_cards` int(11) DEFAULT 0,
  `red_cards` int(11) DEFAULT 0,
  `clean_sheets` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`id`, `club_id`, `name`, `photo`, `status`, `position`, `jersey_number`, `date_of_birth`, `nationality`, `goals`, `assists`, `yellow_cards`, `red_cards`, `clean_sheets`, `created_at`, `id_number`) VALUES
(16, 11, 'Kwazvo Definite', 'player_692b0e587ab46.jpg', 'Inactive', 'MID', 17, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(17, 11, 'Gwenzi Jeffrey', 'player_692b0dc868d60.jpg', 'Inactive', 'FWD', 10, '0000-00-00', 'Zimbabwean', 9, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(18, 11, 'Jenengene Norest', 'player_692b12d165c7f.jpg', 'Inactive', 'MID', 19, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(19, 11, 'Mutume Effort', 'player_692b0ffe9af90.jpg', 'Inactive', 'DEF', 81, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(20, 11, 'Chebhiri Garikai', 'player_692b0c6de6e07.jpg', 'Inactive', 'FWD', 9, '0000-00-00', 'Zimbabwean', 4, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(21, 11, 'Masuka Leonard', 'player_692b10a826c29.jpg', 'Inactive', 'DEF', 91, '0000-00-00', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(22, 11, 'Tetai Tanaka', 'player_692b1217dfe65.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 0, 1, 1, 0, 0, '2025-11-16 09:14:15', ''),
(23, 11, 'Muneseyi Tapiwa', 'player_692b12351dc7e.jpg', 'Inactive', 'DEF', 18, '0000-00-00', 'Zimbabwean', 6, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(24, 11, 'Hore Fungai', 'player_692b128cd4d6f.jpg', 'Inactive', 'DEF', 6, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(25, 11, 'Makina Joshua', 'player_692b1616daad4.jpg', 'Inactive', 'MID', 28, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(26, 11, 'Muchara Tawonga', 'player_692b0ef67a2a1.jpg', 'Inactive', 'MID', 8, '0000-00-00', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(27, 11, 'Mutiba Clemence', 'player_692b0f7f5c429.jpg', 'Inactive', 'DEF', 12, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(28, 11, 'Mutsomekwa Takudzwa', 'player_692b1419a071b.jpg', 'Inactive', 'DEF', 17, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(29, 9, 'Makwenjere Panashe', 'player_692b16509aecd.jpg', 'Inactive', 'DEF', 17, '0000-00-00', 'Zimbabwean', 2, 2, 0, 0, 0, '2025-11-16 09:14:15', ''),
(30, 23, 'Nhongo Prosper', 'player_692b108180a30.jpg', 'Active', 'FWD', 10, '1998-08-10', 'Zimbabwean', 16, 2, 0, 0, 0, '2025-11-16 09:14:15', '04-187487Q-04'),
(31, 9, 'Zvarevashe Tymon', 'player_692aff0f37c1e.jpg', 'Inactive', 'GK', 1, '0000-00-00', 'Zimbabwean', 2, 1, 3, 1, 1, '2025-11-16 09:14:15', ''),
(32, 9, 'Pfumai Challenge T', 'player_692b17e63c339.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 2, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(33, 9, 'Mufari Nyasha', 'player_692b0f8c7fa01.jpg', 'Inactive', 'MID', 63, '0000-00-00', 'Zimbabwean', 2, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(34, 9, 'Marko James', 'player_692b0eba352c4.jpg', 'Inactive', 'DEF', 15, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(35, 23, 'Mawere Calvin', 'player_692b10d23c476.jpg', 'Active', 'FWD', 7, '2008-02-28', 'Zimbabwean', 8, 3, 0, 0, 0, '2025-11-16 09:14:15', '04-2025168E-04'),
(36, 23, 'Machona Message', 'player_6997f77e621cb.jpg', 'Active', 'MID', 25, '2006-11-20', 'Zimbabwean', 0, 2, 0, 0, 0, '2025-11-16 09:14:15', '04-202400V-40'),
(37, 9, 'Mafuka Tinotenda', 'player_692b148741c03.jpg', 'Inactive', 'DEF', 27, '0000-00-00', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(38, 23, 'Mudzimureka Lawrence', 'player_692b14682ced7.jpg', 'Active', 'MID', 19, '2006-06-10', 'Zimbabwean', 4, 1, 1, 0, 0, '2025-11-16 09:14:15', '63-4067229J-07'),
(39, 9, 'Bandauko Luckmore', 'player_692abca15b848.jpg', 'Inactive', 'MID', 12, NULL, 'Zimbabwean', 4, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(40, 9, 'Chiuya Tafara', 'player_692b0c9ecc66e.jpg', 'Inactive', 'DEF', 12, '0000-00-00', 'Zimbabwean', 2, 0, 0, 1, 0, '2025-11-16 09:14:15', ''),
(41, 9, 'Zuwirai Obey', 'player_692b0d1c15b81.jpg', 'Inactive', 'MID', 51, '0000-00-00', 'Zimbabwean', 8, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(42, 23, 'Mudzinganyama Cain T', 'player_692b12e759f4c.jpg', 'Active', 'MID', 17, '2003-01-01', 'Zimbabwean', 2, 1, 0, 0, 0, '2025-11-16 09:14:15', '04-209210C-04'),
(43, 9, 'Mufunda Panashe G', 'player_692b1430b9b2d.jpg', 'Inactive', 'MID', 29, '0000-00-00', 'Zimbabwean', 6, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(44, 9, 'Mukanganwa Takudzwa G', 'player_692b13eb81c09.jpg', 'Inactive', 'DEF', 29, '0000-00-00', 'Zimbabwean', 0, 0, 2, 0, 0, '2025-11-16 09:14:15', ''),
(45, 9, 'Mberi Tapiwa', 'player_692b12553dede.jpg', 'Inactive', 'DEF', 17, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(46, 9, 'Pepereke Seth', 'player_692b17d7513de.jpg', 'Inactive', 'FWD', 34, '0000-00-00', 'Zimbabwean', 1, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(47, 9, 'Kufangowenyu Nickson', 'player_692b134b5a89f.jpg', 'Inactive', 'DEF', 18, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(48, 6, 'Mapuranga Alexious', 'player_692b13b036f8c.jpg', 'Inactive', 'MID', 26, '0000-00-00', 'Zimbabwean', 3, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(49, 23, 'Gambakwe Lloyd', 'player_69a082af87822.jpg', 'Active', 'MID', 16, '2004-04-12', 'Zimbabwean', 4, 1, 2, 0, 0, '2025-11-16 09:14:15', '04-2133158W-04'),
(50, 6, 'Zvirimo Bernard', 'player_692b0cf488572.jpg', 'Inactive', 'FWD', 9, '0000-00-00', 'Zimbabwean', 18, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(51, 6, 'Chaitwa Tichaona', 'player_692b0ba27caad.jpg', 'Inactive', 'DEF', 2, '0000-00-00', 'Zimbabwean', 4, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(52, 6, 'Hofisi Washington', 'player_692b116a063ef.jpg', 'Inactive', 'MID', 19, '0000-00-00', 'Zimbabwean', 0, 3, 0, 0, 0, '2025-11-16 09:14:15', ''),
(53, 6, 'Maedza Anderson', 'player_692b157c6c4db.jpg', 'Inactive', 'FWD', 17, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(54, 6, 'Tavafumisa Believe', 'player_692b12a0e870b.jpg', 'Inactive', 'MID', 11, '0000-00-00', 'Zimbabwean', 9, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(55, 6, 'White Nation', 'player_692b0fe04b239.jpg', 'Inactive', 'DEF', 5, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(56, 6, 'Mapuranga Morgan', 'player_692abbfc0e1d8.jpg', 'Inactive', 'GK', 1, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 6, '2025-11-16 09:14:15', ''),
(57, 6, 'Bingipinge Blessed', 'player_692b099f4c169.jpg', 'Inactive', 'FWD', 10, NULL, 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(58, 6, 'Mazarire Happy', 'player_692b0f3b37d26.jpg', 'Inactive', 'DEF', 3, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(59, 6, 'Gumisirai Blessed', 'player_692b0e15d06fa.jpg', 'Inactive', 'MID', 20, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(60, 6, 'Manema Tadiwanashe', 'player_692b1679f244f.jpg', 'Inactive', 'FWD', 8, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(61, 2, 'Gomora Tadiwa', 'player_692abe6b19af5.jpg', 'Inactive', 'FWD', 21, '0000-00-00', 'Zimbabwean', 9, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(62, 6, 'Muperi Thadious', 'player_692ac67e169f6.jpg', 'Inactive', 'MID', 55, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(63, 6, 'Sigauke Happyson', 'player_692b181572ad5.jpg', 'Inactive', 'MID', 17, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(64, 6, 'Mushava Tinos', 'player_692b10c6e7ecf.jpg', 'Inactive', 'MID', 5, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(65, 7, 'Gumireshe Client', 'player_692b0d9cb8b94.jpg', 'Inactive', 'MID', 15, '0000-00-00', 'Zimbabwean', 6, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(66, 7, 'Dzinokuvara Believe', 'player_692b0db762dad.jpg', 'Inactive', 'MID', 44, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(67, 7, 'Kuzondidini Martin', 'player_692b13777ab35.jpg', 'Inactive', 'FWD', 11, '0000-00-00', 'Zimbabwean', 7, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(68, 7, 'Ganya Pardon', 'player_692b10f2255c3.jpg', 'Inactive', 'FWD', 12, '0000-00-00', 'Zimbabwean', 0, 3, 0, 0, 0, '2025-11-16 09:14:15', ''),
(69, 7, 'Mazarire Admore', 'player_692b10e1c1cb1.jpg', 'Inactive', 'FWD', 12, '0000-00-00', 'Zimbabwean', 6, 1, 1, 0, 0, '2025-11-16 09:14:15', ''),
(70, 23, 'Chemhaka Given', 'player_69980592dc397.jpg', 'Active', 'MID', 1, '2010-11-11', 'Zimbabwean', 2, 0, 0, 0, 1, '2025-11-16 09:14:15', '00-000000C-04'),
(71, 7, 'Sinyerere Munyaradzi', 'player_692b15020f8ac.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(72, 7, 'Havamwe Aleck', 'player_692b112a3155b.jpg', 'Inactive', 'MID', 11, '0000-00-00', 'Zimbabwean', 10, 2, 0, 0, 0, '2025-11-16 09:14:15', ''),
(73, 7, 'Kuzondidini Ananias', 'player_692b0e24e4cac.jpg', 'Inactive', 'FWD', 18, '0000-00-00', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(74, 7, 'Chikomba Jameson', 'player_692b0bc97efe5.jpg', 'Inactive', 'MID', 6, '0000-00-00', 'Zimbabwean', 1, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(75, 7, 'Hore Promise', 'player_692b0dff5c544.jpg', 'Inactive', 'DEF', 17, '0000-00-00', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(76, 7, 'Bendezi Sailas', 'player_692b0984e6d1e.jpg', 'Inactive', 'FWD', 15, NULL, 'Zimbabwean', 0, 3, 1, 0, 0, '2025-11-16 09:14:15', ''),
(77, 7, 'Munaka Oscar', 'player_692b0f99ab341.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(78, 7, 'Marata Douglas', 'player_692b11950fd1f.jpg', 'Inactive', 'MID', 14, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(79, 7, 'Mudiwi Francis', 'player_692b13d49fb06.jpg', 'Inactive', 'DEF', 28, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(80, 7, 'Nematadzira Thomas', 'player_692b0fef369cd.jpg', 'Inactive', 'DEF', 69, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(81, 7, 'Mutete Scientist', 'player_692b18604aba6.jpg', 'Inactive', 'DEF', 38, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(82, 7, 'Taireveyi Bright', 'player_692b14dfea011.jpg', 'Inactive', 'MID', 44, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(83, 5, 'Munyika Taurai', 'player_692b10670db7c.jpg', 'Inactive', 'DEF', 6, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(84, 5, 'Zondai Tadiwa F', 'player_69a9d24090028.png', 'Active', 'MID', 5, '2001-09-25', 'Zimbabwean', 5, 2, 1, 0, 0, '2025-11-16 09:14:15', '13-316614E-07'),
(85, 23, 'Muvavarirwa Esau', 'player_69aad5f153469.jpg', 'Active', 'FWD', 11, '2003-08-11', 'Zimbabwean', 5, 3, 0, 0, 0, '2025-11-16 09:14:15', '04-209433V-04'),
(86, 5, 'Muchindireki Carlson', 'player_692b11e37f75a.jpg', 'Inactive', 'MID', 16, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(87, 5, 'Mufundirwa Refias', 'player_692b0e7f8f74c.jpg', 'Inactive', 'DEF', 3, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(88, 5, 'Raunda Tanyaradzwa', 'player_692b16c48236f.jpg', 'Inactive', 'FWD', 19, '0000-00-00', 'Zimbabwean', 4, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(89, 5, 'Makundidze Gift', 'player_69a9d1e902b52.png', 'Active', 'DEF', 4, '2002-07-31', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', '07-266232C-07'),
(90, 5, 'Hetisani Ashton', 'player_692abc2209386.jpg', 'Inactive', 'GK', 1, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 3, '2025-11-16 09:14:15', ''),
(91, 23, 'Muvavarirwa Philan', 'player_69aaf25a5820f.jpg', 'Active', 'MID', 19, '2007-10-11', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', '04-2024757H-04'),
(92, 5, 'Mufundirwa Believe', 'player_692b104345a88.jpg', 'Inactive', 'MID', 36, '0000-00-00', 'Zimbabwean', 1, 2, 1, 0, 0, '2025-11-16 09:14:15', ''),
(93, 5, 'Chiraye Tinashe', 'player_692b0bdeaa71f.jpg', 'Inactive', 'DEF', 2, '0000-00-00', 'Zimbabwean', 5, 2, 0, 0, 0, '2025-11-16 09:14:15', ''),
(94, 23, 'Muvavarirwa Allen', 'player_69aaba4640d80.jpg', 'Active', 'DEF', 15, '2005-09-24', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', '04-2024912B-04'),
(95, 5, 'Chahweta Likan', 'player_692b0b6647563.jpg', 'Inactive', 'MID', 15, '0000-00-00', 'Zimbabwean', 4, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(96, 5, 'Muvavarirwa Austin', 'player_692b1026a8e3d.jpg', 'Inactive', 'MID', 23, '0000-00-00', 'Zimbabwean', 1, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(97, 5, 'Mufundirwa Tatenda', 'player_692b100a10c4b.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 0, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(98, 5, 'Machipisa Witness', 'player_692b1159b9f48.jpg', 'Inactive', 'MID', 11, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(99, 5, 'Munyika Emmanuel', 'player_692b139a49b1f.jpg', 'Inactive', 'MID', 96, '0000-00-00', 'Zimbabwean', 0, 1, 1, 0, 0, '2025-11-16 09:14:15', ''),
(100, 5, 'Chahweta Victor R', 'player_692b0c35f24ff.jpg', 'Inactive', 'MID', 6, '0000-00-00', 'Zimbabwean', 4, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(101, 5, 'Honye Ngwadzai', 'player_692b11776cd51.jpg', 'Inactive', 'MID', 17, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(102, 5, 'Guzete Givemore', 'player_692b111bde746.jpg', 'Inactive', 'FWD', 17, '0000-00-00', 'Zimbabwean', 6, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(103, 5, 'Mbengo Collen', 'player_692b03b1a86e4.jpg', 'Inactive', 'MID', 15, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(104, 5, 'Kaitano Austin', 'player_692aff90ebb44.jpg', 'Inactive', 'GK', 35, '0000-00-00', 'Zimbabwean', 0, 0, 0, 0, 1, '2025-11-16 09:14:15', ''),
(105, 5, 'Phiri Silas', 'player_692b16debf814.jpg', 'Inactive', 'DEF', 26, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(106, 5, 'Kusakara Tinashe', 'player_692b0e37ca6fe.jpg', 'Inactive', 'MID', 57, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(107, 5, 'Chitsanza Thembelani', 'player_692b0c905b786.jpg', 'Inactive', 'MID', 11, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(108, 3, 'Sanangurai Samson', 'player_69936918df25c.png', 'Active', 'DEF', 6, '1988-09-20', 'Zimbabwean', 8, 1, 0, 0, 0, '2025-11-16 09:14:15', '04-163521Q-04'),
(109, 3, 'Shatirwa Darlington', 'player_699843cdd715c.png', 'Active', 'MID', 18, '1999-02-12', 'Zimbabwean', 10, 0, 0, 0, 0, '2025-11-16 09:14:15', '04-188921Z-04'),
(110, 23, 'Tarova Alfred', 'player_699815fd4c5de.jpg', 'Active', 'MID', 10, '2005-05-05', 'Zimbabwean', 4, 3, 1, 1, 1, '2025-11-16 09:14:15', '04-214627Q-04'),
(111, 3, 'Muwawa Tatenda', 'player_69921cf96e3b2.png', 'Active', 'MID', 17, '1995-04-24', 'Zimbabwean', 2, 3, 0, 1, 0, '2025-11-16 09:14:15', '04-176612V-07'),
(112, 3, 'Jongwe Simon', 'player_692b132345a57.jpg', 'Inactive', 'DEF', 6, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(113, 3, 'Eston Tanaka', 'player_692b0daab5f26.jpg', 'Inactive', 'FWD', 7, '0000-00-00', 'Zimbabwean', 10, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(114, 3, 'Tarova Abel', 'player_6991b6edbf502.png', 'Active', 'GK', 1, '1999-03-02', 'Zimbabwean', 0, 0, 1, 0, 6, '2025-11-16 09:14:15', '04-187492W-04'),
(115, 23, 'Chisiiwa Tawanda', 'player_69961f9bbea07.jpeg', 'Active', 'MID', 44, '2005-05-03', 'Zimbabwean', 0, 0, 2, 0, 0, '2025-11-16 09:14:15', '04-2002469G-04'),
(116, 3, 'Tarova Albert', 'player_6995da45409ae.jpeg', 'Active', 'DEF', 19, '1991-12-01', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', '04-163522R-04'),
(117, 3, 'Mafuta Emmanuel', 'player_692abc8416850.jpg', 'Inactive', 'GK', 44, '0000-00-00', 'Zimbabwean', 0, 0, 0, 0, 1, '2025-11-16 09:14:15', ''),
(118, 3, 'Tazara Tonderai', 'player_6995d712d465d.png', 'Active', 'DEF', 5, '2001-07-14', 'Zimbabwean', 0, 1, 1, 0, 0, '2025-11-16 09:14:15', '04-2008460T-04'),
(119, 3, 'Kutsanza Exavier', 'player_699f36e04947e.png', 'Active', 'DEF', 34, '2001-12-03', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', '07-2013115Z-44'),
(120, 3, 'Mufundirwa Kudakwashe', 'player_69919e3d5c8a7.png', 'Active', 'DEF', 27, '1998-07-01', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', '04-183794B-04'),
(121, 23, 'Zvaipa Darlington', 'player_69916a1c88fca.jpg', 'Active', 'FWD', 8, '2004-01-02', 'Zimbabwean', 8, 1, 0, 0, 0, '2025-11-16 09:14:15', '04-209257D-04'),
(122, 3, 'Maodzeke Tarisai', 'player_692b14798b378.jpg', 'Inactive', 'DEF', 17, '0000-00-00', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(123, 3, 'Paruvi Stiff', 'player_692b17c905e34.jpg', 'Inactive', 'MID', 29, '0000-00-00', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(124, 3, 'Chifamba Tapiwa', 'player_692abc3fe2f0e.jpg', 'Inactive', 'GK', 1, '0000-00-00', 'Zimbabwean', 0, 0, 0, 0, 1, '2025-11-16 09:14:15', ''),
(125, 3, 'Gatyara Wilson', 'player_699841a21820c.png', 'Active', 'DEF', 5, '2007-01-22', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', '04-2036453W-04'),
(126, 3, 'Neruwana Muziwa', 'player_692b132fa2a39.jpg', 'Inactive', 'DEF', 18, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(127, 3, 'Matadzira Peter', 'player_69919db1db270.png', 'Active', 'FWD', 51, '1992-06-04', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', '04-161979P-04'),
(128, 3, 'Tazara Brendon', 'player_692ac54926657.jpg', 'Inactive', 'MID', 50, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(129, 3, 'Fururai Godknows', 'player_692b0cc3b007b.jpg', 'Inactive', 'DEF', 19, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(130, 3, 'Hungwe Prince T', 'player_692b130cb3b71.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(131, 23, 'Zibute Smart', 'player_692b0fd421abb.jpg', 'Active', 'FWD', 8, '2004-03-01', 'Zimbabwean', 4, 0, 1, 0, 0, '2025-11-16 09:14:15', '04-006238F-04'),
(132, 23, 'Bhobho Tinotenda', 'player_692b0992ba0dd.jpg', 'Active', 'FWD', 10, '2006-03-03', 'Zimbabwean', 16, 2, 0, 0, 0, '2025-11-16 09:14:15', '00-000000X-00'),
(133, 2, 'Mukonza Tatenda', 'player_692b1246425ce.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 0, 1, 2, 0, 0, '2025-11-16 09:14:15', ''),
(134, 2, 'Rukai Patson', 'player_692b15e322759.jpg', 'Inactive', 'DEF', 19, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(135, 2, 'Dzinokuvara Damiano', 'player_692b0c11689a3.jpg', 'Inactive', 'MID', 55, '0000-00-00', 'Zimbabwean', 2, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(136, 2, 'Chirochino Collen', 'player_692b0becb05f5.jpg', 'Inactive', 'MID', 6, '0000-00-00', 'Zimbabwean', 2, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(137, 2, 'Mukonza Tanaka', 'player_692b103687f3f.jpg', 'Inactive', 'DEF', 61, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(138, 2, 'Bvirani Mike', 'player_692b0a295e4d0.jpg', 'Inactive', 'DEF', 5, '0000-00-00', 'Zimbabwean', 11, 4, 0, 0, 0, '2025-11-16 09:14:15', ''),
(139, 2, 'Tongei Advance', 'player_692b11f616cfb.jpg', 'Inactive', 'MID', 14, '0000-00-00', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(140, 23, 'Kashambe Zvikomborero', 'player_692abb93be392.jpg', 'Active', 'GK', 10, '2010-02-10', 'Zimbabwean', 0, 0, 0, 0, 2, '2025-11-16 09:14:15', '00-000000X-00'),
(141, 2, 'Sithole Knowledge', 'player_692b14f28e981.jpg', 'Inactive', 'MID', 31, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(142, 2, 'Mandivenga Nathan', 'player_692b1661110b0.jpg', 'Inactive', 'DEF', 16, '0000-00-00', 'Zimbabwean', 2, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(143, 2, 'Marima Tadiwa', 'player_692b138a332b8.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 1, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(144, 2, 'Bvekwa Aaron', 'player_692b09d8d5512.jpg', 'Inactive', 'FWD', 9, NULL, 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(145, 2, 'Muchionei Life', 'player_692b0ee3c5c29.jpg', 'Inactive', 'MID', 6, '0000-00-00', 'Zimbabwean', 6, 7, 0, 0, 0, '2025-11-16 09:14:15', ''),
(146, 2, 'Mlambo Brian', 'player_692b113770b71.jpg', 'Inactive', 'MID', 11, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(147, 23, 'Nyamujara Tarisai', 'player_692b0e8e2826a.jpg', 'Active', 'MID', 44, '2003-09-23', 'Zimbabwean', 0, 1, 1, 0, 0, '2025-11-16 09:14:15', '63-234559V-50'),
(148, 2, 'Ngorima Elvis', 'player_692ac7cbd5358.jpg', 'Inactive', 'GK', 22, '0000-00-00', 'Zimbabwean', 0, 0, 0, 0, 2, '2025-11-16 09:14:15', ''),
(149, 2, 'Makuto Newman', 'player_692b14acad78e.jpg', 'Inactive', 'MID', 19, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(150, 2, 'Machinga Romeo W', 'player_692b1569a9206.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(151, 8, 'Zinyarimwe Morgan', 'player_692b0fb8bcde7.jpg', 'Inactive', 'MID', 16, '0000-00-00', 'Zimbabwean', 2, 0, 3, 0, 0, '2025-11-16 09:14:15', ''),
(152, 8, 'Maziveyi Prosper', 'player_692b10ba175ed.jpg', 'Inactive', 'FWD', 8, '0000-00-00', 'Zimbabwean', 5, 3, 0, 0, 0, '2025-11-16 09:14:15', ''),
(153, 8, 'Muchiziyi Takudzwa', 'player_692abc6c0978d.jpg', 'Inactive', 'GK', 30, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 1, '2025-11-16 09:14:15', ''),
(154, 8, 'Muchabarwa Theophilus', 'player_692b12f829785.jpg', 'Inactive', 'FWD', 9, '0000-00-00', 'Zimbabwean', 2, 2, 2, 0, 0, '2025-11-16 09:14:15', ''),
(155, 8, 'Manziyo Last', 'player_692b168cca9e7.jpg', 'Inactive', 'DEF', 16, '0000-00-00', 'Zimbabwean', 3, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(156, 23, 'Maziveyi Tadiwa', 'player_6998af3334c0b.png', 'Active', 'MID', 5, '2008-04-22', 'Zimbabwean', 6, 0, 0, 0, 0, '2025-11-16 09:14:15', '00-000000X-00'),
(157, 23, 'Tazvikonewa Godfrey', 'player_699569b76a3b2.png', 'Active', 'DEF', 4, '1992-04-16', 'Zimbabwean', 7, 1, 0, 0, 0, '2025-11-16 09:14:15', '04-163819P-04'),
(158, 8, 'Muchabarwa Clariance', 'player_692b11478b01e.jpg', 'Inactive', 'MID', 11, '0000-00-00', 'Zimbabwean', 2, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(159, 23, 'Sigauke Prince', 'player_6994935d41c09.jpg', 'Active', 'GK', 1, '2007-12-07', 'Zimbabwean', 0, 0, 0, 0, 5, '2025-11-16 09:14:15', '04-221798K-07'),
(160, 8, 'Maziveyi Tadiwa_old', 'player_692b0f6d3f005.jpg', 'Inactive', 'DEF', 81, NULL, 'Zimbabwean', 0, 0, 2, 0, 0, '2025-11-16 09:14:15', ''),
(161, 8, 'Makaya Ndikudzeyi', 'player_692b163a64622.jpg', 'Inactive', 'DEF', 27, '0000-00-00', 'Zimbabwean', 4, 0, 0, 1, 0, '2025-11-16 09:14:15', ''),
(162, 8, 'Sigauke George', 'player_692b15ac24bad.jpg', 'Inactive', 'DEF', 35, '0000-00-00', 'Zimbabwean', 2, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(163, 8, 'Mlambo Senior', 'player_69ac742dc2e56.png', 'Active', 'MID', 18, '2006-03-10', 'Zimbabwean', 4, 1, 0, 0, 0, '2025-11-16 09:14:15', '00-000000X-00'),
(164, 8, 'Bvekwa Tapiwa', 'player_69a9c894b4bcd.png', 'Active', 'MID', 11, '2005-11-10', 'Zimbabwean', 4, 3, 0, 0, 0, '2025-11-16 09:14:15', '07-292001M-04'),
(165, 8, 'Mlambo Mathius', 'player_69a9cb31a8ff0.png', 'Active', 'FWD', 11, '2004-05-07', 'Zimbabwean', 6, 1, 0, 0, 0, '2025-11-16 09:14:15', '00-000000X-00'),
(166, 8, 'Mukucha Teererai', 'player_692b105101fd7.jpg', 'Inactive', 'MID', 61, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(167, 8, 'Majozi Nelson', 'player_69ac74c42204f.png', 'Active', 'MID', 51, '1999-11-17', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', '04-195641D-04'),
(168, 23, 'Pfumo Chacnois T', 'player_6985cf63c12d0.png', 'Active', 'FWD', 11, '2003-09-03', 'Zimbabwean', 8, 3, 0, 0, 0, '2025-11-16 09:14:15', '07-2005266S-04'),
(169, 4, 'Kyle Neymar', 'player_692b1558c8011.jpg', 'Inactive', 'FWD', 16, '0000-00-00', 'Zimbabwean', 10, 1, 1, 0, 0, '2025-11-16 09:14:15', ''),
(170, 23, 'Chakamba Samuel', 'player_6985daa87fa63.png', 'Active', 'DEF', 3, '2007-02-06', 'Zimbabwean', 2, 3, 0, 0, 0, '2025-11-16 09:14:15', '00-000000S-00'),
(171, 4, 'Muchayi Norman', 'player_699c9e8847ccc.jpg', 'Active', 'MID', 8, '2004-12-02', 'Zimbabwean', 6, 4, 1, 0, 0, '2025-11-16 09:14:15', '04-2014944R-04'),
(172, 4, 'Chihowa Innocent', 'player_692b0c0191d2d.jpg', 'Inactive', 'DEF', 3, '0000-00-00', 'Zimbabwean', 10, 2, 0, 0, 0, '2025-11-16 09:14:15', ''),
(173, 4, 'Nedombwe Manners', 'player_692b17a885b3e.jpg', 'Inactive', 'DEF', 17, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(174, 23, 'Chizema Allen', 'player_692abb104a3bb.jpg', 'Active', 'GK', 1, '2008-05-15', 'Zimbabwean', 0, 0, 0, 0, 8, '2025-11-16 09:14:15', '00-093930X00'),
(175, 4, 'Murove Joseph', 'player_692b16fa30688.jpg', 'Inactive', 'MID', 11, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(176, 4, 'Mudziwevhu Amos', 'player_699d632d74064.jpeg', 'Active', 'MID', 17, '1998-11-17', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', '66-098182Z=04'),
(177, 4, 'Munoda Pearson', 'player_692b10196f4bf.jpg', 'Inactive', 'MID', 8, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(178, 4, 'Dambwa Panashe', 'player_692b0cafb38e5.jpg', 'Inactive', 'MID', 33, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(179, 4, 'Makaya Nyasha', 'player_692b0e70f25b8.jpg', 'Inactive', 'DEF', 3, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(180, 4, 'Pfumo Knowledge', 'player_692b15929586e.jpg', 'Inactive', 'MID', 16, '0000-00-00', 'Zimbabwean', 4, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(181, 4, 'Charamba Malon', 'player_692b0bb2d7540.jpg', 'Inactive', 'DEF', 2, '0000-00-00', 'Zimbabwean', 2, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(182, 23, 'Pfumo Anyway', 'player_699c7471e693d.png', 'Active', 'MID', 17, '2009-07-09', 'Zimbabwean', 5, 4, 0, 0, 0, '2025-11-16 09:14:15', ''),
(183, 4, 'Mutimukulu Patrick', 'player_692b174e066ad.jpg', 'Inactive', 'MID', 34, '0000-00-00', 'Zimbabwean', 0, 2, 0, 1, 0, '2025-11-16 09:14:15', ''),
(184, 4, 'Munguri Landela', 'player_692b0f5742e46.jpg', 'Inactive', 'GK', 3, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(185, 4, 'Thayi Feckson', 'player_692b1204e95ab.jpg', 'Inactive', 'MID', 19, '0000-00-00', 'Zimbabwean', 6, 1, 1, 0, 0, '2025-11-16 09:14:15', ''),
(186, 4, 'Ndawani Maxwell Jnr', 'player_692b0f18e9d0c.jpg', 'Inactive', 'DEF', 2, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(187, 4, 'Taisireva Desire', 'player_692b145aae8a3.jpg', 'Inactive', 'MID', 28, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(188, 10, 'Nemadire Blessed', 'player_692b0fa9395c1.jpg', 'Inactive', 'MID', 55, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(189, 10, 'Bvekwa Elson', 'player_692b09e56d8cb.jpg', 'Inactive', 'FWD', 7, NULL, 'Zimbabwean', 10, 5, 0, 0, 0, '2025-11-16 09:14:15', ''),
(190, 10, 'Machaka Tadiwanashe', 'player_692b140a8986a.jpg', 'Inactive', 'FWD', 13, '0000-00-00', 'Zimbabwean', 2, 4, 0, 0, 0, '2025-11-16 09:14:15', ''),
(191, 10, 'Chaguma Takunda', 'player_692ad41d42eb3.jpg', 'Inactive', 'GK', 25, '0000-00-00', 'Zimbabwean', 0, 0, 0, 0, 1, '2025-11-16 09:14:15', ''),
(192, 10, 'Chaguma Munyaradzi', 'player_692b0a8c9ad12.jpg', 'Inactive', 'DEF', 6, '0000-00-00', 'Zimbabwean', 4, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(193, 10, 'Kutsanza Delight', 'player_692b0e468cc9f.jpg', 'Inactive', 'DEF', 16, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(194, 3, 'Zindoga Casper', 'player_699842287fb2c.png', 'Active', 'DEF', 3, '1999-09-29', 'Zimbabwean', 5, 0, 0, 0, 0, '2025-11-16 09:14:15', '63-2839514L-07'),
(195, 10, 'Muchara Tadiwanashe', 'player_692b0ed140a32.jpg', 'Inactive', 'MID', 72, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(196, 10, 'Mawonga Lee', 'player_692b15f33018d.jpg', 'Inactive', 'DEF', 36, '0000-00-00', 'Zimbabwean', 0, 0, 0, 1, 0, '2025-11-16 09:14:15', ''),
(197, 10, 'Mavenga Brighton', 'player_692af74909158.jpg', 'Inactive', 'MID', 36, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(198, 10, 'Chakaamba Fanuel', 'player_692b0b974d6a1.jpg', 'Inactive', 'DEF', 4, '0000-00-00', 'Zimbabwean', 4, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(199, 10, 'Chaguma Chomunorwa', 'player_692b0a74bebdf.jpg', 'Inactive', 'MID', 5, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(200, 10, 'Marozvevhu Francis', 'player_692b133d71855.jpg', 'Inactive', 'MID', 13, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(201, 10, 'Bvekwa Artwel', 'player_692b09c889789.jpg', 'Inactive', 'MID', 8, NULL, 'Zimbabwean', 1, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(202, 10, 'Muromo Blessed', 'player_692b055505a87.jpg', 'Inactive', 'GK', 11, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 1, '2025-11-16 09:14:15', ''),
(203, 10, 'Ngorima Brighton', 'player_692acb97672fc.jpg', 'Inactive', 'GK', 1, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 1, '2025-11-16 09:14:15', ''),
(204, 10, 'Chakawanei Romeo', 'player_69a9cff54bf23.png', 'Inactive', 'MID', 15, '2003-01-15', 'Zimbabwean', 3, 1, 0, 0, 0, '2025-11-16 09:14:15', '75-2044289T-22'),
(205, 10, 'Chaguma Charles', 'player_692b0a3ad37b1.jpg', 'Inactive', 'DEF', 2, '0000-00-00', 'Zimbabwean', 1, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(206, 10, 'Muchazora Takunda', 'player_692b0f29b6e04.jpg', 'Inactive', 'DEF', 44, '0000-00-00', 'Zimbabwean', 2, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(207, 10, 'Makanda Panashe', 'player_692b16052e9ac.jpg', 'Inactive', 'DEF', 25, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(208, 10, 'Kudauta Andrew', 'player_692b14bf7174f.jpg', 'Inactive', 'MID', 16, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(209, 10, 'Mawonga Shefta', 'player_692b136246866.jpg', 'Inactive', 'MID', 18, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(210, 10, 'Muchazora Anyway', 'player_692b0f0295c83.jpg', 'Inactive', 'DEF', 4, '0000-00-00', 'Zimbabwean', 2, 0, 0, 0, 0, '2025-11-16 09:14:15', ''),
(211, 10, 'Nemadire Maxwell', 'player_692b11b74d5d3.jpg', 'Inactive', 'MID', 13, '0000-00-00', 'Zimbabwean', 0, 0, 1, 0, 0, '2025-11-16 09:14:15', ''),
(212, 10, 'Chitombo Alios', 'player_692b0c7e7162f.jpg', 'Inactive', 'MID', 41, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(213, 10, 'Nyakudya Believe', 'player_692b187e75bc8.jpg', 'Inactive', 'FWD', 8, '0000-00-00', 'Zimbabwean', 0, 1, 0, 0, 0, '2025-11-16 09:14:15', ''),
(214, 8, 'Manziyo Manziyo', 'player_69a9ca7c280ce.png', 'Active', 'MID', 16, '1988-10-10', NULL, 1, 0, 0, 0, 0, '2025-11-29 10:02:03', '04-147508K-07'),
(215, 11, 'Hore Joseph', 'player_692ad81059ecc.jpg', 'Inactive', 'MID', 58, NULL, NULL, 1, 0, 0, 0, 0, '2025-11-29 11:25:04', ''),
(216, 11, 'Mandehwe Destiny', 'player_692ad89a80a52.jpg', 'Inactive', 'DEF', 85, NULL, NULL, 0, 0, 0, 0, 0, '2025-11-29 11:27:22', ''),
(217, 11, 'Jenengene Allen', 'player_692af6aa00ad6.jpg', 'Inactive', 'GK', 1, NULL, NULL, 0, 0, 0, 0, 0, '2025-11-29 13:35:38', ''),
(218, 5, 'Vhengere Lenox', 'player_693d7c2c69116.jpg', 'Inactive', 'MID', 54, NULL, NULL, 0, 0, 0, 0, 0, '2025-12-13 14:46:04', ''),
(219, 5, 'Makause Thinkwell', 'player_693d7ce198f28.jpg', 'Inactive', 'MID', 15, NULL, NULL, 0, 0, 0, 0, 0, '2025-12-13 14:49:05', ''),
(220, 4, 'Chihowa Samson', 'player_693da4b3ea236.png', 'Inactive', 'FWD', 14, NULL, NULL, 0, 0, 0, 0, 0, '2025-12-13 17:38:59', ''),
(221, 3, 'Tarova Lloyd', 'player_693ed172f3eb7.jpg', 'Inactive', 'MID', 5, NULL, NULL, 0, 0, 0, 0, 0, '2025-12-14 15:02:11', ''),
(222, 23, 'Marozvevhu Nathan', 'player_69a9b0cf1938d.jpg', 'Active', 'DEF', 19, '2005-11-16', NULL, 0, 0, 0, 0, 0, '2025-12-29 06:44:39', '00-000000X-00'),
(223, 8, 'Manziyo Hardlife', '', 'Inactive', 'MID', 11, '2005-11-16', NULL, 0, 0, 0, 0, 0, '2026-01-08 13:24:10', '00-000000X-00'),
(224, 23, 'Mutsomekwa Atanas', 'player_698dc69db916d.png', 'Active', 'DEF', 4, '1998-12-05', NULL, 0, 0, 0, 0, 0, '2026-02-12 12:25:01', '04-205737C-04'),
(225, 3, 'Bvonyongwa Admire', 'player_69919d1cde15a.png', 'Active', 'DEF', 15, '2002-04-14', NULL, 0, 0, 0, 0, 0, '2026-02-15 10:17:00', '04-210683-D-04'),
(226, 3, 'Mutsetsi Sonboy', 'player_69919ee5c4974.png', 'Active', 'MID', 17, '1995-08-01', NULL, 0, 0, 0, 0, 0, '2026-02-15 10:24:37', '04-171251S-04'),
(227, 3, 'Mareni Gift', 'player_69919f661e962.png', 'Active', 'FWD', 15, '2002-01-12', NULL, 0, 0, 0, 0, 0, '2026-02-15 10:26:46', '04-197413E-04'),
(228, 23, 'Zvaipa Panganai', 'player_699499a96b8a8.png', 'Active', 'DEF', 2, '2005-12-07', NULL, 0, 0, 0, 0, 0, '2026-02-17 16:39:05', '04-2030861S-04'),
(229, 24, 'Magadu Cleyton', 'player_6997deebcb6be.png', 'Active', 'MID', 12, '2002-05-26', NULL, 0, 0, 0, 0, 0, '2026-02-20 04:11:23', '34-146787X-34'),
(230, 24, 'Makombe Desire', 'player_6997f1d84774f.jpg', 'Active', 'DEF', 12, '2004-12-12', NULL, 0, 0, 0, 0, 0, '2026-02-20 05:32:08', '63-345159Q-04'),
(231, 23, 'Chenerwi Alexio', 'player_69980604671e3.jpg', 'Active', 'FWD', 36, '2000-09-28', NULL, 0, 0, 0, 0, 0, '2026-02-20 06:58:12', '04-192925B-04'),
(232, 3, 'Sengamai Ronald', 'player_699841e661c2b.png', 'Active', 'MID', 11, '2002-04-09', NULL, 0, 0, 0, 0, 0, '2026-02-20 11:13:42', '07-991641W-07'),
(233, 24, 'Mudikuwana Abisha', 'player_69984983e80b3.jpg', 'Active', 'FWD', 9, '2007-09-16', NULL, 0, 0, 0, 0, 0, '2026-02-20 11:46:11', '04-2016647S-04'),
(234, 24, 'Munasi Prince', 'player_6998ad42ef44b.png', 'Active', 'DEF', 15, '2008-01-15', NULL, 0, 0, 0, 0, 0, '2026-02-20 18:44:38', '04-2018837S-04'),
(235, 24, 'Mudikiwanda Simon', 'player_6998ad5b95e09.png', 'Active', 'MID', 15, '2010-04-15', NULL, 0, 0, 0, 0, 0, '2026-02-20 18:45:52', '04-2017729T-04'),
(236, 24, 'Mawere Washington', 'player_6998ad36dd9fb.png', 'Active', 'FWD', 10, '2006-11-17', NULL, 0, 0, 0, 0, 0, '2026-02-20 18:47:14', '04-220907R-04'),
(237, 23, 'Muchabarwa Andrew', 'player_6998aeb307354.png', 'Active', 'DEF', 15, '2008-03-15', NULL, 0, 0, 0, 0, 0, '2026-02-20 18:57:55', '04-286605R-04'),
(238, 24, 'Mabhinya Gerald', 'player_69996658ef826.jpg', 'Active', 'MID', 36, '2007-05-12', NULL, 0, 0, 0, 0, 0, '2026-02-21 08:01:28', '00-000000C-04'),
(239, 24, 'Chirochangu Antony', 'player_699def2b12d72.png', 'Active', 'DEF', 16, '2004-09-24', NULL, 0, 0, 0, 0, 0, '2026-02-24 18:34:19', '04-206126A-04'),
(240, 24, 'Taguta Wellington', 'player_699f471c6f4ff.png', 'Active', 'DEF', 55, '2004-11-22', NULL, 0, 0, 0, 0, 0, '2026-02-25 19:01:48', '04-210311Z-04'),
(241, 23, 'Mutingwende	Lerato', 'player_69a73ca60cef9.png', 'Active', 'DEF', 15, '2015-05-02', NULL, 0, 0, 0, 0, 0, '2026-02-28 17:44:45', '00-000000X-00'),
(242, 8, 'Manziyo Evidence', 'player_69a9c95a142fe.png', 'Active', 'DEF', 5, '2009-10-16', NULL, 0, 0, 0, 0, 0, '2026-03-05 18:20:10', '00-000000X-00'),
(243, 8, 'Mutigore Tapiwa', 'player_69a9cacecc371.png', 'Active', 'FWD', 16, '2009-09-23', NULL, 0, 0, 0, 0, 0, '2026-03-05 18:21:34', '07-289909N-07'),
(244, 5, 'Mukumiri Tinashe', 'player_69a9d076931af.png', 'Active', 'MID', 62, '2004-08-18', NULL, 0, 0, 0, 0, 0, '2026-03-05 18:50:30', '07-272990Y-07'),
(245, 5, 'Chirume Marvelous', 'player_69a9d0ff0ae97.png', 'Active', 'DEF', 15, '2002-04-18', NULL, 0, 0, 0, 0, 0, '2026-03-05 18:52:47', '07-264252A-07'),
(246, 5, 'Shamidho T Alexio', 'player_69a9d15136864.png', 'Active', 'FWD', 13, '2007-11-30', NULL, 0, 0, 0, 0, 0, '2026-03-05 18:54:09', '07-2056819-P-07'),
(247, 8, 'Mutigore Liberty', 'player_69a9d2adf3147.png', 'Active', 'FWD', 13, '1999-12-12', NULL, 0, 0, 0, 0, 0, '2026-03-05 18:59:57', '00-000000X-00'),
(248, 23, 'Mufundirwa D Henry', 'player_69aabf1a148be.jpg', 'Active', 'FWD', 35, '2003-10-24', NULL, 0, 0, 0, 0, 0, '2026-03-06 11:48:42', '04-205301D-04'),
(249, 24, 'Munapo Blessing', 'player_69ac766d592d7.png', 'Active', 'FWD', 15, '2009-04-14', NULL, 0, 0, 0, 0, 0, '2026-03-07 19:01:49', '04-2029386P-04');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key_name`, `value`, `updated_at`) VALUES
(1, 'league_name', '04 SOCCER LEAGUE', '2025-11-23 12:46:52');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'smtp_host', 'quark.aserv.co.za', '2025-11-28 19:07:44'),
(2, 'smtp_port', '465', '2025-11-28 19:39:27'),
(3, 'smtp_username', 'info@04sl.online', '2025-11-28 20:00:55'),
(4, 'smtp_password', 'Rhema@20201', '2025-11-23 15:41:52'),
(5, 'smtp_from_email', 'info@04sl.online', '2025-11-28 20:01:03'),
(6, 'smtp_from_name', '04 Soccer League', '2025-11-23 15:41:52'),
(7, 'smtp_encryption', 'ssl', '2025-11-23 15:41:52');

-- --------------------------------------------------------

--
-- Table structure for table `slideshow_images`
--

CREATE TABLE `slideshow_images` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slideshow_images`
--

INSERT INTO `slideshow_images` (`id`, `filename`, `caption`, `alt_text`, `sort_order`, `is_active`, `created_at`) VALUES
(19, 'img_6928b4a903c60.png', 'TOURNAMENT', 'TERRA FIRMA 2025', 1, 0, '2025-11-27 19:35:14'),
(20, 'img_6934656ada690.jpg', 'TERRA FIRMA', 'GEDHE FC vs 4B FC', 2, 1, '2025-12-06 17:18:34'),
(21, 'img_6934659018d14.jpg', 'TERRA FIRMA', 'GEDHE FC vs 4B FC', 3, 1, '2025-12-06 17:19:12'),
(22, 'img_6937f8a480957.jpeg', 'TOURNAMENT', 'DHASINDO 2025', 0, 0, '2025-12-09 10:23:32'),
(23, 'img_695fb0d72fc76.jpeg', 'Legends', '', 0, 1, '2026-01-08 13:27:51'),
(24, 'img_695fb0ec3d4e5.jpeg', 'Presentations', '', 0, 1, '2026-01-08 13:28:12'),
(25, 'img_695fb105d4374.jpeg', 'Champions', '', 0, 1, '2026-01-08 13:28:37'),
(26, 'img_695fb1209051b.jpeg', 'Officials', '', 0, 1, '2026-01-08 13:29:04'),
(27, 'img_695fb133b67fd.jpeg', '4B FC', '', 0, 1, '2026-01-08 13:29:23');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` int(11) NOT NULL,
  `platform_name` varchar(50) NOT NULL,
  `icon_class` varchar(100) NOT NULL,
  `url` varchar(500) NOT NULL,
  `display_in_header` tinyint(1) DEFAULT 0,
  `display_in_footer` tinyint(1) DEFAULT 0,
  `display_in_contact` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`id`, `platform_name`, `icon_class`, `url`, `display_in_header`, `display_in_footer`, `display_in_contact`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'Facebook', 'bi bi-facebook', 'https://web.facebook.com/profile.php?id=61574244569467', 1, 1, 1, 1, 1, '2025-11-22 15:08:34'),
(2, 'WhatsApp', 'bi bi-whatsapp', 'https://wa.me/27652788539', 1, 1, 1, 1, 2, '2025-11-22 15:14:28'),
(3, 'Youtube', 'bi bi-youtube', 'https://www.youtube.com/@WARD24COMMUNITYLEAGUE', 1, 0, 0, 1, 3, '2025-11-22 16:06:57'),
(4, 'Location', 'bi-geo-alt-fill', 'https://maps.app.goo.gl/fjknhGTG4GqRA24t6', 0, 0, 1, 1, 4, '2025-11-23 16:42:11'),
(5, 'Email', 'bi-envelope-fill', 'mailto:info@04sl.online', 0, 0, 1, 1, 5, '2025-11-23 16:45:49'),
(6, 'Map', 'bi-globe-asia-australia', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3393.86873154834!2d32.017934674850984!3d-19.951902838990215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1ed2edde59f2188b%3A0x4d0549bea2fa117f!2sMadzivire%20Primary%20School!5e1!3m2!1sen!2sza!4v1763908871350!5m2!1sen!2sza', 0, 0, 1, 1, 6, '2025-11-23 16:59:53'),
(7, 'Youtube_Link_01', 'bi bi-youtube', 'https://youtu.be/E-YaGnO7dQs', 0, 0, 0, 0, 7, '2025-11-24 19:19:33'),
(8, 'Youtube_Link_02', 'bi bi-youtube', 'https://youtu.be/tmzzEqVqp0A', 0, 0, 0, 1, 8, '2025-11-24 19:20:31'),
(9, 'Youtube_Link_03', 'bi bi-youtube', 'https://youtu.be/mGk3gS0o-Pw', 0, 0, 0, 0, 9, '2025-11-24 19:24:48'),
(10, 'Youtube_Link_04', 'bi-info-youtube', 'https://youtu.be/7ZLlhlc0ygM', 0, 0, 0, 1, 10, '2025-12-27 16:35:36');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('manager','coach','referee','assistant_coach') NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tournament_assists`
--

CREATE TABLE `tournament_assists` (
  `goal_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournament_assists`
--

INSERT INTO `tournament_assists` (`goal_id`, `player_id`) VALUES
(12, 111),
(14, 219),
(15, 219),
(18, 111),
(23, 48),
(24, 48);

-- --------------------------------------------------------

--
-- Table structure for table `tournament_cards`
--

CREATE TABLE `tournament_cards` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `card_type` enum('yellow','red') NOT NULL,
  `minute` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournament_cards`
--

INSERT INTO `tournament_cards` (`id`, `match_id`, `player_id`, `card_type`, `minute`, `created_at`) VALUES
(5, 8, 51, 'yellow', 58, '2025-12-06 16:02:12'),
(6, 8, 49, 'yellow', 88, '2025-12-06 16:02:28'),
(7, 8, 185, 'yellow', 84, '2025-12-06 16:06:08'),
(8, 10, 218, 'yellow', 84, '2025-12-13 15:35:09'),
(9, 11, 62, 'yellow', 36, '2025-12-14 14:16:20'),
(10, 11, 57, 'red', 84, '2025-12-14 15:20:55');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_clean_sheets`
--

CREATE TABLE `tournament_clean_sheets` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournament_clean_sheets`
--

INSERT INTO `tournament_clean_sheets` (`id`, `match_id`, `player_id`) VALUES
(2, 8, 56),
(3, 8, 174),
(4, 9, 110),
(5, 20, 174),
(6, 21, 153),
(10, 22, 191),
(8, 23, 153),
(7, 23, 174),
(9, 24, 191),
(11, 25, 90),
(12, 25, 174);

-- --------------------------------------------------------

--
-- Table structure for table `tournament_fixtures`
--

CREATE TABLE `tournament_fixtures` (
  `id` int(11) NOT NULL,
  `home_club_id` int(11) NOT NULL,
  `away_club_id` int(11) NOT NULL,
  `tournament_date` datetime NOT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `status` enum('Scheduled','Played','Postponed') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `competition_season_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournament_fixtures`
--

INSERT INTO `tournament_fixtures` (`id`, `home_club_id`, `away_club_id`, `tournament_date`, `venue`, `status`, `created_at`, `competition_season_id`) VALUES
(7, 6, 4, '2025-12-06 15:00:00', 'Tafara', '', '2025-11-22 18:07:02', 2),
(8, 3, 5, '2025-12-07 15:00:00', 'Nehanda', '', '2025-11-22 18:43:10', 2),
(9, 5, 3, '2025-12-13 15:00:00', 'Village 3', '', '2025-11-22 18:43:45', 2),
(10, 4, 6, '2025-12-14 15:00:00', 'Village 4B', '', '2025-11-22 18:44:05', 2),
(15, 5, 4, '2025-12-26 09:00:00', 'Village 4B', '', '2025-12-01 09:14:34', 2),
(16, 3, 6, '2025-12-26 10:30:00', 'Village 4B', '', '2025-12-01 09:15:11', 2),
(17, 7, 4, '2025-12-20 15:00:00', 'Nehanda', '', '2025-12-08 12:23:25', 4),
(18, 11, 8, '2025-12-20 15:00:00', 'Village 6', '', '2025-12-08 13:27:13', 4),
(19, 2, 9, '2025-12-21 15:00:00', 'Mutsinzwa', '', '2025-12-08 13:27:32', 4),
(20, 9, 4, '2025-12-28 23:00:00', 'Village 4B', '', '2025-12-08 13:28:25', 4),
(21, 8, 7, '2025-12-28 14:30:00', 'Village 4B', '', '2025-12-08 13:28:39', 4),
(22, 4, 8, '2026-01-03 09:00:00', 'Village 4B', '', '2025-12-10 21:40:02', 5),
(23, 10, 5, '2026-01-03 10:00:00', 'Village 4A', '', '2025-12-10 21:40:34', 5),
(24, 5, 4, '2026-01-03 11:30:00', 'Village 4A', '', '2025-12-10 21:41:10', 5),
(25, 8, 10, '2026-01-03 12:30:00', 'Village 4A', '', '2025-12-10 21:42:12', 5),
(26, 5, 8, '2026-01-03 14:00:00', 'Village 4A', '', '2025-12-10 21:42:47', 5),
(27, 4, 10, '2026-01-03 15:00:00', 'Village 4A', '', '2025-12-10 21:43:10', 5),
(28, 11, 10, '2025-12-28 09:00:00', 'Village 4B', '', '2025-12-22 10:13:59', 4);

-- --------------------------------------------------------

--
-- Table structure for table `tournament_goals`
--

CREATE TABLE `tournament_goals` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `minute` int(11) NOT NULL,
  `is_penalty` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournament_goals`
--

INSERT INTO `tournament_goals` (`id`, `match_id`, `player_id`, `minute`, `is_penalty`, `created_at`) VALUES
(12, 9, 121, 14, 0, '2025-12-07 16:56:55'),
(13, 9, 119, 78, 0, '2025-12-07 16:57:08'),
(14, 10, 218, 23, 0, '2025-12-13 14:47:42'),
(15, 10, 218, 26, 0, '2025-12-13 14:47:51'),
(17, 10, 121, 14, 1, '2025-12-13 14:55:49'),
(18, 10, 119, 48, 0, '2025-12-13 15:34:12'),
(19, 10, 218, 85, 0, '2025-12-13 15:34:45'),
(20, 11, 48, 9, 0, '2025-12-14 14:10:22'),
(22, 11, 168, 43, 1, '2025-12-14 14:21:56'),
(23, 11, 49, 38, 0, '2025-12-14 15:11:22'),
(24, 11, 49, 40, 0, '2025-12-14 15:11:34'),
(25, 12, 72, 55, 0, '2025-12-21 15:01:00'),
(26, 14, 132, 24, 0, '2025-12-21 18:32:59'),
(27, 15, 168, 58, 0, '2025-12-26 10:27:29'),
(28, 15, 218, 12, 0, '2025-12-26 10:29:26'),
(29, 15, 218, 78, 0, '2025-12-26 10:29:36'),
(30, 16, 109, 45, 0, '2025-12-26 13:03:39'),
(31, 20, 176, 54, 0, '2026-01-08 13:22:20'),
(32, 21, 223, 15, 0, '2026-01-08 13:24:28'),
(33, 22, 190, 85, 0, '2026-01-08 13:25:52');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_images`
--

CREATE TABLE `tournament_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `competition_season_id` int(10) UNSIGNED NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament_images`
--

INSERT INTO `tournament_images` (`id`, `competition_season_id`, `image`, `caption`, `created_at`) VALUES
(1, 2, 'tournament_1765027782_7272.jpg', 'Gedhe FC vs 4B FC 06 Dec 2025, 01', '2025-12-06 13:29:42'),
(2, 2, 'tournament_1765027793_1978.jpg', 'Gedhe FC vs 4B FC 06 Dec 2025, 02', '2025-12-06 13:29:53'),
(3, 2, 'tournament_1766782180_1646.jpg', '', '2025-12-26 20:49:40'),
(4, 2, 'tournament_1766782199_4126.jpg', '', '2025-12-26 20:49:59'),
(5, 2, 'tournament_1766782213_3744.jpg', '', '2025-12-26 20:50:13'),
(6, 2, 'tournament_1766782228_6103.jpg', '', '2025-12-26 20:50:28'),
(7, 2, 'tournament_1766782244_2299.jpg', '', '2025-12-26 20:50:44'),
(8, 2, 'tournament_1766782259_1011.jpg', '', '2025-12-26 20:50:59'),
(9, 2, 'tournament_1766847208_4969.jpeg', '', '2025-12-27 14:53:28'),
(10, 2, 'tournament_1766847221_1660.jpeg', '', '2025-12-27 14:53:41'),
(11, 4, 'tournament_1767073011_7181.jpg', 'Final', '2025-12-30 05:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_matches`
--

CREATE TABLE `tournament_matches` (
  `id` int(11) NOT NULL,
  `fixture_id` int(11) NOT NULL,
  `home_score` int(11) DEFAULT 0,
  `away_score` int(11) DEFAULT 0,
  `match_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournament_matches`
--

INSERT INTO `tournament_matches` (`id`, `fixture_id`, `home_score`, `away_score`, `match_date`, `created_at`) VALUES
(8, 7, 0, 0, '2025-12-06 15:28:39', '2025-12-06 13:28:39'),
(9, 8, 2, 0, '2025-12-07 17:21:45', '2025-12-07 15:21:45'),
(10, 9, 3, 2, '2025-12-13 16:47:06', '2025-12-13 14:47:06'),
(11, 10, 1, 3, '2025-12-14 16:10:04', '2025-12-14 14:10:04'),
(12, 17, 1, 0, '2025-12-20 21:44:14', '2025-12-20 19:44:14'),
(13, 18, 0, 1, '2025-12-20 21:44:27', '2025-12-20 19:44:27'),
(14, 19, 1, 1, '2025-12-21 20:32:40', '2025-12-21 18:32:40'),
(15, 15, 2, 1, '2025-12-26 12:26:55', '2025-12-26 10:26:55'),
(16, 16, 1, 1, '2025-12-26 15:03:22', '2025-12-26 13:03:22'),
(17, 28, 1, 0, '2025-12-29 14:53:26', '2025-12-29 12:53:26'),
(18, 20, 0, 3, '2025-12-29 14:53:42', '2025-12-29 12:53:42'),
(19, 21, 1, 2, '2025-12-30 07:35:28', '2025-12-30 05:35:28'),
(20, 27, 1, 0, '2026-01-08 15:20:55', '2026-01-08 13:20:55'),
(21, 26, 0, 1, '2026-01-08 15:21:10', '2026-01-08 13:21:10'),
(22, 25, 0, 1, '2026-01-08 15:21:25', '2026-01-08 13:21:25'),
(23, 22, 0, 0, '2026-01-08 15:21:32', '2026-01-08 13:21:32'),
(24, 23, 2, 0, '2026-01-08 15:21:43', '2026-01-08 13:21:43'),
(25, 24, 0, 0, '2026-01-08 15:21:50', '2026-01-08 13:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(2, 'gadzairobert@gmail.com', '$2y$12$AfjiJ.v7/z1pbOsu14epj.OPStozxYpaljaXyg5GjfbRBS7EnIH5.', 'robert@gmail.com', 'admin', '2025-11-16 08:34:51'),
(3, 'roberttest', '$2y$10$XXsa.S8AII1SH.VcYkOdSO5FIAI10p6lxr3FQPRcQI0XX/QNh.tri', '', 'admin', '2025-11-22 11:10:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `assists`
--
ALTER TABLE `assists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player_id` (`player_id`),
  ADD KEY `idx_goal` (`goal_id`);

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player_id` (`player_id`),
  ADD KEY `idx_match_player` (`match_id`,`player_id`);

--
-- Indexes for table `clean_sheets`
--
ALTER TABLE `clean_sheets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `competition_seasons`
--
ALTER TABLE `competition_seasons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `competition_name` (`competition_name`,`season`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contributions`
--
ALTER TABLE `contributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `recorded_by` (`recorded_by`),
  ADD KEY `idx_contributor` (`contributor_type`,`contributor_id`);

--
-- Indexes for table `contribution_expenses`
--
ALTER TABLE `contribution_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `contribution_purposes`
--
ALTER TABLE `contribution_purposes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contribution_types`
--
ALTER TABLE `contribution_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fixtures`
--
ALTER TABLE `fixtures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_fixture` (`home_club_id`,`away_club_id`,`fixture_date`),
  ADD KEY `away_club_id` (`away_club_id`),
  ADD KEY `idx_date` (`fixture_date`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uploaded_at` (`uploaded_at`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player_id` (`player_id`),
  ADD KEY `idx_match_player` (`match_id`,`player_id`);

--
-- Indexes for table `logos`
--
ALTER TABLE `logos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_purpose` (`purpose`);

--
-- Indexes for table `management`
--
ALTER TABLE `management`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_name` (`full_name`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fixture` (`fixture_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `nav_items`
--
ALTER TABLE `nav_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_publish_date` (`publish_date`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_club_position` (`club_id`,`position`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name` (`key_name`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD UNIQUE KEY `unique_setting` (`setting_key`);

--
-- Indexes for table `slideshow_images`
--
ALTER TABLE `slideshow_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_club_role` (`club_id`,`role`);

--
-- Indexes for table `tournament_assists`
--
ALTER TABLE `tournament_assists`
  ADD PRIMARY KEY (`goal_id`,`player_id`);

--
-- Indexes for table `tournament_cards`
--
ALTER TABLE `tournament_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tournament_clean_sheets`
--
ALTER TABLE `tournament_clean_sheets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `match_id` (`match_id`,`player_id`);

--
-- Indexes for table `tournament_fixtures`
--
ALTER TABLE `tournament_fixtures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tournament_fixture` (`home_club_id`,`away_club_id`,`tournament_date`),
  ADD KEY `away_club_id` (`away_club_id`),
  ADD KEY `idx_date` (`tournament_date`);

--
-- Indexes for table `tournament_goals`
--
ALTER TABLE `tournament_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player_id` (`player_id`),
  ADD KEY `idx_match_player` (`match_id`,`player_id`);

--
-- Indexes for table `tournament_images`
--
ALTER TABLE `tournament_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_season` (`competition_season_id`);

--
-- Indexes for table `tournament_matches`
--
ALTER TABLE `tournament_matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fixture` (`fixture_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `assists`
--
ALTER TABLE `assists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `clean_sheets`
--
ALTER TABLE `clean_sheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `competition_seasons`
--
ALTER TABLE `competition_seasons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contributions`
--
ALTER TABLE `contributions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `contribution_expenses`
--
ALTER TABLE `contribution_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contribution_purposes`
--
ALTER TABLE `contribution_purposes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contribution_types`
--
ALTER TABLE `contribution_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fixtures`
--
ALTER TABLE `fixtures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- AUTO_INCREMENT for table `logos`
--
ALTER TABLE `logos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `management`
--
ALTER TABLE `management`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `nav_items`
--
ALTER TABLE `nav_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `slideshow_images`
--
ALTER TABLE `slideshow_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tournament_cards`
--
ALTER TABLE `tournament_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tournament_clean_sheets`
--
ALTER TABLE `tournament_clean_sheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tournament_fixtures`
--
ALTER TABLE `tournament_fixtures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tournament_goals`
--
ALTER TABLE `tournament_goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tournament_images`
--
ALTER TABLE `tournament_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tournament_matches`
--
ALTER TABLE `tournament_matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assists`
--
ALTER TABLE `assists`
  ADD CONSTRAINT `assists_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assists_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`);

--
-- Constraints for table `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cards_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`);

--
-- Constraints for table `contributions`
--
ALTER TABLE `contributions`
  ADD CONSTRAINT `contributions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contributions_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `contribution_types` (`id`),
  ADD CONSTRAINT `contributions_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `contribution_expenses`
--
ALTER TABLE `contribution_expenses`
  ADD CONSTRAINT `contribution_expenses_ibfk_1` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `fixtures`
--
ALTER TABLE `fixtures`
  ADD CONSTRAINT `fixtures_ibfk_1` FOREIGN KEY (`home_club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `fixtures_ibfk_2` FOREIGN KEY (`away_club_id`) REFERENCES `clubs` (`id`);

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `goals_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`);

--
-- Constraints for table `management`
--
ALTER TABLE `management`
  ADD CONSTRAINT `management_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`fixture_id`) REFERENCES `fixtures` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tournament_fixtures`
--
ALTER TABLE `tournament_fixtures`
  ADD CONSTRAINT `tournament_fixtures_ibfk_1` FOREIGN KEY (`home_club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `tournament_fixtures_ibfk_2` FOREIGN KEY (`away_club_id`) REFERENCES `clubs` (`id`);

--
-- Constraints for table `tournament_goals`
--
ALTER TABLE `tournament_goals`
  ADD CONSTRAINT `tournament_goals_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `tournament_matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_goals_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`);

--
-- Constraints for table `tournament_images`
--
ALTER TABLE `tournament_images`
  ADD CONSTRAINT `fk_tournament_images_season` FOREIGN KEY (`competition_season_id`) REFERENCES `competition_seasons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tournament_matches`
--
ALTER TABLE `tournament_matches`
  ADD CONSTRAINT `tournament_matches_ibfk_1` FOREIGN KEY (`fixture_id`) REFERENCES `tournament_fixtures` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
