<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
require_once dirname(__DIR__).'/first1DB.php';

$arr = $arr2 = array() ;
$cCertifiedId = trim(addslashes($_POST['certified_id'])) ;
$bid = trim(addslashes($_POST['bBranch'])) ;
$index = trim(addslashes($_POST['index'])) ;

$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '案件仲介簡訊修改') ;

if (count($_POST['isSelect']) > 0) {
	foreach ($_POST['isSelect'] as $k => $v) {
		$arr[] = trim(addslashes($v)) ;

	}

	$isSelect = implode(',',$arr) ;

	if(count($_POST['add'])>0)
	{
	
		for ($i=0; $i <count($_POST['add']) ; $i++) { 

			$number = $_POST['add'][$i]-1;
			$tmp3[]=trim(addslashes($_POST['smsphone'][$number]));

		}

		$phone=implode(',', $tmp3);
		$isSelect =$isSelect.','.$phone;
	}
	
	unset($arr) ;
	
	
	//更新合約書代書簡訊發送對象清單
	if ($cCertifiedId) {
		if ($isSelect) {
			$sql = 'UPDATE tContractRealestate SET cSmsTarget'.$index.'="'.$isSelect.'" WHERE cCertifyId="'.$cCertifiedId.'" AND cBranchNum'.$index.'="'.$bid.'";' ;
		
			$_conn = new first1DB();
			$_conn->exeSql($sql);
			$_conn = null; unset($_conn);
		}
	}
	##
}
else {

	if(count($_POST['add'])>0)
	{
		
		for ($i=0; $i <count($_POST['add']) ; $i++) { 

			$number = $_POST['add'][$i]-1;
			$tmp3[]=trim(addslashes($_POST['smsphone'][$number]));

		}

		$phone=implode(',', $tmp3);
		$isSelect =$phone;
	}

	$sql = 'UPDATE tContractRealestate SET cSmsTarget'.$index.'="'.$isSelect.'" WHERE cCertifyId="'.$cCertifiedId.'" AND cBranchNum'.$index.'="'.$bid.'";' ;
	// echo $sql ;
	 $conn->Execute($sql) ;
}

##
//額外新增簡訊對象

if(count($_POST['add'])>0)
{
	
	for ($i=0; $i <count($_POST['add']) ; $i++) 
	{ 
		$number = $_POST['add'][$i]-1;

		$tmp1[] = trim(addslashes($_POST['title'][$number]));
		$tmp2[] = trim(addslashes($_POST['smsname'][$number]));


		$sql='INSERT INTO tBranchSms (bBranch,bNID,bName,bMobile,bCheck_id ) VALUES ('.$bid.','.$tmp1[$i].',"'.$tmp2[$i].'","'.$tmp3[$i].'","'.$cCertifiedId.'")';
		
		$_conn = new first1DB();
		$_conn->exeSql($sql);
		$_conn = null; unset($_conn);
	}
	

	unset($tmp1) ;
	unset($tmp2) ;
	unset($tmp3) ;
}

##

  header('location: formcasesmsrealty.php?bid='.$bid.'&cid='.$cCertifiedId.'&ok=1&in='.($index+1)) ;
?>