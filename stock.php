<?php
include('connect.inc.php');
 include('phpMyGraph5.0.php');
 include('cop.php');
session_start();

if(!isset($_SESSION['stock_user']) || empty($_SESSION['stock_user']))
{
	header('Location: login.php');
}
$stk_n;
$id_user=$_SESSION['stock_user'];
$st=$_GET['id'];
$id=substr($st,-2,2);
$id_s=$id;

 if(substr($id,0,1)==='0')
 $id_s=substr($id,1,1);
 
$in_st_mon=0;
$in_net=0;
$c=0;

$pa="SELECT `trade` FROM `pause`";
$paq=mysql_query($pa);
$pause=mysql_result($paq,0,'trade');
 
				if(strpos($st,"com")===0)
{
$query="SELECT `name`,`change`,`price`,`old_price` FROM `commo` WHERE `id`= '$id_s'";
		$result=mysql_query($query);
		if(mysql_num_rows($result)==1)
		{
			$name=mysql_result($result,0,'name');
			$change=mysql_result($result,0,'change');
			$price=mysql_result($result,0,'price');
			$old=mysql_result($result,0,'old_price');
			
		}
	$stk_n='comm'.substr($st,-1,2);		
	$share_q="SELECT $stk_n FROM `people` WHERE `id`='$id_user'";
	$share_r=mysql_query($share_q);
	$share_no=mysql_result($share_r,0,$stk_n);
	
}
else if(strpos($st,"stk")===0)
{
	if($id<10)
	$stk_n='stk'.substr($st,-1,2);
	else
	$stk_n='stk'.$id;
	
	$share_q="SELECT $stk_n FROM `people` WHERE `id`='$id_user'";
	$share_r=mysql_query($share_q);
	$share_no=mysql_result($share_r,0,$stk_n);
	
	$query="SELECT `name`,`change`,`price`,`old_price` FROM `stocks` WHERE `id`= '$id_s'";
		$result=mysql_query($query);
		if(mysql_num_rows($result)==1)
		{
		$name=mysql_result($result,0,'name');
		$change=mysql_result($result,0,'change');
		$price=mysql_result($result,0,'price');
		$old=mysql_result($result,0,'old_price');
		}
}
					if($change>0)
					$c=1;
					
					
					
					if($change<0)
					$c=-1;
					
					 $in=array();
					 $s="SELECT * FROM `sensex` WHERE `id`=1";
				$re=mysql_query($s);
				
				$sensex_old=mysql_result($re,0,'old');
				$sensex_current=mysql_result($re,0,'price');
				$sensex_chg=mysql_result($re,0,'change');

				for($i=1;$i<=3;$i++)
					{
					$comm='comm'.$i;
					$query="SELECT `people`.$comm FROM `people` WHERE `id`='$id_user'";
					$result=mysql_query($query);
					$no=mysql_result($result,0,$comm);
					
					if($no!=0){
					$qprice="SELECT `price` FROM `commo` WHERE `id`='$i'";
					$rprice=mysql_query($qprice);
					$sprice=mysql_result($rprice,0,'price');
					$in_st_mon=$in_st_mon + ($no*$sprice);
					}
					}
					
					for($i=1;$i<=30;$i++)
					{
					$comm='stk'.$i;
					$query="SELECT `people`.$comm FROM `people` WHERE `id`='$id_user'";
					$result=mysql_query($query);
					$no=mysql_result($result,0,$comm);
					
					if($no!=0){
					$qprice="SELECT `price` FROM `stocks` WHERE `id`='$i'";
					$rprice=mysql_query($qprice);
					$sprice=mysql_result($rprice,0,'price');
					$in_st_mon=$in_st_mon + ($no*$sprice);
					}
					}
					
					$q="SELECT `money` FROM `people` WHERE `id`='$id_user'";
					$r=mysql_query($q);
					$p=mysql_result($r,0,'money');
					
					$in_net=$in_st_mon+$p;
					
					$qs="UPDATE `people` SET  `people`.`stk_money` = '$in_st_mon' WHERE  `people`.`id` ='$id_user'";
					mysql_query($qs);
					
					$qn="UPDATE `people` SET  `people`.`net` = '$in_net' WHERE  `people`.`id` ='$id_user'";
					mysql_query($qn);
				
				
				
				
