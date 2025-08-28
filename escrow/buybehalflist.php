<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/lib.php';

$advance = new Advance();

$_iden = trim(addslashes($_REQUEST['iden']));
$save  = trim(addslashes($_REQUEST['save']));
$del   = trim(addslashes($_POST['del']));

$cCertifiedId = trim(addslashes($_REQUEST['cCertifyId']));
$sign         = trim(addslashes($_REQUEST['SignCategory']));

if ($_iden == 'b') { // 5買方登記名義人6買方代理人7賣方代理人8賣方登記名義人
    $_ide      = '買';
    $cIdentity = 5;
} else if ($_iden == 'o') {
    $_ide      = '賣';
    $cIdentity = 8;
}

if (!in_array($_iden, ['b', 'o'])) {
    exit('資料錯誤!!');
}

//刪除資料
if ($del == 'ok') {
    $del_no = $_POST['del_no'];

    if (!empty($del_no) && preg_match("/^\d+$/", $del_no)) {
        $sql = 'DELETE FROM tContractOthers	WHERE cId = "' . $del_no . '";';
        $conn->Execute($sql);
    }
}
##

//儲存資料
if ($save == 'ok') {
    //取得表格所有資料
    $data = $_POST;
    ##

    //是否有新增對象及處裡
    if ($data['new_cName'] && $data['new_cIdentifyId'] && $data['new_cIdentity']) {
        $sqls = 'INSERT INTO
                    tContractOthers
                    (
                        cCertifiedId,
                        cTarget,
                        cIdentity,
                        cIdentifyId,
                        cName,
                        cBirthdayDay,
                        cCountryCode,
                        cTaxTreatyCode,
                        cResidentLimit,
                        cPaymentDate,
                        cNHITax,
                        cMobileNum,
                        cRegistZip,
                        cRegistAddr,
                        cBaseZip,
                        cBaseAddr,
                        cBankMain,
                        cBankBranch,
                        cBankAccName,
                        cBankAccNum,
                        cInvoiceMoney,
                        cInterestMoney
                    )
                VALUES
                    (
                        "' . $data['new_cCertifiedId'] . '",
                        "' . $data['new_cTarget'] . '",
                        "' . $data['new_cIdentity'] . '",
                        "' . strtoupper($data['new_cIdentifyId']) . '",
                        "' . $data['new_cName'] . '",
                        "' . date_convert($data['new_cBirthdayDay']) . '",
                        "' . $data['new_cCountryCode'] . '",
                        "' . $data['new_cTaxTreatyCode'] . '",
                        "' . $data['new_cResidentLimit'] . '",
                        "' . $data['new_cPaymentDate'] . '",
                        "' . $data['new_cNHITax'] . '",
                        "' . $data['new_cMobileNum'] . '",
                        "' . $data['new_cRegistZip'] . '",
                        "' . $data['new_cRegistAddr'] . '",
                        "' . $data['new_cBaseZip'] . '",
                        "' . $data['new_cBaseAddr'] . '",
                        "' . $data['new_cBankMain'] . '",
                        "' . $data['new_cBankBranch'] . '",
                        "' . $data['new_cBankAccName'] . '",
                        "' . $data['new_cBankAccNum'] . '",
                        "0",
                        "0"
                    );';
        $conn->Execute($sqls);
    }
    ##

    //儲存更新的資料
    $max = count($data['cId']);
    for ($i = 0; $i < $max; $i++) {

        $ck = 0;
        if (count($data['cTarget'])) {
            foreach ($data['cTarget'] as $k => $v) {
                if ($v == $data['cId'][$i]) {
                    $ck = '1';
                }
            }
        }

        $sqls = 'UPDATE
				    tContractOthers
                SET
                    cName="' . $data['cName'][$i] . '",
                    cTarget="' . $ck . '",
                    cIdentifyId="' . strtoupper($data['cIdentifyId'][$i]) . '",
                    cMobileNum="' . $data['cMobileNum'][$i] . '",
                    cBirthdayDay="' . date_convert($data['cBirthdayDay'][$i]) . '",
                    cCountryCode="' . $data['cCountryCode'][$i] . '",
                    cTaxTreatyCode="' . $data['cTaxTreatyCode'][$i] . '",
                    cPaymentDate="' . $data['cPaymentDate'][$i] . '",
                    cIdentity="' . $data['cIdentity'][$i] . '",
                    cRegistZip="' . $data['cRegistZip'][$i] . '",
                    cRegistAddr="' . $data['cRegistAddr'][$i] . '",
                    cBaseZip="' . $data['cBaseZip'][$i] . '",
                    cBaseAddr="' . $data['cBaseAddr'][$i] . '",
                    cBankMain="' . $data['cBankMain'][$i] . '",
                    cBankBranch="' . $data['cBankBranch'][$i] . '",
                    cBankAccNum="' . $data['cBankAccNum'][$i] . '",
                    cBankAccName="' . $data['cBankAccName'][$i] . '",
                    cResidentLimit="' . $resident . '",
                    cNHITax="' . $NHI . '"
                WHERE
                    cId="' . $data['cId'][$i] . '"
                    AND cCertifiedId="' . $data['cCertifiedId'][$i] . '";';
        $conn->Execute($sqls);
    }
    ##
}
##

