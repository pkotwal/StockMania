<?php
include('connect.inc.php');
session_start();

if(!isset($_SESSION['stock_user']) || empty($_SESSION['stock_user']))
{
	header('Location: login.php');
}

$id=$_SESSION['stock_user'];


$in_st_mon=0;
$in_net=0;


$s="SELECT * FROM `sensex` WHERE `id`=1";
				$re=mysql_query($s);
				
				$sensex_old=mysql_result($re,0,'old');
				$sensex_current=mysql_result($re,0,'price');
				$sensex_chg=mysql_result($re,0,'change');
				
					for($i=1;$i<=3;$i++)
					{
					$comm='comm'.$i;
					$query="SELECT `people`.$comm FROM `people` WHERE `id`='$id'";
					$result=mysql_query($query);
					$no=mysql_result($result,0,$comm);
					
					if($no!=0){
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
					
					if($no!=0){
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
 $in=array();
				$name_query="SELECT `name`,`surname`,`money` FROM `people` WHERE `id`='$id'";
				$result=mysql_query($name_query);
				
				if(mysql_num_rows($result)==1)
				{
					$name=mysql_result($result,0,'name');
					$surname=mysql_result($result,0,'surname');
					$money=mysql_result($result,0,'money');
				}
				
				if($money<0)
				$debt=true;
				
				else
				$debt=false;
				
$pa="SELECT `trade` FROM `pause`";
$paq=mysql_query($pa);
$pause=mysql_result($paq,0,'trade');
		

if(isset($_POST['buy']) && !empty($_POST['buy']))
{
	if($pause==='R')
	{
	if(isset($_POST['stkId']) && !empty($_POST['stkId']))
	{
	$num=$_POST['buy'];
	$stid=$_POST['stkId'];
	$q="SELECT * FROM `stocks` WHERE `id`='$stid'";
	$r=mysql_query($q);
	if(mysql_num_rows($r)==1)
	{
		$p=mysql_result($r,0,'price');
		$changed=mysql_result($r,0,'change');
		$old=mysql_result($r,0,'old_price');
	}
	$sto='stk'.$stid;
	
	$q1="SELECT `people`.`money`,`people`.$sto FROM `people` WHERE `id`='$id'";
	$r1=mysql_query($q1);
	if(mysql_num_rows($r1)==1)
	{
		$p1=mysql_result($r1,0,'money');
		$oldst=mysql_result($r1,0,$sto);
		
	}
	
	if($p1-($p*$num)>-50000)
	{
	if($debt==true)
	$new_bal=$p1-($p*$num*1.1);

	else
	$new_bal=$p1-($p*$num);
	
	$up="UPDATE `people` SET  `money` =  '$new_bal' WHERE  `id` ='$id'";
	mysql_query($up);
	
	
	 $newst=$oldst+$num;
	$upp="UPDATE `people` SET  `people`.$sto = '$newst' WHERE  `people`.`id` ='$id'";
	mysql_query($upp);
	
		if($st_id>=10)
	$stock_name='stk'.$stid;
	
	else
	$stock_name='stk0'.$stid;
	
	$l="INSERT INTO `logs` VALUES ('','$id','$stock_name','$p','$num')";
	mysql_query($l);
	
	
	
	
		$chg_p=$num*$p*0.01/100;
	$new_price=round($p+$chg_p,2);
	$chg_price=round($new_price-$old,2);
	
	$query="UPDATE `stocks` SET  `stocks`.`price` = '$new_price' WHERE  `stocks`.`id` ='$stid'";
		mysql_query($query);
		
		$query2="UPDATE `stocks` SET  `stocks`.`change` = '$chg_price' WHERE  `stocks`.`id` ='$stid'";
		mysql_query($query2);
		
	@$in[$stid+2]=$new_price;
	
	
	
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
					
					
					$sensex_current=round($sensex_current/12345678,2);
					
					$sensex_chg=$sensex_current-$sensex_old;
					
					$se="UPDATE `sensex` SET  `sensex`.`price` = '$sensex_current' WHERE  `sensex`.`id` =1";
					mysql_query($se);
					
					$see="UPDATE `sensex` SET  `sensex`.`change` = '$sensex_chg' WHERE  `sensex`.`id` =1";
					mysql_query($see);
					
					
$q="INSERT INTO `graph` VALUES('','$in[0]','$in[1]','$in[2]','$in[3]','$in[4]','$in[5]','$in[6]','$in[7]','$in[8]','$in[9]','$in[10]',
'$in[11]','$in[12]','$in[13]','$in[14]','$in[15]','$in[16]','$in[17]','$in[18]','$in[19]','$in[20]',
'$in[21]','$in[22]','$in[23]','$in[24]','$in[25]','$in[26]','$in[27]','$in[28]','$in[29]','$in[30]',
'$in[31]','$in[32]',$sensex_current)";
$r=mysql_query($q);
	
	unset($buy);
	header('Location:'. $_SERVER['REQUEST_URI']);
	}
	else
	{
		 echo '<script type="text/javascript">alert("You do not have enough money");</script>';
	}
	}
	
	
	else{
	$num=$_POST['buy'];
	$stid=$_POST['commId'];
	$q="SELECT * FROM `commo` WHERE `id`='$stid'";
	$r=mysql_query($q);
	if(mysql_num_rows($r)==1)
	{
		$p=mysql_result($r,0,'price');
		$changed=mysql_result($r,0,'change');
		$old=mysql_result($r,0,'old_price');
	}
	$sto='comm'.$stid;
	
	$q1="SELECT `people`.`money`,`people`.$sto FROM `people` WHERE `id`='$id'";
	$r1=mysql_query($q1);
	if(mysql_num_rows($r1)==1)
	{
		$p1=mysql_result($r1,0,'money');
		$oldst=mysql_result($r1,0,$sto);
		
	}
	if($p1-($p*$num)>-50000)
	{
	if($debt==true)
	$new_bal=$p1-($p*$num*1.1);

	else
	$new_bal=$p1-($p*$num);
	
	$up="UPDATE `people` SET  `money` =  '$new_bal' WHERE  `id` ='$id'";
	mysql_query($up);
	
	
	 $newst=$oldst+$num;
	$upp="UPDATE `people` SET  `people`.$sto = '$newst' WHERE  `people`.`id` ='$id'";
	mysql_query($upp);
	
	$stock_name='com0'.$stid;
	$l="INSERT INTO `logs` VALUES ('','$id','$stock_name','$p','$num')";
	mysql_query($l);
	
	
	

$chg_p=$num*$p*0.01/100;
	$new_price=round($p+$chg_p,2);
	$chg_price=round($new_price-$old,2);
	
	$query="UPDATE `commo` SET  `commo`.`price` = '$new_price' WHERE  `commo`.`id` ='$stid'";
		mysql_query($query);
		
		$query2="UPDATE `commo` SET  `commo`.`change` = '$chg_price' WHERE  `commo`.`id` ='$stid'";
		mysql_query($query2);
		
				@$in[$stid-1]=$new_price;
$q="INSERT INTO `graph` VALUES('','$in[0]','$in[1]','$in[2]','$in[3]','$in[4]','$in[5]','$in[6]','$in[7]','$in[8]','$in[9]','$in[10]',
'$in[11]','$in[12]','$in[13]','$in[14]','$in[15]','$in[16]','$in[17]','$in[18]','$in[19]','$in[20]',
'$in[21]','$in[22]','$in[23]','$in[24]','$in[25]','$in[26]','$in[27]','$in[28]','$in[29]','$in[30]',
'$in[31]','$in[32]','0')";
$r=mysql_query($q);
	unset($buy);
	header('Location:'. $_SERVER['REQUEST_URI']);
	}
	else
	{
		 echo '<script type="text/javascript">alert("You do not have enough money");</script>';
	}
	}
	}
}




if(isset($_POST['sell']) && !empty($_POST['sell']))
{
	if($pause==='R')
	{
	if(isset($_POST['stkId']) && !empty($_POST['stkId']))
	{
		$num=$_POST['sell'];
	$stid=$_POST['stkId'];
	$sto='stk'.$stid;
	
	$q1="SELECT `people`.`money`,`people`.$sto FROM `people` WHERE `id`='$id'";
	$r1=mysql_query($q1);
	if(mysql_num_rows($r1)==1)
	{
		$p1=mysql_result($r1,0,'money');
		$oldst=mysql_result($r1,0,$sto);
		
	}
	if($num<=$oldst)
	{
	
	$q="SELECT * FROM `stocks` WHERE `id`='$stid'";
	$r=mysql_query($q);
	if(mysql_num_rows($r)==1)
	{
		$p=mysql_result($r,0,'price');
		$changed=mysql_result($r,0,'change');
		$old=mysql_result($r,0,'old_price');
	}
	
	if($debt==true){
	$new_bal=$p1+($p*$num*0.9);}
	else{
	$new_bal=$p1+($p*$num);}
	
	$up="UPDATE `people` SET  `money` =  '$new_bal' WHERE  `id` ='$id'";
	mysql_query($up);
	
	
	 $newst=$oldst-$num;
	$upp="UPDATE `people` SET  `people`.$sto = '$newst' WHERE  `people`.`id` ='$id'";
	mysql_query($upp);
	
	
$chg_p=$num*$p*0.01/100;
	$new_price=round($p-$chg_p,2);
	$chg_price=round($new_price-$old,2);
	
	$query="UPDATE `stocks` SET  `stocks`.`price` = '$new_price' WHERE  `stocks`.`id` ='$stid'";
		mysql_query($query);
		
		$query2="UPDATE `stocks` SET  `stocks`.`change` = '$chg_price' WHERE  `stocks`.`id` ='$stid'";
		mysql_query($query2);
	
			@$in[$stid+2]=$new_price;
			
				
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
					
					/*mgm law nerul
					sudin patil*/
					
					
					
					
					$sensex_current=round($sensex_current/12345678,2);
					
					$se="UPDATE `sensex` SET  `sensex`.`price` = '$sensex_current' WHERE  `sensex`.`id` =1";
					mysql_query($se);
					
					$sensex_chg=$sensex_current-$sensex_old;
					$see="UPDATE `sensex` SET  `sensex`.`change` = '$sensex_chg' WHERE  `sensex`.`id` =1";
					mysql_query($see);
					
$q="INSERT INTO `graph` VALUES('','$in[0]','$in[1]','$in[2]','$in[3]','$in[4]','$in[5]','$in[6]','$in[7]','$in[8]','$in[9]','$in[10]',
'$in[11]','$in[12]','$in[13]','$in[14]','$in[15]','$in[16]','$in[17]','$in[18]','$in[19]','$in[20]',
'$in[21]','$in[22]','$in[23]','$in[24]','$in[25]','$in[26]','$in[27]','$in[28]','$in[29]','$in[30]',
'$in[31]','$in[32]',$sensex_current)";
$r=mysql_query($q);
	unset($sell);
	header('Location:'. $_SERVER['REQUEST_URI']);
	}
	}
	
	
	else{
	$num=$_POST['sell'];
	$stid=$_POST['commId'];
	$sto='comm'.$stid;
	
	$q1="SELECT `people`.`money`,`people`.$sto FROM `people` WHERE `id`='$id'";
	$r1=mysql_query($q1);
	if(mysql_num_rows($r1)==1)
	{
		$p1=mysql_result($r1,0,'money');
		$oldst=mysql_result($r1,0,$sto);
		
	}
	if($num<=$oldst)
	{
	$q="SELECT * FROM `commo` WHERE `id`='$stid'";
	$r=mysql_query($q);
	if(mysql_num_rows($r)==1)
	{
		$p=mysql_result($r,0,'price');
		$changed=mysql_result($r,0,'change');
		$old=mysql_result($r,0,'old_price');
	}
	
	if($debt==true){
	$new_bal=$p1+($p*$num*0.9);}
	else{
	$new_bal=$p1+($p*$num);}
	
	$up="UPDATE `people` SET  `money` =  '$new_bal' WHERE  `id` ='$id'";
	mysql_query($up);
	
	
	 $newst=$oldst-$num;
	$upp="UPDATE `people` SET  `people`.$sto = '$newst' WHERE  `people`.`id` ='$id'";
	mysql_query($upp);
	
$chg_p=$num*$p*0.01/100;
	$new_price=round($p-$chg_p,2);
	$chg_price=round($new_price-$old,2);
	
	$query="UPDATE `commo` SET  `commo`.`price` = '$new_price' WHERE  `commo`.`id` ='$stid'";
		mysql_query($query);
		
		$query2="UPDATE `commo` SET  `commo`.`change` = '$chg_price' WHERE  `commo`.`id` ='$stid'";
		mysql_query($query2);
		
						@$in[$stid-1]=$new_price;
$q="INSERT INTO `graph` VALUES('','$in[0]','$in[1]','$in[2]','$in[3]','$in[4]','$in[5]','$in[6]','$in[7]','$in[8]','$in[9]','$in[10]',
'$in[11]','$in[12]','$in[13]','$in[14]','$in[15]','$in[16]','$in[17]','$in[18]','$in[19]','$in[20]',
'$in[21]','$in[22]','$in[23]','$in[24]','$in[25]','$in[26]','$in[27]','$in[28]','$in[29]','$in[30]',
'$in[31]','$in[32]','0')";
$r=mysql_query($q);
	
	unset($sell);
	header('Location:'. $_SERVER['REQUEST_URI']);
	}
	}
	}
	
}


