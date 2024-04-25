
--
-- Database: `spas_db`
--

create schema spas_db;
use spas_db;

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `auth_type` varchar(255) NOT NULL,
  `selector` text NOT NULL,
  `token` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `Students` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(255) GENERATED ALWAYS AS (SUBSTRING_INDEX(`email`, '@', 1)) STORED,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Teachers` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(255) GENERATED ALWAYS AS (
    CONCAT(
      SUBSTRING_INDEX(`name`, ' ', 1),
      '-',
      SUBSTRING_INDEX(SUBSTRING_INDEX(`email`, '@', 1), '-', -1)
    )
  ) STORED,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phoneNo` int(11) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `Admin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Class` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `standard` varchar(255) NOT NULL,
  `id_name` varchar(255) GENERATED ALWAYS AS (CONCAT(`standard`, ' ', `name`)) STORED,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `Admin` (`name`, `username`, `password`) VALUES
('Root Admin', 'AdminRoot', '$2y$10$1PsnOG7W94a.0fytWE6IFeKBhuQLxJReUO8lsryHHzUWBL/9Sws8C');

--
-- Indexes for table `auth_tokens`
--

ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `users`
--

ALTER TABLE `Students`
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `Teachers`
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `Admin`
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE Class ADD INDEX idx_id_name (id_name);

ALTER TABLE `Students`
  ADD FOREIGN KEY (class) REFERENCES Class(id_name);

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- Attendance
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `attendance` (
  `id` int(11) UNSIGNED NOT NULL,
  `barcodeId` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `attendance`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

--
-- Table for visualisation
--

CREATE TABLE IF NOT EXISTS diff_visualisation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    latest_date DATE,
    tot_att_dif INT,
    tot_abs_dif INT,
    per_att_dif FLOAT,
    peak_hour_dif INT
);

CREATE TABLE detailedClass_Visualisation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class VARCHAR(255) NOT NULL,
    attendees INT NOT NULL,
    absentees INT NOT NULL,
    date DATE NOT NULL,
    UNIQUE KEY class_date_unique (class, date)
);

CREATE TABLE detailedStandard_Visualisation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    standard VARCHAR(255) NOT NULL,
    attendees INT NOT NULL,
    absentees INT NOT NULL,
    date DATE NOT NULL,
    UNIQUE KEY standard_date_unique (standard, date)
);

--
-- Table for dashboard
--

CREATE TABLE calendar_event_master (
  event_id int(11) NOT NULL AUTO_INCREMENT,
  event_name varchar(255) DEFAULT NULL,
  event_description text,
  event_start_date date DEFAULT NULL,
  event_end_date date DEFAULT NULL,
  PRIMARY KEY (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- AttendanceTC
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE attendancetc (
  id int(11) UNSIGNED NOT NULL,
  barcodeId varchar(255) NOT NULL,
  date timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for table attendance
--
ALTER TABLE attendancetc
  ADD PRIMARY KEY (id);

ALTER TABLE attendancetc
  MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;