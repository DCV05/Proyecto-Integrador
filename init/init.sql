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