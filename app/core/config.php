<?php

// Set the server name based on each user's virtual host or host name.
if ($_SERVER['SERVER_NAME'] == 'ucschelpdesk.local') {
    define('ROOT', 'http://ucschelpdesk.local');
} else {
    define('ROOT', 'https://www.yourwebsite.com');
}

define('DBHOST', "localhost");
define('DBPORT', 5432);
define('DBNAME', "ucschelpdesk");
define('DBUSER', "postgres");
define('DBPASSWORD', "0000");

define('DEBUG', true);

