<?php
$list = array();
if($_GET["list"] == "TB") {
	$DBname = $_GET["DB"];
	if($DBname == NULL) die('DB 명을 입력하세요');

	$db_conn = mysqli_connect('localhost','arduino','arduino',$DBname);
	if(mysqli_connect_errno()) die('DB 서버에 연결할 수 없습니다. '.mysqli_connect_error());

	$q = mysqli_query($db_conn,'show tables');
	$i = 0;
	while($row = mysqli_fetch_array($q)) {
		$list[$i] = $row[0];
		$i += 1;
	}

	for($i=0;$i<count($list);$i++) {
		echo $list[$i];
		if($i != count($list)-1) echo ',';
	}
	mysqli_close($db_conn);
}
else {
	$db_conn = mysqli_connect('localhost','arduino','arduino');
        if(mysqli_connect_errno()) die('DB 서버에 연결할 수 없습니다. '.mysqli_connect_error());

        $q = mysqli_query($db_conn,'show databases');
        $list = array();
        $i = 0;
        while($row = mysqli_fetch_array($q)) {
                $list[$i] = $row[0];
                $i += 1;
        }

        for($i=0;$i<count($list);$i++) {
                echo $list[$i];
                if($i != count($list)-1) echo ',';
        }
        mysqli_close($db_conn);
}
?>
