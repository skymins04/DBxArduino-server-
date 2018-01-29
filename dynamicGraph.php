<?php
/*DB 연결 및 테이블 데이터 로드*/
$db_conn = mysqli_connect('localhost','','','');
if(mysqli_connect_errno()) die('DB 서버에 연결할 수 없습니다. '.mysqli_connect_error());

$TBname = $_GET["TB"];

$i = -2;
$datatitle = array();
$query = mysqli_query($db_conn,'DESC '.$TBname);
while($row = mysqli_fetch_array($query)) {
	if($i>=0)$datatitle[$i] = $row["Field"];
	$i += 1;
}

$i = 0;
$time=array(); $datas=array();
$query = mysqli_query($db_conn,'SELECT * FROM '.$TBname);
while($row = mysqli_fetch_array($query)) {
	$time[$i] = explode(' ',$row["time"])[1];
	for($j=0;$j<count($datatitle);$j++) {
		$datas[$j][$i]=$row[$datatitle[$j]];
	}
	$i += 1 ;
}

mysqli_close($db_conn);

/*그래프 제목*/
$graphtitle = $TBname.' Graph';

/*그래프 인덱스*/
$im_w = 800 + $_GET["addx"]; //전체 이미지 크기
$im_h = 400 + $_GET["addy"];
$title_h = 100;

$left_margin = 50; //여백 크기
$bottom_margin = round(7.5*strlen($time[0])+15);

$window_w = $im_w - $left_margin; //실질적 그래프 크기
$window_h = $im_h - $bottom_margin;

/*이미지 생성*/
$im = imagecreate($im_w,$im_h+$title_h);
/*색상 정의*/
//라인색
$white = imagecolorallocate($im,255,255,255);
$black = imagecolorallocate($im,0,0,0);
$gray = imagecolorallocate($im,210,210,210);
$red = imagecolorallocate($im,255,0,0);
$lightred = imagecolorallocate($im,255,100,100);
$orange = imagecolorallocate($im,238,101,0);
$darkorange = imagecolorallocate($im,187,80,0);
$lightblue = imagecolorallocate($im,30,154,255);
$lightgreen = imagecolorallocate($im,0,150,157);
//그래프 값 문자열 색
$lightred_forV = imagecolorallocate($im,175,20,20);
$orange_forV = imagecolorallocate($im,158,21,0);
$darkorange_forV = imagecolorallocate($im,107,0,0);
$lightblue_forV = imagecolorallocate($im,0,74,175);
$lightgreen_forV = imagecolorallocate($im,0,70,77);

$linecolor = array($orange,$darkorange,$lightblue,$lightgreen,$lightred);
$valuecolor = array($orange_forV,$darkorange_forV,$lightblue_forV,$lightgreen_forV,$lightred_forV);

/*그래프 제목 생성*/
$newX = ($im_w-7.5*strlen($graphtitle))/2;
$newY = $title_h/2-9;
imagestring($im,5,$newX,$newY,$graphtitle,$black);

/*배경색 및 구분선*/
imagefill($im,0,0,$white);
imageline($im,$left_margin,$title_h,$left_margin,$window_h+$title_h,$black);
imageline($im,$left_margin,$window_h+$title_h,$im_w,$window_h+$title_h,$black);

/*가로선*/
$min_tenth = (int)($_GET["min"]/10)*10;
$min_one = (int)(($_GET["min"]-$min_tenth)/5)*5;
$minValue = $min_tenth + $min_one;
for($i = $window_h-5+$title_h; $i >= $_GET["unit"]*$_GET["height"]+$title_h; $i -= $_GET["unit"]*$_GET["height"]) {
	imageline($im,$left_margin+1,$i,$im_w,$i,$gray);
	imagestring($im,4,$left_margin-7.5*strlen((string)$minValue)-5,$i-9,$minValue,$black);
	$minValue += $_GET["unit"];
}
$minValue = $min_tenth + $min_one;

/*세부 그래프*/
imagesetthickness($im,2);
for($i = 0; $i < count($datas); $i++) {
	$newX = $left_margin + 5;
	$newY = $window_h-5-$datas[$i][0]*$_GET["height"]+$minValue*$_GET["height"]+$title_h;
	if($_GET["showPoint"] == 'True')imageline($im,$newX,$newY-2,$newX,$newY+2,$red);
	//if($_GET["showValue"] == 'True')imageStringup($im,3,$newX,$newY-5,$datas[$i][0],$valuecolor[$i]);
	imagestringup($im,3,$newX-7,$window_h+5+7.5*strlen($time[0])+$title_h,$time[0],$black);
	$oldX = $newX;
	$oldY = $newY;
	for($j = 1; $j < count($datas[$i]); $j++) {
		$newX += ($window_w - 10)/count($datas[$i]);
		$newY = $window_h-5-$datas[$i][$j]*$_GET["height"]+$minValue*$_GET["height"]+$title_h;
		if($_GET["showPoint"] == 'True')imageline($im,$newX,$newY-2,$newX,$newY+2,$red);
		//if($_GET["showValue"] == 'True')imageStringup($im,3,$newX,$newY-5,$datas[$i][$j],$valuecolor[$i]);
		imageline($im,$oldX,$oldY,$newX,$newY,$linecolor[$i]);
		$oldX = $newX;
        	$oldY = $newY;
		if($i == count($datas)-1) {
			imagestringup($im,3,$newX-7,$window_h+5+7.5*strlen($time[$j])+$title_h,$time[$j],$black);
		}
	}
}

/*그래프 값 출력*/
if($_GET["showValue"] == 'True') {
	imagesetthickness($im,2);
	for($i = 0; $i < count($datas); $i++) {
        	$newX = $left_margin + 5;
        	$newY = $window_h-5-$datas[$i][0]*$_GET["height"]+$minValue*$_GET["height"]+$title_h;
        	imageStringup($im,3,$newX,$newY-5,$datas[$i][0],$valuecolor[$i]);
        	for($j = 1; $j < count($datas[$i]); $j++) {
                	$newX += ($window_w - 10)/count($datas[$i]);
                	$newY = $window_h-5-$datas[$i][$j]*$_GET["height"]+$minValue*$_GET["height"]+$title_h;
                	imageStringup($im,3,$newX,$newY-5,$datas[$i][$j],$valuecolor[$i]);
        	}
	}

}

/*항목 설명*/
for($i = 0; $i < count($datas); $i++) {
	$newX = $im_w-20-7.5*strlen($datatitle[$i])-10;
	$newY = $title_h+1+$i*20;
	imageline($im,$newX,$newY,$newX+15,$newY,$linecolor[$i]);
	imagestring($im,4,$newX+20,$newY-9,$datatitle[$i],$linecolor[$i]);
}

/*전송*/
header('Content-type: image/png');
imagepng($im);
unset($im);
?>
