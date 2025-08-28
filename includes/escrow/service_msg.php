<?php
include_once '../../openadodb.php';
include_once '../../session_check.php' ;

if (session_status() != 2) {
    session_start();
}

$_POST = escapeStr($_POST) ;

$id = $_POST['id'];
$cid = $_POST['cid'];
$date = dateChange($_POST['date'])." ".$_POST['hour'].":".$_POST['min'].":00";
$name = $_POST['man'];
$note = $_POST['note'];

switch ($_POST['type']) {
	case 'add':
			Add_Msg($cid,$date,$name,$note);
			Show_Msg($cid);
		break;
	case 'del':
			Del_Msg($id);
			Show_Msg($cid);
		break;
	default:
		# code...
		break;
}

function Del_Msg($id)
{
	global $conn;
	$sql = "UPDATE tContractService SET cDel = 1 WHERE cId ='".$id."'";

	$conn->Execute($sql);
}

function Add_Msg($cid,$date,$name,$note){
	global $conn;
	$sql = "INSERT INTO 
			tContractService
		(
			cCertifiedId,
			cDateTime,
			cName,
			cNote
		)VALUES(
			'".$cid."',
			'".date('Y-m-d H:i:s')."',
			'".$_SESSION['member_name']."',
			'".$note."'
		)";
	$conn->Execute($sql);


}

function Show_Msg($cid){
	global $conn;
	$sql = "SELECT * FROM tContractService WHERE cCertifiedId ='".$cid."' AND cDel = 0 ";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$data_service[] = $rs->fields;
		$rs->MoveNext();
	}
	$table = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
	$table .= '<tr >';
	$table .= '<td width="5%" align="left" class="tb-title2">序號</td>';
	$table .= '<td width="20%" align="left" class="tb-title2">日期/時間</td>';
	$table .= '<td width="10%" align="left" class="tb-title2">承辦</td>';
	$table .= '<td width="60%"align="left" class="tb-title2">內容</td>';
	$table .= '<td width="5%"align="center" class="tb-title2">刪除</td>';

	$table .= '</tr>';

	for ($i=0; $i < count($data_service); $i++) { 
		
		$table .='<tr>';
		$table .='<td width="5%" align="center">'.($i+1).'</td>';
		$table .='<td width="20%">'.dateChange2($data_service[$i]['cDateTime']).'</td>';
		$table .='<td width="10%">'.$data_service[$i]['cName'].'</td>';
		$table .='<td width="60%">'.$data_service[$i]['cNote'].'</td>';
		$table .='<td width="5%" align="center"><a href="javascript:void(0)" onclick="DelServiceMsg('.$data_service[$i]['cId'].')">刪除</a></td>';                                           
		$table .='</tr>';
 
	}

	echo $table;
}



function dateChange($val)
{
	// $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
	$tmp = explode('-',$val) ;
		
	if (preg_match("/000/",$tmp[0])) {	$tmp[0] = '0000' ; }
	else { $tmp[0] += 1911 ; }
		
	$val = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	return $val;
}

function dateChange2($val)
{
	// $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;
	$tmp2 = explode(' ',$val) ;
	$tmp = explode('-',$tmp2[0]) ;
		
	if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
		
	$val = $tmp[0].'-'.$tmp[1].'-'.$tmp[2]." ".$tmp2[1] ;
	unset($tmp) ;

	return $val;
}
?>