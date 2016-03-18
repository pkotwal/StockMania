<?php
include('connect.inc.php');
include('cop.php');
session_start();

if(!isset($_SESSION['stock_user']) || empty($_SESSION['stock_user']))
{
	header('Location: login.php');
}

$id=$_SESSION['stock_user'];
$n=true;
$sensex_current=0;
$sensex_old=0;
$sensex_chg=0;
$num=0;
$in_st_mon=0;
$in_net=0;

for($i=1;$i<=3;$i++)
{
	$comm='comm'.$i;
	$query="SELECT `people`.$comm FROM `people` WHERE `id`='$id'";
	$result=mysql_query($query);
	$no=mysql_result($result,0,$comm);
				
	if($no!=0)
	{
		$qprice="SELECT `price` FROM `commo` WHERE `id`='$i'";
		$rprice=mysql_query($qprice);
		$price=mysql_result($rprice,0,'price');
		$in_st_mon=$in_st_mon + ($no*$price);
	}
}
				
for($i=1;$i<=30;$i++)
{
	$comm='stk'.$i;
	$query="SELECT `people`.$comm FROM `people` WHERE `id`='$id'";
	$result=mysql_query($query);
	$no=mysql_result($result,0,$comm);
						
	if($no!=0)
	{
		$qprice="SELECT `price` FROM `stocks` WHERE `id`='$i'";
		$rprice=mysql_query($qprice);
		$price=mysql_result($rprice,0,'price');
		$in_st_mon=$in_st_mon + ($no*$price);
	}
}
					
$q="SELECT `money` FROM `people` WHERE `id`='$id'";
$r=mysql_query($q);
$p=mysql_result($r,0,'money');
					
$in_net=$in_st_mon+$p;
				
$qs="UPDATE `people` SET  `people`.`stk_money` = '$in_st_mon' WHERE  `people`.`id` ='$id'";
mysql_query($qs);
		
$qn="UPDATE `people` SET  `people`.`net` = '$in_net' WHERE  `people`.`id` ='$id'";
mysql_query($qn);


