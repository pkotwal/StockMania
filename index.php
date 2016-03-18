<?php
session_start();
if(isset($_SESSION['stock_user']) && !empty($_SESSION['stock_user']))
{
	header('Location: home.php');
}
else
{
	header('Location: login.php');
}

?>