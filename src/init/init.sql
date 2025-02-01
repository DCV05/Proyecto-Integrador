CREATE DATABASE polaris;
USE polaris;

CREATE TABLE polaris_pages (
  page_id INT AUTO_INCREMENT PRIMARY KEY,
  url varchar(100) not null,
  redirect varchar(100) not null,
  page_title varchar(100) not null,
  file varchar(100) not null,
  title_seo varchar(100) not null
);

INSERT INTO polaris_pages (page_id, url, redirect, page_title, file, title_seo) VALUES
(1, '/index', '', 'Index', 'Index/Index', 'Index'),
(2, '/login', '', 'Login', 'Login/Login', 'Login'),
(3, '/signup', '', 'Signup', 'Signup/Signup', 'Signup'),
(4, '/participant/edit', '', 'ParticipantEdit', 'ParticipantEdit/participants_edit', 'Editar participante'),
(5, '/tutor/edit', '', 'TutorEdit', 'TutorEdit/TutorEdit', 'Editar tutor'),
(6, '/', '/login', '', '', '');

CREATE DATABASE proyecto_integrador;
USE proyecto_integrador;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE activities (
  activity_id INT(11) NOT NULL AUTO_INCREMENT,
  activity_id2 VARCHAR(32) NOT NULL,
  activity_name VARCHAR(100) NOT NULL,
  activity_description TEXT NOT NULL,
  activity_time DATETIME NOT NULL,
  PRIMARY KEY (activity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO activities (activity_id, activity_id2, activity_name, activity_description, activity_time) VALUES
(1, '58b3d1f2bf25ba639db113ccdbd37e08', 'Misión de Exploradores', 'Los niños trabajan juntos como astronautas en una misión espacial, resolviendo desafíos y explorando un nuevo planeta mientras fortalecen su espíritu de equipo.', '2025-01-13 15:00:00');

CREATE TABLE attendance (
  attendance_id INT(11) NOT NULL AUTO_INCREMENT,
  attendance_id2 VARCHAR(32) NOT NULL,
  activity_id INT(11) NOT NULL,
  participant_id INT(11) NOT NULL,
  checkin_datetime DATETIME NOT NULL,
  checkout_datetime DATETIME NOT NULL,
  PRIMARY KEY (attendance_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO attendance (attendance_id, attendance_id2, activity_id, participant_id, checkin_datetime, checkout_datetime) VALUES
(1, 'f3fe12b84bb737d358e9179d253cb3fa', 1, 1, '2025-01-13 15:00:00', '2025-01-13 18:00:00');

CREATE TABLE blog_posts (
  post_id INT(11) NOT NULL AUTO_INCREMENT,
  post_id2 VARCHAR(32) NOT NULL,
  user_id INT(11) NOT NULL,
  post_title VARCHAR(100) NOT NULL,
  post_description VARCHAR(256) NOT NULL,
  insert_date DATETIME NOT NULL,
  PRIMARY KEY (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

CREATE TABLE participants (
  participant_id INT(11) NOT NULL AUTO_INCREMENT,
  participant_id2 VARCHAR(32) NOT NULL,
  user_id INT(11) NOT NULL,
  participant_name VARCHAR(100) NOT NULL,
  birth_date DATE NOT NULL,
  allergies TEXT NOT NULL,
  special_needs TEXT NOT NULL,
  PRIMARY KEY (participant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO participants (participant_id, participant_id2, user_id, participant_name, birth_date, allergies, special_needs) VALUES
(1, 'e26b20124473be7d8ff9eb4cead70a9f', 1, 'Diego Sánchez', '2016-03-10', 'Alérgico a los frutos secos y al polen. Para evitar cualquier reacción, su dieta en el campamento es estrictamente controlada y su equipo siempre lleva un EpiPen en caso de emergencia. Durante las actividades al aire libre, los monitores supervisan de cerca cualquier exposición al entorno natural, asegurándose de que siempre tenga un espacio seguro donde jugar sin riesgos. Además, su grupo ha sido informado sobre su alergia para fomentar un ambiente de apoyo y cuidado.', 'Para que su experiencia en el campamento sea cómoda, cuenta con un horario estructurado y zonas de descanso donde puede relajarse si necesita un momento de calma. Su monitor ha sido capacitado para comprender sus necesidades y adaptar las actividades según su ritmo, asegurando que pueda participar en cada aventura de manera divertida y sin estrés.');

CREATE TABLE payments (
  payment_id INT(11) NOT NULL AUTO_INCREMENT,
  payment_id2 VARCHAR(32) NOT NULL,
  user_id INT(11) NOT NULL,
  status VARCHAR(32) NOT NULL,
  amount FLOAT NOT NULL,
  payment_date DATE NOT NULL,
  PRIMARY KEY (payment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

CREATE TABLE users (
  user_id INT(11) NOT NULL AUTO_INCREMENT,
  user_id2 VARCHAR(32) NOT NULL,
  user_name VARCHAR(256) NOT NULL,
  user_email VARCHAR(100) NOT NULL,
  user_password VARCHAR(256) NOT NULL,
  role INT(11) NOT NULL,
  enabled TINYINT(4) NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE KEY email (user_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO users (user_id, user_id2, user_name, user_email, user_password, role, enabled) VALUES
(1, '4476b3f8cc574da8014d6f16e6fa5de5', 'Laura Sánchez', 'tutor1@example.com', '$2y$10$Lg4ETIn6wlPyPF.gisuwV.nq/AmpaMFpfqV7zoxFTC.k15Fc8caHS', 0, 1),
(2, 'dcdfe398b9540c5b78f4e90e4b57e9f3', 'Isabel Rodríguez', 'monitor1@example.com', '$2y$10$wScjuh9blod3ez9gPbwZt.nscC23xAylF/iyw1iFR2piKC0KmGJSS', 1, 1),
(3, '7880121ba2504dd37d857a41b7049442', 'Michael Scott', 'admin1@example.com', '$2y$10$DpWtM3w84h7uN81UeaFAOu2HKA7b2ZmJFgHXtOIOSsLKEPlppIW0u', 2, 1);

CREATE TABLE user_details (
  detail_id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  user_name VARCHAR(256) NOT NULL,
  dni VARCHAR(9) NOT NULL,
  phone_number VARCHAR(16) NOT NULL,
  PRIMARY KEY (detail_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO user_details (detail_id, user_id, user_name, dni, phone_number) VALUES
(1, 1, 'Laura Sánchez', '11111111A', '55555555'),
(2, 1, 'Carlos Rodríguez', '2222222B', '66666666');