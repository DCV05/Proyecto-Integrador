DROP DATABASE IF EXISTS `proyecto_integrador`;
CREATE DATABASE `proyecto_integrador`;
USE `proyecto_integrador`;

CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id2` VARCHAR(32) NOT NULL,
  `user_email` VARCHAR(124) NOT NULL,
  `user_password` VARCHAR(256) NOT NULL,
  `role` INT NOT NULL,
  `enabled` TINYINT NOT NULL
);

INSERT INTO `users` (`user_id2`, `user_email`, `user_password`, `role`, `enabled`) VALUES
('user1', 'tutor1@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 0, 1),
('user2', 'tutor2@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 0, 1),
('user3', 'monitor1@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 1, 1),
('user4', 'monitor2@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 1, 1),
('user5', 'admin@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 2, 1);

CREATE TABLE `groups` (
  `group_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_name` VARCHAR(100) NOT NULL
);

INSERT INTO `groups` (`group_name`) VALUES
('Grupo A'), ('Grupo B'), ('Grupo C'), ('Grupo D'), ('Grupo E');

CREATE TABLE `participants` (
  `participant_id` INT AUTO_INCREMENT PRIMARY KEY,
  `participant_id2` VARCHAR(32) NOT NULL,
  `user_id` INT NOT NULL,
  `group_id` INT NOT NULL,
  `participant_name` VARCHAR(100) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`),
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`)
);

INSERT INTO `participants` (`participant_id2`, `user_id`, `group_id`, `participant_name`) VALUES
('p1', 1, 1, 'Juan Pérez'),
('p2', 1, 1, 'María López'),
('p3', 1, 1, 'Carlos Ruiz'),
('p4', 2, 2, 'Ana Gómez'),
('p5', 2, 2, 'Pedro Fernández'),
('p6', 2, 2, 'Lucía Sánchez'),
('p7', 3, 3, 'Jorge Herrera'),
('p8', 3, 3, 'Laura Ramírez'),
('p9', 3, 3, 'Diego Torres'),
('p10', 4, 4, 'Sofía Martín'),
('p11', 4, 4, 'David Navarro'),
('p12', 4, 4, 'Carmen Ortega'),
('p13', 5, 5, 'Raúl Castro'),
('p14', 5, 5, 'Beatriz Domínguez'),
('p15', 5, 5, 'Manuel Ríos');

CREATE TABLE `activities` (
  `activity_id` INT AUTO_INCREMENT PRIMARY KEY,
  `activity_id2` VARCHAR(32) NOT NULL,
  `activity_name` VARCHAR(100) NOT NULL,
  `activity_description` TEXT NOT NULL,
  `activity_time` DATETIME NOT NULL
);

INSERT INTO `activities` (`activity_id2`, `activity_name`, `activity_description`, `activity_time`) VALUES
('a1', 'Exploración Espacial', 'Misión espacial interactiva.', '2025-03-10 10:00:00'),
('a2', 'Robótica', 'Competencia de robots.', '2025-03-11 14:00:00');

CREATE TABLE `attendance` (
  `attendance_id` INT AUTO_INCREMENT PRIMARY KEY,
  `activity_id` INT NOT NULL,
  `participant_id` INT NOT NULL,
  `checkin_datetime` DATETIME DEFAULT NULL,
  `checkout_datetime` DATETIME DEFAULT NULL,
  FOREIGN KEY (`activity_id`) REFERENCES `activities`(`activity_id`),
  FOREIGN KEY (`participant_id`) REFERENCES `participants`(`participant_id`)
);

INSERT INTO `attendance` (`activity_id`, `participant_id`, `checkin_datetime`, `checkout_datetime`) VALUES
(1, 1, '2025-03-10 09:55:00', NULL),
(1, 2, '2025-03-10 09:57:00', NULL);

CREATE TABLE `group_activities` (
  `relation_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `activity_id` INT NOT NULL,
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`),
  FOREIGN KEY (`activity_id`) REFERENCES `activities`(`activity_id`)
);

INSERT INTO `group_activities` (`group_id`, `activity_id`) VALUES
(1, 1), (2, 2);

CREATE TABLE `group_participants` (
  `relation_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `participant_id` INT NOT NULL,
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`),
  FOREIGN KEY (`participant_id`) REFERENCES `participants`(`participant_id`)
);

INSERT INTO `group_participants` (`group_id`, `participant_id`) VALUES
(1, 1), (1, 2), (2, 4);

CREATE TABLE `payments` (
  `payment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `status` VARCHAR(32) NOT NULL,
  `amount` FLOAT NOT NULL,
  `payment_date` DATE NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
);

INSERT INTO `payments` (`user_id`, `status`, `amount`, `payment_date`) VALUES
(1, 'Paid', 150.00, '2025-02-20'),
(2, 'Pending', 200.00, '2025-02-21');

CREATE TABLE `schedule_participants` (
  `schedule_id` INT AUTO_INCREMENT PRIMARY KEY,
  `participant_id` INT NOT NULL,
  `start_day` DATE NOT NULL,
  `end_day` DATE NOT NULL,
  FOREIGN KEY (`participant_id`) REFERENCES `participants`(`participant_id`)
);

INSERT INTO `schedule_participants` (`participant_id`, `start_day`, `end_day`) VALUES
(1, '2025-03-01', '2025-03-10'),
(2, '2025-03-05', '2025-03-15');

COMMIT;
