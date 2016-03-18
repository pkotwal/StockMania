<?php
include('connect.inc.php');
session_start();
if(!isset($_SESSION['admin']) || empty($_SESSION['admin']))
{
		header('Location: login.php');
}
$s="SELECT * FROM `sensex` WHERE `id`=1";
				$re=mysql_query($s);
				
				$sensex_old=mysql_result($re,0,'old');
				$sensex_current=mysql_result($re,0,'price');
				$sensex_chg=mysql_result($re,0,'change');
				
	
$pa="SELECT `trade` FROM `pause`";
$paq=mysql_query($pa);
$pause=mysql_result($paq,0,'trade');

if(isset($_POST['pause']) && !empty($_POST['pause']))
{
	if($pause==='P')
	{
		$qw="UPDATE `pause` SET `trade`='R'";
		$qqq=mysql_query($qw);
	}
	
	if($pause==='R')
	{
		$qw="UPDATE `pause` SET `trade`='P'";
		$qqq=mysql_query($qw);
	}
}

if(isset($_POST['upd']) && !empty($_POST['upd']))
{
	$in=array();
	$up=false;
	
	for($i=1;$i<=3;$i++)
	{
		if(isset($_POST['comm'.$i]) && !empty($_POST['comm'.$i]))
		{
			$get="SELECT `price` FROM `commo` WHERE `id`='$i'";
			$run=mysql_query($get);
			if(mysql_num_rows($run)==1)
			{
				$got=mysql_result($run,0,'price');
			}
			$new_price=$_POST['comm'.$i];
			$change=round($new_price-$got,2);
			
			$query="UPDATE `commo` SET  `commo`.`price` = '$new_price' WHERE  `commo`.`id` ='$i'";
			mysql_query($query);
			
			$query1="UPDATE `commo` SET  `commo`.`old_price` = '$got' WHERE  `commo`.`id` ='$i'";
			mysql_query($query1);
			
			$query2="UPDATE `commo` SET  `commo`.`change` = '$change' WHERE  `commo`.`id` ='$i'";
			mysql_query($query2);		
			
			
			@($in[$i-1]=$new_price);
			$up=true;
		}
		else
		$in[$i-1]=0;
	}


	for($i=1;$i<=30;$i++)
	{
		if(isset($_POST['upd'.$i]) && !empty($_POST['upd'.$i]))
		{
			$get="SELECT `price` FROM `stocks` WHERE `id`='$i'";
			$run=mysql_query($get);
			if(mysql_num_rows($run)==1)
			{
				$got=mysql_result($run,0,'price');
			}
			$new_price=round($got+($_POST['upd'.$i]*$got/100),2);
			$change=round($new_price-$got,2);
			
			$query="UPDATE `stocks` SET  `stocks`.`price` = '$new_price' WHERE  `stocks`.`id` ='$i'";
			mysql_query($query);
			
			$query1="UPDATE `stocks` SET  `stocks`.`old_price` = '$got' WHERE  `stocks`.`id` ='$i'";
			mysql_query($query1);
			
			$query2="UPDATE `stocks` SET  `stocks`.`change` = '$change' WHERE  `stocks`.`id` ='$i'";
			mysql_query($query2);	
			
				@$in[$i+2]=$new_price;
				$up=true;
		}
		else
		$in[$i+2]=0;
	}









					$q="SELECT `id`,`price` FROM `stocks` ORDER BY `change`/`old_price` DESC";
					$run=mysql_query($q);
					
					if(mysql_num_rows($run)>0)
					{$inc=1;
						while($row=mysql_fetch_assoc($run))
						{	
							
							$st_id=$row['id'];
							$st_price=$row['price'];
							
							
							$sensex_current+=($st_price*10000000);
							
						}
						}
					$s="SELECT `price` FROM `sensex` WHERE `id`=1";
					$re=mysql_query($s);
					
					$sensex_old=mysql_result($re,0,'price');

					
					$sensex_current=round($sensex_current/12345678,2);
					$sensex_chg=round($sensex_current-$sensex_old,2);
					$se="UPDATE `sensex` SET  `sensex`.`price` = '$sensex_current' WHERE  `sensex`.`id` =1";
					mysql_query($se);
					
					$se2="UPDATE `sensex` SET  `sensex`.`old` = '$sensex_old' WHERE  `sensex`.`id` =1";
					mysql_query($se2);
					
					$se1="UPDATE `sensex` SET  `sensex`.`change` = '$sensex_chg' WHERE  `sensex`.`id` =1";
					mysql_query($se1);


	if($up==true)
	{
	$q="INSERT INTO `graph` VALUES('','$in[0]','$in[1]','$in[2]','$in[3]','$in[4]','$in[5]','$in[6]','$in[7]','$in[8]','$in[9]','$in[10]',
	'$in[11]','$in[12]','$in[13]','$in[14]','$in[15]','$in[16]','$in[17]','$in[18]','$in[19]','$in[20]',
	'$in[21]','$in[22]','$in[23]','$in[24]','$in[25]','$in[26]','$in[27]','$in[28]','$in[29]','$in[30]',
	'$in[31]','$in[32]',$sensex_current)";
	$r=mysql_query($q);


	}
}


