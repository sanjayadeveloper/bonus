<?php 
//include("../../config.php"); 
$url = "hr/bonus/add_bonus_request_list.php";
$menuid = getMenuById($con, $url);


//***************New Added Start
function getApproverList($con, $menuid, $stage_no){
    $stage_nos = $stage_no+1;
    $approval_sqry = mysqli_query($con,"SELECT * FROM module_approval_matrix WHERE menu_id = '".$menuid."'");
    $getapproval=mysqli_fetch_object($approval_sqry);
    $parent_id = $getapproval->id;
    $approval_sqrydtls_a = mysqli_query($con,"SELECT * FROM module_approval_matrix_details WHERE parent_id = '$parent_id' AND Stage_no = '$stage_nos'");
    $nmRows = mysqli_num_rows($approval_sqrydtls_a);
    if ($nmRows==0) {
        $approval_sqrydtls = mysqli_query($con,"SELECT * FROM module_approval_matrix_details WHERE parent_id = '$parent_id' AND Stage_no = '0'");
        $getapprovaldtls = mysqli_fetch_object($approval_sqrydtls); 
    }else{
        $getapprovaldtls = mysqli_fetch_object($approval_sqrydtls_a);
    }
    $dtlsparentid = $getapprovaldtls->id;
    $approval_empdtls = mysqli_query($con,"SELECT * FROM module_approval_employee_details WHERE parent_id = '$dtlsparentid'");
    $getempdtls = mysqli_fetch_object($approval_empdtls);
    $emparray = $getempdtls->emp_id;
    return $emparray;
};
function getApproverListView($con, $menuid){
    $approval_sqry = mysqli_query($con,"SELECT * FROM module_approval_matrix WHERE menu_id = '".$menuid."'");
    $getapproval=mysqli_fetch_object($approval_sqry);
    $parent_id = $getapproval->id;
    $approval_sqrydtls = mysqli_query($con,"SELECT * FROM module_approval_matrix_details WHERE parent_id = '$parent_id'");
    $resArray=[];
    while ($getapprovaldtls = mysqli_fetch_object($approval_sqrydtls)) {
        $resArray[]=$getapprovaldtls->id;
    }
    return $resArray;
};
//***************New Added End



function checkbonuscount($con,$sessionid,$status){

	// $apprvdcount = mysqli_query($con, "SELECT a.*,b.*,c.* FROM hr_bonus a, master_type_dtls b, mstr_emp c WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.b_status='$status' AND a.created_by='$sessionid'");
    // $apprvcount = mysqli_num_rows($apprvdcount);
    // return $apprvcount;

    //************

    $apprvdcount = mysqli_query($con, "SELECT a.*,b.*,c.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.b_status='$status'");
    $apprvcount = mysqli_num_rows($apprvdcount);
    $counter=0;
    if ($apprvcount>0) {
        while ($rows = mysqli_fetch_object($apprvdcount)) {
            $stage_no = $rows->stage_no;
            $url = "hr/bonus/add_bonus_request_list.php";
            $menuid = getMenuById($con, $url);
            $getApproverList = getApproverList($con, $menuid, $stage_no);
            if ($rows->created_by==$sessionid || $getApproverList==$sessionid) {
                $counter++;
            }
        }
    }
    return $counter;
}
function checkpendingcount($con,$sessionid,$status){
    $status_cnt = explode(',', $status);
    $res = '';
    for ($i=0; $i < count($status_cnt); $i++) {
        $counter = $i+1;
        if ($counter==count($status_cnt)) {
            $stsVls .= $status_cnt[$i];
        }else{
            $stsVls .= $status_cnt[$i].',';
        }
    }
    // $apprvdcount = mysqli_query($con, "SELECT a.*,b.*,c.* FROM hr_bonus a, master_type_dtls b, mstr_emp c WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.b_status IN ($stsVls) AND a.created_by='$sessionid'");
    $apprvdcount = mysqli_query($con, "SELECT a.*,b.*,c.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.b_status IN ($stsVls)");
    // $apprvcount = mysqli_num_rows($apprvdcount);
    $counter=0;
    while ($rows = mysqli_fetch_object($apprvdcount)) {
        $stage_no = $rows->stage_no;
        $url = "hr/bonus/add_bonus_request_list.php";
        $menuid = getMenuById($con, $url);
        $getApproverList = getApproverList($con, $menuid, $stage_no);
        if ($rows->created_by==$sessionid || $getApproverList==$sessionid) {
            $counter++;
        }
    }
    return $counter;
}
function allcount($con, $sessionid){
    $detailscount = mysqli_query($con, "SELECT a.*,b.*,c.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id");
    $allcount = mysqli_num_rows($detailscount);
    return $allcount;
}

//*********************************************

function getStateName($con, $stid)
{
    $splyqr = mysqli_query($con, "SELECT * FROM `prj_state` WHERE `id`='$stid'");
    $fthst = mysqli_fetch_object($splyqr);
    return $fthst->sname;
}
function checkcustomerholdcount($con,$sessionid,$status){
    $holdcount = mysqli_query($con, "SELECT a.id,d.id,s.id,e.fullname as raiser,emp.fullname as approver 
    FROM fin_customers as a 
    LEFT JOIN fin_customers_history_details as d on a.id = d.fin_cust_id 
    LEFT JOIN fin_grouping_subtype as s on a.group_subtype = s.id 
    LEFT JOIN mstr_emp as e on e.id = a.created_by 
    LEFT JOIN mstr_emp as emp on emp.id = d.created_by 
    WHERE a.cust_status = '".$status."' AND d.created_by ='" . $sessionid . "' ");
    $apprvcount = mysqli_num_rows($holdcount);
    return $apprvcount;
}





