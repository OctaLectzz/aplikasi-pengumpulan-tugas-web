-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2025 at 08:14 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pengumpulan-tugas`
--

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `referral_code` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `user_id`, `title`, `referral_code`, `date`, `created_at`) VALUES
(11, 3, 'Tugas Bahasa Inggris Pertama', 'TJCHP4', '2025-01-31', '2025-01-29 19:07:13'),
(12, 3, 'Proposan pengajuan lomba', 'RGOX8M', '2025-01-29', '2025-01-29 19:07:34');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `referral_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `referral_id`, `date`, `title`, `answer`, `file`, `score`, `comment`, `created_at`) VALUES
(7, 4, 11, '2025-01-30', 'Tugas Bahasa Inggris Pertama', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', '1738177764_AplikasiPengumpulanTugasWeb.pdf', 94, 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', '2025-01-29 19:09:24'),
(8, 4, 11, '2025-01-31', 'Tugas Bahasa Inggris Pertama', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', NULL, NULL, NULL, '2025-01-29 19:09:39'),
(9, 4, 11, '2025-01-31', 'Tugas Bahasa Inggris Pertama', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', NULL, 76, 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', '2025-01-29 19:10:09'),
(10, 4, 11, '2025-01-29', 'Tugas Bahasa Inggris Pertama', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', '1738177829_AplikasiPengumpulanTugasWeb.pdf', 87, 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', '2025-01-29 19:10:29'),
(11, 10, 12, '2025-01-30', 'Proposan pengajuan lomba', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', NULL, 99, 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', '2025-01-29 19:11:50'),
(12, 10, 12, '2025-01-31', 'Proposan pengajuan lomba', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', '1738177932_AplikasiPengumpulanTugasWeb.pdf', NULL, NULL, '2025-01-29 19:12:12'),
(13, 10, 11, '2025-01-29', 'Tugas Bahasa Inggris Pertama', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', NULL, 98, 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', '2025-01-29 19:12:38'),
(14, 10, 11, '2025-01-31', 'Tugas Bahasa Inggris Pertama', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Architecto mollitia ullam laudantium, unde ab veniam, eos molestiae, cum beatae fugiat nisi? Quod minima ipsam accusantium similique autem in laboriosam maxime provident aspernatur, ipsum ut alias accusamus quidem praesentium perspiciatis numquam nostrum dolores dolorem, repellat aut sit, fuga laudantium modi! Eligendi.', '1738177971_AplikasiPengumpulanTugasWeb.pdf', NULL, NULL, '2025-01-29 19:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `NIM` varchar(20) NOT NULL,
  `role` enum('Mahasiswa','Dosen') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `NIM`, `role`, `created_at`) VALUES
(3, 'dosen', '$2y$10$Fq1nKY3An6OtzfxiGnvayOV.to.D5ktGlZklH7kyRxeSboTPJFloG', '119119911', 'Dosen', '2025-01-28 10:37:02'),
(4, 'mahasiswa', '$2y$10$VwsdUDZuP.i.5U5i.kn0V.6Easo/oFy6EyzmvaFqpe1HawPcH23L.', '2299229922', 'Mahasiswa', '2025-01-28 10:39:49'),
(10, 'octalectzz', '$2y$10$aBE.NVWlzpxRp6xZltL0QOHq.zCk12H9H5hI26IwysdNsr0993rc2', '220404', 'Mahasiswa', '2025-01-29 19:11:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `referral_code` (`referral_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `referral_id` (`referral_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `NIM` (`NIM`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
