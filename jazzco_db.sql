-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2026 at 11:47 AM
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
-- Database: `jazzco_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password_hash`, `created_at`) VALUES
(1, 'admin', 'admin@jazzco.local', '$2y$12$HRU1.T2MeGPBxowCJVYRtuZOoy1MYmGSqt57yA.dPPJN9zuK9.1Y6', '2026-05-15 18:54:25');

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(160) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `cover_path` varchar(255) DEFAULT 'assets/images/covers/default-cover.svg',
  `release_year` year(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`id`, `user_id`, `title`, `artist_id`, `cover_path`, `release_year`, `created_at`) VALUES
(4, NULL, 'Narien', 4, 'uploads/covers/ab67616d0000b2732e50096092c24bd9acb0749d_20260515_210600_12101f5877cc.jpg', '2025', '2026-05-15 19:06:00'),
(5, NULL, 'بيستهبل', 6, 'uploads/covers/Screenshot_2026-05-16_012224_20260516_002335_373cb35cd73f.png', '2025', '2026-05-15 19:29:27'),
(6, NULL, 'ICEMAN', 7, 'uploads/covers/Screenshot_2026-05-16_014314_20260516_004338_75ac9e5d1460.png', '2026', '2026-05-15 22:43:38'),
(7, NULL, 'Habebna', 6, 'uploads/covers/Screenshot_2026-05-16_021557_20260516_011647_7090ca49e186.png', '2025', '2026-05-15 23:16:47'),
(8, NULL, 'THE SORROWS ALBUM', 6, 'uploads/covers/Screenshot_2026-05-16_022800_20260516_012911_a9deecbe4737.png', '2026', '2026-05-15 23:29:11'),
(9, NULL, 'Placebo', 9, 'uploads/covers/LEGE-CY_-_PLACEBO__2025_20260516_015822_7f0c021c3fbd.jpg', '2024', '2026-05-15 23:58:22'),
(10, NULL, 'Ahla W Ahla', 10, 'uploads/covers/ahlaWAhla_20260516_110706_011c6e5a6b9a.jpg', '2016', '2026-05-16 09:07:06');

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `bio` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT 'assets/images/avatars/default-avatar.svg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `artists`
--

INSERT INTO `artists` (`id`, `name`, `bio`, `image_path`, `created_at`) VALUES
(4, 'Tul8te', 'Tul8te is a rising Egyptian artist known for blending Arabic pop, rap, and emotional storytelling into a modern sound.', 'uploads/artists/5f5dcdf79a8a7e882b239cfdac466c85_20260516_000121_43fa47be4ca9.jpg', '2026-05-15 19:05:38'),
(5, 'Ramy Sabry', 'Ramy Sabry is a famous Egyptian pop singer and songwriter known for his emotional songs, smooth voice, and modern Arabic music style.', 'uploads/artists/Ramy_sabry_2024_20260516_001001_1e07c028908a.jpg', '2026-05-15 19:11:09'),
(6, 'Ahmed Saad', 'Ahmed Saad is a popular Egyptian singer, actor, and composer known for blending traditional Egyptian vocals with modern pop music.', 'uploads/artists/ab67616100005174ea92ed5d03d2d6174a80514c_20260515_212756_4731e224bef9.jpg', '2026-05-15 19:27:56'),
(7, 'Drake', 'Drake is a globally famous Canadian rapper, singer, and songwriter known for blending rap, R&B, and emotional storytelling into chart-topping music.', 'uploads/artists/download__2_20260516_001650_a16b271c0073.jpg', '2026-05-15 22:16:50'),
(8, 'Hamo El Morshedy', 'Hamo El Morshedy is an Egyptian artist known for his energetic style, modern beats, and popular songs in the Arabic music scene', 'uploads/artists/91eb02e495409e3c88abf7a42ed9a9ff.1000x1000x1_20260516_015034_568c56af6bb2.png', '2026-05-15 23:50:34'),
(9, 'Lege-Cy', 'Lege-Cy is an Egyptian rapper known for his modern trap style, energetic flows, and songs that connect with younger audiences in the Arabic hip-hop scene.', 'uploads/artists/Lege-Cy_on_TikTok_20260516_015733_5807e3b98a8d.jpg', '2026-05-15 23:57:33'),
(10, 'Amr Diab', 'Amr Diab is a legendary Egyptian singer and composer known as one of the biggest stars in Arabic pop music, famous for his timeless hits and modern musical style.', 'uploads/artists/download__3_20260516_110329_a314decb4c52.jpg', '2026-05-16 09:03:29');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) DEFAULT NULL,
  `album_id` int(11) DEFAULT NULL,
  `playlist_id` int(11) DEFAULT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `song_id`, `album_id`, `playlist_id`, `artist_id`, `created_at`) VALUES