?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="style.css"> 
		<meta http-equiv="refresh" content="10" />
		<title>Stockmania-Home</title>		
	</head>
	
	<body onload="start();">
		<div id="wrapper">
			<header>
				<img src="header-logo.png" height="100" id="logo">
				<img src="header.png" height="100" id="name">
			</header>

			<nav id="nav">
				<ul>
					<li><a href="home.php">Home</a></li>
					<li><a href="news.php">News</a></li>
					<li><a href="trade.php">Trade</a></li>
					<li><a href="profile.php">Your Profile</a></li>
				</ul>
			</nav>
			
			<section>
				<div id="profile">
					<div style="float:left;">
						<h2> 
							Name:<?php  
							$name_query="SELECT `name`,`surname`,`money`,`stk_money`,`net` FROM `people` WHERE `id`='$id'";
							$result=mysql_query($name_query);
							
							if(mysql_num_rows($result)==1)
							{
								$name=mysql_result($result,0,'name');
								$surname=mysql_result($result,0,'surname');
								$money=mysql_result($result,0,'money');
								$stk_money=mysql_result($result,0,'stk_money');
								$net=mysql_result($result,0,'net');
								echo ' '.$name.' '.$surname;
							}
							?>
						</h2>
						<h2 <?php if($money<0) echo'style="color:red";'; ?> > Current Balance: <?php echo ' '.$money;?></h2>
					</div>
					
					<div id="right" style="float:right;">
						<h2>Balance in stocks: <?php echo ' '.$stk_money;?></h2>
						<h2> Net Worth: <?php echo ' '.$net;?></h2>
					</div>
				</div>
				
				
				<table cellpadding="1">
				<?php 
				$q="SELECT `id`,`price`,`old_price`,`change`,`name` FROM `commo` ORDER BY `id`";
				$run=mysql_query($q);
				
				if(mysql_num_rows($run)>0)
				{
					while($row=mysql_fetch_assoc($run))
					{
						$co_id=$row['id'];
						$co_name=$row['name'];
						$co_price=$row['price'];
						$co_old=$row['old_price'];
						$co_chg=round($row['change'],2);
						
						if($co_chg==0)
						$per=0;
						else
						$per=round(($co_chg/$co_old)*100,2);
							if($co_id%3==1)
							{
								?>
								<tr>
								<?php
							}?>
							
							<th colspan="2">
							<a href="stock.php?id=com<?php if($co_id>9) echo $co_id; else echo '0'.$co_id;?>"><?php echo $co_name?></a><br>
							<div style="margin:5px auto;"><p style="float:left; margin-left:33%;"><?php echo $co_price?></p><?php if($co_chg>0) {echo '<img src="Up.png" style="float:left;" height="20">';} else if($co_chg<0){echo '<img src="Down.png" style="float:left;"  height="20">';}  ?></div><br>
							<p style="clear:both;" ><?php echo $co_chg.' ('.$per.'%)'?><p></th>
							<?php
							if($co_id%3==0)
							{
							?>
							</tr>
							<?php
							}
						
						
						
					}
				}
				?>
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				<?php 
				$q="SELECT `id`,`price`,`old_price`,`change`,`name` FROM `stocks` ORDER BY `change`/`old_price` DESC";
				$run=mysql_query($q);
				
				if(mysql_num_rows($run)>0)
				{$inc=1;
					while($row=mysql_fetch_assoc($run))
					{	
						
						$st_id=$row['id'];
						$st_price=$row['price'];
						$st_old=$row['old_price'];
						$st_chg=$row['change'];
						$st_name=$row['name'];
						
						/*$sensex_current+=($st_price*10000000);*/
						if($st_chg==0)
						$per=0;
						else
						$per=round(($st_chg/$st_old)*100,2);
						
						
						
							if($inc % 6 == 1)
							{
								?>
								<tr>
								<?php
							}?>
							
							<td
							<?php
							$positives=array('00','1c','42','4c','54','5e','60','75','78','7a','82','87','8a','8c','8f','94','9c','ab','ad','b5','b8','cc','d4','d6','d9','e0','f0','f7','fa','fc');
							$negatives=array('fc','fa','f7','f0','e0','d9','d6','d4','cc','b8','b5','ad','ab','9c','94','8f','8c','8a','87','82','7a','78','75','60','5e','54','4c','42','1c','00');
							$color="";
							if($per>=0)
							{
								$color=$positives[$num].'ff'.$positives[$num];
								$num++;
							}
							
							$ch=false;
							if($per<0 && $ch==false && $n==true)
							{
								$num=0;
								$ch=true;
							}
							
							if($per<0)
							{
								$color='ff'.$negatives[$num].''.$negatives[$num];
								$num++;
								$n=false;
							}
								echo ('style="background:#'.$color.';"');
							?>
							>
							<h4><a href="stock.php?id=stk<?php if($st_id>9) echo $st_id; else echo '0'.$st_id;?>"><?php echo $st_name?></a></h4>
							<?php echo $st_price?><br>
							<?php echo $st_chg.' ('.$per.'%)'?>
							</td>
							<?php
							if($inc % 6 == 0)
							{
							?>
							</tr>
							<?php
							}$inc++;
					}
				}
				?>
									
				</table>
				
				
				<div id="graph" style="margin-top:1px;">	
				
					<div id="sensex_disp" style="background-color:#bac8de;color:black; margin-top:1px; padding-top:20px; padding-left:100px; margin-bottom:1px; height:50px; width:500px; text-align:center;">
					<?php
				
				$s="SELECT * FROM `sensex` WHERE `id`=1";
				$re=mysql_query($s);
				
				$sensex_old=mysql_result($re,0,'old');
				$sensex_current=mysql_result($re,0,'price');
				$sensex_chg=mysql_result($re,0,'change');
				
				$sensex_chg=round($sensex_chg,2);

				if($sensex_chg>0)
					$c=1;
					
					
					
					if($sensex_chg<0)
					$c=-1;
				/*$sensex_current=round($sensex_current/12345678,2);
				$sensex_chg=round($sensex_current-$sensex_old,2);
				$se="UPDATE `sensex` SET  `sensex`.`price` = '$sensex_current' WHERE  `sensex`.`id` =1";
				mysql_query($se);
				
				$se1="UPDATE `sensex` SET  `sensex`.`change` = '$sensex_chg' WHERE  `sensex`.`id` =1";
				mysql_query($se1);*/

				?>
				
				<h1 style=" float:left;">Sensex</h1>
				<h1 style="<?php if ($c==1) echo 'color:#006401;';  if ($c==-1) echo 'color:#d50a0a;';?> float:left;  margin-left:20px;"><?php echo $sensex_current;?></h1>
				
				
				<?php
					if($c==1)
					echo'<img src="Up.png" height="30" style=" float:left; margin-left:20px;" >';
					
					
					
					else if($c==-1)
					echo'<img src="Down.png" height="30" style=" float:left; margin-left:20px;">';
						?>
				<h1 style="<?php if ($c==1) echo 'color:#006401;';  if ($c==-1) echo 'color:#d50a0a;';?> float:left;  margin-left:20px;"><?php echo $sensex_chg;?></h1>
				<h1 style="<?php if ($c==1) echo 'color:#006401;';  if ($c==-1) echo 'color:#d50a0a;';?> float:left;  margin-left:20px;"><?php if($sensex_chg==0)
						$per=0;
						else
						$per=round(($sensex_chg/$sensex_old)*100,2); echo '('.$per.'%)';?></h1>
					</div>
					<img id="graph "src="sweg.php?id=sen00" style="margin-top:2px;">
				</div>
			</section>
		</div>
		<?php copyss();?>
	</body>
	

</html>
