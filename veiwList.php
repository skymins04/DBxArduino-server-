<?php
$db_conn = mysqli_connect('localhost','','','');
if(mysqli_connect_errno()) die('DB 서버에 연결할 수 없습니다. '.mysqli_connect_error());

$q = mysqli_query($db_conn,'show tables');
$tables = array();
$i = 0;
while($row = mysqli_fetch_array($q)) {
	$tables[$i] = $row[0];
	$i += 1;
}

for($i=0;$i<count($tables);$i++) {
	echo $tables[$i];
	if($i != count($tables)-1) echo ',';
}
mysqli_close($db_conn);
?>
