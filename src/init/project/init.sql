CREATE DATABASE `proyecto_integrador`;
USE `proyecto_integrador`;

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id2` varchar(32) NOT NULL,
  `activity_name` varchar(100) NOT NULL,
  `activity_description` text NOT NULL,
  `activity_time` datetime NOT NULL,
  PRIMARY KEY (`activity_id`)
);

INSERT INTO `activities` (`activity_id2`, `activity_name`, `activity_description`, `activity_time`) VALUES
('58b3d1f2bf25ba639db113ccdbd37e08', 'Misión de Exploradores', 'Los niños trabajan juntos como astronautas en una misión espacial.', '2025-01-03 15:00:00'),
('FDUYSIFYDSUI', 'Misión de Descubrimiento', 'Exploración de un nuevo planeta con actividades interactivas.', '2025-01-04 10:30:00');

CREATE TABLE `activities_monitors` (
  `relation_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `monitor_id` int(11) NOT NULL,
  PRIMARY KEY (`relation_id`)
);

INSERT INTO `activities_monitors` (`activity_id`, `monitor_id`) VALUES
(1, 2),
(2, 3);

CREATE TABLE `activities_participants` (
  `relation_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  PRIMARY KEY (`relation_id`)
);

INSERT INTO `activities_participants` (`relation_id`, `activity_id`, `participant_id`) VALUES
(1, 1, 1),
(2, 1, 5),
(3, 2, 1),
(4, 2, 5);

CREATE TABLE `participants` (
  `participant_id` int(11) NOT NULL AUTO_INCREMENT,
  `participant_id2` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `participant_name` varchar(100) NOT NULL,
  `participant_birth_date` date NOT NULL,
  `participant_address` varchar(128) NOT NULL,
  `participant_allergies` text NOT NULL,
  `participant_special_needs` text NOT NULL,
  `participant_medical_treatment` text NOT NULL,
  PRIMARY KEY (`participant_id`)
);

INSERT INTO `participants` (`participant_id2`, `user_id`, `participant_name`, `participant_birth_date`, `participant_address`, `participant_allergies`, `participant_special_needs`, `participant_medical_treatment`) VALUES
('e26b20124473be7d8ff9eb4cead70a9f', 1, 'Diego Sánchez', '2016-03-23', 'Madrid, España', 'Alergia a frutos secos', 'Necesita estructura en actividades', 'Lleva EpiPen'),
('D7S8A9D78AS9D', 2, 'Daniel Pérez', '2015-06-12', 'Barcelona, España', 'Sin alergias', 'Dificultades de atención', 'Ninguno');

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id2` varchar(32) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `checkin_datetime` datetime DEFAULT NULL,
  `checkout_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`attendance_id`)
);

INSERT INTO `attendance` (`attendance_id2`, `activity_id`, `participant_id`, `checkin_datetime`, `checkout_datetime`) VALUES
('f3fe12b84bb737d358e9179d253cb3fa', 1, 1, '2025-02-09 10:12:00', NULL),
('F4E362F685EED572A68644E8CF7DA5F7', 1, 2, '2025-02-09 10:16:00', NULL);

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id2` varchar(32) NOT NULL,
  `user_email` varchar(124) NOT NULL,
  `user_password` varchar(256) NOT NULL,
  `role` int(11) NOT NULL,
  `enabled` tinyint(4) NOT NULL,
  PRIMARY KEY (`user_id`)
);

INSERT INTO `users` (`user_id2`, `user_email`, `user_password`, `role`, `enabled`) VALUES
('4476b3f8cc574da8014d6f16e6fa5de5', 'tutor1@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 0, 1),
('dcdfe398b9540c5b78f4e90e4b57e9f3', 'monitor1@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 1, 1),
('D8SA79D7AS89D7A89', 'admin1@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 2, 1);

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id2` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(32) NOT NULL,
  `amount` float NOT NULL,
  `payment_date` date NOT NULL,
  PRIMARY KEY (`payment_id`)
);

INSERT INTO `payments` (`payment_id2`, `user_id`, `status`, `amount`, `payment_date`) VALUES
('A7F8G9H7D89F7G9H', 1, 'Paid', 150.00, '2025-01-15'),
('F4G78H97DFG897H', 2, 'Pending', 200.00, '2025-01-16');

CREATE TABLE `user_details` (
  `detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `detail_id2` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(256) NOT NULL,
  `user_email` varchar(124) NOT NULL,
  `user_relationship` varchar(128) NOT NULL,
  `user_birth_date` date NOT NULL,
  `user_dni` varchar(9) NOT NULL,
  `user_phone_number` varchar(16) NOT NULL,
  PRIMARY KEY (`detail_id`)
);

INSERT INTO `user_details` (`detail_id2`, `user_id`, `user_name`, `user_email`, `user_relationship`, `user_birth_date`, `user_dni`, `user_phone_number`) VALUES
('D78S9A0D7A89SD7A89', 1, 'Laura Sánchez', 'tutor1@example.com', 'Madre', '1980-05-12', '11111111A', '555555555'),
('H7GF89F789H78F9G', 2, 'Carlos Rodríguez', 'monitor1@example.com', 'Monitor', '1992-08-23', '2222222B', '666666666');

CREATE TABLE `schedule` (
  `schedule_id` int(11) NOT NULL,
  `schedule_id2` varchar(32) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `start_day` date NOT NULL,
  `end_day` date NOT NULL
);

INSERT INTO `schedule` (`schedule_id`, `schedule_id2`, `participant_id`, `start_day`, `end_day`) VALUES
(1, 'DF78SF78DS9F789DS7F89SD7G89', 1, '2025-02-05', '2025-02-12');

COMMIT;