<?php    
include('connect.inc.php');
 include('/jpgraph-3.5.0b1/src/jpgraph.php');
 include('/jpgraph-3.5.0b1/src/jpgraph_line.php'); 
 header("Content-type: image/png");
 $st=$_GET['id'];
	$id=substr($st,-2,2);
	$name="";
	
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
		$s=$row[$st];
		if($s!=0){
		$b[$a]=$s;
		$a++;}
	}
	}
	

// Create the graph. These two calls are always required
$graph = new Graph(600,355);
$graph->SetScale('textlin');
// Create the linear plot
$lineplot=new LinePlot($b);
$lineplot->SetColor('blue');
$lineplot->SetFillColor('#ddeeff');


// Add the plot to the graph
$graph->Add($lineplot);
$graph->title->Set($name);
// Display the graph
$graph->Stroke();
?>