<?php
function Set_status_details($con, $bns_id, $approve_culumn, $status_table, $appr_dtl_table, $nstatus, $stage_no, $sessionid, $msg,$remarks)
{
    include_once('../../../approvalmatrixfunction.php');
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $entry_date = date('Y-m-d H:i:s');

    $sqlqry = mysqli_query($con, "SELECT * FROM hr_bonus WHERE id='$bns_id'");
    $rows = mysqli_fetch_object($sqlqry);
    $b_type = $rows->b_type;
    $b_on = $rows->b_on;
    $b_mode = $rows->b_mode;
    $applicable = $rows->applicable;
    $b_per = $rows->b_per;
    $based_on = $rows->based_on;
    $created_by = $rows->created_by;

    if ($status_table != "" && $approve_culumn != "") {
        if ($nstatus == 1 || $nstatus == 2 ) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $bns_id . "";
            $sqldt = mysqli_query($con, $querydt);
        } else if($nstatus == 6) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $bns_id . "";
            $sqldt = mysqli_query($con, $querydt);
        } 
    }
    $query1 = "INSERT INTO `$appr_dtl_table` (`bns_id`,`bh_type`,`bh_on`,`bh_mode`,`bh_applicable`,`bh_per`,`bh_based_on`,`reason`,`created_by`,`action_on`,`action_by`,`remarks`,`stage_no`,`act_status`,`ip_addr`)VALUES('$bns_id', '$b_type', '$b_on', '$b_mode', '$applicable', '$b_per', '$based_on', '$msg', '$created_by', '$entry_date', '$sessionid', '$remarks', '$stage_no', '$nstatus', '$ip_addr')";
    $sql_query = mysqli_query($con, $query1);
    if ($sql_query) {
        //--------Notification
        $created_on = date('Y-m-d H:i:s');
        $url = 'hr/bonus/add_bonus_request_list.php';
        $menuid = getMenuById($con, $url);
        $emp_id = $_SESSION['ERP_SESS_ID'];
        $deptid = getdeptid($con, $emp_id);
        $reciever = getapprover($con, $menuid, $stage_no, $nstatus, '', '', $deptid, '', $emp_id);
        $notificationmsg = "Bonus request type ".$b_type." pending for approve on your site.";
        saveNotice($con, $bns_id, "Bonus Request", '0', "hr/bonus/manage_bonus_request_list.php", '0', $notificationmsg, $reciever, $emp_id, $created_on);
        //--------/Notification

        return 1;
    } else {
        return 2;
    }
}
function Set_status_recheck_details($con, $bns_id, $approve_culumn, $status_table, $appr_dtl_table, $nstatus, $stage_no, $sessionid, $msg,$remarks)
{
    include_once('../../../approvalmatrixfunction.php');
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $sqlqry = mysqli_query($con, "SELECT * FROM hr_bonus WHERE id='$bns_id'");
    $rows = mysqli_fetch_object($sqlqry);
    $b_type = $rows->b_type;
    $b_on = $rows->b_on;
    $b_mode = $rows->b_mode;
    $applicable = $rows->applicable;
    $b_per = $rows->b_per;
    $based_on = $rows->based_on;
    $created_by = $rows->created_by;

    $entry_date = date('Y-m-d H:i:s');
    $entry_time = date('H:i:s'); 
    if ($status_table != "" && $approve_culumn != "") {
        if($nstatus == 3) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $bns_id . "";
            $sqldt = mysqli_query($con, $querydt);
        } 
    }
        $query1 = "INSERT INTO `$appr_dtl_table` (`bns_id`,`bh_type`,`bh_on`,`bh_mode`,`bh_applicable`,`bh_per`,`bh_based_on`,`reason`,`created_by`,`action_on`,`action_by`,`remarks`,`stage_no`,`act_status`,`ip_addr`)VALUES('$bns_id', '$b_type', '$b_on', '$b_mode', '$applicable', '$b_per', '$based_on', '$msg', '$created_by', '$entry_date', '$sessionid', '$remarks', '$stage_no', '$nstatus', '$ip_addr')";

        $sql_query = mysqli_query($con, $query1);
    if ($sql_query) {
        //--------Notification
        $created_on = date('Y-m-d H:i:s');
        $url = 'hr/bonus/add_bonus_request_list.php';
        $menuid = getMenuById($con, $url);
        $emp_id = $_SESSION['ERP_SESS_ID'];
        $deptid = getdeptid($con, $emp_id);
        $reciever = getapprover($con, $menuid, $stage_no, $nstatus, '', '', $deptid, '', $emp_id);
        $notificationmsg = "Bonus request type ".$b_type." pending for update on your site.";
        saveNotice($con, $bns_id, "Bonus Request", '0', "hr/bonus/manage_bonus_request_list.php", '0', $notificationmsg, $reciever, $emp_id, $created_on);
        //--------/Notification

        return 1;
    } else {
        return 2;
    }
}

