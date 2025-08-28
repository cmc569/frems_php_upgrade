<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';

$_POST    = escapeStr($_POST);
$_REQUEST = escapeStr($_REQUEST);

$_iden        = $_REQUEST['iden'];
$save         = $_REQUEST['save'];
$del          = $_POST['del'];
$cCertifiedId = $_REQUEST['cCertifyId'];

if ($_iden == 'o') { // 賣：2
    $BankIden = 52;
}

if ($_iden == 'b') { // 買：1
    $BankIden = 53;
}

if ($_POST) {
    // print_r($_POST);
    // file_put_contents('/var/www/html/first.twhg.com.tw/log2/QQ.log', json_encode($_POST));

    //修改
    for ($i = 0; $i <= count($_POST['oldId']); $i++) {
        if ($_POST['oldBirthdayDay_' . $i]) {
            $tmp      = explode('-', $_POST['oldBirthdayDay_' . $i]);
            $birthday = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
            $tmp      = null;unset($tmp);
        }

        if ($_POST['oldId'][$i]) {
            $sql = "UPDATE
                        tContractOthers
                    SET
                        cIdentifyId = '" . $_POST['oldIdentifyId_' . $i] . "',
                        cName = '" . $_POST['oldName_' . $i] . "',
                        cBirthdayDay = '" . $birthday . "',
                        cCountryCode = '" . $_POST['oldCountryCode_' . $i] . "',
                        cPassport = '" . $_POST['oldPassport_' . $i] . "',
                        cTaxTreatyCode = '" . $_POST['oldTaxTreatyCode_' . $i] . "',
                        cResidentLimit = '" . $_POST['oldResidentLimit_' . $i] . "',
                        cPaymentDate = '" . $_POST['oldPaymentDate_' . $i] . "',
                        cNHITax = '" . $_POST['oldcNHITax_' . $i] . "',
                        cMobileNum = '" . $_POST['oldMobileNum_' . $i] . "',
                        cRegistZip = '" . $_POST['oldRegistZip_' . $i] . "',
                        cRegistAddr = '" . $_POST['oldRegistAddr_' . $i] . "',
                        cBaseZip = '" . $_POST['oldBaseZip_' . $i] . "',
                        cBaseAddr = '" . $_POST['oldBaseAddr_' . $i] . "',
                        cBankMain = '" . $_POST['oldBankMain_' . $i][0] . "',
                        cBankBranch = '" . $_POST['oldcBankBranch_' . $i][0] . "',
                        cBankAccName = '" . $_POST['oldBankAccName_' . $i][0] . "',
                        cBankAccNum = '" . $_POST['oldBankAccNum_' . $i][0] . "',
                        cBankMoney = '" . $_POST['oldBankAccMoney_' . $i][0] . "',
                        cChecklistBank = '" . $_POST['oldChecklistBank_' . $i][0] . "',
                        cOtherName = '" . $_POST['oldOtherName_' . $i] . "',
                        cEmail = '" . $_POST['oldEmail_' . $i] . "'
                    WHERE
                        cId = '" . $_POST['oldId'][$i] . "'";
            $conn->Execute($sql);
        }

        //tContractCustomerBank後來增加所以只能切開做
        for ($j = 0; $j <= count($_POST['otherBankId_' . $i]); $j++) {
            if (($j > 0) && ($_POST['oldBankMain_' . $i][$j] != '0') && ($_POST['oldcBankBranch_' . $i][$j] != 0)) {
                $sql = "INSERT INTO
                            tContractCustomerBank
                        SET
                            cCertifiedId = '" . $_POST['cCertifiedId'] . "',
                            cIdentity = '" . $BankIden . "',
                            cBankMain = '" . $_POST['oldBankMain_' . $i][$j] . "',
                            cBankBranch = '" . $_POST['oldcBankBranch_' . $i][$j] . "',
                            cBankAccountName = '" . $_POST['oldBankAccName_' . $i][$j] . "',
                            cBankAccountNo = '" . $_POST['oldBankAccNum_' . $i][$j] . "',
                            cBankMoney = '" . $_POST['oldBankAccMoney_' . $i][$j] . "',
                            cChecklistBank  = '" . $_POST['oldChecklistBank_' . $i . '_' . $j] . "',
                            cOtherId = '" . $_POST['oldId'][$i] . "';";

                if ($_POST['otherBankId_' . $i][$j] != '') {
                    $sql = "UPDATE
                                tContractCustomerBank
                            SET
                                cBankMain = '" . $_POST['oldBankMain_' . $i][$j] . "',
                                cBankBranch = '" . $_POST['oldcBankBranch_' . $i][$j] . "',
                                cBankAccountName = '" . $_POST['oldBankAccName_' . $i][$j] . "',
                                cBankAccountNo = '" . $_POST['oldBankAccNum_' . $i][$j] . "',
                                cBankMoney = '" . $_POST['oldBankAccMoney_' . $i][$j] . "',
                                cChecklistBank  = '" . $_POST['oldChecklistBank_' . $i . '_' . $j] . "'
                            WHERE
                                cId = '" . $_POST['otherBankId_' . $i][$j] . "';";
                }

                $conn->Execute($sql);
            }
        }
    }

    //新增
    for ($i = 0; $i <= $_POST['newRowCount']; $i++) {
        if ($_POST['newName_' . $i] && $_POST['newIdentifyId_' . $i]) {
            if ($_POST['newBirthdayDay_' . $i]) {
                $tmp      = explode('-', $_POST['newBirthdayDay_' . $i]);
                $birthday = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2];
                $tmp      = null;unset($tmp);
            }

            //
            $sql = "INSERT INTO
						tContractOthers
					SET
						cCertifiedId = '" . $_POST['cCertifiedId'] . "',
						cIdentity = '" . $_POST['cIdentity'] . "',
						cIdentifyId = '" . $_POST['newIdentifyId_' . $i] . "',
						cName = '" . $_POST['newName_' . $i] . "',
						cBirthdayDay = '" . $birthday . "',
						cCountryCode = '" . $_POST['newCountryCode_' . $i] . "',
						cPassport = '" . $_POST['newPassport_' . $i] . "',
						cTaxTreatyCode = '" . $_POST['newTaxTreatyCode_' . $i] . "',
						cResidentLimit = '" . $_POST['newResidentLimit_' . $i] . "',
						cPaymentDate = '" . $_POST['newPaymentDate_' . $i] . "',
						cNHITax = '" . $_POST['newcNHITax_' . $i] . "',
						cMobileNum = '" . $_POST['newMobileNum_' . $i] . "',
						cRegistZip = '" . $_POST['newRegistZip_' . $i] . "',
						cRegistAddr = '" . $_POST['newRegistAddr_' . $i] . "',
						cBaseZip = '" . $_POST['newBaseZip_' . $i] . "',
						cBaseAddr = '" . $_POST['newBaseAddr_' . $i] . "',
						cBankMain = '" . $_POST['newBankMain_' . $i][0] . "',
						cBankBranch = '" . $_POST['newcBankBranch_' . $i][0] . "',
						cBankAccName = '" . $_POST['newBankAccName_' . $i][0] . "',
						cBankAccNum = '" . $_POST['newBankAccNum_' . $i][0] . "',
						cBankMoney = '" . $_POST['newBankAccMoney_' . $i][0] . "',
						cChecklistBank = '" . $_POST['newChecklistBank_' . $i] . "',
						cEmail = '" . $_POST['newEmail_' . $i] . "',
						cOtherName = '" . $_POST['newOtherName_' . $i] . "';";
            $conn->Execute($sql);
            $id = $conn->Insert_ID();

            for ($j = 0; $j <= $_POST['newIndex_' . $i]; $j++) { //newIndex_0
                if (($j > 0) && ($_POST['newBankMain_' . $i][$j] != '0') && ($_POST['newcBankBranch_' . $i][$j] != 0)) {
                    $sql = "INSERT INTO
                                tContractCustomerBank
                            SET
                                cCertifiedId = '" . $_POST['cCertifiedId'] . "',
                                cIdentity = '" . $BankIden . "',
                                cBankMain = '" . $_POST['newBankMain_' . $i][$j] . "',
                                cBankBranch = '" . $_POST['newcBankBranch_' . $i][$j] . "',
                                cBankAccountName = '" . $_POST['newBankAccName_' . $i][$j] . "',
                                cBankAccountNo = '" . $_POST['newBankAccNum_' . $i][$j] . "',
                                cBankMoney = '" . $_POST['newBankAccMoney_' . $i][$j] . "',
                                cChecklistBank  = '" . $_POST['oldChecklistBank_' . $i . '_' . $j] . "',
                                cOtherId = '" . $id . "';";
                    $conn->Execute($sql);
                }
            }
        }
    }
    //

    //刪除
    if ($del == 'ok') {
        $del_no = $_POST['del_no'];

        if ($del_no) {
            $sql = 'DELETE FROM tContractOthers WHERE cId="' . $del_no . '";';
            $conn->Execute($sql);

            $sql = "DELETE FROM tContractCustomerBank WHERE cOtherId = '" . $del_no . "'";
            $conn->Execute($sql);

            $tlog = new TraceLog();
            $tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '多筆買賣案件刪除');
        }
    }
    ##
}

exit('OK');
