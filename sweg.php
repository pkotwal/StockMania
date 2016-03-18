<?php    
   include("phpgraphlib.php");
   include("connect.inc.php");
   
   $st=$_GET['id'];
   $data=array();
   for($i=0;$i<$st;$i++)
   $data[$i]=$i;
   
	$st=$_GET['id'];
	$id=substr($st,-2,2);
	$name="";
	$low=50000;
	$high=0;
	
if(strpos($st,"com")===0)
{
$query="SELECT `name` FROM `commo` WHERE `id`= '$id'";
		$result=mysql_query($query);
		if(mysql_num_rows($result)==1)
		{
			 $name=mysql_result($result,0,'name');
		}
}
else if(strpos($st,"stk")===0)
{
	$query="SELECT `name` FROM `stocks` WHERE `id`= '$id'";
		$result=mysql_query($query);
		if(mysql_num_rows($result)==1)
		{
		 $name=mysql_result($result,0,'name');
		}
}

else if(strpos($st,"sen")===0)
{
	$name="Sensex";
	$st="Sensex";
}

	
	$b=array();
	$a=0;
	$q="SELECT $st FROM `graph` ORDER BY `id`";
	$r=mysql_query($q);
	if(mysql_num_rows($r)>0)
	{
	while($row=mysql_fetch_assoc($r))
	{
		$b[$a]=$row[$st];
		$a++;
	}
	}
	$data=array();
	$count=0; 
	$disp=0;
	while($count<$a)
		{
			if($b[$count]!=0)
			{		
		$data[$disp]=$b[$count];
		$disp++;
		}
$count++;

		}
		
		$s=floor($disp/100);
		$data=array();
	$count=0; 
	$disp=0;
	while($count<$a)
		{
			if($b[$count]!=0)
			{
			
			
		if($b[$count]>$high)
			$high=$b[$count];
			
			if($b[$count]<$low)
			$low=$b[$count];
			
			
		$data[$disp]=$b[$count];
		$disp++;
		}
$count=$count+$s+1;

		}
		
		$graph = new PHPGraphLib(600,355);
$graph->addData($data);
$graph->setTitle($name);
$graph->setBars(false);
$graph->setLineColor('blue');
$graph->setGrid(false);
$graph->setLine(true);
$graph->setDataPoints(false);
$graph->setDataPointColor('maroon');
$graph->setDataValues(false);
$graph->setDataValueColor('maroon');
$graph->setGoalLineColor('red');
$graph->setRange($high,$low);
$graph->setXValues(true);
$graph->createGraph();

?>