(1, 2, 6, NULL, NULL, NULL, '2026-05-15 19:35:02');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`, `created_at`) VALUES
(1, 'Jazz', '2026-05-15 18:54:25'),
(2, 'Hip-Hop', '2026-05-15 18:54:25'),
(3, 'Pop', '2026-05-15 18:54:25'),
(4, 'Electronic', '2026-05-15 18:54:25'),
(5, 'R&B', '2026-05-15 18:54:25'),
(6, 'Lo-Fi', '2026-05-15 18:54:25'),
(7, 'Afrobeat', '2026-05-15 18:54:25'),
(8, 'Indie', '2026-05-15 18:54:25'),
(9, 'Shaaby', '2026-05-15 23:52:43');

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(130) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT 'assets/images/covers/default-cover.svg',
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `playlists`
--

INSERT INTO `playlists` (`id`, `user_id`, `name`, `description`, `cover_image`, `is_public`, `created_at`, `updated_at`) VALUES
(3, 2, 'lelet aldmooa', 'midnight vibes', 'assets/images/covers/default-cover.svg', 0, '2026-05-15 19:03:42', '2026-05-15 19:03:42');

-- --------------------------------------------------------

--
-- Table structure for table `playlist_songs`
--

CREATE TABLE `playlist_songs` (
  `playlist_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `playlist_songs`
--

