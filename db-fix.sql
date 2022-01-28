UPDATE
    mysql.user
SET
    plugin = 'mysql_native_password',
    authentication_string  = ''
WHERE
    user = 'root';

/*
CREATE DATABASE phpmyadmin;
SHOW DATABASES LIKE '%phpmyadmin%';
*/

/*
CREATE 
    USER 'test1'@'localhost'
    IDENTIFIED BY 'test1password';

GRANT ALL PRIVILEGES ON phpmyadmin.* TO 'test1'@'localhost';

SELECT user, host FROM mysql.user WHERE user='test1' AND host='localhost';

*/
