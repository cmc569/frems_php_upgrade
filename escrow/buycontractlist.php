<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/includes/lib.php';
require_once dirname(__DIR__) . '/includes/IDCheck.php';
$advance = new Advance();

$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '多買賣') ;



$_iden = trim($_REQUEST['iden']) ;
$save = trim($_REQUEST['save']) ;
$del = trim($_POST['del']) ;

$cCertifiedId = trim($_REQUEST['cCertifyId']) ;

$sign = trim($_REQUEST['cSingCategory']) ;

if ($_iden == 'o') {				// 6買方代理人7賣方代理人
	$_ide = '賣方代理人' ;
	$cIdentity = 7 ;
}
else if ($_iden == 'b') {			// 6買方代理人7賣方代理人
	$_ide = '買方代理人' ;
	$cIdentity = 6 ;
}
else {
	echo "資料錯誤!!" ;
	exit ;
}

//刪除資料
if ($del == 'ok') {
	$del_no = $_POST['del_no'] ;
	
	if ($del_no) {
		$sql = '
			DELETE FROM
				tContractOthers
			WHERE
				cId="'.$del_no.'"
		' ;
		
		$conn->Execute($sql) ;
	}
}
##

//儲存資料
if ($_POST) {
	// echo "<pre>";
	// print_r($_POST);

	if (!empty($_POST['oldId'])) {
		//修改
		for ($i=0; $i <= count($_POST['oldId']); $i++) { 
			if ($_POST['oldBirthdayDay_'.$i]) {
				$tmp = explode('-', $_POST['oldBirthdayDay_'.$i]);
				$birthday = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
				unset($tmp);
			}

			$sql = "UPDATE
						tContractOthers
					SET
						cIdentifyId = '".$_POST['oldIdentifyId_'.$i]."',
						cName = '".$_POST['oldName_'.$i]."',
						cBirthdayDay = '".$birthday."',
						cCountryCode = '".$_POST['oldCountryCode_'.$i]."',
						cPassport = '".$_POST['oldPassport_'.$i]."',
						cTaxTreatyCode = '".$_POST['oldTaxTreatyCode_'.$i]."',					
						cResidentLimit = '".$_POST['oldResidentLimit_'.$i]."',
						cPaymentDate = '".$_POST['oldPaymentDate_'.$i]."',
						cNHITax = '".$_POST['oldcNHITax_'.$i]."',
						cMobileNum = '".$_POST['oldMobileNum_'.$i]."',
						cRegistZip = '".$_POST['oldRegistZip_'.$i]."',
						cRegistAddr = '".$_POST['oldRegistAddr_'.$i]."',
						cBaseZip = '".$_POST['oldBaseZip_'.$i]."',
						cBaseAddr = '".$_POST['oldBaseAddr_'.$i]."',
						cBankMain = '".$_POST['oldBankMain_'.$i][0]."',
						cBankBranch = '".$_POST['oldcBankBranch_'.$i][0]."',
						cBankAccName = '".$_POST['oldBankAccName_'.$i][0]."',
						cBankAccNum = '".$_POST['oldBankAccNum_'.$i][0]."',
						cBankMoney = '".$_POST['oldBankAccMoney_'.$i][0]."',
						cChecklistBank = '".$_POST['oldChecklistBank_'.$i][0]."',
						cOtherName = '".$_POST['oldOtherName_'.$i]."',
						cEmail = '".$_POST['oldEmail_'.$i]."'
					WHERE
						cId = '".$_POST['oldId'][$i]."'";
						
			$conn->Execute($sql);
			
					 
			// echo $sql."<br>";
		}
	}
	
	//新增
	for ($i=0; $i <= $_POST['newRowCount']; $i++) { 

		if ($_POST['newName_'.$i] && $_POST['newIdentifyId_'.$i]) {
			if ($_POST['newBirthdayDay_'.$i]) {
				$tmp = explode('-', $_POST['newBirthdayDay_'.$i]);
				$birthday = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
				unset($tmp);
			}
		
			//
			$sql = "INSERT INTO 
						tContractOthers
					SET
						cCertifiedId = '".$_POST['cCertifiedId']."',
						cIdentity = '".$_POST['cIdentity']."',
						cIdentifyId = '".$_POST['newIdentifyId_'.$i]."',
						cName = '".$_POST['newName_'.$i]."',
						cBirthdayDay = '".$birthday."',
						cCountryCode = '".$_POST['newCountryCode_'.$i]."',
						cPassport = '".$_POST['newPassport_'.$i]."',
						cTaxTreatyCode = '".$_POST['newTaxTreatyCode_'.$i]."',					
						cResidentLimit = '".$_POST['newResidentLimit_'.$i]."',
						cPaymentDate = '".$_POST['newPaymentDate_'.$i]."',
						cNHITax = '".$_POST['newcNHITax_'.$i]."',
						cMobileNum = '".$_POST['newMobileNum_'.$i]."',
						cRegistZip = '".$_POST['newRegistZip_'.$i]."',
						cRegistAddr = '".$_POST['newRegistAddr_'.$i]."',
						cBaseZip = '".$_POST['newBaseZip_'.$i]."',
						cBaseAddr = '".$_POST['newBaseAddr_'.$i]."',
						cBankMain = '".$_POST['newBankMain_'.$i][0]."',
						cBankBranch = '".$_POST['newcBankBranch_'.$i][0]."',
						cBankAccName = '".$_POST['newBankAccName_'.$i][0]."',
						cBankAccNum = '".$_POST['newBankAccNum_'.$i][0]."',
						cBankMoney = '".$_POST['newBankAccMoney_'.$i][0]."',
						cChecklistBank = '".$_POST['newChecklistBank_'.$i]."',
						cEmail = '".$_POST['newEmail_'.$i]."',
						cOtherName = '".$_POST['newOtherName_'.$i]."'";

				// echo $sql;
			$conn->Execute($sql);
			$id = $conn->Insert_ID(); 
				// echo $sql."<br>";		
				// echo $_POST['newIndex_'.$i]."<bR>";

		
		}

		


		
	}
	//
	//刪除
	if ($del == 'ok') {
		$del_no = $_POST['del_no'] ;
	
		if ($del_no) {
			$sql = '
				DELETE FROM
					tContractOthers
				WHERE
					cId="'.$del_no.'"
			' ;
			
			$conn->Execute($sql) ;

			$sql = "DELETE FROM tContractCustomerBank WHERE cOtherId = '".$del_no."'";
			$conn->Execute($sql) ;

			$tlog = new TraceLog() ;
			$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '多筆買賣案件刪除') ;
		}
	}

}
##

