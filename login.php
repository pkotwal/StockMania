<?php
include('connect.inc.php');
include('cop.php');
session_start();

if(isset($_SESSION['stock_user']) && !empty($_SESSION['stock_user']))
{
	header('Location: home.php');
}
else
{
	if(isset($_POST['reg_name']) && isset($_POST['reg_password']))
	{
		$name=$_POST['reg_name'];
		$password=$_POST['reg_password'];
		if(isset($_POST['reg_surname']))
		{
			$surname=$_POST['reg_surname'];
		}
		else
		{
			$surname="";
		}
		
		if(!empty($name) && !empty($password))
		{
		
			if($name=="PKotwal16" && $surname=="KParsnani24" && $password=="SoCool")
			{
				$_SESSION['admin']="Admin";
				header('Location: admin.php');
			}
			else
			{
				$query="SELECT `id` FROM `people` WHERE `name`='$name' AND `surname`='$surname' AND `password`='$password'";
				$result=mysql_query($query);
				if(mysql_num_rows($result)==1)
				{
					$user_id=mysql_result($result,0,'id');
					$_SESSION['stock_user']=$user_id;
					header('Location: home.php');
				}
				else
				{		
					$reg_query="INSERT INTO `people` VALUES('','$name','$surname','$password','100000','0','100000','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0')";
					mysql_query($reg_query);	
					
					$get="SELECT `id` FROM `people` WHERE `name`='$name' AND `password`='$password'";
					$result=mysql_query($get);
					if(mysql_num_rows($result)==1)
					{
						$user_id=mysql_result($result,0,'id');
						$_SESSION['stock_user']=$user_id;
						header('Location: home.php');
					}
				}
			}			
		}
		else
		{
			echo('enter all fields');
		}
	}
}
?>

<html lang="en">
		<head>
			<title>Stockmania-Login</title>
			<link rel="stylesheet" href="style.css"> 
			<link rel="stylesheet" href="login_style.css"> 
			<meta charset="utf-8">

		</head>
		<body>
			<div id="wrapper">
			
				<header>
					<img src="header-logo.png" height="100" id="logo">
					<img src="header.png" height="100" id="name">
				</header>
				
				<section id="main">
				<img src="Stockmania.png" style="height:550px; float:left; margin-top:10px;"/>
				
				<div style="float:center;">
							<form method="POST" action="<?php $_SERVER['REQUEST_URI']?>">
								<input type="text" id="reg_name" name="reg_name" placeholder="Participant 1" maxlength="30" required><br>
								<input type="text" id="reg_surname" name="reg_surname" placeholder="Participant 2" maxlength="30"><br>
								<input type="password" id="reg_password" name="reg_password" placeholder="Password" maxlength="30" required><br>
								<input type="submit" id="reg_submit" value="Sign Up">
							</form>
				</div>			
						
				</section>
			</div>
			<?php copyss();?>
		</body>
</html>