//****************************************

function Set_status_hold_details($con, $bns_id, $approve_culumn, $status_table, $appr_dtl_table, $nstatus, $stage_no, $sessionid,$hold_days,$msg,$remarks)
{
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $sqlqry = mysqli_query($con, "SELECT * FROM hr_bonus WHERE id='$bns_id'");
    $rows = mysqli_fetch_object($sqlqry);
    $b_type = $rows->b_type;
    $b_on = $rows->b_on;
    $b_mode = $rows->b_mode;
    $applicable = $rows->applicable;
    $b_per = $rows->b_per;
    $based_on = $rows->based_on;
    $created_by = $rows->created_by;

    $entry_date = date('Y-m-d H:i:s');
    $entry_time = date('H:i:s'); 
    if ($status_table != "" && $approve_culumn != "") {
        /*if ($nstatus == 1 || $nstatus == 2 ) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $fin_cust_id . "";
            $sqldt = mysqli_query($con, $querydt);
        } else */
        if($nstatus == 4) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $bns_id . "";
            $sqldt = mysqli_query($con, $querydt);
        } 
    }
    $hold_end_date = date('Y-m-d', strtotime("+".$hold_days." days"));
    $query1 = "INSERT INTO `$appr_dtl_table` (`bns_id`,`bh_type`,`bh_on`,`bh_mode`,`bh_applicable`,`bh_per`,`bh_based_on`,`reason`,`created_by`,`action_on`,`action_by`,`remarks`,`stage_no`,`bh_hold_day`,`bh_hold_end_day`,`act_status`,`ip_addr`)VALUES('$bns_id', '$b_type', '$b_on', '$b_mode', '$applicable', '$b_per', '$based_on', '$msg', '$created_by', '$entry_date', '$sessionid', '$remarks', '$stage_no', '$hold_days', '$hold_end_date', '$nstatus', '$ip_addr')";

    $sql_query = mysqli_query($con, $query1);
    if ($sql_query) {
        return 1;
    } else {
        return 2;
    }
}

//*********************************

function Set_status_release_details($con, $bns_id, $approve_culumn, $status_table, $appr_dtl_table, $nstatus, $stage_no, $sessionid, $msg,$remarks)
{
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $sqlqry = mysqli_query($con, "SELECT * FROM hr_bonus WHERE id='$bns_id'");
    $rows = mysqli_fetch_object($sqlqry);
    $b_type = $rows->b_type;
    $b_on = $rows->b_on;
    $b_mode = $rows->b_mode;
    $applicable = $rows->applicable;
    $b_per = $rows->b_per;
    $based_on = $rows->based_on;
    $created_by = $rows->created_by;

    $entry_date = date('Y-m-d H:i:s');
    $entry_time = date('H:i:s'); 
    if($stage_no==0){
        $querydt = "UPDATE `$status_table` SET `$approve_culumn`='0',`stage_no`='$stage_no' WHERE id = " . $bns_id . "";
    }else{
        $querydt = "UPDATE `$status_table` SET `$approve_culumn`='2' WHERE id = " . $bns_id . "";	
    }
    $sqldt = mysqli_query($con, $querydt);

    $query1 = "INSERT INTO `$appr_dtl_table` (`bns_id`,`bh_type`,`bh_on`,`bh_mode`,`bh_applicable`,`bh_per`,`bh_based_on`,`reason`,`created_by`,`action_on`,`action_by`,`remarks`,`stage_no`,`act_status`,`ip_addr`)VALUES('$bns_id', '$b_type', '$b_on', '$b_mode', '$applicable', '$b_per', '$based_on', '$msg', '$created_by', '$entry_date', '$sessionid', '$remarks', '$stage_no', '$nstatus', '$ip_addr')";
    $sql_query = mysqli_query($con, $query1);
    if ($sql_query) {
        return 1;
    } else {
        return 2;
    }
}

//************************************ 2nd *****************************************

