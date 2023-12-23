CREATE DATABASE IF NOT EXISTS `moonshine_demo`;
CREATE DATABASE IF NOT EXISTS `moonshine_demo_test`;

CREATE USER IF NOT EXISTS 'moonshine_demo'@'%' IDENTIFIED BY '12345';

GRANT ALL PRIVILEGES ON `moonshine_demo`.* TO 'moonshine_demo'@'%';
GRANT ALL PRIVILEGES ON `moonshine_demo_test`.* TO 'moonshine_demo'@'%';

GRANT SELECT  ON `information\_schema`.* TO 'moonshine_demo'@'%';
FLUSH PRIVILEGES;

SET GLOBAL time_zone = 'Europe/Moscow';
