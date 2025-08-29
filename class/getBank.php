<?php
/* 銀行相關資料取得 */

//取得銀行總行資料
function getBankMain($_conn, $main = '')
{
    $str = '<option value=""';
    if ($main == '') {
        $str .= ' selected="selected"';
    }
    $str .= '>總行' . "</option>\n";

    $sql = 'SELECT * FROM tBank WHERE bBank4="" ORDER BY bBank3 ASC;';
    $rs  = $_conn->Execute($sql);
    if ($rs && ! $rs->EOF) {
        while (! $rs->EOF) {
            $str .= '<option value="' . $rs->fields['bBank3'] . '"';
            if ($rs->fields['bBank3'] == $main) {
                $str .= ' selected="selected"';
            }
            $str .= '>' . $rs->fields['bBank3_name'] . '(' . $rs->fields['bBank3'] . ')' . "</option>\n";
            $rs->MoveNext();
        }
    }

    return $str;
}
##

//取得銀行分行資料
function getBankBranch($_conn, $main = '', $branch = '')
{
    $str = '<option value=""';
    if ($branch == '') {
        $str .= ' selected="selected"';
    }
    $str .= '>分行' . "</option>\n";

    $sql = 'SELECT * FROM tBank WHERE bBank3="' . $main . '" AND bBank4<>"" AND bOK =0 ORDER BY bBank4 ASC;';
    $rs  = $_conn->Execute($sql);
    if ($rs && ! $rs->EOF) {
        while (! $rs->EOF) {
            $str .= '<option value="' . $rs->fields['bBank4'] . '"';
            if ($rs->fields['bBank4'] == $branch) {
                $str .= ' selected="selected"';
            }
            $str .= '>' . $rs->fields['bBank4_name'] . '(' . $rs->fields['bBank4'] . ')' . "</option>\n";
            $rs->MoveNext();
        }
    }

    return $str;

}
##

function getBankBranchName($_conn, $main = '', $branch = '')
{
    if (empty($main) || empty($branch)) {
        return '';
    }

    $sql = 'SELECT * FROM tBank WHERE bBank3="' . $main . '" AND bBank4<>"" AND bBank4="' . $branch . '" ORDER BY bBank4 ASC;';

    $rs = $_conn->Execute($sql);

    if ($rs && ! $rs->EOF && isset($rs->fields['bBank4_name']) && isset($rs->fields['bBank4'])) {
        $str = $rs->fields['bBank4_name'] . '(' . $rs->fields['bBank4'] . ')';
        return $str;
    }

    return '';
}