function br_Set_status_details($con, $bns_id, $approve_culumn, $status_table, $appr_dtl_table, $nstatus, $stage_no, $sessionid, $msg,$remarks)
{
    include_once('../../../approvalmatrixfunction.php');
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $entry_date = date('Y-m-d H:i:s');

    $sqlqry = mysqli_query($con, "SELECT * FROM hr_bonus_request WHERE id='$bns_id'");
    $rows = mysqli_fetch_object($sqlqry);
    $reqNoVls = $rows->req_no;
    $org_id = $rows->org_id;
    $b_type = $rows->b_type;
    $b_on = $rows->b_on;
    $b_mode = $rows->b_mode;
    $b_per = $rows->b_per;
    $based_on = $rows->based_on;
    $b_msg = $rows->b_msg;
    $created_by = $rows->created_by;

    if ($status_table != "" && $approve_culumn != "") {
        if ($nstatus == 1 || $nstatus == 2 ) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $bns_id . "";
            $sqldt = mysqli_query($con, $querydt);
            $notificationmsg = "Bonus request no : ".$reqNoVls." is pending for approve on your site.";
            $pageUrl = "hr/bonus/manage_bonus_request_list.php";
        } else if($nstatus == 6) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $bns_id . "";
            $sqldt = mysqli_query($con, $querydt);
            $notificationmsg = "Bonus request no : ".$reqNoVls." is rejected on your site.";
            $pageUrl = "hr/bonus/add_bonus_request_list.php";
        } 
    }
    $query1 = "INSERT INTO `$appr_dtl_table` (`br_id`, `org_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `created_by`, `action_by`, `action_on`, `stage_no`, `act_status`, `reason`, `remarks`, `ip_addr`)VALUES('$bns_id', '$org_id', '$b_type', '$b_mode', '$b_on', '$based_on', '$b_per', '$b_msg', '$created_by', '$sessionid', '$entry_date', '$stage_no', '$nstatus', '$msg', '$remarks', '$ip_addr')";
    $sql_query = mysqli_query($con, $query1);
    if ($sql_query) {
        //--------Notification
        $created_on = date('Y-m-d H:i:s');
        $url = 'hr/bonus/add_bonus_request_list.php';
        $menuid = getMenuById($con, $url);
        $emp_id = $_SESSION['ERP_SESS_ID'];
        $deptid = getdeptid($con, $emp_id);
        $reciever = getapprover($con, $menuid, $stage_no, $nstatus, '', '', $deptid, '', $emp_id);
        // $notificationmsg = "Bonus request no : ".$reqNoVls." is pending for approve on your site.";
        saveNotice($con, $bns_id, "Bonus Request", '0', $pageUrl, '0', $notificationmsg, $reciever, $emp_id, $created_on);
        //--------/Notification
        return 1;
    } else {
        return 2;
    }
}
function br_Set_status_recheck_details($con, $bns_id, $approve_culumn, $status_table, $appr_dtl_table, $nstatus, $stage_no, $sessionid, $msg,$remarks)
{
    include_once('../../../approvalmatrixfunction.php');
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $sqlqry = mysqli_query($con, "SELECT * FROM hr_bonus_request WHERE id='$bns_id'");
    $rows = mysqli_fetch_object($sqlqry);
    $reqNoVls = $rows->req_no;
    $org_id = $rows->org_id;
    $b_type = $rows->b_type;
    $b_on = $rows->b_on;
    $b_mode = $rows->b_mode;
    $b_per = $rows->b_per;
    $based_on = $rows->based_on;
    $b_msg = $rows->b_msg;
    $created_by = $rows->created_by;

    $entry_date = date('Y-m-d H:i:s');
    $entry_time = date('H:i:s'); 
    if ($status_table != "" && $approve_culumn != "") {
        if($nstatus == 3) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $bns_id . "";
            $sqldt = mysqli_query($con, $querydt);
        } 
    }
        $query1 = "INSERT INTO `$appr_dtl_table` (`br_id`, `org_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `created_by`, `action_by`, `action_on`, `stage_no`, `act_status`, `reason`, `remarks`, `ip_addr`)VALUES('$bns_id', '$org_id', '$b_type', '$b_mode', '$b_on', '$based_on', '$b_per', '$b_msg', '$created_by', '$sessionid', '$entry_date', '$stage_no', '$nstatus', '$msg', '$remarks', '$ip_addr')";

        $sql_query = mysqli_query($con, $query1);
    if ($sql_query) {
        //--------Notification
        $created_on = date('Y-m-d H:i:s');
        $url = 'hr/bonus/add_bonus_request_list.php';
        $menuid = getMenuById($con, $url);
        $emp_id = $_SESSION['ERP_SESS_ID'];
        $deptid = getdeptid($con, $emp_id);
        // $reciever = getapprover($con, $menuid, $stage_no, $nstatus, '', '', $deptid, '', $emp_id);
        $reciever = $created_by;
        $notificationmsg = "Bonus request no : ".$reqNoVls." pending for update on your site.";
        saveNotice($con, $bns_id, "Bonus Request", '0', "hr/bonus/add_bonus_request_list.php", '0', $notificationmsg, $reciever, $emp_id, $created_on);
        //--------/Notification
        return 1;
    } else {
        return 2;
    }
}