if(isset($_POST['buy']) && !empty($_POST['buy']))
{
	if($pause==='R')
	{
	if(substr($st,0,1)==="s") 
	{
	$num=$_POST['buy'];
	$q="SELECT * FROM `stocks` WHERE `id`='$id_s'";
	$r=mysql_query($q);
	if(mysql_num_rows($r)==1)
	{
		$p=mysql_result($r,0,'price');
		$changed=mysql_result($r,0,'change');
		$old=mysql_result($r,0,'old_price');
	}
	$sto='stk'.$id_s;
	
	$q1="SELECT `people`.`money`,`people`.$sto FROM `people` WHERE `id`='$id_user'";
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
	
	$up="UPDATE `people` SET  `money` =  '$new_bal' WHERE  `id` ='$id_user'";
	mysql_query($up);
	
	
	 $newst=$oldst+$num;
	$upp="UPDATE `people` SET  `people`.$sto = '$newst' WHERE  `people`.`id` ='$id_user'";
	mysql_query($upp);
		
	$l="INSERT INTO `logs` VALUES ('','$id_user','$st','$p','$num')";
	mysql_query($l);
	
		$chg_p=$num*$p*0.01/100;
	$new_price=round($p+$chg_p,2);
	$chg_price=round($new_price-$old,2);
	
	$query="UPDATE `stocks` SET  `stocks`.`price` = '$new_price' WHERE  `stocks`.`id` ='$id_s'";
		mysql_query($query);
		
		$query2="UPDATE `stocks` SET  `stocks`.`change` = '$chg_price' WHERE  `stocks`.`id` ='$id_s'";
		mysql_query($query2);
		
	@$in[$id_s+2]=$new_price;
	
	
	
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
	}
	else
	{
		$num=$_POST['buy'];
	$q="SELECT * FROM `commo` WHERE `id`='$id_s'";
	$r=mysql_query($q);
	if(mysql_num_rows($r)==1)
	{
		$p=mysql_result($r,0,'price');
		$changed=mysql_result($r,0,'change');
		$old=mysql_result($r,0,'old_price');
	}
	$id=substr($id,1,1);
	$sto='comm'.$id;
	
	$q1="SELECT `people`.`money`,`people`.$sto FROM `people` WHERE `id`='$id_user'";
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
	
	$up="UPDATE `people` SET  `money` =  '$new_bal' WHERE  `id` ='$id_user'";
	mysql_query($up);
	
	
	 $newst=$oldst+$num;
	$upp="UPDATE `people` SET  `people`.$sto = '$newst' WHERE  `people`.`id` ='$id_user'";
	mysql_query($upp);
	
	
	$l="INSERT INTO `logs` VALUES ('','$id_user','$st','$p','$num')";
	mysql_query($l);
	
$chg_p=$num*$p*0.01/100;
	$new_price=round($p+$chg_p,2);
	$chg_price=round($new_price-$old,2);
	
	$query="UPDATE `commo` SET  `commo`.`price` = '$new_price' WHERE  `commo`.`id` ='$id_s'";
		mysql_query($query);
		
		$query2="UPDATE `commo` SET  `commo`.`change` = '$chg_price' WHERE  `commo`.`id` ='$id_s'";
		mysql_query($query2);
		
				@$in[$id_s-1]=$new_price;
$q="INSERT INTO `graph` VALUES('','$in[0]','$in[1]','$in[2]','$in[3]','$in[4]','$in[5]','$in[6]','$in[7]','$in[8]','$in[9]','$in[10]',
'$in[11]','$in[12]','$in[13]','$in[14]','$in[15]','$in[16]','$in[17]','$in[18]','$in[19]','$in[20]',
'$in[21]','$in[22]','$in[23]','$in[24]','$in[25]','$in[26]','$in[27]','$in[28]','$in[29]','$in[30]',
'$in[31]','$in[32]','0')";
$r=mysql_query($q);
	unset($buy);
	header('Location:'. $_SERVER['REQUEST_URI']);
	}
}
}
}




