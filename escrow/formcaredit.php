<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;

$id = empty($_POST["id"]) 
        ? $_GET["id"]
        : $_POST["id"];

$cSignCategory = trim($_POST['cSignCategory']);

$sql = " SELECT * FROM  `tContractParking` Where `cCertifiedId` = '" . $id . "' ";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {

	$data[] =$rs->fields;

	$rs->MoveNext();
}


if (!$data[0]['cId']) {
	$type='add';
}else
{
	$type='';
}




##停車場類別
$category = array(
					'1' =>  '坡道平面式',
					'2' =>  '昇降平面式',
					'3' => '坡道機械式',
					'4' => '昇降機械式'
				);
##
##

##權屬

$owner_type = array(
				'1' =>'有獨立權狀', 
				'2' =>'持分併入公共設施'
			);

##

##權屬

$ground = array(
				'1' =>'地上', 
				'2' =>'地下'
			);

##

##權屬

$owner = array(
				
				'3' =>'須承租繳租金',
				'4' =>'需定期抽籤', 
				'5' =>'需排隊等候',
				'6' =>'其他'
			);

##
$smarty->assign('data',$data);
$smarty->assign('type',$type);
$smarty->assign('owner',$owner);
$smarty->assign('Category',$category);
$smarty->assign('Ownertype',$owner_type);
$smarty->assign('Ground',$ground);
$smarty->assign('check',$owner_check);
$smarty->assign('cCertifiedId',$id);
$smarty->assign('cSignCategory',$cSignCategory);
$smarty->display('formcaredit.inc.tpl', '', 'escrow');

?>