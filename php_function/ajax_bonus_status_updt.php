<?php
require_once('../../../auth.php');
require_once('../../../config.php');
include_once('../../../workflownotif.php');
include_once('../../../approvalmatrixfunction.php');
include_once('set_bonus_status_updt.php');
include_once('bonus_function.php');
date_default_timezone_set("Asia/Calcutta");
?>
<?php
$sessionid = $_SESSION['ERP_SESS_ID'];
$ip_addr = $_SERVER['REMOTE_ADDR'];
if (!empty($_POST['action'])) {
    $action = $_POST['action'];
    $viewid = $_POST['viewid'];
    $empIds = $_REQUEST['empIds'];
}

// for approve
if ($action == 'approve') {
    $msg = $_REQUEST['appr_reason'];
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $dept_id = getdeptid($con, $creator);
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = getdynamicRemarks($con, $parent_id, $appro_stage_no); // Get Dynamic Remark
    }
    if ($appro_stage_no == 0) {
        $b_status = 1;
        $updatestage_no = $stage_no + 1;
    } else {
        $b_status = 2;
        $updatestage_no = $appro_stage_no;
    }
    // echo "Sanjay :- ".$updatestage_no;
    // exit();
    $approve_culumn = "b_status";
    $main_table = "hr_bonus";
    $history_table = "hr_bonus_history";
    $sql_query = Set_status_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $b_status, $updatestage_no, $sessionid, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $b_status = $requestrecord->b_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $b_status, '', '', $deptid, '',$created_by);
        $notificationmsg = "New Bonus approved by approver :  " . $creatornm . ""; //notification msg
        // sendWfnotification($con, $notificationmsg, '', "Bonus", '', '', $employeeid,$url, '');
        echo 1;
    }
}


//for recheck
if ($action == 'recheck') {
    $msg = $_REQUEST['recheck_reason'];
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $dept_id = getdeptid($con, $creator);
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = 'Recheck'; // Get Dynamic Remark
    }
        $b_status = 3;
        $updatestage_no = 0;
    $approve_culumn = "b_status";
    $main_table = "hr_bonus";
    $history_table = "hr_bonus_history";
    $sql_query = Set_status_recheck_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $b_status, $updatestage_no, $sessionid, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $b_status = $requestrecord->b_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $b_status, '', '', $deptid, '',$created_by);
        $notificationmsg = "Rechecked by approver :  " . $creatornm . ""; //notification msg
        // sendWfnotification($con, $notificationmsg, '', "Bonus", '', '', $employeeid,$url, '');
        echo 1;
    }
}

//***************************************

//for hold
if ($action == 'hold') {
    $msg = $_REQUEST['hold_reason'];
    $hold_days = $_REQUEST['hold_days'];
    $hold_end_date = date('Y-m-d', strtotime("+" . $hold_days . " days"));
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $dept_id = getdeptid($con, $creator);
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = 'Hold'; // Get Dynamic Remark
    }
    $b_status = 4;
    $updatestage_no = $stage_no;
    $approve_culumn = "b_status";
    $main_table = "hr_bonus";
    $history_table = "hr_bonus_history";
    $sql_query = Set_status_hold_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $b_status, $updatestage_no, $sessionid, $hold_days, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $b_status = $requestrecord->b_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $b_status, '', '', $deptid, '',$created_by);
        $notificationmsg = "Hold by approver :  " . $creatornm . ""; //notification msg
        // sendWfnotification($con, $notificationmsg, '', "Customer", '', '', $employeeid,$url, '');
        echo 1;
    }
}
// for reject
if ($action == 'reject') {
    $msg = $_REQUEST['reject_reason'];
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $dept_id = getdeptid($con, $creator);
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = 'Reject'; // Get Dynamic Remark
    }
    $b_status = 6;
    $approve_culumn = "b_status";
    $main_table = "hr_bonus";
    $history_table = "hr_bonus_history";
    $sql_query = Set_status_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $b_status, $updatestage_no, $sessionid, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $b_status = $requestrecord->b_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $b_status, '', '', $deptid, '');
        $notificationmsg = "Rejected by approver :  " . $creatornm . ""; //notification msg
        // sendWfnotification($con, $notificationmsg, '', "Customer", '', '', $employeeid,$url, '');
        echo 1;
    }
}



//for release
if ($action == 'release') {
    $msg = $_REQUEST['release_reason'];
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = 'Released'; // Get Dynamic Remark
    }
    $cust_status = 2;
    $updatestage_no = $stage_no;
    $approve_culumn = "b_status";
    $main_table = "hr_bonus";
    $history_table = "hr_bonus_history";
    $sql_query = Set_status_release_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $cust_status, $updatestage_no, $sessionid, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $cust_status = $requestrecord->cust_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $cust_status, '', '', $deptid, '',$created_by);
        $notificationmsg = "Released by approver :  " . $creatornm . ""; //notification msg
        // sendWfnotification($con, $notificationmsg, '', "Customer", '', '', $employeeid,$url, '');
        echo 1;
    }
}


