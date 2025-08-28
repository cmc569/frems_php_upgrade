<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$type      = addslashes(trim($_POST['add']));
$cid       = trim(addslashes($_POST['cid']));
$cateogry  = trim(addslashes($_POST['cateogry']));
$new_phone = addslashes(trim($_POST['new_phone']));
$others_id = addslashes(trim($_POST['others_id']));

if ($new_phone) {
    $_others_id = empty($others_id) ? 'NULL' : '"' . $others_id . '"';
    $sql        = 'INSERT INTO tContractPhone(cCertifiedId, cIdentity, cMobileNum, cOthersId) VALUES ("' . $cid . '", "' . $cateogry . '", "' . $new_phone . '", ' . $_others_id . ');';
    $conn->Execute($sql);

    $_others_id = null;unset($_others_id);
}

for ($i = 0; $i < count($_POST['phone']); $i++) {
    $_others_id = empty($others_id) ? 'IS NULL' : '"' . $others_id . '"';

    $sql = 'UPDATE tContractPhone SET cMobileNum = "' . $_POST['phone'][$i] . '" WHERE cCertifiedId = "' . $cid . '" AND cId = "' . $_POST['id'][$i] . '" AND cOthersId ' . $_others_id . ';';
    $conn->Execute($sql);

    $_others_id = null;unset($_others_id);
}

exit('ok');
