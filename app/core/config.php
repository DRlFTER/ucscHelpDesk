<?php

// Set the server name based on each user's virtual host or host name.
if ($_SERVER['SERVER_NAME'] == 'ucschelpdesk.local') {
    define('ROOT', 'http://ucschelpdesk.local');
} else {
    define('ROOT', 'https://www.yourwebsite.com');
}

// MySQL connection settings (simple local defaults)
define('DBHOST', 'localhost');
define('DBPORT', 3306);
define('DBNAME', 'ucscHelpDesk');
define('DBUSER', 'root');
define('DBPASSWORD', '');

define('DEBUG', true);