INSERT INTO `playlist_songs` (`playlist_id`, `song_id`, `added_at`) VALUES
(3, 5, '2026-05-15 21:13:45'),
(3, 7, '2026-05-15 19:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `recently_played`
--

CREATE TABLE `recently_played` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `played_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recently_played`
--

INSERT INTO `recently_played` (`id`, `user_id`, `song_id`, `played_at`) VALUES
(5, 2, 6, '2026-05-15 19:16:28'),
(6, 2, 6, '2026-05-15 19:16:28'),
(7, 2, 7, '2026-05-15 19:19:38'),
(8, 2, 7, '2026-05-15 19:19:38'),
(9, 2, 7, '2026-05-15 19:19:40'),
(10, 2, 4, '2026-05-15 19:19:45'),
(11, 2, 5, '2026-05-15 19:19:46'),
(16, 2, 7, '2026-05-15 19:19:49'),
(17, 2, 7, '2026-05-15 19:20:14'),
(18, 2, 7, '2026-05-15 19:20:14'),
(19, 2, 7, '2026-05-15 19:20:28'),
(20, 2, 7, '2026-05-15 19:20:28'),
(21, 2, 7, '2026-05-15 19:20:45'),
(22, 2, 7, '2026-05-15 19:20:45'),
(23, 2, 8, '2026-05-15 19:30:52'),
(24, 2, 8, '2026-05-15 19:30:52'),
(25, 2, 7, '2026-05-15 19:31:23'),
(26, 2, 5, '2026-05-15 19:31:27'),
(27, 2, 6, '2026-05-15 19:31:32'),
(28, 2, 8, '2026-05-15 19:34:45'),
(29, 2, 8, '2026-05-15 19:34:45'),
(30, 2, 7, '2026-05-15 19:34:48'),
(31, 2, 6, '2026-05-15 19:34:49'),
(32, 2, 8, '2026-05-15 19:35:15'),
(33, 2, 8, '2026-05-15 19:35:15'),
(35, 2, 7, '2026-05-15 19:35:30'),
(37, 2, 7, '2026-05-15 19:35:32'),
(38, 2, 5, '2026-05-15 19:35:33'),
(40, 2, 4, '2026-05-15 19:35:36'),
(41, 2, 5, '2026-05-15 19:35:38'),
(42, 2, 8, '2026-05-15 19:36:03'),
(43, 2, 8, '2026-05-15 19:36:03'),
(44, 2, 8, '2026-05-15 19:36:19'),
(45, 2, 8, '2026-05-15 19:49:37'),
(46, 2, 8, '2026-05-15 19:49:37'),
(47, 2, 8, '2026-05-15 19:50:08'),
(48, 2, 8, '2026-05-15 19:50:42'),
(49, 2, 8, '2026-05-15 19:50:50'),
(50, 2, 7, '2026-05-15 19:50:59'),
(51, 2, 6, '2026-05-15 19:51:05'),
(52, 2, 6, '2026-05-15 19:51:15'),
(53, 2, 6, '2026-05-15 19:51:18'),
(54, 2, 6, '2026-05-15 19:51:43'),
(55, 2, 6, '2026-05-15 19:52:46'),
(56, 2, 6, '2026-05-15 19:53:41'),
(57, 2, 6, '2026-05-15 19:53:49'),
(58, 2, 6, '2026-05-15 19:53:55'),
(59, 2, 6, '2026-05-15 19:54:06'),
(60, 2, 6, '2026-05-15 19:54:21'),
(61, 2, 6, '2026-05-15 19:54:43'),
(62, 2, 5, '2026-05-15 19:54:54'),
(63, 2, 7, '2026-05-15 19:57:52'),
(64, 2, 6, '2026-05-15 19:58:06'),
(65, 2, 5, '2026-05-15 19:58:10'),
(66, 2, 8, '2026-05-15 19:58:17'),
(67, 2, 7, '2026-05-15 19:58:19'),
(68, 2, 6, '2026-05-15 19:58:21'),
(69, 2, 4, '2026-05-15 19:58:23'),
(70, 2, 4, '2026-05-15 21:12:48'),
(71, 2, 4, '2026-05-15 21:13:08'),
(72, 2, 4, '2026-05-15 21:13:13'),
(73, 2, 4, '2026-05-15 21:13:18'),
(74, 2, 4, '2026-05-15 21:13:19'),
(75, 2, 4, '2026-05-15 21:13:29'),
(76, 2, 4, '2026-05-15 21:13:31'),
(77, 2, 4, '2026-05-15 21:13:36'),
(78, 2, 4, '2026-05-15 21:13:37'),
(79, 2, 4, '2026-05-15 21:13:46'),
(80, 2, 4, '2026-05-15 21:13:49'),
(81, 2, 5, '2026-05-15 21:13:53'),
(82, 2, 7, '2026-05-15 21:13:57'),
(83, 2, 7, '2026-05-15 21:14:22'),
(84, 2, 4, '2026-05-15 21:14:43'),
(85, 2, 6, '2026-05-15 21:14:53'),
(86, 2, 5, '2026-05-15 21:14:58'),
(87, 2, 5, '2026-05-15 21:50:04'),
(88, 2, 5, '2026-05-15 21:52:04'),
(89, 2, 5, '2026-05-15 21:52:12'),
(90, 2, 5, '2026-05-15 21:52:23'),
(91, 2, 5, '2026-05-15 21:52:54'),
(92, 2, 5, '2026-05-15 21:53:22'),
(93, 2, 5, '2026-05-15 21:53:26'),
(94, 2, 5, '2026-05-15 21:53:33'),
(95, 2, 5, '2026-05-15 21:53:48'),
(96, 2, 5, '2026-05-15 21:54:04'),
(97, 2, 6, '2026-05-15 21:54:21'),
(98, 2, 6, '2026-05-15 21:55:24'),
(99, 2, 6, '2026-05-15 21:55:28'),
(100, 2, 6, '2026-05-15 21:59:08'),
(101, 2, 5, '2026-05-15 21:59:32'),
(102, 2, 5, '2026-05-15 22:01:25'),
(103, 2, 5, '2026-05-15 22:10:06'),
(104, 2, 5, '2026-05-15 22:11:13'),
(105, 2, 5, '2026-05-15 22:13:31'),
(106, 2, 5, '2026-05-15 22:13:36'),
(107, 2, 5, '2026-05-15 22:14:27'),
(108, 2, 5, '2026-05-15 22:16:53'),
(109, 2, 5, '2026-05-15 22:16:55'),
(110, 2, 8, '2026-05-15 22:20:35'),
(111, 2, 8, '2026-05-15 22:20:46'),
(112, 2, 8, '2026-05-15 22:23:40'),
(113, 2, 8, '2026-05-15 22:23:42'),
(114, 2, 8, '2026-05-15 22:23:43'),
(115, 2, 8, '2026-05-15 22:23:45'),
(116, 2, 8, '2026-05-15 22:23:46'),
(117, 2, 8, '2026-05-15 22:23:52'),
(118, 2, 8, '2026-05-15 23:00:28'),
(119, 2, 8, '2026-05-15 23:00:30'),
(120, 2, 8, '2026-05-15 23:00:32'),
(121, 2, 8, '2026-05-15 23:00:34'),
(122, 2, 9, '2026-05-15 23:00:36'),
(123, 2, 6, '2026-05-15 23:04:42'),
(124, 2, 9, '2026-05-15 23:04:43'),
(125, 2, 6, '2026-05-15 23:10:16'),
(126, 2, 9, '2026-05-15 23:10:17'),
(127, 2, 9, '2026-05-15 23:10:50'),
(128, 2, 9, '2026-05-15 23:11:08'),
(129, 2, 6, '2026-05-15 23:13:23'),
(130, 2, 6, '2026-05-15 23:13:50'),
(131, 2, 6, '2026-05-15 23:13:52'),
(132, 2, 6, '2026-05-15 23:13:54'),
(133, 2, 6, '2026-05-15 23:14:05'),
(134, 2, 8, '2026-05-15 23:14:10'),
(135, 2, 8, '2026-05-15 23:19:00'),
(136, 2, 10, '2026-05-15 23:19:03'),
(137, 2, 9, '2026-05-15 23:19:21'),
(138, 2, 10, '2026-05-15 23:19:25'),
(139, 2, 10, '2026-05-15 23:20:06'),
(140, 2, 10, '2026-05-15 23:20:07'),
(141, 2, 10, '2026-05-15 23:23:13'),
(142, 2, 11, '2026-05-15 23:23:30'),
(143, 2, 11, '2026-05-15 23:31:14'),
(144, 2, 11, '2026-05-15 23:32:48'),
(145, 2, 12, '2026-05-15 23:32:50'),
(146, 2, 12, '2026-05-15 23:34:53'),
(147, 2, 12, '2026-05-15 23:43:11'),
(148, 2, 12, '2026-05-15 23:54:02'),
(149, 2, 13, '2026-05-15 23:54:05'),
(150, 2, 13, '2026-05-15 23:54:58'),
(151, 2, 6, '2026-05-15 23:58:44'),
(152, 2, 6, '2026-05-16 00:01:38'),
(153, 2, 6, '2026-05-16 00:01:58'),
(154, 2, 6, '2026-05-16 00:23:32'),
(155, 2, 6, '2026-05-16 00:23:33'),
(156, 2, 6, '2026-05-16 00:23:43'),
(157, 2, 6, '2026-05-16 00:24:10'),
(158, 2, 6, '2026-05-16 00:24:12'),
(159, 2, 6, '2026-05-16 00:24:14'),
(160, 2, 6, '2026-05-16 00:24:19'),
(161, 2, 6, '2026-05-16 00:24:20'),
(162, 2, 6, '2026-05-16 00:24:26'),
(163, 2, 6, '2026-05-16 00:24:31'),
(164, 2, 6, '2026-05-16 00:24:32'),
(165, 2, 6, '2026-05-16 00:24:35'),
(166, 2, 6, '2026-05-16 09:08:15'),
(167, 2, 6, '2026-05-16 09:08:17'),
(168, 2, 6, '2026-05-16 09:10:25'),
(169, 2, 6, '2026-05-16 09:10:31'),
(170, 2, 6, '2026-05-16 09:10:43'),
(171, 2, 6, '2026-05-16 09:12:10'),
(172, 2, 19, '2026-05-16 09:12:12'),
(173, 2, 6, '2026-05-16 09:15:31'),
(174, 2, 6, '2026-05-16 09:15:32'),
(175, 2, 20, '2026-05-16 09:15:37'),
(176, 2, 20, '2026-05-16 09:15:51'),
(177, 2, 20, '2026-05-16 09:19:59'),
(178, 2, 20, '2026-05-16 09:20:03'),
(179, 2, 21, '2026-05-16 09:20:05'),
(180, 2, 22, '2026-05-16 09:20:07'),
(181, 2, 22, '2026-05-16 09:21:38'),
(182, 2, 22, '2026-05-16 09:21:44'),
(183, 2, 22, '2026-05-16 09:21:53'),
(184, 2, 22, '2026-05-16 09:22:01'),
(185, 2, 22, '2026-05-16 09:22:05'),
(186, 2, 22, '2026-05-16 09:22:15'),
(187, 2, 22, '2026-05-16 09:22:24'),
(188, 2, 22, '2026-05-16 09:22:26'),
(189, 2, 22, '2026-05-16 09:23:09'),
(190, 2, 14, '2026-05-16 09:24:06'),
(191, 2, 4, '2026-05-16 09:24:07'),
(192, 2, 12, '2026-05-16 09:24:07'),
(193, 2, 7, '2026-05-16 09:24:08'),
(194, 2, 7, '2026-05-16 09:24:09'),
(195, 2, 18, '2026-05-16 09:24:10'),
(196, 2, 15, '2026-05-16 09:24:11'),
(197, 2, 14, '2026-05-16 09:24:12'),
(198, 2, 17, '2026-05-16 09:24:13'),
(199, 2, 18, '2026-05-16 09:24:15'),
(200, 2, 17, '2026-05-16 09:24:17'),
(201, 2, 18, '2026-05-16 09:24:18'),
(202, 2, 20, '2026-05-16 09:24:18'),
(203, 2, 18, '2026-05-16 09:24:19'),
(204, 2, 17, '2026-05-16 09:27:39'),
(205, 2, 14, '2026-05-16 09:30:47'),
(206, 2, 14, '2026-05-16 09:38:05'),
(207, 2, 14, '2026-05-16 09:38:43'),
(208, 2, 14, '2026-05-16 09:38:49'),
(209, 2, 14, '2026-05-16 09:43:29'),
(210, 2, 14, '2026-05-16 09:43:36'),
(211, 2, 14, '2026-05-16 09:43:41'),
(212, 2, 14, '2026-05-16 09:43:43'),
(213, 2, 14, '2026-05-16 09:44:25'),
(214, 2, 14, '2026-05-16 09:44:59'),
(215, 2, 14, '2026-05-16 09:45:26'),
(216, 2, 14, '2026-05-16 09:45:36'),
(217, 2, 14, '2026-05-16 09:45:54'),
(218, 2, 14, '2026-05-16 09:46:06'),
(219, 2, 14, '2026-05-16 09:46:08'),
(220, 2, 14, '2026-05-16 09:46:08'),
(221, 2, 14, '2026-05-16 09:46:12'),
(222, 2, 14, '2026-05-16 09:46:16');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'JazzCO', '2026-05-15 18:54:25'),
(2, 'tagline', 'Premium dark-mode music streaming', '2026-05-15 18:54:25'),
(3, 'allow_registration', '1', '2026-05-15 18:54:25');

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `album_id` int(11) DEFAULT NULL,
  `genre_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `cover_path` varchar(255) DEFAULT 'assets/images/covers/default-cover.svg',
  `duration_seconds` int(11) DEFAULT 0,
  `plays` int(11) DEFAULT 0,
  `is_trending` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id`, `title`, `artist_id`, `album_id`, `genre_id`, `file_path`, `cover_path`, `duration_seconds`, `plays`, `is_trending`, `created_at`, `updated_at`) VALUES
