<?php

/* for site
$mysql_host='mysql.serversfree.com';
$mysql_user='u889048381_admin';
$mysql_pass='pnk11031995';
$mysql_db='u889048381_conn';*/

$mysql_host='localhost';
$mysql_user='root';
$mysql_pass='';
$mysql_db='stockmania';

if(!@mysql_connect($mysql_host,$mysql_user,$mysql_pass) || !@mysql_select_db($mysql_db))
{
	die('Could not connect to server. Please try again later.');
}
?>