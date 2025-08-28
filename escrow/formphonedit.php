<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/sms.class.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$cid           = trim(addslashes($_GET['cid']));
$cateogry      = trim(addslashes($_GET['t']));
$cSignCategory = trim(addslashes($_GET['cSignCategory']));
$others_id     = trim(addslashes($_GET['others_id']));

$_others_id = empty($others_id) ? 'IS NULL' : ' = "' . $others_id . '"';

$sql = 'SELECT * FROM tContractPhone WHERE cCertifiedId = "' . $cid . '" AND cIdentity = "' . $cateogry . '" AND cOthersId ' . $_others_id . ';';
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $list[] = $rs->fields;
    $rs->MoveNext();
}

$smarty->assign('cSignCategory', $cSignCategory);
$smarty->assign('data', $list);
$smarty->assign('cCertifiedId', $cid);
$smarty->assign('cateogry', $cateogry);
$smarty->assign('others_id', $others_id);
$smarty->display('formphonedit.inc.tpl', '', 'escrow');