//************************************* 2nd ***************************************


//************** getEmpListOfSingleBonus Start
if ($action == 'getEmpListOfSingleBonus') {
    $bnsReqIds = $_REQUEST['bnsReqIds'];
    $empNames = $_REQUEST['empNames'];

    $sltQry = "SELECT a.*,b.* FROM hr_bonus_emp_list a, hr_bonus_request b WHERE a.ebr_id='$bnsReqIds'";
    $empDtlsRows = mysqli_query($con, $sltQry);
    $res=[];
    while ($empDtls = mysqli_fetch_object($empDtlsRows)) {
        $emp_sl_id = $empDtls->emp_sl_id;
        $emp_id = $empDtls->emp_id;
        $org_id = $empDtls->org_id;

        if ($empNames=='0') {
            // $qry = "SELECT x.*, y.*, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$org_id' AND x.department_id=a.id AND x.location_id=b.id AND y.id='$emp_id' ORDER BY x.id DESC";

            $qry = "SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.org_nm='$org_id' AND y.id='$emp_id' AND x.department_id=a.id AND x.location_id=b.id UNION (SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.org_nm='$org_id' AND y.id='$emp_id' AND x.department_id=a.id AND x.location_id=b.id)";

            // $qry = "SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.org_nm='$orgNameVls' AND y.fullname LIKE '%$empName%' AND x.department_id=a.id AND x.location_id=b.id UNION (SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.org_nm='$orgNameVls' AND y.fullname LIKE '%$empName%' AND x.department_id=a.id AND x.location_id=b.id)";

        }else{
            // $qry = "SELECT x.*, y.*, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$org_id' AND x.department_id=a.id AND x.location_id=b.id AND y.id='$emp_id' AND y.fullname LIKE '%$empNames%' ORDER BY x.id DESC";

            $qry = "SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.org_nm='$org_id' AND y.id='$emp_id' AND y.fullname LIKE '%$empName%' AND x.department_id=a.id AND x.location_id=b.id UNION (SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.org_nm='$org_id' AND y.id='$emp_id' AND y.fullname LIKE '%$empName%' AND x.department_id=a.id AND x.location_id=b.id)";

            // $qry = "SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id UNION (SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id)";

        }
        $empRows = mysqli_query($con, $qry);
        $empVls = mysqli_fetch_object($empRows);
        if ($empVls->name!='' && $empVls->name!=null) {
            $res[]=['emp_sl_id'=>$emp_sl_id,'empName'=>$empVls->name,'empDeg'=>$empVls->desig,'dept'=>$empVls->deptname,'location'=>$empVls->locname,'b_on'=>$empDtls->bns_on,'salary'=>$empDtls->bns_rate,'b_per'=>$empDtls->bns_pre,'days'=>$empDtls->bns_days,'getBonus'=>$empDtls->bns_amt];
        }
    }
    echo json_encode($res);

}
//************** getEmpListOfSingleBonus End


// for approve
if ($action == 'br_approve') {
    $msg = $_REQUEST['appr_reason'];
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $dept_id = getdeptid($con, $creator);
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = getdynamicRemarks($con, $parent_id, $appro_stage_no); // Get Dynamic Remark
    }
    if ($appro_stage_no == 0) {
        $b_status = 1;
        $updatestage_no = $stage_no + 1;
    } else {
        $b_status = 2;
        $updatestage_no = $appro_stage_no;
    }
    // echo "Sanjay :- ".$updatestage_no;
    // exit();
    $approve_culumn = "b_status";
    $main_table = "hr_bonus_request";
    $history_table = "hr_bonus_request_history";
    $sql_query = br_Set_status_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $b_status, $updatestage_no, $sessionid, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus_request` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $b_status = $requestrecord->b_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $b_status, '', '', $deptid, '',$created_by);
        $notificationmsg = "New Bonus approved by approver :  " . $creatornm . ""; //notification msg
        // sendWfnotification($con, $notificationmsg, '', "Bonus", '', '', $employeeid,$url, '');
        echo 1;
        // echo $bns_id.', '.$approve_culumn.', '.$main_table.', '.$history_table.', '.$b_status.', '.$updatestage_no.', '.$sessionid.', '.$msg.', '.$remarks.', '.$appro_stage_no;
    }
}


//for recheck
if ($action == 'br_recheck') {
    $msg = $_REQUEST['recheck_reason'];
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $dept_id = getdeptid($con, $creator);
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = 'Recheck'; // Get Dynamic Remark
    }
        $b_status = 3;
        $updatestage_no = 0;
    $approve_culumn = "b_status";
    $main_table = "hr_bonus_request";
    $history_table = "hr_bonus_request_history";
    $sql_query = br_Set_status_recheck_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $b_status, $updatestage_no, $sessionid, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus_request` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $b_status = $requestrecord->b_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $b_status, '', '', $deptid, '',$created_by);
        $notificationmsg = "Rechecked by approver :  " . $creatornm . ""; //notification msg
        echo 1;
    }
}

