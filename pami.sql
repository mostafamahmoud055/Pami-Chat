-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2022 at 02:26 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pami`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_img` tinyint(1) NOT NULL DEFAULT 0,
  `is_file` tinyint(1) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `message`, `is_img`, `is_file`, `date`) VALUES
(1, 'ggfdgfd', 0, 0, '2022-11-14 14:18:28'),
(2, 'gdfgdf', 0, 0, '2022-11-14 14:18:34'),
(3, 'gfgfgfg', 0, 0, '2022-11-14 14:18:44'),
(4, 'gfgfg', 0, 0, '2022-11-14 14:18:48'),
(5, 'ghhgh', 0, 0, '2022-11-14 14:19:08'),
(6, 'hghg', 0, 0, '2022-11-14 14:19:21'),
(7, 'hjhj', 0, 0, '2022-11-14 14:20:20'),
(8, 'jhh', 0, 0, '2022-11-14 14:20:43'),
(9, 'jhjh', 0, 0, '2022-11-14 14:20:51'),
(10, 'hfg', 0, 0, '2022-11-14 14:21:04'),
(11, 'uu', 0, 0, '2022-11-14 14:45:26'),
(12, 'Done 😍', 0, 0, '2022-11-14 14:54:59'),
(13, 'Good', 0, 0, '2022-11-14 14:55:47'),
(14, '😉', 0, 0, '2022-11-14 14:56:30'),
(15, 'hi', 0, 0, '2022-11-14 15:16:41'),
(16, 'heelo', 0, 0, '2022-11-14 15:16:48');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `notification` varchar(255) DEFAULT NULL,
  `readed` tinyint(1) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `to_user_id` int(11) NOT NULL,
  `users_rooms_id` int(11) DEFAULT NULL,
  `for_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `from_user_id`, `notification`, `readed`, `date`, `to_user_id`, `users_rooms_id`, `for_admin`) VALUES
(19, 1, 'you have been banned from FRINDES', 1, '2022-11-14 14:23:20', 3, NULL, 0),
(20, 1, 'bann removed from FRINDES', 1, '2022-11-14 14:24:33', 3, NULL, 0),
(21, 1, 'you have been banned from FRINDES', 1, '2022-11-14 14:28:26', 3, NULL, 0),
(22, 1, 'bann removed from FRINDES', 1, '2022-11-14 14:30:29', 3, NULL, 0),
(88, 3, 'ahmedali wants to join to Family', -1, '2022-11-14 14:53:45', 1, 60, 1),
(89, 1, 'you accepted to join to Family', 1, '2022-11-14 14:54:38', 3, 60, 0),
(90, 3, 'ahmedali wants to join to Frindes', -1, '2022-11-14 14:54:40', 1, 61, 1),
(91, 1, 'you accepted to join to Frindes', 1, '2022-11-14 14:54:43', 3, 61, 0),
(92, 1, 'mostafa wants to join to augst', -1, '2022-11-14 15:15:54', 4, 63, 1),
(93, 4, 'basma wants to join to Frindes', 0, '2022-11-14 15:19:01', 1, 64, 1),
(94, 4, 'basma wants to join to Frindes', 0, '2022-11-14 15:19:02', 1, 65, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `details` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `photo`, `details`) VALUES
(1, 'Family', '', 'Reference site about Lorem Ipsum, giving information on its origins, as well as a random Lipsum generator.'),
(2, 'Friends', '', 'Reference site about Lorem Ipsum, giving information on its origins, as well as a random Lipsum generator.'),
(4, 'Family', '', 'Reference site about Lorem Ipsum, giving information on its origins, as well as a random Lipsum generator.'),
(5, 'Frindes', '', 'Reference site about Lorem Ipsum, giving information on its origins, as well as a random Lipsum generator Reference as well as a random Lipsum generator.'),
(6, 'augst', '', 'gsgdfgf');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(125) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `admin` varchar(100) NOT NULL DEFAULT '0',
  `token` int(255) NOT NULL,
  `conn_id` int(11) NOT NULL,
  `online` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `photo`, `email`, `details`, `admin`, `token`, `conn_id`, `online`) VALUES
(1, 'mostafa', '8cb2237d0679ca88db6464eac60da96345513964', NULL, 'mostafamahmoud@gmail.com', NULL, '1', 1668431752, 86, 1),
(3, 'ahmedali', '8cb2237d0679ca88db6464eac60da96345513964', NULL, 'ahmedali@gmail.com', NULL, '0', 1668430424, 259, 0),
(4, 'basma', '8cb2237d0679ca88db6464eac60da96345513964', NULL, 'basma@gmail.com', NULL, '1', 1668431719, 271, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_chats_rooms`
--

CREATE TABLE `users_chats_rooms` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `users_rooms_id` int(11) NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_chats_rooms`
--

INSERT INTO `users_chats_rooms` (`id`, `chat_id`, `users_rooms_id`, `seen`) VALUES
(12, 12, 60, 1),
(13, 13, 17, 1),
(14, 14, 61, 0),
(15, 15, 62, 0),
(16, 16, 62, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_rooms`
--

CREATE TABLE `users_rooms` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `room_count` int(11) NOT NULL DEFAULT 0,
  `readed` tinyint(1) NOT NULL DEFAULT 1,
  `date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bann` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_rooms`
--

INSERT INTO `users_rooms` (`id`, `user_id`, `room_id`, `rank`, `room_count`, `readed`, `date`, `bann`) VALUES
(16, 1, 4, 2, 0, 1, '2022-11-14 14:55:11', 0),
(17, 1, 5, 2, 1, 0, '2022-11-14 14:56:30', 0),
(60, 3, 4, 1, 0, 1, '2022-11-14 14:54:38', 0),
(61, 3, 5, 1, 0, 1, '2022-11-14 14:55:52', 0),
(62, 4, 6, 2, 0, 1, '2022-11-14 15:15:38', 0),
(63, 1, 6, 1, 0, 0, '2022-11-14 15:16:41', 0),
(64, 4, 5, 0, 0, 1, '2022-11-14 15:19:01', 0),
(65, 4, 5, 0, 0, 1, '2022-11-14 15:19:02', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_user_id` (`from_user_id`),
  ADD KEY `to_user_id` (`to_user_id`),
  ADD KEY `users_rooms_id` (`users_rooms_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_chats_rooms`
--
ALTER TABLE `users_chats_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `users_rooms_id` (`users_rooms_id`);

--
-- Indexes for table `users_rooms`
--
ALTER TABLE `users_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_relation` (`user_id`),
  ADD KEY `room_relation` (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users_chats_rooms`
--
ALTER TABLE `users_chats_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users_rooms`
--
ALTER TABLE `users_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `from_user_relation` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `to_user_relation2` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_rooms_id_relation` FOREIGN KEY (`users_rooms_id`) REFERENCES `users_rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_chats_rooms`
--
ALTER TABLE `users_chats_rooms`
  ADD CONSTRAINT `users_chats_rooms_ibfk_2` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_chats_rooms_ibfk_3` FOREIGN KEY (`users_rooms_id`) REFERENCES `users_rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_rooms`
--
ALTER TABLE `users_rooms`
  ADD CONSTRAINT `room_relation` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_relation` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