?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="style.css"> 
		<meta http-equiv="refresh" content="30" />
		<title>Stockmania-Trade</title>

	</head>
	
	<body>
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
				<h2> Name:<?php  
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
				?></h2>
				<h2 <?php if($money<0) echo'style="color:red";'; ?> > Current Balance: <?php echo ' '.$money;
				?></h2>
				</div>
				
				<div style="float:left;">
				<?php
					if($pause==='P')
					{
					echo '<h2 style="color:red";> Simulation Paused</h2>';
					}
				?>
				</div>
				
				<div id="right" style="float:right;">
				<h2> Balance in stocks: <?php echo ' '.$stk_money;
				?></h2>
				
				<h2> Net Worth: <?php echo ' '.$net;
				?></h2>
				</div>
				</div>
				
				<table cellpadding="1" id="trade">
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
							<div><a href="stock.php?id=com0<?php echo $co_id;?>">
							<?php echo $co_name.' '?>&nbsp;&nbsp;&nbsp;&nbsp;
							<?php echo round($co_price,2);
							?></a></div>
							<div>
								<div style="float:left; margin-left:135px;">
								<form action="trade.php" method="POST">
								<input type="number" name="buy" min="1" id="buy"><input type="Submit" value="BUY " >
								<input type="hidden" name="commId" value="<?php echo $co_id ?>" >
								</form>
								</div>
								<div>
								<?php
							if($debt==true){
							echo '<p style="float:left; color:red;">('.(1.1*$co_price).')</p>';
							}
							?>
								</div>
								<div style="clear:both;">
									
										<?php 
										$stok='comm'.$co_id;
										$find="SELECT `people`.$stok FROM `people` WHERE `id`='$id'";
										$found=mysql_query($find);
										if(mysql_num_rows($found)==1)
										{
											$no=mysql_result($found,0,$stok);
										}
										if($no>0)
										{?>
										<div style="float:left;margin-left:135px;">
											<form action="trade.php" method="POST">
											<input type="number" name="sell" min="1" max="<?php echo $no?>" id="sell"><input type="Submit" value="SELL" >
											<input type="hidden" name="commId" value="<?php echo $co_id ?>" >
											</form>
									</div>
									<div>
								<?php
							if($debt==true){
							echo '<p style="float:left;color:red;">('.(0.9*$co_price).')</p>';
							}
							?>
								</div>
								</div>
							<?php
							
							}
							
							
							
							?>
							
							
							
							
							</td>
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
							<div><a href="stock.php?id=stk<?php if($st_id<10) echo '0'.$st_id; else echo $st_id; ?>">
							<?php echo $st_name?>&nbsp;&nbsp;&nbsp;&nbsp;
							<?php echo round($st_price,2);
							?></a></div>
							<div>
								<div style="float:left;">
								<form action="trade.php" method="POST">
								<input type="number" name="buy" min="1" id="buy"><input type="Submit" value="BUY " >
								<input type="hidden" name="stkId" value="<?php echo $st_id ?>" >
								</form>
								</div>
								<div>
								<?php
							if($debt==true){
							echo '<p style="float:left; color:red;">('.(1.1*$st_price).')</p>';
							}
							?>
								</div>
								<div style="clear:both;">
									
										<?php 
										$stok='stk'.$st_id;
										$find="SELECT `people`.$stok FROM `people` WHERE `id`='$id'";
										$found=mysql_query($find);
										if(mysql_num_rows($found)==1)
										{
											$no=mysql_result($found,0,$stok);
										}
										if($no>0)
										{?>
										<div style="float:left;">
											<form action="trade.php" method="POST">
											<input type="number" name="sell" min="1" max="<?php echo $no?>" id="sell"><input type="Submit" value="SELL" >
											<input type="hidden" name="stkId" value="<?php echo $st_id ?>" >
											</form>
									</div>
									<div>
								<?php
							if($debt==true){
							echo '<p style="float:left;color:red;">('.(0.9*$st_price).')</p>';
							}
							?>
								</div>
								</div>
							<?php
							
							}
							
							
							
							?>
							
							
							
							
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
				
				
				
				
				
				
				
				
				
				
									
				</table>
				
			</section>
		</div>
	</body>
	

</html>
