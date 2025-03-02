CREATE DATABASE if not exists `polaris`;
USE `polaris`;

CREATE TABLE `polaris_pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL,
  `redirect` varchar(255) NOT NULL,
  `page_title` varchar(100) NOT NULL,
  `file` varchar(100) NOT NULL,
  `title_seo` varchar(100) NOT NULL,
  PRIMARY KEY (`page_id`)
);

INSERT INTO `polaris_pages` (`page_id`, `url`, `redirect`, `page_title`, `file`, `title_seo`) VALUES
(1, '/index', '', 'Index', 'Index/Index', 'Index'),
(2, '/login', '', 'Login', 'Login/Login', 'Login'),
(3, '/signup', '', 'Signup', 'Signup/Signup', 'Signup'),
(4, '/', '/index', '', '', ''),
(5, '/index', '', 'Index', 'Index/Index', 'Index'),
(6, '/tutor/account', '', 'Account', 'Tutor/Account/Account', 'Account'),
(7, '/tutor/desktop', '', 'Desktop', 'Tutor/Desktop/Desktop', 'Desktop'),
(8, '/tutor/participant', '', 'Participant', 'Tutor/Participant/Participant', 'Participante'),
(9, '/monitor/desktop', '', 'Desktop', 'Monitor/Desktop/Desktop', 'Desktop'),
(10, '/monitor/account', '', 'Account', 'Monitor/Account/Account', 'Account'),
(11, '/activity', '', 'Activity', 'Activity/Activity', 'Activity'),
(12, '/monitor/attendance', '', 'Attendance', 'Monitor/Attendance/Attendance', 'Attendance'),
(13, '/participant', '', 'Participant', 'Participant/Participant', 'Participante'),
(14, '/participants', '', 'Participants', 'Participants/Participants', 'Participantes'),
(15, '/admin/account', '', 'Account', 'Admin/Account/Account', 'Account'),
(16, '/admin/desktop', '', 'Desktop', 'Admin/Desktop/Desktop', 'Desktop'),
(17, '/admin/finances', '', 'Finances', 'Admin/Finances/Finances', 'Finanzas'),
(18, '/activities', '', 'Activities', 'Activities/Activities', 'Activities'),
(20, '/users', '', 'Users', 'Users/Users', 'Usuarios'),
(21, '/contact', '', 'Contact', 'Contact/Contact', 'Contact'),
(22, '/groups', '', 'Groups', 'Groups/Groups', 'Groups'),
(23, '/group', '', 'Group', 'Groups/Group', 'Group');