if(isset($_POST['news']) && !empty($_POST['news']))
	{
		$getid=$_POST['newsId'];
		
		$ins="UPDATE `news` SET `news`.`show`='Y' WHERE `id`='$getid'";
		$ex=mysql_query($ins);

	}
	
	
if(isset($_POST['rnews']) && !empty($_POST['rnews']))
	{
		$getidf=$_POST['newsIdf'];
		
		
		$insf="UPDATE `news` SET `news`.`show`='N' WHERE `id`='$getidf'";
		mysql_query($insf);
	
	}



?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="style.css"> 
		<title>Stockmania-Admin</title>		
	</head>
	
	<body>
		<div id="wrapper">
			<header>
				<img src="header-logo.png" height="100" id="logo">
				<img src="header.png" height="100" id="name">
			</header>
			
			<section>
			<div id="admin_news" style=" overflow-y:hidden;">
			
			<div  style="width:100%; overflow-y:auto; height:250px; ">
				<?php 
					$qu="SELECT * FROM `news` WHERE `show`='N' ORDER BY `id`";
					$ru=mysql_query($qu);
					if(mysql_num_rows($ru)>0)
					{
						while($row=mysql_fetch_assoc($ru))
						{
							$qnid=$row['id'];
							$qntitle=$row['title'];
							$qnimg=$row['image'];
							$qncont=$row['content'];
							$qaff=$row['affect'];
							$qshow=$row['show'];
							?>
							<div style="clear:both;">
							<img src="<?php echo $qnimg?>" width="50" height="50" style="float:left;">
							<h4 style="float:left;"><?php echo $qntitle?></h4><br>
							<form action="admin.php" method="POST">
							<input type="Submit" value="Add" name="news" style="float:right;">
							<input type="hidden" name="newsId" value="<?php echo $qnid ?>" >
							</form>
							<br><br>
							</div>
							<?php
							
							
						}
					
					}
				
				
				?>
			</div>
			
			<div style="width:100%; overflow-y:auto; height:250px; ">
				<?php 
					$qu="SELECT * FROM `news` WHERE `show`='Y' ORDER BY `id`";
					$ru=mysql_query($qu);
					if(mysql_num_rows($ru)>0)
					{
						while($row=mysql_fetch_assoc($ru))
						{
							$qnidf=$row['id'];
							$qntitlef=$row['title'];
							$qnimgf=$row['image'];
							$qncontf=$row['content'];
							$qafff=$row['affect'];
							$qshowf=$row['show'];
							?>
							<div style="clear:both;">
							<img src="<?php echo $qnimgf?>" width="50" height="50" style="float:left;">
							<h4 style="float:left;"><?php echo $qntitlef?></h4><br>
							<form action="admin.php" method="POST">
							<input type="Submit" value="Remove" name="rnews" style="float:right;">
							<input type="hidden" name="newsIdf" value="<?php echo $qnidf ?>" >
							</form>
							<br><br>
							</div>
							<?php
							
							
						}
					
					}
				
				
				?>
			</div>
			
			</div>
			
				<table cellpadding="1">
				<?php 
				$q="SELECT * FROM `commo` ORDER BY `id`";
				$run=mysql_query($q);
				
				if(mysql_num_rows($run)>0)
				{
					while($row=mysql_fetch_assoc($run))
					{
						$co_id=$row['id'];
						$co_name=$row['name'];
						$co_price=$row['price'];
						
							if($co_id%3==1)
							{
								?>
								<tr>
								<?php
							}?>
							
							<th colspan="2">
							<?php echo $co_name?></br>
							<?php echo $co_price?>
							<form action="admin.php" method="POST">
							<input type="text" name="comm<?php echo $co_id?>" min="1" id="buy">
							<input type="hidden" name="commId" value="<?php echo $co_id ?>" >
							</th>
							<?php
							if($co_id%3==0)
							{
							?>
							</tr>
							<?php
							}
						
					}
				}
	 
				$q="SELECT * FROM `stocks` ORDER BY `id`";
				$run=mysql_query($q);
				
				if(mysql_num_rows($run)>0)
				{
					while($row=mysql_fetch_assoc($run))
					{
						$st_id=$row['id'];
						$st_price=$row['price'];
						$st_name=$row['name'];
						
							if($st_id%6==1)
							{
								?>
								<tr>
								<?php
							}?>
							
							<td>
							<?php echo $st_name?></br>
							<?php echo $st_price?>
							<input type="text" name="upd<?php echo $st_id?>" min="1" id="buy">
							<input type="hidden" name="stkId" value="<?php echo $st_id ?>" >
							</td>
							<?php
							if($st_id%6==0)
							{
							?>
							</tr>
							<?php
							}
						
					}
				}
				?>
				<tr>
				<td colspan="6">
				<input type="Submit" name="upd" value="Update">
				</form>	
				
				<form method="POST" action="admin.php">
				<?php 
				$pa1="SELECT `trade` FROM `pause`";
				$paq1=mysql_query($pa1);
				$pause1=mysql_result($paq1,0,'trade');
				?>
				<input type="Submit" name="pause" value="<?php if($pause1==='P')echo 'Resume'; if($pause1==='R')echo 'Pause'; ?>">
				</form>
				</td>

				</tr>							
				</table>
				
				
			</section>
		</div>
	</body>
	

</html>