(4, 'Shedeeny', 4, 4, 3, 'uploads/songs/TUL8TE_-_Shedeeny_I______________-_20260516_001422_8115ca2695bf.mp3', 'uploads/covers/Heseeny_20260516_001422_7e28db7a9f86.jpg', 213, 16, 1, '2026-05-15 19:06:51', '2026-05-16 09:24:07'),
(5, 'Ghareeb Haly', 4, 4, 3, 'uploads/songs/TUL8TE_-_Ghareeb_Haly_I______________-_20260516_001319_9529a3159454.mp3', 'uploads/covers/Heseeny_20260516_001319_5497d2032422.jpg', 236, 27, 0, '2026-05-15 19:08:03', '2026-05-15 22:16:55'),
(6, 'Kelma', 5, NULL, 3, 'uploads/songs/Ramy_Sabry_-_Kelma____________________-_20260516_004024_aea5681d02d9.mp3', 'uploads/covers/Screenshot_2026-05-16_013741_20260516_004024_4f4398852ed8.png', 247, 52, 0, '2026-05-15 19:13:38', '2026-05-16 09:15:32'),
(7, 'Masadaat Etabelna', 5, NULL, 3, 'uploads/songs/Ramy_Sabry_-_Masadaat_Etabelna____________________-_20260515_211917_dd4886f1db3b.mp3', 'uploads/covers/artworks-000134851877-474bya-t500x500_20260515_211917_2934d14ae832.jpg', 277, 21, 1, '2026-05-15 19:19:17', '2026-05-16 09:24:09'),
(8, 'Sheftishi', 6, 5, 4, 'uploads/songs/Ahmed_Saad_-_Shefteshi__Official_Music_Video___________________-_20260516_003037_cd5ebb97dc3b.mp3', 'uploads/covers/download__3_20260515_213047_ef692cd79281.jpg', 225, 29, 1, '2026-05-15 19:30:47', '2026-05-15 23:19:00'),
(9, '2 Hard 4 The Radio', 7, 6, 2, 'uploads/songs/SpotiDownloader.com_-_2_Hard_4_The_Radio_-_Drake_20260516_010021_99511f3e5c6d.mp3', 'uploads/covers/Screenshot_2026-05-16_014314_20260516_010021_62705b27a686.png', 184, 6, 1, '2026-05-15 23:00:21', '2026-05-15 23:19:21'),
(10, 'Law Tigi', 6, 7, 3, 'uploads/songs/SpotiDownloader.com_-_Law_Tigi_-_Ahmed_Saad__1_20260516_012002_6b187bd222b1.mp3', 'uploads/covers/Screenshot_2026-05-16_021557_20260516_011839_cda5cf5eda48.png', 252, 5, 1, '2026-05-15 23:18:39', '2026-05-15 23:23:13'),
(11, 'Ana Mesheet', 6, 7, 3, 'uploads/songs/SpotiDownloader.com_-_Ana_Mesheet_-_Ahmed_Saad_20260516_012258_78a971fb4cdb.mp3', 'uploads/covers/Screenshot_2026-05-16_021557_20260516_012258_d89ba2356bc9.png', 222, 3, 0, '2026-05-15 23:22:58', '2026-05-15 23:32:48'),
(12, 'Wasalt Ma3ak', 6, 8, 3, 'uploads/songs/SpotiDownloader.com_-_Wasalt_Ma3ak_-_Ahmed_Saad_20260516_013238_a17c452d5ad7.mp3', 'uploads/covers/Screenshot_2026-05-16_022800_20260516_013238_70c09d0aa169.png', 282, 5, 0, '2026-05-15 23:32:38', '2026-05-16 09:24:07'),
(13, 'Ana 5 Star', 8, NULL, 9, 'uploads/songs/SpotiDownloader.com_-________5______________________________-_20260516_015350_44e928001774.mp3', 'uploads/covers/Screenshot_2026-05-16_025334_20260516_015350_1e952788e88a.png', 278, 2, 0, '2026-05-15 23:53:50', '2026-05-15 23:54:58'),
(14, 'Placebo', 9, 9, 2, 'uploads/songs/Lege-Cy_-_Placebo__________-_____-_________________Official_Audio_20260516_020009_ec96290cafcd.mp3', 'uploads/covers/LEGE-CY_-_PLACEBO__2025_20260516_020009_64a6c9e6fb74.jpg', 219, 20, 1, '2026-05-16 00:00:09', '2026-05-16 09:46:16'),
(15, '99', 9, 9, 2, 'uploads/songs/SpotiDownloader.com_-_99_-_Lege-Cy_20260516_020132_add62e14a1ff.mp3', 'uploads/covers/LEGE-CY_-_PLACEBO__2025_20260516_020132_34fdfa82109a.jpg', 195, 1, 0, '2026-05-16 00:01:32', '2026-05-16 09:24:11'),
(16, 'LMA TGHEEBY', 9, 9, 2, 'uploads/songs/SpotiDownloader.com_-_LMA_TGHEEBY_-_Lege-Cy_20260516_021835_07b863fe45bc.mp3', 'uploads/covers/LEGE-CY_-_PLACEBO__2025_20260516_021835_cd355e97c710.jpg', 201, 0, 0, '2026-05-16 00:18:35', '2026-05-16 00:18:35'),
(17, 'Fel Galeed', 9, 9, 2, 'uploads/songs/SpotiDownloader.com_-_Fel_Galeed_-_Lege-Cy_20260516_022155_8cab62be6cc7.mp3', 'uploads/covers/LEGE-CY_-_PLACEBO__2025_20260516_022155_561109853d90.jpg', 187, 3, 1, '2026-05-16 00:21:55', '2026-05-16 09:27:39'),
(18, 'Daftar', 9, 9, 2, 'uploads/songs/SpotiDownloader.com_-_Daftar_-_Lege-Cy_20260516_022310_6a10f23f1114.mp3', 'uploads/covers/LEGE-CY_-_PLACEBO__2025_20260516_022310_1ad312238992.jpg', 199, 4, 0, '2026-05-16 00:23:10', '2026-05-16 09:24:19'),
(19, 'Maak Alby', 10, 10, 3, 'uploads/songs/SpotiDownloader.com_-_Maak_Alby_-_Amr_Diab_20260516_111138_c2c746ee8e76.mp3', 'uploads/covers/ahlaWAhla_20260516_111138_6a4962de090f.jpg', 198, 1, 0, '2026-05-16 09:11:38', '2026-05-16 09:12:12'),
(20, 'Aks Baad', 10, 10, 3, 'uploads/songs/SpotiDownloader.com_-_Aks_Baad_-_Amr_Diab_20260516_111428_6bd57ca217e3.mp3', 'uploads/covers/ahlaWAhla_20260516_111428_fb4b8cbd15ca.jpg', 267, 5, 0, '2026-05-16 09:14:28', '2026-05-16 09:24:18'),
(21, 'Ragea', 10, 10, 3, 'uploads/songs/SpotiDownloader.com_-_Ragea_-_Amr_Diab_20260516_111736_9fd68eb4c1b5.mp3', 'uploads/covers/ahlaWAhla_20260516_111736_ccab30bfaffe.jpg', 208, 1, 1, '2026-05-16 09:17:36', '2026-05-16 09:20:05'),
(22, 'Ana W Enta', 10, 10, 3, 'uploads/songs/SpotiDownloader.com_-_Ana_W_Enta_-_Amr_Diab_20260516_111913_8df58e593b86.mp3', 'uploads/covers/ahlaWAhla_20260516_111913_4681ee14dafd.jpg', 241, 10, 0, '2026-05-16 09:19:13', '2026-05-16 09:23:09'),
(23, 'Omrena Ma Hanergea', 10, 10, 3, 'uploads/songs/SpotiDownloader.com_-_Omrena_Ma_Hanergea_-_Amr_Diab_20260516_112118_787c3d47c21e.mp3', 'uploads/covers/ahlaWAhla_20260516_112118_6b9b05c09584.jpg', 208, 0, 0, '2026-05-16 09:21:18', '2026-05-16 09:21:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'assets/images/avatars/default-avatar.svg',
  `remember_token_hash` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `profile_picture`, `remember_token_hash`, `created_at`, `updated_at`) VALUES