//顯示相關資料
$sql = '
	SELECT
		a.*,
		b.zCity as cRegistCity,
		b.zArea as cRegistArea,
		c.zCity as cBaseCity,
		c.zArea as cBaseArea
	FROM
		tContractOthers AS a
	LEFT JOIN
		tZipArea AS b ON a.cRegistZip=b.zZip 
	LEFT JOIN
		tZipArea AS c ON a.cBaseZip=c.zZip
	WHERE
		a.cCertifiedId="'.$cCertifiedId.'"
		AND a.cIdentity="'.$cIdentity.'"
	ORDER BY
		a.cIdentity
	ASC ;
' ;
// echo "SQL=".$sql ;
$rs = $conn->Execute($sql) ;
$list = array();
$i = 0;
while (!$rs->EOF) {
	$arr = array();
	$arr = $rs->fields;
    switch($arr['cIdentity']) {       
	    case '6' :
	        $arr['_ide'] = '買' ;  //6買方代理人7賣方代理人
	        break ;
	    case '7' :
	        $arr['_ide'] = '賣' ;  //6買方代理人7賣方代理人
	        break ;
	    default :
	        $arr['_ide'] = '其他' ;
	        break ;
	}

	$arr['cRegistAreaMenu'] = getArea($arr['cRegistCity']);
	$arr['cBaseAreaMenu'] = getArea($arr['cBaseCity']);
	if ($arr['cBaseZip']==$arr['cRegistZip'] && $arr['cBaseAddr']==$arr['cRegistAddr']) {
        $arr['sameAddr'] ="checked=checked";
    }
    $arr['no'] = $i+1;
    $arr['cBirthdayDay'] = $advance->ConvertDateToRoc($arr['cBirthdayDay'], base::DATE_FORMAT_NUM_DATE);
	$arr['checkIDImg'] = (checkUID($arr['cIdentifyId'])) ? '<img src="/images/ok.png">' : '<img src="/images/ng.png">';
	array_push($list, $arr);
	unset($arr);
	$i++;
	$rs->MoveNext();
}
##
##檢查是否會計發票關掉
$sql = "SELECT cInvoiceClose,cSignCategory FROM tContractCase WHERE cCertifiedId ='".$cCertifiedId."'";
$rs = $conn->Execute($sql);
$cInvoiceClose = $rs->fields['cInvoiceClose'];
$cSignCategory = $rs->fields['cSignCategory'];
$checkSave = 1;
if ($cInvoiceClose == 'Y' && ( $_SESSION['member_pDep']!= 9 && $_SESSION['member_pDep'] != 10 && $_SESSION['member_pDep'] != 1)) {
	$checkSave = 0;
}
##
##縣市
$sql = 'SELECT DISTINCT zCity FROM tZipArea ORDER BY nid ASC' ;
$rs = $conn->CacheExecute($sql) ;
$menuCity[0] = "縣市";
while (!$rs->EOF) {
	$menuCity[$rs->fields['zCity']] = $rs->fields['zCity'];

	$rs->MoveNext();
}
##
function getArea($city){
	global $conn;
	$sql = 'SELECT zZip,zArea FROM tZipArea WHERE zCity="'.$city.'";' ;
	$rs = $conn->CacheExecute($sql) ;

	while (!$rs->EOF) {
		$arr[$rs->fields['zZip']] = $rs->fields['zArea'];

		$rs->moveNext();
	}
	
	return $arr;
}

##
$smarty->assign('meunCity',$menuCity);
$smarty->assign('_ide',$_ide);
$smarty->assign('save',$save);
$smarty->assign('del',$del);
$smarty->assign('cInvoiceClose',$cInvoiceClose);
$smarty->assign('cSignCategory',$cSignCategory);
$smarty->assign('checkSave',$checkSave);
$smarty->assign('cCertifiedId',$cCertifiedId);
$smarty->assign('cIdentity',$cIdentity);
$smarty->assign('list',$list);
$smarty->display('buycontractlist.inc.tpl', '', 'escrow');
?>
