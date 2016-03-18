<?php    
    //Set content-type header
	
 header("Content-type: image/png");

    //Include phpMyGraph5.0.php
	include('connect.inc.php');
    include_once('phpMyGraph5.0.php');
    //Set config directives

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
	//006401
	//d50a0a
   $cfg['title'] = $name;
    $cfg['width'] = 600;
    $cfg['height'] = 355;
	$cfg['column-color']='#006401';
	
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
	$disp=1;
	while($count<$a)
		{
			if($b[$count]!=0)
			{
		$data[$disp]=$b[$count];
		$disp++;
		}
$count++;

		}
    //Set data
   /* $data = array(
        'Jan' => 200,
        'Feb' => 356,
        'Mar' => 133,
        'Apr' => 767,
        'May' => 870,
        'Jun' => 670,
        'Jul' => 450,
        'Aug' => 668,
        'Sep' => 235,
        'Oct' => 233,
        'Nov' => 778,
        'Dec' => 213
    );*/
    
    //Create phpMyGraph instance
    $graph = new phpMyGraph();

    //Parse
    $graph->parseVerticalPolygonGraph($data, $cfg);
?>