DROP DATABASE IF EXISTS `proyecto_integrador`;
CREATE DATABASE `proyecto_integrador`;
USE `proyecto_integrador`;

CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id2` VARCHAR(32) NOT NULL,
  `user_email` VARCHAR(124) NOT NULL,
  `user_password` VARCHAR(256) NOT NULL,
  `role` INT NOT NULL,
  `enabled` TINYINT NOT NULL,
  `has_schedule` tinyint(4) NOT NULL
);

INSERT INTO `users` (`user_id`, `user_id2`, `user_email`, `user_password`, `role`, `enabled`, `has_schedule`) VALUES
(1, 'DD7F7D89G7D897G8FD97G89D', 'tutor1@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 0, 1, 1),
(2, 'G7F89D7F8D97G8F9D7897F98', 'tutor2@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 0, 1, 1),
(3, 'F78GTB7DF86FD7S86G7F8D67', 'tutor3@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 0, 1, 1),
(4, 'GUF87F6D78CVCB78678F678D', 'tutor4@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 0, 1, 1),
(5, 'BGFY8HTDYSFUIDYBIUYDFUIY', 'tutor5@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 0, 1, 1),
(6, 'F56D7F5S6FG56HD78F67D86D', 'monitor1@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 1, 1, 0),
(7, 'GJFD7T6S7F8GFD678FG96DF7', 'monitor2@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 1, 1, 0),
(8, 'GFDU89FAS7DF89GFG7B8S9D7', 'monitor3@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 1, 1, 0),
(9, 'DFGHBYF7CYBVFG78YD8FYVYF', 'admin1@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 2, 1, 0),
(10, 'FYGD78G6DF78DG6786S7D8F6', 'admin2@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 2, 1, 0),
(11, 'DSADASFGFGHRE5F346V45666', 'admin3@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 2, 1, 0),
(12, 'FU8BYDS789AD6F78B678S678', 'admin4@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 2, 1, 0),
(13, 'FHU8DSFT78GH8HFC8GH8DI88', 'admin5@example.com', '$2y$10$HxDS3vwtVn/1t/zKgG7B0.a7CxShwvwWfK9uFdJz3MhaJTm0m2vcG', 2, 1, 0);

CREATE TABLE `user_details` (
  `detail_id` INT AUTO_INCREMENT PRIMARY KEY,
  `detail_id2` VARCHAR(32) NOT NULL,
  `user_id` INT NOT NULL,
  `user_name` VARCHAR(256) NOT NULL,
  `user_email` VARCHAR(124) NOT NULL,
  `user_relationship` VARCHAR(128) NOT NULL,
  `user_dni` VARCHAR(9) NOT NULL,
  `user_phone_number` VARCHAR(16) NOT NULL,
  `is_main` tinyint(4) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
);

INSERT INTO `user_details` (`detail_id2`, `user_id`, `user_name`, `user_email`, `user_relationship`, `user_dni`, `user_phone_number`, `is_main`) VALUES
('D1A2B3C4E5F6G7H8', 1, 'Laura Sánchez', 'tutor1@example.com', 'Madre', '11111111A', '600123456', 1),
('I9J8K7L6M5N4O3P2', 2, 'Carlos Pérez', 'tutor2@example.com', 'Padre', '22222222B', '601987654', 1),
('K8L7M6N5O4P3Q2R1', 3, 'Ana Torres', 'tutor3@example.com', 'Madre', '33333333C', '602234567', 1),
('M9N8O7P6Q5R4S3T2', 4, 'José Ramírez', 'tutor4@example.com', 'Padre', '44444444D', '603345678', 1),
('N8O7P6Q5R4S3T2U1', 5, 'Sofía Domínguez', 'tutor5@example.com', 'Madre', '55555555E', '604456789', 1),
('O7P6Q5R4S3T2U1V9', 6, 'Luis Gómez', 'monitor1@example.com', 'Monitor', '66666666F', '605567890', 1),
('P6Q5R4S3T2U1V9W8', 7, 'María Fernández', 'monitor2@example.com', 'Monitor', '77777777G', '606678901', 1),
('Q5R4S3T2U1V9W8X7', 8, 'Javier Herrera', 'monitor3@example.com', 'Monitor', '88888888H', '607789012', 1),
('R4S3T2U1V9W8X7Y6', 9, 'Raúl Martín', 'monitor4@example.com', 'Monitor', '99999999I', '608890123', 1),
('S3T2U1V9W8X7Y6Z5', 10, 'Carmen Ortega', 'monitor5@example.com', 'Monitor', '10101010J', '609901234', 1),
('T2U1V9W8X7Y6Z5A4', 11, 'José Fernández', 'admin1@example.com', 'Administrador', '11111111K', '610112345', 1),
('U1V9W8X7Y6Z5A4B3', 12, 'Elena Ruiz', 'admin2@example.com', 'Administrador', '12121212L', '611223456', 1),
('V9W8X7Y6Z5A4B3C2', 13, 'Pedro López', 'admin3@example.com', 'Administrador', '13131313M', '612334567', 1);

CREATE TABLE `groups` (
  `group_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id2` varchar(32) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `monitor_id` int(11) NOT NULL,
  `group_size` int(11) NOT NULL
);

INSERT INTO `groups` (`group_id2`, `group_name`, `monitor_id`, `group_size`) VALUES
('A1B2C3D4E5F6G7H8', 'Grupo A', 6, 6),
('I9J8K7L6M5N4O3P2', 'Grupo B', 7, 6),
('Q1R2S3T4U5V6W7X8', 'Grupo C', 8, 6),
('Y9Z8A7B6C5D4E3F2', 'Grupo D', 9, 6),
('T2U1V9W8X7Y6Z5A4', 'Grupo E', 10, 6);

CREATE TABLE `participants` (
  `participant_id` INT AUTO_INCREMENT PRIMARY KEY,
  `participant_id2` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `participant_name` varchar(100) NOT NULL,
  `participant_birth_date` date NOT NULL,
  `participant_allergies` text NOT NULL,
  `participant_special_needs` text NOT NULL,
  `participant_medical_treatment` text NOT NULL
);

INSERT INTO `participants` (`participant_id2`, `user_id`, `participant_name`, `participant_birth_date`, `participant_allergies`, `participant_special_needs`, `participant_medical_treatment`) VALUES
('e26b20124473be7d8ff9eb4cead70a9f', 1, 'Diego Sánchez', '2016-03-23', 'Alergia a frutos secos', 'Necesita estructura en actividades', 'Lleva EpiPen'),
('D7S8A9D78AS9D', 1, 'María López', '2015-06-12', 'Sin alergias', 'Dificultades de atención', 'Ninguno'),
('F83D98A7D89F7G9H', 1, 'Carlos Ruiz', '2017-07-19', 'Intolerancia a la lactosa', '', ''),
('A7F8G9H7D89F7G9H', 2, 'Ana Gómez', '2016-09-14', 'Alergia al polen', 'Hiperactividad', 'Medicación diaria'),
('B8G9H7F8D79G8H9J', 2, 'Pedro Fernández', '2018-11-22', '', 'Dificultades motoras', 'Sesiones de fisioterapia'),
('C9H8G7F6D85G79H8', 2, 'Lucía Sánchez', '2019-05-30', 'Alergia al huevo', '', 'Lleva antihistamínico'),
('D9E8F7G6H5J4K3L2', 3, 'Jorge Herrera', '2015-08-08', 'Alergia al marisco', '', ''),
('E7F6G5H4J3K2L1M9', 3, 'Laura Ramírez', '2017-04-17', '', '', ''),
('F6G5H4J3K2L1M9N8', 3, 'Diego Torres', '2018-01-29', 'Alergia al polvo', '', ''),
('G5H4J3K2L1M9N8O7', 4, 'Sofía Martín', '2016-02-10', '', 'Problemas de visión', 'Usa gafas correctivas'),
('H4J3K2L1M9N8O7P6', 4, 'David Navarro', '2015-10-12', 'Alergia a frutos secos', '', 'Lleva EpiPen'),
('I3J2K1L9M8N7O6P5', 4, 'Carmen Ortega', '2019-06-20', 'Alergia a los ácaros', '', ''),
('J2K1L9M8N7O6P5Q4', 5, 'Raúl Castro', '2017-03-25', '', 'Problemas de movilidad', 'Requiere apoyo para desplazarse'),
('K1L9M8N7O6P5Q4R3', 5, 'Beatriz Domínguez', '2018-09-15', 'Alergia a los frutos rojos', '', ''),
('L9M8N7O6P5Q4R3S2', 5, 'Manuel Ríos', '2016-12-03', 'Alergia al gluten', '', 'Dieta especial sin gluten');

CREATE TABLE `group_participants` (
  `relation_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `participant_id` INT NOT NULL
);