//****************************************

function br_Set_status_hold_details($con, $bns_id, $approve_culumn, $status_table, $appr_dtl_table, $nstatus, $stage_no, $sessionid,$hold_days,$msg,$remarks)
{
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $sqlqry = mysqli_query($con, "SELECT * FROM hr_bonus_request WHERE id='$bns_id'");
    $rows = mysqli_fetch_object($sqlqry);
    $reqNoVls = $rows->req_no;
    $org_id = $rows->org_id;
    $b_type = $rows->b_type;
    $b_on = $rows->b_on;
    $b_mode = $rows->b_mode;
    $b_per = $rows->b_per;
    $based_on = $rows->based_on;
    $b_msg = $rows->b_msg;
    $created_by = $rows->created_by;

    $entry_date = date('Y-m-d H:i:s');
    $entry_time = date('H:i:s'); 
    if ($status_table != "" && $approve_culumn != "") {
        /*if ($nstatus == 1 || $nstatus == 2 ) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $fin_cust_id . "";
            $sqldt = mysqli_query($con, $querydt);
        } else */
        if($nstatus == 4) {
            $querydt = "UPDATE `$status_table` SET `$approve_culumn`='$nstatus',stage_no='$stage_no' WHERE id = " . $bns_id . "";
            $sqldt = mysqli_query($con, $querydt);
        } 
    }
    $hold_end_date = date('Y-m-d', strtotime("+".$hold_days." days"));
    $query1 = "INSERT INTO `$appr_dtl_table` (`br_id`, `org_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `created_by`, `action_by`, `action_on`, `stage_no`, `bh_hold_day`, `bh_hold_end_day`, `act_status`, `reason`, `remarks`, `ip_addr`)VALUES('$bns_id', '$org_id', '$b_type', '$b_mode', '$b_on', '$based_on', '$b_per', '$b_msg', '$created_by', '$sessionid', '$entry_date', '$stage_no', '$hold_days', '$hold_end_date', '$nstatus', '$msg', '$remarks', '$ip_addr')";

    $sql_query = mysqli_query($con, $query1);
    if ($sql_query) {
        return 1;
    } else {
        return 2;
    }
}

//*********************************

function br_Set_status_release_details($con, $bns_id, $approve_culumn, $status_table, $appr_dtl_table, $nstatus, $stage_no, $sessionid, $msg,$remarks)
{
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $sqlqry = mysqli_query($con, "SELECT * FROM hr_bonus_request WHERE id='$bns_id'");
    $rows = mysqli_fetch_object($sqlqry);
    $reqNoVls = $rows->req_no;
    $org_id = $rows->org_id;
    $b_type = $rows->b_type;
    $b_on = $rows->b_on;
    $b_mode = $rows->b_mode;
    $b_per = $rows->b_per;
    $based_on = $rows->based_on;
    $b_msg = $rows->b_msg;
    $created_by = $rows->created_by;

    $entry_date = date('Y-m-d H:i:s');
    $entry_time = date('H:i:s'); 
    if($stage_no==0){
        $querydt = "UPDATE `$status_table` SET `$approve_culumn`='0',`stage_no`='$stage_no' WHERE id = " . $bns_id . "";
    }else{
        $querydt = "UPDATE `$status_table` SET `$approve_culumn`='2' WHERE id = " . $bns_id . "";   
    }
    $sqldt = mysqli_query($con, $querydt);

    $query1 = "INSERT INTO `$appr_dtl_table` (`br_id`, `org_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `created_by`, `action_by`, `action_on`, `stage_no`, `act_status`, `reason`, `remarks`, `ip_addr`)VALUES('$bns_id', '$org_id', '$b_type', '$b_mode', '$b_on', '$based_on', '$b_per', '$b_msg', '$created_by', '$sessionid', '$entry_date', '$stage_no', '$nstatus', '$msg', '$remarks', '$ip_addr')";
    $sql_query = mysqli_query($con, $query1);
    if ($sql_query) {
        return 1;
    } else {
        return 2;
    }
}

?>