if(isset($_POST['sell']) && !empty($_POST['sell']))
{
	if($pause==='R')
	{
	if(substr($st,0,1)==="s") 
	{
	$num=$_POST['sell'];
	$sto='stk'.$id_s;
	
	$q1="SELECT `people`.`money`,`people`.$sto FROM `people` WHERE `id`='$id_user'";
	$r1=mysql_query($q1);
	if(mysql_num_rows($r1)==1)
	{
		$p1=mysql_result($r1,0,'money');
		$oldst=mysql_result($r1,0,$sto);
		
	}
	if($num<=$oldst)
	{
	$q="SELECT * FROM `stocks` WHERE `id`='$id_s'";
	$r=mysql_query($q);
	if(mysql_num_rows($r)==1)
	{
		$p=mysql_result($r,0,'price');
		$changed=mysql_result($r,0,'change');
		$old=mysql_result($r,0,'old_price');
	}
	
	if($debt==true)
	$new_bal=$p1+($p*$num*0.9);

	else
	$new_bal=$p1+($p*$num);
	
	$up="UPDATE `people` SET  `money` =  '$new_bal' WHERE  `id` ='$id_user'";
	mysql_query($up);
	
	
	 $newst=$oldst-$num;
	$upp="UPDATE `people` SET  `people`.$sto = '$newst' WHERE  `people`.`id` ='$id_user'";
	mysql_query($upp);
	
	
	
		$chg_p=$num*$p*0.01/100;
	$new_price=round($p-$chg_p,2);
	$chg_price=round($new_price-$old,2);
	
	$query="UPDATE `stocks` SET  `stocks`.`price` = '$new_price' WHERE  `stocks`.`id` ='$id_s'";
		mysql_query($query);
		
		$query2="UPDATE `stocks` SET  `stocks`.`change` = '$chg_price' WHERE  `stocks`.`id` ='$id_s'";
		mysql_query($query2);
		
	@$in[$id_s+2]=$new_price;
	
	
	
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
	
	unset($sell);
	header('Location:'. $_SERVER['REQUEST_URI']);
	
	
	}
	}
	else
	{
		$num=$_POST['sell'];
		$id=substr($id,1,1);
		echo $sto='comm'.$id;
	
	$q1="SELECT `people`.`money`,`people`.$sto FROM `people` WHERE `id`='$id_user'";
	$r1=mysql_query($q1);
	if(mysql_num_rows($r1)==1)
	{
		$p1=mysql_result($r1,0,'money');
		$oldst=mysql_result($r1,0,$sto);
		
	}
	if($num<=$oldst)
		{
	$q="SELECT * FROM `commo` WHERE `id`='$id_s'";
	$r=mysql_query($q);
	if(mysql_num_rows($r)==1)
	{
		$p=mysql_result($r,0,'price');
		$changed=mysql_result($r,0,'change');
		$old=mysql_result($r,0,'old_price');
	}
	$id=substr($id,1,1);
	
	if($debt==true)
	$new_bal=$p1+($p*$num*0.9);

	else
	$new_bal=$p1+($p*$num);
	
	$up="UPDATE `people` SET  `money` =  '$new_bal' WHERE  `id` ='$id_user'";
	mysql_query($up);
	
	
	 $newst=$oldst-$num;
	$upp="UPDATE `people` SET  `people`.$sto = '$newst' WHERE  `people`.`id` ='$id_user'";
	mysql_query($upp);
	

$chg_p=$num*$p*0.01/100;
	$new_price=round($p-$chg_p,2);
	$chg_price=round($new_price-$old,2);
	
	$query="UPDATE `commo` SET  `commo`.`price` = '$new_price' WHERE  `commo`.`id` ='$id_s'";
		mysql_query($query);
		
		$query2="UPDATE `commo` SET  `commo`.`change` = '$chg_price' WHERE  `commo`.`id` ='$id_s'";
		mysql_query($query2);
		
				@$in[$id_s-1]=$new_price;
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
		<meta http-equiv="refresh" content="15" />
		<title>Stockmania-Stock</title>
		
		<script type="text/javascript">
 /* setInterval("my_function();",60000); 
  
    function my_function(){
        window.location = location.href;
    }*/
	
	function reloadGraph() {
   var now = new Date();

   document.images['graph'].src = 'graph' + now.getTime();

   // Start new timer (1 min)
   timeoutID = setTimeout('reloadGraph()', 1000);
}
</script>
		
	</head>
	
	<body  onload="reloadGraph()">
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
				$name_query="SELECT `name`,`surname`,`money`,`stk_money`,`net` FROM `people` WHERE `id`='$id_user'";
				$result=mysql_query($name_query);
				
				if(mysql_num_rows($result)==1)
				{
					$name_u=mysql_result($result,0,'name');
					$surname=mysql_result($result,0,'surname');
					$money=mysql_result($result,0,'money');
					$stk_money=mysql_result($result,0,'stk_money');
					$net=mysql_result($result,0,'net');
					echo ' '.$name_u.' '.$surname;
				}
				if($money<0)
				$debt=true;
				
				else 
				$debt=false;
				
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
				
				
				
				
				
				
				
				
				
				<div id="graph" style="margin-top:1px;">	
				<div id="stock_disp" style="background-color:#bac8de;color:black; margin-top:1px; padding-top:20px; padding-left:100px; margin-bottom:1px; height:50px; width:500px; text-align:center;">
					<?php
				if($change>0)
					$c=1;
					
					
					
					if($change<0)
					$c=-1;
				?>
				
				<h1 style=" float:left;"><?php echo $name?></h1>
				<h1 style="<?php if ($c==1) echo 'color:#006401;';  if ($c==-1) echo 'color:#d50a0a;';?> float:left;  margin-left:20px;"><?php echo $price;?></h1>
				
				
				<?php
					if($c==1)
					echo'<img src="Up.png" height="30" style=" float:left; margin-left:20px;" >';
					
					
					
					else if($c==-1)
					echo'<img src="Down.png" height="30" style=" float:left; margin-left:20px;">';
						?>
				<h1 style="<?php if ($c==1) echo 'color:#006401;';  if ($c==-1) echo 'color:#d50a0a;';?> float:left;  margin-left:20px;"><?php echo $change;?></h1>
				<h1 style="<?php if ($c==1) echo 'color:#006401;';  if ($c==-1) echo 'color:#d50a0a;';?> float:left;  margin-left:20px;"><?php if($change==0)
						$per=0;
						else
						$per=round(($change/$old)*100,2); echo '('.$per.'%)';?></h1>
					</div>
					<img id="graph "src="sweg.php?id=<?php echo $st;?>">
				</div>
				
				
				<div style="margin-top:1px;">
				
				<div style="background-color:#bac8de;color:black; float:right; margin-top:1px; padding-top:20px; padding-left:50px; margin-bottom:1px; height:50px; width:530px; text-align:center;">
						<div style="float:left;">
						<form action="stock.php?id=<?php echo $st;?>" method="POST">
								<input type="number" name="buy" min="1" id="buy"><input type="Submit" value="BUY " >
								</form>
						</div>
								<?php if($debt==true){
							echo '<p style="float:left; color:red;">('.(1.1*$price).')</p>';
							}?>
							
							<?php
							if($share_no>0)
							{?>
							<div style="float:left; margin-left:75px">
						<form action="stock.php?id=<?php echo $st;?>" method="POST">
								<input type="number" name="sell" min="1" max="<?php echo $share_no ?>" id="sell"><input type="Submit" value="SELL " >
								</form>
						</div>
								<?php if($debt==true){
							echo '<p style="float:left; color:red;">('.(0.9*$price).')</p>';
							}
							
							}?>
							
							
		
				</div>
				
				
				<div style=" width:590px; float:right; overflow-y:auto; height:355px;">
				<?php
				$qu="SELECT * FROM `news` WHERE `show`='Y' ORDER BY `id` DESC";
					$ru=mysql_query($qu);
					if(mysql_num_rows($ru)>0)
					{
						while($row=mysql_fetch_assoc($ru))
						{
							$nid=$row['id'];
							$ntitle=$row['title'];
							$nimg=$row['image'];
							$ncont=$row['content'];
							$aff=$row['affect'];
							
							if (strpos($aff,','.$st.',') !== false) {
							?>
							<div style="clear:both; text-align:center;">
							<hr>
							<h2><?php echo $ntitle?></h2><br>
							<img src="<?php echo $nimg?>" width="150"  style="float:left;">
							<div style="text-align:left;"><?php echo $ncont?></div>
							</div>
							<br>
							<?php
							
							
						}
						}
					
					}?>
				</div>
				</div>
				
			</section>
			<?php copyss();?>
		</div>
		
	</body>
	

</html>
