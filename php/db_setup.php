<?php

//Database constats
define( 'DB_SERVER', 'localhost' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '1234' );
define( 'DB', 'pottery' );


$db = mysql_pconnect( DB_SERVER, DB_USER, DB_PASSWORD ) or die( 'Cannot connect to server' );
mysql_select_db( DB, $db ) or die('Cannot connect to Database');

?>