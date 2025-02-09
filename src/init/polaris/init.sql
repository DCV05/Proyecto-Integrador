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

INSERT INTO `polaris_pages` (`url`, `redirect`, `page_title`, `file`, `title_seo`) VALUES
('/index', '', 'Index', 'Index/Index', 'Index'),
('/login', '', 'Login', 'Login/Login', 'Login'),
('/signup', '', 'Signup', 'Signup/Signup', 'Signup'),
('/', '/login', '', '', ''),
('/debug', '', 'Debug', 'Debug/Debug', 'Debug'),
('/tutor/account', '', 'Account', 'Tutor/Account/Account', 'Account'),
('/tutor/desktop', '', 'Desktop', 'Tutor/Desktop/Desktop', 'Desktop'),
('/tutor/participant', '', 'Participant', 'Tutor/Participant/Participant', 'Participante'),
('/monitor/desktop', '', 'Desktop', 'Monitor/Desktop/Desktop', 'Desktop'),
('/monitor/account', '', 'Account', 'Monitor/Account/Account', 'Account'),
('/activity', '', 'Activity', 'Activity/Activity', 'Activity'),
('/monitor/attendance', '', 'Attendance', 'Monitor/Attendance/Attendance', 'Attendance'),
('/participant', '', 'Participant', 'Participant/Participant', 'Participante'),
('/participants', '', 'Participants', 'Participants/Participants', 'Participantes'),
('/admin/account', '', 'Account', 'Admin/Account/Account', 'Account'),
('/admin/desktop', '', 'Desktop', 'Admin/Desktop/Desktop', 'Desktop'),
('/admin/finances', '', 'Finances', 'Admin/Finances/Finances', 'Finanzas'),
('/activities', '', 'Activities', 'Activities/Activities', 'Activities');