//***************************************

//for hold
if ($action == 'br_hold') {
    $msg = $_REQUEST['hold_reason'];
    $hold_days = $_REQUEST['hold_days'];
    $hold_end_date = date('Y-m-d', strtotime("+" . $hold_days . " days"));
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $dept_id = getdeptid($con, $creator);
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = 'Hold'; // Get Dynamic Remark
    }
    $b_status = 4;
    $updatestage_no = $stage_no;
    $approve_culumn = "b_status";
    $main_table = "hr_bonus_request";
    $history_table = "hr_bonus_request_history";
    $sql_query = br_Set_status_hold_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $b_status, $updatestage_no, $sessionid, $hold_days, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus_request` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $b_status = $requestrecord->b_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $b_status, '', '', $deptid, '',$created_by);
        $notificationmsg = "Hold by approver :  " . $creatornm . ""; //notification msg
        echo 1;
    }
}
// for reject
if ($action == 'br_reject') {
    $msg = $_REQUEST['reject_reason'];
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $dept_id = getdeptid($con, $creator);
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = 'Reject'; // Get Dynamic Remark
    }
    $b_status = 6;
    $approve_culumn = "b_status";
    $main_table = "hr_bonus_request";
    $history_table = "hr_bonus_request_history";
    $sql_query = br_Set_status_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $b_status, $updatestage_no, $sessionid, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus_request` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $b_status = $requestrecord->b_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $b_status, '', '', $deptid, '');
        $notificationmsg = "Rejected by approver :  " . $creatornm . ""; //notification msg
        echo 1;
    }
}



//for release
if ($action == 'br_release') {
    $msg = $_REQUEST['release_reason'];
    $menuid = $_REQUEST['menu_id'];
    $status = $_REQUEST['status'];
    $creator = $_REQUEST['creator'];
    $bns_id = $_REQUEST['bns_id'];
    $stage_no = $_REQUEST['stage_no'];
    $tot_stage = getTotalapprovalstage($con, $menuid);
    $created_on = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $cashstatus = Checkcashstatus($con, $menuid); // to get financial status
    $recordetails = mysqli_query($con, "SELECT * FROM module_approval_matrix WHERE menu_id = '$menuid'");
    $getrecid = mysqli_fetch_object($recordetails);
    $parent_id = $getrecid->id;
    if ($cashstatus == '0') {
        $approval_sqrydtls = mysqli_query($con, "SELECT * FROM module_approval_matrix_details WHERE parent_id = '" . $parent_id . "' AND stage_no > '" . $stage_no . "' ORDER BY stage_no ASC LIMIT 1");
        $getstage_details = mysqli_fetch_object($approval_sqrydtls);
        $appro_stage_no = $getstage_details->stage_no; // get approval stage number 
        $remarks = 'Released'; // Get Dynamic Remark
    }
    $cust_status = 2;
    $updatestage_no = $stage_no;
    $approve_culumn = "b_status";
    $main_table = "hr_bonus_request";
    $history_table = "hr_bonus_request_history";
    $sql_query = br_Set_status_release_details($con, $bns_id, $approve_culumn, $main_table, $history_table, $cust_status, $updatestage_no, $sessionid, $msg, $remarks);
    if ($sql_query) {
        $requestdetails = mysqli_query($con, "SELECT * FROM `hr_bonus_request` WHERE `id` = '" . $bns_id . "'");
        $requestrecord = mysqli_fetch_object($requestdetails);
        $cust_status = $requestrecord->cust_status;
        $created_by = $requestrecord->created_by;
        $stage_no = $requestrecord->stage_no;
        $deptid = getdeptid($con, $created_by);
        $empdetails = mysqli_query($con, "SELECT * FROM `mstr_emp` WHERE id =" . $sessionid);
        $egqcr = mysqli_fetch_object($empdetails);
        $creatornm = $egqcr->fullname;
        $employeeid = getapprover($con, $menuid, $stage_no, $cust_status, '', '', $deptid, '',$created_by);
        // $notificationmsg = "Released by approver :  " . $creatornm . ""; //notification msg
        
        echo 1;
    }
}


?>