//顯示相關資料
$sql = 'SELECT
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
            a.cCertifiedId="' . $cCertifiedId . '"
            AND a.cIdentity="' . $cIdentity . '"
        ORDER BY
            a.cIdentity
        ASC;';
$rs = $conn->Execute($sql);
##

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>代理人</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<link href="/css/transferArea.css?v=20230816" rel="stylesheet">
<script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/rocCal.js"></script>
<script src="/js/IDCheck.js"></script>
<script src="/js/transferArea.js?v=20230818"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('.idc').each(function() {
		let _id = $(this).prop("id") ;
		let _val = $(this).val() ;
		_id = $('#' + _id + '_img') ;

		/* 檢核身分證字號或統一編號合法性 */
		if (checkUID(_val)) {
			_id.html('<img src="/images/ok.png">') ;
		} else {
			_id.html('<img src="/images/ng.png">') ;
		}
	});

	$('.idc').keyup(function() {
		let _id = $(this).prop("id") ;
		let _val = $(this).val() ;
		_id = $('#' + _id + '_img') ;

		/* 檢核身分證字號或統一編號合法性 */
		if (checkUID(_val)) {
			_id.html('<img src="/images/ok.png">') ;
		} else {
			_id.html('<img src="/images/ng.png">') ;
		}
	}) ;

	$('[name="new_cIdentifyId"]').keyup(function() {
		let _val = $(this).val() ;
		let _id = $('#new_cIdentifyId_img') ;

		/* 檢核身分證字號或統一編號合法性 */
		if (checkUID(_val)) {
			_id.html('<img src="/images/ok.png">') ;
		}
		else {
			_id.html('<img src="/images/ng.png">') ;
		}
	}) ;


	//初始設定
	$('#new_record').hide() ;

	//新增一筆紀錄
	$('#addnew').click(function() {
		$('#new_record').show() ;
		$('#addnew_field').html('&nbsp;') ;
	}) ;

	//儲存資料
	$('#savedata').click(function() {
		$('[name="save"]').val('ok') ;
		$('[name="myform"]').submit() ;
	}) ;

