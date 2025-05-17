<?php
require_once('../../auth.php');
require_once('../../config.php');
include_once('../../workflownotif.php');
include_once('../../approvalmatrixfunction.php');
include_once('php_function/bonus_function.php');
$sessionid = $_SESSION['ERP_SESS_ID'];
// $dateView = date('d-m-Y', strtotime($rows->b_date));
// include_once('dbconnect.php');
// $rowsQry = $conn_obj->getNumRows("*","hr_bonus","");


    $org_name = $_GET['org_name'];
    $frm_date = $_GET['frm_date'];
    $to_date = $_GET['to_date'];
    $bonus_type = $_GET['bonus_type'];
    $bonus_mode = $_GET['bonus_mode'];
    $actVls = $_GET['actVls'];
    $tag = $_GET['tag'];
    
    $tagVls_a = explode(',', $tag);

    if ($actVls=='All') {
        $filename = 'Bonus_All.csv';
    }else if($actVls=='Approved'){
        $filename = 'Bonus_Approved.csv';
    }else if($actVls=='Reject'){
        $filename = 'Bonus_Reject.csv';
    }else if($actVls=='Pending'){
        $filename = 'Bonus_Pending.csv';
    }else if($actVls=='Re-Check'){
        $filename = 'Bonus_Re-Check.csv';
    }else if($actVls=='Hold'){
        $filename = 'Bonus_Hold.csv';
    }


    //*****************************
    

    //*********Excel
    $file = fopen($filename, 'w');
    $column_headers=[];
    for ($i=0; $i < count($tagVls_a); $i++) { 
        $tagVls = explode('|', $tagVls_a[$i]);
        $column_headers[] = $tagVls[0];
    }
    // $column_headers = array("Sl.No.", "Date", "Bonus Type", "Bonus On", "Bonus Mode", "Applicable Form", "Bonus (%)", "Status", "Status Details", "Status Dt.", "Action");
    fputcsv($file, $column_headers);
    //*********Excel


    $allQryVls = "";

    if (!empty($frm_date)) {
        if (!empty($to_date)) {
            $allQryVls .= " AND DATE(a.created_on)>='$frm_date'";
        }else{
            $allQryVls .= " AND DATE(a.created_on)='$frm_date'";
        }
    }else{
        $allQryVls .= "";
    }
    if (!empty($to_date)) {
        if (!empty($frm_date)) {
            $allQryVls .= " AND DATE(a.created_on)<='$to_date'";
        }else{
            $allQryVls .= " AND DATE(a.created_on)='$to_date'";
        }
    }else{
        $allQryVls .= "";
    }
    if (!empty($bonus_type)) {
        $allQryVls .= " AND a.b_type='$bonus_type'";
    }else{
        $allQryVls .= "";
    }
    if (!empty($bonus_mode)) {
        $allQryVls .= " AND a.b_mode='$bonus_mode'";
    }else{
        $allQryVls .= "";
    }
    if (!empty($org_name)) {
        $allQryVls .= " AND a.org_id='$org_name'";
    }else{
        $allQryVls .= "";
    }

    //*****************************

    if ($actVls=='All') {
        $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) $allQryVls ORDER BY a.id DESC");
    }else if($actVls=='Approved'){
        $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) AND a.b_status='1' $allQryVls ORDER BY a.id DESC");
    }else if($actVls=='Reject'){
        $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) AND a.b_status='6' $allQryVls ORDER BY a.id DESC");
    }else if($actVls=='Pending'){
        $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) AND (a.b_status='0' || a.b_status='2') $allQryVls ORDER BY a.id DESC");
    }else if($actVls=='Re-Check'){
        $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) AND a.b_status='3' $allQryVls ORDER BY a.id DESC");
    }else if($actVls=='Hold'){
        $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) AND a.b_status='4' $allQryVls ORDER BY a.id DESC");
    }

    //*****************************
    $bnsQry_results = mysqli_num_rows($bnsQry);

    if ($bnsQry_results>0) {
        // code...
        $i=1;
        $arrayVls = [];
        while($rows=mysqli_fetch_object($bnsQry)){
            //********Approved User Access
            $getApproverList = getApproverList($con, $menuid, $rows->stage_no);
            //********Approved User Access
            $refid = $rows->ids;
            $req_no = $rows->req_no;
            $created_on = $rows->created_on;
            $getfield = "remarks"; //to fetch approved by id from details table
            $dateView = date('d-m-Y', strtotime($rows->created_at));
            $stsVls = $rows->b_status;
            $refcolmn = "act_status";

            if($stsVls == '0'){
                $status = 'Request Raised';
                $colorR = 'color:Orange';
            }else{
                $status =  getstatus($con, 'hr_bonus_request_history', 'br_id', $refid, $getfield, $refcolmn);
                $colorR = 'color:green';
            }
            $bnsQry_a = mysqli_query($con,"SELECT * FROM master_type_dtls WHERE mstr_type_value='$rows->b_mode'");
            $rows_a=mysqli_fetch_object($bnsQry_a);

            $bnsQry_b = mysqli_query($con,"SELECT * FROM hr_bonus_request_history WHERE br_id='$rows->ids' ORDER BY id DESC LIMIT 1"); //*****New Added
            $rows_b=mysqli_fetch_object($bnsQry_b);

            //********
            $empid = $rows->created_by;
            $deptid = getdeptid($con, $empid);
            $stage_no = $rows->stage_no;
            if ($stsVls == 0 || $stsVls == 2) {
                $datas =  payliststatuswith($con, $menuid, $stage_no, $stsVls, '', '', $deptid, '', $empid);
            } else {
                $datas = statuswithother($con, 'hr_bonus_request_history', 'br_id', $rows->ids, $stsVls, 'action_by');
            }
            //********

            $dataVls = array($i, $req_no, $dateView, $rows->organisation, $rows->mstr_type_name, $rows->b_on, $rows_a->mstr_type_name, $rows->b_per, $status, 'datas', $created_on);
            $column_values=[];
            for ($i=0; $i < count($tagVls_a); $i++) { 
                $tagVls = explode('|', $tagVls_a[$i]);
                $column_values[] = $dataVls[$tagVls[1]];
            }
            
            if ($actVls=='All') {
                fputcsv($file, $column_values);
            }else{
                if ($rows->created_by==$sessionid || $getApproverList==$sessionid) {
                    fputcsv($file, $column_values);
                }
            }
            $i++;
        }
    }
    fclose($file);
    // Download
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=" . $filename);
    header("Content-Type: application/csv");
    header("Content-Type: text/html");

    readfile($filename);

    // deleting file
    unlink($filename);
    exit();

?>