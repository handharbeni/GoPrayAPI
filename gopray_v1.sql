-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 25, 2017 at 03:19 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gopray_v1`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_aktivitas`
--

CREATE TABLE `m_aktivitas` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `prefix_table` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_aktivitas`
--

INSERT INTO `m_aktivitas` (`id`, `nama`, `prefix_table`) VALUES
(1, 'Doa', 'doa'),
(2, 'Puasa', 'puasa'),
(3, 'Sholat', 'sholat');

-- --------------------------------------------------------

--
-- Table structure for table `m_akun`
--

CREATE TABLE `m_akun` (
  `id` int(11) NOT NULL,
  `nama` varchar(25) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `verifikasi` enum('Y','N') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_akun`
--

INSERT INTO `m_akun` (`id`, `nama`, `email`, `password`, `key`, `profile_picture`, `tanggal`, `verifikasi`) VALUES
(15, 'Reksa Rangga Wardhana', 'reksa.rangga@gmail.com', 'be1265e3c931c1466c929739c7e563b1', 'd5421e0897-MU5KcSf-5414242941', NULL, '2017-04-17 17:15:45', 'N'),
(16, 'Rangga', 'reksarangga@gmail.com', 'cc03e747a6afbbcbf8be7668acfebee5', 'd5421e0897-MU5KcSf-5414242599', NULL, '2017-04-17 19:17:21', 'N');

-- --------------------------------------------------------

--
-- Table structure for table `m_doa`
--

CREATE TABLE `m_doa` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `max_point` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_doa`
--

INSERT INTO `m_doa` (`id`, `nama`, `max_point`) VALUES
(1, 'Doa Tidur', 50);

-- --------------------------------------------------------

--
-- Table structure for table `m_friend`
--

CREATE TABLE `m_friend` (
  `id` int(11) NOT NULL,
  `id_origin` int(11) DEFAULT NULL,
  `id_destination` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '1 pending, 2 accept, decline',
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `m_jadwal_sholat`
--

CREATE TABLE `m_jadwal_sholat` (
  `id` int(11) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `method` int(11) DEFAULT NULL,
  `method_name` varchar(150) DEFAULT NULL,
  `daylight` int(11) DEFAULT NULL,
  `timezone` int(11) DEFAULT NULL,
  `mapimage` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_jadwal_sholat`
--

INSERT INTO `m_jadwal_sholat` (`id`, `title`, `type`, `method`, `method_name`, `daylight`, `timezone`, `mapimage`) VALUES
(1, NULL, NULL, 5, 'Muslim World League', 1, 7, 'http:\\/\\/maps.google.com\\/maps\\/api\\/staticmap?center=51.508129,-0.128005&sensor=false&zoom=13&size=300x300');

-- --------------------------------------------------------

--
-- Table structure for table `m_puasa`
--

CREATE TABLE `m_puasa` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `max_point` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_puasa`
--

INSERT INTO `m_puasa` (`id`, `nama`, `max_point`) VALUES
(1, 'Puasa Ramadhan', '15');

-- --------------------------------------------------------

--
-- Table structure for table `m_sholat`
--

CREATE TABLE `m_sholat` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `max_point` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_sholat`
--

INSERT INTO `m_sholat` (`id`, `nama`, `max_point`) VALUES
(1, 'Sholat Subuh', 50);

-- --------------------------------------------------------

--
-- Table structure for table `t_closest_family`
--

CREATE TABLE `t_closest_family` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `kerabat` varchar(50) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `gambar` longtext NOT NULL,
  `no_hp` varchar(12) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_closest_family`
--

INSERT INTO `t_closest_family` (`id`, `id_user`, `kerabat`, `nama`, `email`, `gambar`, `no_hp`, `tanggal`, `jam`) VALUES
(1, 15, 'Ibu', 'nama', 'email', '', '089', '2017-04-17', '18:03:03'),
(2, 15, 'Ibu', 'nama', 'email', '', '089', '2017-04-17', '18:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `t_jadwal_sholat`
--

CREATE TABLE `t_jadwal_sholat` (
  `id` int(11) NOT NULL,
  `id_jadwal` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `subuh` time DEFAULT NULL,
  `dhuha` time DEFAULT NULL,
  `dhuhur` time DEFAULT NULL,
  `ashar` time DEFAULT NULL,
  `maghrib` time DEFAULT NULL,
  `isya` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_jadwal_sholat`
--