<?php if ($save == 'ok') {?>
	$('#dialog_save').dialog({
		modal: true,
		buttons: {
			"確認": function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
<?php }?>

<?php if ($del == 'ok') {?>
	$('#dialog_save').dialog({
		modal: true,
		buttons: {
			"確認": function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
<?php }?>

	//關閉視窗
	$('#closewin').click(function() {
		window.close() ;
	}) ;

	//變更按鈕樣式
	$('#addnew').button({
		icons:{
			primary: "ui-icon-plus"
		}
	}) ;

	//檢查"同上"按鈕併動作
	$('#sync_registaddr').click(function() {
		if ($('#sync_registaddr').prop('checked')) {
			$('[name="new_cBaseAddr"]').val($('[name="new_cRegistAddr"]').val()) ;		//地址複製
			$('#new_cBaseCity').val($('#new_cRegistCity').val()) ;						//縣市複製
			$('#new_cBaseZip').val($('#new_cRegistZip').val()) ;						//郵遞區號複製

			let url = 'zipConvert.php' ;
			let _city = $('#new_cRegistCity').val() ;

			$.post(url,{'ct':_city},function(txt) {
				$('#new_cBaseArea').html(txt) ;
				$('#new_cBaseArea').val($('#new_cRegistArea').val()) ;
			}) ;
		}
		else {
			$('[name="new_cBaseAddr"]').val('') ;
			$('#new_cBaseCity').val('') ;
			$('#new_cBaseArea').empty().html('<option value="">區域</option>') ;
			$('#new_cBaseZip').val('') ;
		}
	}) ;

	$('#closewin').button({
		icons:{
			primary: "ui-icon-close"
		}
	}) ;

}) ;

//依據縣市選擇改變鄉鎮市選項
function zip_area(city,area,zips) {
	let url = 'zipConvert.php' ;
	let _city = $('#'+city).val() ;

	$.post(url,{'ct':_city},function(txt) {
		$('#'+area).html(txt) ;
		$('#'+zips).val('') ;
		$('#'+zips+'F').val('') ;
	}) ;
}

//依據鄉鎮市選擇改變郵遞區號顯示
function zip_change(area,zip) {
	let _zip = $('#'+area).val() ;

	$('#'+zip).val(_zip) ;
	$('#'+zip+'F').val(_zip.substr(0,3)) ;
}


//資料更新存檔
function save_data() {
	$('[name="save"]').val('ok') ;
	$('[name="myform"]').submit() ;
}

function addr(id)
{
	if ($('#same'+id).prop('checked')) {
        $('#cBaseAddr'+id).val($('#cRegistAddr'+id).val()) ;		//地址複製
        $('#cBaseCity-'+id).val($('#cRegistCity-'+id).val()) ;		//縣市複製
        $('#cBaseZip-'+id).val($('#cRegistZip-'+id).val()) ;		//郵遞區號複製
        $('#cBaseZip-'+id+'F').val($('#cRegistZip-'+id).val()) ;

        let url = 'zipConvert.php' ;
        let _city = $('#cRegistCity-'+id).val() ;

        $.post(url,{'ct':_city},function(txt) {
            $('#cBaseArea-'+id).html(txt) ;
            $('#cBaseArea-'+id).val($('#cRegistArea-'+id).val()) ;
        }) ;
    } else {
        $('#cBaseAddr'+id).val('') ;
        $('#cBaseCity-'+id).val('') ;
        $('#cBaseArea-'+id).empty().html('<option value="">區域</option>') ;
        $('#cBaseZip-'+id).val('') ;
    }
}

//刪除資料
function del(no) {
	$('#dialog_save').html('確認是否刪除本筆資料？') ;
	$('#dialog_save').prop('title','ID = ' + no + ', 刪除？') ;

	$('#dialog_save').dialog({
		resizable: false,
		height: 140,
		modal: true,
		buttons: {
			"確認": function() {
				$('form[name="del_form"] input[name="del"]').val('ok') ;
				$('form[name="del_form"] input[name="del_no"]').val(no) ;
				$('form[name="del_form"]').submit() ;
			},
			"取消": function() {
				$(this).dialog("close") ;
				$('[name="myform"]').submit() ;
			}
		}
	}) ;
}

function getCustomer(iden,id){
	let val = $("#_"+iden+id).val();
	let check = 0;

	let type = (iden == 'o') ? 2 : 1;
	if (iden == 'new') {
		val = $("[name='"+iden+"_cIdentifyId']").val();
	} else {
		if (checkUID(val) && !confirm("已有資料存在，是否要取代?")) {
            return false;
		}
	}

	$.ajax({
		url: '/includes/escrow/getCustomer.php',
		type: 'POST',
		dataType: 'html',
		data: {id: val,cId:"<?=$cCertifiedId?>",iden:iden},
	}).done(function(msg) {
		var obj = JSON.parse(msg);
		if (obj.msg == 'ok') {
			if (iden  == 'new') {
				$("[name='"+iden+"_cName']").val(obj.name);
				$("[name='"+iden+"_cBirthdayDay']").val(obj.birthday);//new_cBirthdayDay
				$("[name='"+iden+"_cMobileNum']").val(obj.mobile);
				$("[name='"+iden+"_cRegistZip']").val(obj.zip);//new_cRegistZip
				$("[name='"+iden+"_cRegistCity']").val(obj.city);//new_cRegistZip
				$("[name='"+iden+"_cRegistAddr']").val(obj.addr);

				$("[name='"+iden+"_cRegistArea'] option").remove() ; //
				$.post('listArea.php',{"city":obj.city},function(txt) {
	                $("[name='"+iden+"_cRegistArea']").append(txt) ;
	                $("[name='"+iden+"_cRegistArea']").val(obj.zip);
	            }) ;
			} else {
				$('#name'+id).val(obj.name);
	            $('#BD'+id).val(obj.birthday);
	            $('#mobile'+id).val(obj.mobile);

	            $('#cRegistCity-'+id).val(obj.city);
	            $('#cRegistZip-'+id).val(obj.zip);
	            $('#cRegistZip-'+id+'F').val(obj.zip);
	            $('#cRegistAddr'+id).val(obj.addr);
	            $('#cRegistArea-'+id+' option').remove() ; //buyer_registarea

	            $.post('listArea.php',{"city":obj.city},function(txt) {
	                $('#cRegistArea-'+id).append(txt) ;
	                $('#cRegistArea-'+id).val(obj.zip);
	            }) ;

	            if ($('#same'+id).prop('checked')) {
	            	$('#same'+id).prop('checked', '');
	            }

	            $('#cBaseZip-'+id).val('');
	            $('#cBaseZip-'+id+'F').val('');
	            $('#cBaseCity-'+id).val('');
	            $('#cBaseAddr'+id).val('');
	            $('#cBaseArea-'+id+' option').remove() ;
			}
		}
	});
}
</script>
<style>
.sign-red {
	color:red;
}

table tr td {
	text-align:left;
}

fieldset {
	border-radius: 6px;
}
</style>
</head>

<body style="background-color:#F8ECE9;">
<form id="myform" name="myform" method="POST">
<input type="hidden" name="cCertifyId" value="<?=$cCertifiedId?>">

<div style="height:10px;">
</div>
<input type="hidden" name="cInvoiceOther" value="<?=$cInvoiceOther?>">
<input type="hidden" name="save" value="">
<table border="0" style="width:1000px;">
<?php
while (!$rs->EOF) {
    switch ($rs->fields['cIdentity']) {
        case '5':
            $_ide = '買';
            break;
        case '8':
            $_ide = '賣';
            break;
        default:
            $_ide = '其他';
            break;
    }
    ?>
	<tr>
		<td colspan="6" style="background-color:#E4BEB1;font-size:12pt;font-weight:bold;padding:5px;">
			<?=$_ide?>方登記名義人資料　(<?=$cCertifiedId?>)
			<input type="hidden" name="cId[]" value="<?=$rs->fields['cId']?>">
			<input type="hidden" name="cCertifiedId[]" value="<?=$rs->fields['cCertifiedId']?>">
			<?php
if ($sign == 1) {?>
					<span style="font-size:10pt;"><a href="#" onclick="del('<?=$rs->fields['cId']?>')">刪除</a></span>
			<?php }?>

		</td>
	</tr>

	<tr>
		<th style="width:120px;">
			<table border="0" style="width:150px;">
				<tr>
					<td><span class="sign-red">*</span>身分別：</td>
				</tr>
				<tr>
					<td><span class="sign-red">*</span>身分證號/統編︰</td>
				</tr>
				<tr>
					<td><span class="sign-red">*</span><?=$_ide?>方登記名義人︰</td>
				</tr>

				<tr>
					<td><span class="sign-red">*</span>行動電話︰</td>
				</tr>
				<tr>
				     <td><span class="sign-red">&nbsp;&nbsp;</span>出生日期︰</td>
				</tr>
			</table>
		</th>
		<td style="width:380px;">
			<table border="0" style="width:300px;">
				<tr>
					<td>
						<input type="hidden" name="cIdentity[]" value="<?=$rs->fields['cIdentity']?>">
						 <?php $checked = '';if ($rs->fields['cTarget'] != 0) {$checked = 'checked';}?>
						<?=$_ide?>方登記名義人資料 &nbsp;&nbsp; <!-- <input type="checkbox" name='cTarget[]' value="<?=$rs->fields['cId']?>" <?=$checked?>>同買方(甲方) -->

					</td>
				</tr>
				<tr>
					<td>
						<input type="text" maxlength="10" style="width:120px;" class="idc" id="_<?=$_iden . $rs->fields['cId']?>" name="cIdentifyId[]" value="<?=$rs->fields['cIdentifyId']?>" onkeyup="getCustomer('<?=$_iden?>','<?=$rs->fields['cId']?>')"/>
						<span id="_<?=$_iden . $rs->fields['cId']?>_img" style="padding-left:5px;"></span>
					</td>
				</tr>
				<tr>
					<td><input type="text" name="cName[]" style="width:120px;" id="name<?=$rs->fields['cId']?>" value="<?=$rs->fields['cName']?>" /></td>
				</tr>

				<tr>
					<td><input type="text" maxlength="10" style="width:120px;" id="mobile<?=$rs->fields['cId']?>" name="cMobileNum[]" value="<?=$rs->fields['cMobileNum']?>" /></td>
				</tr>
				<tr>
				     <td>
				     	<?php
if ($rs->fields['cBirthdayDay'] == '0000-00-00') {?>

				     	<input type="text" maxlength="10" name="cBirthdayDay[]" style="width:120px;" value="" onclick="showdate(myform.BD<?=$rs->fields['cId']?>)" class="calender input-text-big" id="BD<?=$rs->fields['cId']?>" readonly/>
				    	<?php
} else {?>
						<input type="text" maxlength="10" name="cBirthdayDay[]" style="width:120px;" value="<?=$advance->ConvertDateToRoc($rs->fields['cBirthdayDay'], base::DATE_FORMAT_NUM_DATE)?>" onclick="showdate(myform.BD<?=$rs->fields['cId']?>)" class="calender input-text-big" id="BD<?=$rs->fields['cId']?>" readonly/>
				    <?php }?>

				     </td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<th>
			<table border="0" style="width:150px;">
				<tr>
					<td >&nbsp;&nbsp;戶籍地址︰</td>
				</tr>
			</table>
		</th>
		<td colspan="3">
			<table border="0" width="100%">
				<tr>
					<td  style="width:60px;">&nbsp;</td>
					<td>
						<input type="hidden" id="cRegistZip-<?=$rs->fields['cId']?>" name="cRegistZip[]" value="<?=$rs->fields['cRegistZip']?>" style="background-color:#CCC;width:50px;"/>
						<input type="text" maxlength="6" id="cRegistZip-<?=$rs->fields['cId']?>F" readonly="readonly" value="<?=substr($rs->fields['cRegistZip'], 0, 3)?>" style="background-color:#CCC;width:50px;"/>
						<select id="cRegistCity-<?=$rs->fields['cId']?>" style="width:80px;" onchange="zip_area('cRegistCity-<?=$rs->fields['cId']?>','cRegistArea-<?=$rs->fields['cId']?>','cRegistZip-<?=$rs->fields['cId']?>')">
							<option value="">縣市</option>
							<?php
$sql    = 'SELECT DISTINCT zCity FROM tZipArea;';
    $rs_zip = $conn->CacheExecute($sql);

    while (!$rs_zip->EOF) {
        echo "\t\t\t\t\t\t\t" . '<option value="' . $rs_zip->fields['zCity'] . '"';
        if ($rs->fields['cRegistCity'] == $rs_zip->fields['zCity']) {
            echo ' selected="selected"';
        }
        echo '>' . $rs_zip->fields['zCity'] . '</option>' . "\n";

        $rs_zip->MoveNext();
    }
    ?>
						</select>

						<select id="cRegistArea-<?=$rs->fields['cId']?>" style="width:80px;" onchange="zip_change('cRegistArea-<?=$rs->fields['cId']?>','cRegistZip-<?=$rs->fields['cId']?>')">
							<?php
$sql    = 'SELECT zZip,zArea FROM tZipArea WHERE zCity="' . $rs->fields['cRegistCity'] . '";';
    $rs_zip = $conn->CacheExecute($sql);

    while (!$rs_zip->EOF) {
        echo "\t\t\t\t\t\t\t" . '<option value="' . $rs_zip->fields['zZip'] . '"';
        if ($rs->fields['cRegistZip'] == $rs_zip->fields['zZip']) {
            echo ' selected="selected"';
        }
        echo '>' . $rs_zip->fields['zArea'] . '</option>' . "\n";

        $rs_zip->MoveNext();
    }
    ?>
						</select>
						<input name="cRegistAddr[]" value="<?=$rs->fields['cRegistAddr']?>" style="width:500px;" id="cRegistAddr<?=$rs->fields['cId']?>"/>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>
			<table border="0" style="width:150px;">
				<tr>
					<td><span class="sign-red">*</span>通訊地址︰</td>
				</tr>
			</table>
		</th>
		<td colspan="3">
			<table border="0" width="100%">
				<tr>
					<td  style="width:60px;">
						<?php
$check = '';
    if ($rs->fields['cBaseZip'] == $rs->fields['cRegistZip'] && $rs->fields['cBaseAddr'] == $rs->fields['cRegistAddr']) {
        $check = 'checked';
    }
    ?>
						<input type="checkbox" id="same<?=$rs->fields['cId']?>" onclick="addr(<?=$rs->fields['cId']?>);" <?=$check?>> 同上
					</td>
					<td>
			<input type="hidden" id="cBaseZip-<?=$rs->fields['cId']?>" name="cBaseZip[]" value="<?=$rs->fields['cBaseZip']?>" style="background-color:#CCC;width:50px;"/>
			<input type="text" maxlength="6" id="cBaseZip-<?=$rs->fields['cId']?>F" readonly="readonly" value="<?=substr($rs->fields['cBaseZip'], 0, 3)?>" style="background-color:#CCC;width:50px;"/>
			<select id="cBaseCity-<?=$rs->fields['cId']?>" style="width:80px;" onchange="zip_area('cBaseCity-<?=$rs->fields['cId']?>','cBaseArea-<?=$rs->fields['cId']?>','cBaseZip-<?=$rs->fields['cId']?>')">
				<option value="">縣市</option>
				<?php
$sql    = 'SELECT DISTINCT zCity FROM tZipArea;';
    $rs_zip = $conn->CacheExecute($sql);

    while (!$rs_zip->EOF) {
        echo "\t\t\t\t" . '<option value="' . $rs_zip->fields['zCity'] . '"';
        if ($rs->fields['cBaseCity'] == $rs_zip->fields['zCity']) {
            echo ' selected="selected"';
        }
        echo '>' . $rs_zip->fields['zCity'] . '</option>' . "\n";

        $rs_zip->MoveNext();
    }
    ?>
			</select>
			<select id="cBaseArea-<?=$rs->fields['cId']?>" style="width:80px;" onchange="zip_change('cBaseArea-<?=$rs->fields['cId']?>','cBaseZip-<?=$rs->fields['cId']?>')">
				<?php
$sql    = 'SELECT zZip,zArea FROM tZipArea WHERE zCity="' . $rs->fields['cBaseCity'] . '";';
    $rs_zip = $conn->CacheExecute($sql);

    while (!$rs_zip->EOF) {
        echo "\t\t\t\t\t\t\t" . '<option value="' . $rs_zip->fields['zZip'] . '"';
        if ($rs->fields['cBaseZip'] == $rs_zip->fields['zZip']) {
            echo ' selected="selected"';
        }
        echo '>' . $rs_zip->fields['zArea'] . '</option>' . "\n";

        $rs_zip->MoveNext();
    }
    ?>
			</select>

			<input name="cBaseAddr[]" value="<?=$rs->fields['cBaseAddr']?>" style="width:500px;" id="cBaseAddr<?=$rs->fields['cId']?>"/>
					</td>
				</tr>
			</table>
		</td>
	</tr>
    <tr>
        <th>
            <table border="0" style="width:150px;">
                <tr>
                    <td><span class="sign-red">&nbsp;&nbsp;</span>移轉範圍︰</td>
                </tr>
            </table>
        </th>
        <td colspan="3">
            <div><input type="button" style="padding: 5px;" onclick="transferArea('<?=$cCertifiedId?>', 5, '<?=$rs->fields['cId']?>')" value="設定"></div>
        </td>
    </tr>
<?php
$rs->MoveNext();
}
?>
	<tr>
		<td id="addnew_field" colspan="6" style="text-align:right;"><button id="addnew">增加對象</button></a></td>
	</tr>
</table>
<table border="0" id="new_record" style="width:1000px;">
	<tr>
		<td colspan="6" style="background-color:#E4BEB1;font-size:12pt;font-weight:bold;padding:5px;">
			新增資料　(<?=$cCertifiedId?>)　<<新增紀錄>>
			<input type="hidden" name="new_cCertifiedId" value="<?=$cCertifiedId?>">
		</td>
	</tr>
	<tr>
		<th style="width:120px;">
			<table border="0" style="width:150px;">
				<tr>
					<td><span class="sign-red">*</span>身分別：</td>
				</tr>
				<tr>
					<td><span class="sign-red">*</span>身分證號/統編︰</td>
				</tr>
				<tr>
					<td><span class="sign-red">*</span>新增登記名義人︰</td>
				</tr>
				<tr>
					<td><span class="sign-red">*</span>行動電話︰</td>
				</tr>
				<tr>
					<td><span class="sign-red">&nbsp;&nbsp;</span>出生日期︰</td>
				</tr>
			</table>
		</th>
		<td style="width:380px;">
			<table border="0" style="width:300px;">
				<tr>
					<td>
						<input type="hidden" name="new_cIdentity" value="<?=$cIdentity?>">
							<?=$_ide?>方登記名義人資料 &nbsp;&nbsp;<!-- <input type="checkbox" name='new_cTarget' value="1">同買方(甲方) -->
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" maxlength="10" style="width:120px;" name="new_cIdentifyId" value="" onkeyup="getCustomer('new','')"/>
						<span id="new_cIdentifyId_img" style="padding-left:5px;"></span>
					</td>
				</tr>
				<tr>
					<td><input type="text" name="new_cName" style="width:120px;" value="" /></td>
				</tr>

				<tr>
					<td><input type="text" maxlength="10" name="new_cMobileNum" style="width:120px;" value="" /></td>
				</tr>
				<tr>
					<td><input type="text" maxlength="10" name="new_cBirthdayDay" style="width:120px;" value="" onclick="showdate(myform.cBirthdayDay)" class="calender input-text-big" id="cBirthdayDay" readonly/></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>
			<table border="0" style="width:150px;">
				<tr>
					<td>&nbsp;&nbsp;戶籍地址︰</td>
				</tr>
			</table>
		</th>
		<td colspan="3">
			<table border="0" width="100%">
				<tr>
					<td style="width:60px;">
					&nbsp;
					</td>
					<td>
						<input type="text" maxlength="6" id="new_cRegistZip" name="new_cRegistZip" readonly="readonly" value="" style="background-color:#CCC;width:50px;"/>
						<select id="new_cRegistCity" name="new_cRegistCity" style="width:80px;" onchange="zip_area('new_cRegistCity','new_cRegistArea')">
							<option value="">縣市</option>
							<?php
$sql    = 'SELECT DISTINCT zCity FROM tZipArea;';
$rs_zip = $conn->CacheExecute($sql);

while (!$rs_zip->EOF) {
    echo "\t\t\t\t\t\t\t" . '<option value="' . $rs_zip->fields['zCity'] . '">' . $rs_zip->fields['zCity'] . '</option>' . "\n";

    $rs_zip->MoveNext();
}
?>
						</select>

						<select id="new_cRegistArea" name="new_cRegistArea" style="width:80px;" onchange="zip_change('new_cRegistArea','new_cRegistZip')">
							<option value="">區域</option>
						</select>
						<input name="new_cRegistAddr" value="" style="width:500px;"/>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>
			<table border="0" style="width:150px;">
				<tr>
					<td><span class="sign-red">*</span>通訊地址︰</td>
				</tr>
			</table>
		</th>
		<td colspan="3">
			<table border="0" width="100%">
				<tr>
					<td style="width:60px;">
						<input type="checkbox" id="sync_registaddr"> 同上
					</td>
					<td>
			<input type="text" maxlength="6" id="new_cBaseZip" name="new_cBaseZip" readonly="readonly" value="" style="background-color:#CCC;width:50px;"/>
			<select id="new_cBaseCity" style="width:80px;" onchange="zip_area('new_cBaseCity','new_cBaseArea')">
				<option value="">縣市</option>
				<?php
$sql    = 'SELECT DISTINCT zCity FROM tZipArea;';
$rs_zip = $conn->CacheExecute($sql);

while (!$rs_zip->EOF) {
    echo "\t\t\t\t\t\t\t" . '<option value="' . $rs_zip->fields['zCity'] . '">' . $rs_zip->fields['zCity'] . '</option>' . "\n";

    $rs_zip->MoveNext();
}
?>
			</select>
			<select id="new_cBaseArea" style="width:80px;" onchange="zip_change('new_cBaseArea','new_cBaseZip')">
				<option value="">區域</option>
			</select>

			<input name="new_cBaseAddr" value="" style="width:500px;"/>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<br>
<hr align="left" style="width:1100px;">

<div style="width:1100px;text-align:right;">
<?php
if ($sign == 1) {?>
		<input id="savedata" type="button" style="width:100px;" value="存檔">
<?php }?>

</div>

</form>
<form method="post" name="del_form">
<input type="hidden" name="del" value="">
<input type="hidden" name="del_no" value="">
</form>
<input type="hidden" id="dialog_confirm_count" value="0">
<div id="dialog_confirm">
</div>
<div id="dialog_save">
<?php
if ($save == "ok") {
    echo '資料已更新!!';
} else if ($del == 'ok') {
    echo '資料已刪除!!';
}
?>
</div>
<div id="dialog"></div>
</body>
</html>