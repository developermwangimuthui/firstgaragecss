<?php
define('WEB_URL', 'http://garage.osofyahtech.com/');
define('ROOT_PATH', '/home/pickiepr/garage.osofyahtech.com/');


define('DB_HOSTNAME', 'garage.osofyahtech.com');
define('DB_USERNAME', 'pickiepr_garage');
define('DB_PASSWORD', 'qTopz)9+Cc5z');
define('DB_DATABASE', 'pickiepr_garage');
$link = mysql_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD) or die(mysql_error());mysql_select_db(DB_DATABASE, $link) or die(mysql_error());?>