INSERT INTO `t_jadwal_sholat` (`id`, `id_jadwal`, `tanggal`, `subuh`, `dhuha`, `dhuhur`, `ashar`, `maghrib`, `isya`) VALUES
(1, 1, '2017-04-11', '04:35:00', '05:55:00', '11:52:00', '15:11:00', '18:54:00', '19:01:00'),
(2, 1, '2017-04-18', '04:34:00', '05:50:00', '11:51:00', '15:11:00', '17:53:00', '19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `t_meme`
--

CREATE TABLE `t_meme` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `path_meme` text,
  `tanggal` date DEFAULT NULL,
  `jam` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_message`
--

CREATE TABLE `t_message` (
  `id` int(11) NOT NULL,
  `id_kerabat` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `message` longtext,
  `tanggal` date DEFAULT NULL,
  `jam` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_timeline`
--

CREATE TABLE `t_timeline` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_aktivitas` int(11) DEFAULT NULL COMMENT 'table m_aktivitas',
  `id_ibadah` int(11) DEFAULT NULL COMMENT 'dari berbagai table master',
  `tempat` varchar(150) DEFAULT NULL,
  `bersama` varchar(150) DEFAULT NULL,
  `nominal` varchar(15) NOT NULL,
  `point` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_timeline`
--

INSERT INTO `t_timeline` (`id`, `id_user`, `id_aktivitas`, `id_ibadah`, `tempat`, `bersama`, `nominal`, `point`, `tanggal`, `jam`) VALUES
(1, 15, 1, 1, 'Rumah', 'Orang Tua', '0', 60, '2017-04-17', '18:24:46'),
(2, 15, 2, 1, 'Rumah', 'Orang Tua', '0', 100, '2017-04-25', '03:19:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_aktivitas`
--
ALTER TABLE `m_aktivitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_akun`
--
ALTER TABLE `m_akun`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_doa`
--
ALTER TABLE `m_doa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_friend`
--
ALTER TABLE `m_friend`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_m_friend_m_akun` (`id_origin`),
  ADD KEY `FK_m_friend_m_akun_2` (`id_destination`);

--
-- Indexes for table `m_jadwal_sholat`
--
ALTER TABLE `m_jadwal_sholat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_puasa`
--
ALTER TABLE `m_puasa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_sholat`
--
ALTER TABLE `m_sholat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_closest_family`
--
ALTER TABLE `t_closest_family`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_t_closest_family_m_akun` (`id_user`);

--
-- Indexes for table `t_jadwal_sholat`
--
ALTER TABLE `t_jadwal_sholat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_t_jadwal_sholat_m_jadwal_sholat` (`id_jadwal`);

--
-- Indexes for table `t_meme`
--
ALTER TABLE `t_meme`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_message`
--
ALTER TABLE `t_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_timeline`
--
ALTER TABLE `t_timeline`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_aktivitas`
--
ALTER TABLE `m_aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `m_akun`
--
ALTER TABLE `m_akun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `m_doa`
--
ALTER TABLE `m_doa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `m_friend`
--
ALTER TABLE `m_friend`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `m_jadwal_sholat`
--
ALTER TABLE `m_jadwal_sholat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `m_puasa`
--
ALTER TABLE `m_puasa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `m_sholat`
--
ALTER TABLE `m_sholat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `t_closest_family`
--
ALTER TABLE `t_closest_family`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `t_jadwal_sholat`
--
ALTER TABLE `t_jadwal_sholat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `t_meme`
--
ALTER TABLE `t_meme`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_message`
--
ALTER TABLE `t_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `t_timeline`
--
ALTER TABLE `t_timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `m_friend`
--
ALTER TABLE `m_friend`
  ADD CONSTRAINT `FK_m_friend_m_akun` FOREIGN KEY (`id_origin`) REFERENCES `m_akun` (`id`),
  ADD CONSTRAINT `FK_m_friend_m_akun_2` FOREIGN KEY (`id_destination`) REFERENCES `m_akun` (`id`);

--
-- Constraints for table `t_closest_family`
--
ALTER TABLE `t_closest_family`
  ADD CONSTRAINT `FK_t_closest_family_m_akun` FOREIGN KEY (`id_user`) REFERENCES `m_akun` (`id`);

--
-- Constraints for table `t_jadwal_sholat`
--
ALTER TABLE `t_jadwal_sholat`
  ADD CONSTRAINT `FK_t_jadwal_sholat_m_jadwal_sholat` FOREIGN KEY (`id_jadwal`) REFERENCES `m_jadwal_sholat` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
