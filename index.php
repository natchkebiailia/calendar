<?php
if ( isset( $_GET['y'] ) && !empty( $_GET['y'] ) ){
	if($_GET['y']>1901 &&$_GET['y'] <2038)
		$year=$_GET['y'];
	else
		$year=2014;
}
else
	$year=2014;
function createDateRangeArray($year,$month)
{
    $dateArr=array();

    $dateFrom=mktime(0,0,0,$month,1,$year);
    $dateTo=mktime(0,0,0,$month+1,0,$year);
    if ($dateTo>=$dateFrom)
    {
        array_push($dateArr,date('d-m-Y',$dateFrom));
        while ($dateFrom<$dateTo)
        {
            $dateFrom+=24*60*60;
            array_push($dateArr,date('d-m-Y',$dateFrom));
        }
    }
    return $dateArr;
}
function makeTime($str){
	$mdy = explode("-", $str);
	return mktime(0, 0, 0, $mdy[1], $mdy[0], $mdy[2]);
}
function printOut($month,$year,$arr){
	$months=array("იანვარი ","თებერვალი ","მარტი ","აპრილი ","მაისი ","ივნისი ","ივლისი ","აგვისტო ","სექტემბერი ","ოქტომბერი ","ნოემბერი ","დეკემბერი ");
	echo"<div class='month'>
		<table class='caln'>
			<tbody>
				<tr>
				<td colspan='8'>
					<table>
						<tbody>
							<tr>
								<th>";
echo "<a style='display:inline'>".$months[$month]."</a>";
echo "<a style='display:inline'>".$year."</a>";
echo "</th>
							</tr>
						</tbody>
					</table>
				</td>
				</tr>
				<tr class='dayheaders'>
					<td title='ორშაბათი'>ორშ</td>
					<td title='სამშაბათი'>სამშ</td>
					<td title='ოთხშაბათი'>ოთხ</td>
					<td title='ხუთშაბათი'>ხუთ</td>
					<td title='პარასკევი'>პარ</td>
					<td title='შაბათი' class='hld'>შაბ</td>
					<td title='კვირა' class='hld'>კვ</td>
				</tr>";
$weekCnt=count($arr);
for($i=0;$i<$weekCnt;$i++){
	echo "<tr>";
	for($j=0;$j<7;$j++){
		if($arr[$i][$j]!="&nbsp;"){
			$now = time();
			$dt  = mktime(0, 0, 0,  $month+1,$arr[$i][$j], $year);
			$week = (int)date('W', $dt);
			$dist= floor(($dt-$now)/(60*60*24));
			$dt=getdate($dt);
			$yday=$dt['yday']+1;
		}else{
			$yday="";
			$week="";
			$dist="";
		}
		echo "<td  onmouseover='thisday(this)' onmouseout='thisdayout()' onclick='clicked(this)' id='";echo $yday."$".$week."$".($dist+1);
		echo "' class='past ";
		if($j>4)
			echo "hld";
		if($dist==-1 && $yday!="")
			echo " today";
		echo "'><a ><span>".$arr[$i][$j]."</span></a></td>";
	}
	echo "</tr>";
}
echo "</tbody>
		</table>
</div>";
}
function getArr($year,$month){
	$dateArr=createDateRangeArray($year,$month);
	$arrLength = count($dateArr);
	$curWeek=(int)date('W',makeTime($dateArr[0]));
	$ans=array();
	$tmp=array();
	for ($i = 0; $i < $arrLength; $i++) {
		if((int)date('W',makeTime($dateArr[$i]))!=$curWeek){
			array_push($ans,$tmp);
			$curWeek=(int)date('W',makeTime($dateArr[$i]));
			unset($tmp);
			$tmp = array();
			$cDay=getdate(makeTime($dateArr[$i]));
			array_push($tmp,$cDay["mday"]);
		}else{
			$cDay=getdate(makeTime($dateArr[$i]));
			array_push($tmp,$cDay["mday"]);
		}
	}
	if(count($tmp))
		array_push($ans,$tmp);
	return $ans;
}
function beautify($arr){
	$arr[0]=addWhiteSpace($arr[0],0);
	$arr[count($arr)-1]=addWhiteSpace($arr[count($arr)-1],1);
	return ($arr);
	}
function addWhiteSpace($arr,$fl){
	$dayCount=count($arr);
	$toFill=7-$dayCount;
	$tmp=array();
	if(!$fl){
		if($toFill)$tmp=array_fill(0,$toFill,"&nbsp;");
		for($i=0;$i<$dayCount;$i++)
			array_push($tmp,$arr[$i]);
	}
	else{
		for($i=0;$i<$dayCount;$i++)
			array_push($tmp,$arr[$i]);
		for($i=$toFill;$i>0;$i--)
			array_push($tmp,"&nbsp;");
	}	
	return $tmp;
}


echo "<html>
<head>
<meta charset='utf-8'>
<link rel='stylesheet' type='text/css' href='style.css'>
<link rel='stylesheet' type='text/css' href='resp.css'>
</head>
<body>";
//<body oncontextmenu='return false'>";
echo"<div class='calendar' id='calendar'>";

for($i=0;$i<12;$i++)
	printOut($i,$year,beautify(getArr($year,$i+1)));

echo"</div>";
echo "<div class='dinfo' style='display:none;'><span id='yDay'></span>
<br>
<span class='nw' id='yWeek'></span>
<br>
<span id='dist'></span>
<br>
</div>
<script>
var tipTool=document.getElementsByClassName('dinfo')[0];
var span1=document.getElementById('yDay');
var span2=document.getElementById('yWeek');
var span3=document.getElementById('dist');
function thisday(obj){

var infoArr=obj.id.split('$');
	if(infoArr[0]!=''){
		tipTool.style.display='block';
		var left=obj.offsetLeft+obj.parentElement.parentElement.parentElement.parentElement.offsetLeft;
		var top=obj.offsetTop+obj.parentElement.parentElement.parentElement.parentElement.offsetTop;
		if(top>500)var offT=-70;
		else var offT=20;
		tipTool.style.left=(left-80)+'px';
		tipTool.style.top=(top+offT)+'px';
		span1.innerHTML='დღე №'+infoArr[0];
		span2.innerHTML='კვირა №'+infoArr[1];
		if(infoArr[2]<0)span3.innerHTML=Math.abs(infoArr[2])+' დღის წინ';
		else span3.innerHTML=infoArr[2]+' დღის შემდეგ';
	}
};
function thisdayout(){
	tipTool.style.display='none';
};
function clicked(obj){
	thisday(obj);
}
</script>";
echo"</body>
</html>"
?>