(1, 'Youssef', 'Youssef@jazzco.local', '$2y$10$.KtTeq/cR6W7ovalSlxr9umpjkd4DmtszjTFuTXWrzzABuS23c6sq', 'uploads/profiles/WhatsApp_Image_2026-04-06_at_1.11.21_PM_20260515_205838_68b8cdaa16e0.jpg', NULL, '2026-05-15 18:54:25', '2026-05-15 18:58:38'),
(2, 'Yasser', 'Yasser@gmail.com', '$2y$10$0R9R0DHF97DulIJag2M12uWAVLKJ65kCDCc8FY9dsLaQi7qauJWyC', 'uploads/profiles/Screenshot_2026-05-15_220226_20260515_210237_bae772f66cfa.png', NULL, '2026-05-15 19:01:16', '2026-05-15 19:02:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_albums_artist` (`artist_id`),
  ADD KEY `idx_albums_user` (`user_id`);

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_song` (`user_id`,`song_id`),
  ADD KEY `fk_favorites_song` (`song_id`),
  ADD KEY `fk_favorites_album` (`album_id`),
  ADD KEY `fk_favorites_playlist` (`playlist_id`),
  ADD KEY `fk_favorites_artist` (`artist_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_playlists_user` (`user_id`),
  ADD KEY `idx_playlists_public` (`is_public`);

--
-- Indexes for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  ADD PRIMARY KEY (`playlist_id`,`song_id`),
  ADD KEY `fk_playlist_songs_song` (`song_id`);

--
-- Indexes for table `recently_played`
--
ALTER TABLE `recently_played`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_recent_song` (`song_id`),
  ADD KEY `idx_recent_user_played` (`user_id`,`played_at`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_songs_artist` (`artist_id`),
  ADD KEY `fk_songs_album` (`album_id`),
  ADD KEY `fk_songs_genre` (`genre_id`),
  ADD KEY `idx_songs_title` (`title`),
  ADD KEY `idx_songs_trending` (`is_trending`),
  ADD KEY `idx_songs_created` (`created_at`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `recently_played`
--
ALTER TABLE `recently_played`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `fk_albums_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_albums_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorites_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorites_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorites_song` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `fk_playlists_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  ADD CONSTRAINT `fk_playlist_songs_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_playlist_songs_song` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recently_played`
--
ALTER TABLE `recently_played`
  ADD CONSTRAINT `fk_recent_song` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recent_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `songs`
--
ALTER TABLE `songs`
  ADD CONSTRAINT `fk_songs_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_songs_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_songs_genre` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
