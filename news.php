 <?php
include('connect.inc.php');
include('cop.php');
session_start();

if(!isset($_SESSION['stock_user']) || empty($_SESSION['stock_user']))
{
	header('Location: login.php');
}
$id=$_SESSION['stock_user'];

$in_st_mon=0;
$in_net=0;

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

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="style.css"> 
		<meta http-equiv="refresh" content="10" />
		<title>Stockmania-News</title>
		
		<script type="text/javascript">
  setInterval("my_function();",60000); 
  
    function my_function(){
        window.location = location.href;
    }
</script>
		
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
				
				<div id="right" style="float:right;">
				<h2> Worth in stock: <?php echo ' '.$stk_money;
				?></h2>
				
				<h2> Net Worth: <?php echo ' '.$net;
				?></h2>
				</div>
				</div>
				
				<div style=" width:100%; overflow-y:auto; height:440px;">
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
							?>
							<div style="clear:both; text-align:center;">
							<hr>
							<h2><?php echo $ntitle?></h2><br>
							<img src="<?php echo $nimg?>" width="250"  style="float:left;">
							<div style="text-align:left;"><?php echo $ncont?></div>
							</div>
							<br>
							<?php
							
							
						}
					
					}
					else
					{
						echo '<h2 style="text-align:center;">No news stories to show at the moment.</h2>';
					}
				
				
				?>
			</div>
				
				
			</section>
		</div>
		<?php copyss();?>
	</body>
	

</html>
