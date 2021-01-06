
CREATE TABLE `board` (
  `id` int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `color` enum('red','white') DEFAULT NULL,
  `thesi` int(11) DEFAULT NULL,
  `blocked` tinyint(1) DEFAULT NULL
);

CREATE TABLE `boardstatus` (
  `id` int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `loggedInUsers` enum('0','1','2') DEFAULT NULL,
  `next` enum('0','1','2') DEFAULT NULL,
  `color1` enum('red','white') DEFAULT NULL,
  `color2` enum('red','white') DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `zari1` int(11) DEFAULT NULL,
  `zari2` int(11) DEFAULT NULL,
  `apomenoun` int(11) DEFAULT NULL,
  `winnner` varchar(30) DEFAULT NULL,
  `games_status` enum('0','1') DEFAULT NULL,
  `paixtike` enum('0','1','2') DEFAULT NULL
);

CREATE TABLE `initboard` (
  `id` int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `color` enum('red','white') DEFAULT NULL,
  `thesi` int(11) DEFAULT NULL,
  `blocked` tinyint(1) DEFAULT NULL
);

CREATE TABLE `users` (
  `id` int(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(30) NOT NULL,
  `passwd` varchar(30) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `connected` enum('0','1') DEFAULT NULL
);

INSERT INTO `users` (`id`, `username`, `passwd`, `reg_date`, `connected`) VALUES
(1, 'tomas', 'tomious', '2021-01-06 20:33:56', '0'),
(2, 'makis', 'makious', '2021-01-06 20:33:47', '0');

INSERT INTO `boardstatus` (`id`, `loggedInUsers`, `next`, `color1`, `color2`, `time`, `zari1`, `zari2`, `apomenoun`, `winnner`, `games_status`, `paixtike`) VALUES
(1, '0', '0', 'red', 'white', '2021-01-06 20:33:30', 0, 0, 0, NULL, '0', '0');

INSERT INTO `initboard` (`id`, `color`, `thesi`, `blocked`) VALUES
(1, 'red', 1, 0),
(2, 'red', 1, 0),
(3, 'red', 1, 0),
(4, 'red', 1, 0),
(5, 'red', 1, 0),
(6, 'red', 1, 0),
(7, 'red', 1, 0),
(8, 'red', 1, 0),
(9, 'red', 1, 0),
(10, 'red', 1, 0),
(11, 'red', 1, 0),
(12, 'red', 1, 0),
(13, 'red', 1, 0),
(14, 'red', 1, 0),
(15, 'red', 1, 0),
(16, 'white', 24, 0),
(17, 'white', 24, 0),
(18, 'white', 24, 0),
(19, 'white', 24, 0),
(20, 'white', 24, 0),
(21, 'white', 24, 0),
(22, 'white', 24, 0),
(23, 'white', 24, 0),
(24, 'white', 24, 0),
(25, 'white', 24, 0),
(26, 'white', 24, 0),
(27, 'white', 24, 0),
(28, 'white', 24, 0),
(29, 'white', 24, 0),
(30, 'white', 24, 0);