INSERT INTO `group_participants` (`group_id`, `participant_id`) VALUES
(1, 1), (1, 2), (1, 3),
(2, 5), (2, 6), (3, 7),
(3, 9), (4, 10), (4, 12),
(5, 13), (5, 14), (5, 15);


CREATE TABLE `activities` (
  `activity_id` INT AUTO_INCREMENT PRIMARY KEY,
  `activity_id2` varchar(32) NOT NULL,
  `activity_name_es` varchar(100) NOT NULL,
  `activity_name_en` varchar(100) NOT NULL,
  `activity_description_es` text NOT NULL,
  `activity_description_en` text NOT NULL,
  `activity_tags_es` varchar(128) NOT NULL,
  `activity_tags_en` varchar(128) NOT NULL,
  `activity_datetime_start` datetime NOT NULL,
  `activity_datetime_end` datetime NOT NULL
);

INSERT INTO `activities` (`activity_id`, `activity_id2`, `activity_name_es`, `activity_name_en`, `activity_description_es`, `activity_description_en`, `activity_tags_es`, `activity_tags_en`, `activity_datetime_start`, `activity_datetime_end`) VALUES
(1, 'GFY8D97D8S97G89FD78GF9D789', 'Construye tu Explorador Robótico', 'Build Your Robotic Explorer', 'Los niños diseñarán y ensamblarán su propio rover robótico, equipado con motores y sensores, para explorar terrenos simulados. Aprenderán sobre movilidad autónoma, adaptación a obstáculos y el uso de programación para controlar su explorador. Esta actividad fomenta la creatividad y el pensamiento lógico, permitiéndoles experimentar con ingeniería mecánica y robótica.', 'Children will design and assemble their own robotic rover, equipped with motors and sensors, to explore simulated terrains. They will learn about autonomous mobility, obstacle adaptation, and how to use programming to control their explorer. This activity fosters creativity and logical thinking, allowing them to experiment with mechanical engineering and robotics.', 'Exploración y Tecnología', 'Exploration and Technology', '2025-03-02 12:00:00', '2025-03-02 18:00:00'),
(2, 'HJD98D7G87D9F78G9FD7GF98D7', 'Vuelo de Drones', 'Drone Flight', 'Una competencia emocionante donde los participantes pilotarán drones a través de un circuito lleno de obstáculos. Los niños aprenderán sobre estabilidad, control y maniobrabilidad, además de conceptos básicos de aerodinámica. Esta actividad mejora la coordinación ojo-mano y desarrolla habilidades de precisión y toma de decisiones bajo presión.', 'An exciting competition where participants will pilot drones through a circuit full of obstacles. Children will learn about stability, control, and maneuverability, as well as basic aerodynamics concepts. This activity improves hand-eye coordination and develops precision skills and decision-making under pressure.', 'Estrategia y Competencia', 'Strategy and Competition', '2025-03-02 15:00:00', '2025-03-02 17:00:00'),
(3, 'KJH87D98G79D87F9G8FD79G8D7', 'El Último Guerrero en Pie', 'Last Warrior Standing', 'Enfrenta tu robot en un combate de sumo donde el objetivo es empujar al oponente fuera del ring. Los niños diseñarán, programarán y optimizarán sus robots para mejorar su estabilidad y fuerza. Aprenderán sobre sensores de proximidad, estrategias de defensa y ataque, y la importancia del ajuste mecánico en la robótica de competencia.', 'Face off your robot in a sumo battle where the goal is to push the opponent out of the ring. Children will design, program, and optimize their robots to improve stability and strength. They will learn about proximity sensors, defensive and attack strategies, and the importance of mechanical adjustments in competition robotics.', 'Estrategia y Competencia', 'Strategy and Competition', '2025-02-18 10:00:00', '2025-02-18 12:00:00'),
(4, 'LKJH98D7G98F7D9G87FD98G7D9', 'Construye tu Catapulta Robótica', 'Build Your Robotic Catapult', 'Usando principios de ingeniería y mecánica, los niños diseñarán y construirán una catapulta robótica capaz de lanzar proyectiles con precisión. Experimentarán con diferentes ángulos de lanzamiento, tensión de los materiales y programación de mecanismos automatizados para optimizar su puntería. Esta actividad combina creatividad, resolución de problemas y habilidades matemáticas.', 'Using engineering and mechanical principles, children will design and build a robotic catapult capable of launching projectiles with precision. They will experiment with different launch angles, material tension, and program automated mechanisms to optimize accuracy. This activity combines creativity, problem-solving, and mathematical skills.', 'Ingeniería y Puntería', 'Engineering and Accuracy', '2025-02-18 12:00:00', '2025-02-18 14:00:00'),
(5, 'POIU98D7F98G7D98G7FD98G7D9', 'Crea tu propia Bombilla', 'Create Your Own Light Bulb', 'Los participantes aprenderán sobre energías renovables y la importancia de la electricidad en la robótica. Construirán circuitos eléctricos básicos, explorando el uso de paneles solares, motores y LEDs. Descubrirán cómo los circuitos eléctricos alimentan los robots y experimentarán con conexiones en serie y paralelo para optimizar el consumo energético.', 'Participants will learn about renewable energies and the importance of electricity in robotics. They will build basic electrical circuits, exploring the use of solar panels, motors, and LEDs. They will discover how electrical circuits power robots and experiment with series and parallel connections to optimize energy consumption.', 'Innovación y Energía', 'Innovation and Energy', '2025-02-20 10:00:00', '2025-02-20 12:00:00');


CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `attendance_id2` varchar(32) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `checkin_datetime` datetime DEFAULT NULL,
  `checkout_datetime` datetime DEFAULT NULL
);

INSERT INTO `attendance` (`attendance_id`, `attendance_id2`, `activity_id`, `participant_id`, `checkin_datetime`, `checkout_datetime`) VALUES
(1, '40D771E4671625387847ACEB222C86F1', 1, 1, '2025-02-28 10:35:00', NULL),
(2, '255663C9FD7B3E17D4D7ECC261E4ED8C', 1, 2, '2025-02-28 10:35:00', NULL);

ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `unique_attendance` (`activity_id`,`participant_id`);


CREATE TABLE `group_activities` (
  `relation_id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `activity_id` INT NOT NULL,
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`group_id`),
  FOREIGN KEY (`activity_id`) REFERENCES `activities`(`activity_id`)
);

INSERT INTO `group_activities` (`relation_id`, `group_id`, `activity_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(4, 2, 2),
(6, 3, 2),
(7, 4, 1),
(9, 5, 1),
(10, 5, 2);

CREATE TABLE `payments` (
  `payment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `payment_id2` varchar(32) NOT NULL,
  `user_id` INT NOT NULL,
  `status` VARCHAR(32) NOT NULL,
  `amount` FLOAT NOT NULL,
  `payment_date` DATE NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
);

INSERT INTO `payments` (`payment_id2`, `user_id`, `status`, `amount`, `payment_date`) VALUES
('A1B2C3D4E5F6G7H8', 1, 'Paid', 150.00, '2025-02-20'),
('I9J8K7L6M5N4O3P2', 2, 'Pending', 200.00, '2025-02-21'),
('Q1R2S3T4U5V6W7X8', 3, 'Paid', 180.00, '2025-02-22'),
('Y9Z8A7B6C5D4E3F2', 4, 'Pending', 220.00, '2025-02-23'),
('T2U1V9W8X7Y6Z5A4', 5, 'Paid', 170.00, '2025-02-24'),
('B2C3D4E5F6G7H8I9', 6, 'Pending', 250.00, '2025-02-25'),
('C3D4E5F6G7H8I9J8', 7, 'Paid', 160.00, '2025-02-26'),
('D4E5F6G7H8I9J8K7', 8, 'Pending', 190.00, '2025-02-27'),
('E5F6G7H8I9J8K7L6', 9, 'Paid', 200.00, '2025-02-28'),
('F6G7H8I9J8K7L6M5', 10, 'Pending', 210.00, '2025-03-01');

CREATE TABLE `schedule_participants` (
  `schedule_id` INT AUTO_INCREMENT PRIMARY KEY,
  `participant_id` INT NOT NULL,
  `start_day` DATE NOT NULL,
  `end_day` DATE NOT NULL,
  FOREIGN KEY (`participant_id`) REFERENCES `participants`(`participant_id`)
);

INSERT INTO `schedule_participants` (`participant_id`, `start_day`, `end_day`) VALUES
(1, '2025-03-02', '2025-03-16'),
(2, '2025-03-03', '2025-03-17'),
(3, '2025-03-04', '2025-03-18'),
(4, '2025-03-05', '2025-03-19'),
(5, '2025-03-06', '2025-03-20'),
(6, '2025-03-07', '2025-03-21'),
(7, '2025-03-08', '2025-03-22'),
(8, '2025-03-09', '2025-03-23'),
(9, '2025-03-10', '2025-03-24'),
(10, '2025-03-11', '2025-03-25');

COMMIT;
