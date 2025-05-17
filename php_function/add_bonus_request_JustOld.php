<?php
    require_once('../../../config.php');
    require_once('../../../auth.php');

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    $ERP_SESS_ID = $_SESSION['ERP_SESS_ID'];
    $sessionid = $ERP_SESS_ID;
    $crntDate = date('Y-m-d');
    $crntMY = date('m-Y');
    $crntM = date('m');
    $crntY = date('Y');

}



if ($action=='sessionArrayCheck') {
    print_r($_SESSION);
}


if ($action=='modeCheck') {
    $ModeVls = $_POST['ModeVls'];

    //********************Approved Start
    $aprovQry = mysqli_query($con,"SELECT * FROM `hr_bonus` WHERE created_by='$ERP_SESS_ID' AND b_status=1 ORDER BY id DESC LIMIT 1");
    $aprovQry_results = mysqli_num_rows($aprovQry);

    if($aprovQry_results == 0){
        $res['dt'] = '00-0000';
    }else{
        $aprov_rows=mysqli_fetch_object($aprovQry);
        $aprov_mode = $aprov_rows->b_mode;
        if ($aprov_mode!=$ModeVls) {
            switch ($ModeVls) {
                case 'monthly':
                    if ($crntM==12) {
                        $sltMnth = '01';
                    }else{
                        $sltMnth = '0'.$crntM+1;
                    }
                    break;
                
                case 'quarterly':
                    if ($crntM >= 1 && $crntM <= 3) {
                        $sltMnth = '04';
                    }elseif ($crntM >= 4 && $crntM <= 6) {
                        $sltMnth = '07';
                    }elseif ($crntM >= 7 && $crntM <= 9) {
                        $sltMnth = 10;
                    }elseif ($crntM >= 10 && $crntM <= 12) {
                        $sltMnth = 1;
                    }
                    break;
                
                case 'half yearly':
                    if ($crntM >= 1 && $crntM <= 6) {
                        $sltMnth = '07';
                    }elseif ($crntM >= 7 && $crntM <= 12) {
                        $sltMnth = 1;
                    }
                    break;
                
                default:
                    $sltMnth = 1;
                    break;
            }
        }else{
            if ($crntM<10) {
                $crntM = $crntM+1;
            }else{
                $crntM = '0'.$crntM+1;
            }
            $sltMnth = '';
        }
        

        if ($sltMnth=='') {
            // $res['dt'] = date($crntM.'/Y');
        }elseif ($sltMnth==1) {
            $aprovCrntMY = date('Y', strtotime('+1 year'));
            // $res['dt'] = '01/'.$aprovCrntMY;
        }else{
            // $res['dt'] = date($sltMnth.'/Y');
        }
    }
    //********************Approved End

    $aprovQry_a = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE mstr_type_value='$ModeVls'");
    $aprovQry_a_results = mysqli_num_rows($aprovQry_a);

    if($aprovQry_a_results == 0){
        $Error_message="NO RECORDS FOUND.";
    }else{
        $aprov_rows=mysqli_fetch_object($aprovQry_a);
        $res['type_name'] = $aprov_rows->mstr_type_name;
    }

    echo json_encode($res);
}


if ($action=='bonusRequestSubmit') {
    $bonusType = $_POST['bonusType'];
    $bonusOn = $_POST['bonusOn'];
    $bonusMode = $_POST['bonusMode'];
    $applicableForm = $_POST['applicableForm'];
    $bonusPer = $_POST['bonusPer'];
    $basedOn = $_POST['basedOn'];
    $bonusReMark = $_POST['bonusReMark'];
    
    if (isset($_POST['update'])) {
        $bonus_id = $_POST['bonus_id'];
        $qry = "UPDATE `hr_bonus` SET `b_date`='$crntDate', `b_type`='$bonusType', `b_on`='$bonusOn', `b_mode`='$bonusMode', `applicable`='$applicableForm', `b_per`='$bonusPer', `based_on`='$basedOn', `b_status`='0', `stage_no`='0', `created_by`='$ERP_SESS_ID'";
    }else{
        $qry = "INSERT INTO `hr_bonus`(`b_date`, `b_type`, `b_on`, `b_mode`, `applicable`, `b_per`, `based_on`, `stage_no`, `created_by`) VALUES ('$crntDate','$bonusType','$bonusOn','$bonusMode','$applicableForm','$bonusPer','$basedOn','0','$ERP_SESS_ID')";
    }

    // $qry = "INSERT INTO `hr_bonus`(`b_date`, `b_type`, `b_on`, `b_mode`, `applicable`, `b_per`, `based_on`, `stage_no`, `created_by`, `remarks`) VALUES ('$crntDate','$bonusType','$bonusOn','$bonusMode','$applicableForm','$bonusPer','$basedOn','0','$ERP_SESS_ID','$bonusReMark')";

    $sqlqry = mysqli_query($con, $qry);
    if ($sqlqry) {
        $res = 1;
    }else{
        $res = 0;
    }
    echo $res;
}




//******************************* ( Calculate Page ) ******************************************************





if ($action=='orgNameCheck') {
    $getEdit = $_POST['getEdit'];
    $orgNameVls = $_POST['orgNameVls'];
    $orgBonusType = $_POST['orgBonusType'];
    $empName = $_POST['empName'];
    if ($orgNameVls!='' || $orgNameVls!=null) {

        // $qry = "SELECT x.id as empID, y.fullname as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' UNION (SELECT x.id as empID, x.emp_name as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.department_id='2')";

        if ($empName!='0') {
            $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%' ORDER BY x.id DESC";
        }else{
            $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id ORDER BY x.id DESC";
        }


        // if ($empName!='0') {
        //     $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.*, c.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b, prj_organisation c WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm=c.id AND c.id='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%' ORDER BY x.id DESC";
        // }else{
        //     $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.*, c.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b, prj_organisation c WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm=c.id AND c.id='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id ORDER BY x.id DESC";
        // }
        
        // echo $qry;
        // exit();
        $orgQry = mysqli_query($con, $qry);
        if (mysqli_num_rows($orgQry)>0) {
            $i=0;
            $counter=0;
            $emp_id = getBonusEmpList($con, $orgBonusType);
            $exp_emp_id = explode(',', $emp_id);
            $aryData = [];
            for ($a=0; $a < count($exp_emp_id); $a++) { 
                $aryData[] = $exp_emp_id[$a];
            }


            $getSlVls=$slVls;
            while ($rows = mysqli_fetch_object($orgQry)) {
                $idVls = $rows->empIds;
                if ($getEdit==0) {
                    if (!in_array($idVls, $aryData)) {
                    $i++;
                        $new_str = str_pad($idVls,'5',"0", STR_PAD_LEFT);
                        $empSlIdsVls = 'bns_'.$new_str;
    ?>
            <tr>
               <td><input type="checkbox" class="bonusCheck" name="bonusCheckVls[]" id="listId_<?=$i?>" value="<?=$idVls?>" onclick="allCheckFn(<?=$i?>,'bonus'), saveIdsVls(<?=$i?>, <?=$idVls?>,'bonus')"> &#x00A0; <span style="margin-top: 2px; position: absolute;"><?=$i?></span></td>
               <td id="empSlIdsVls_<?=$i;?>"><?=$empSlIdsVls;?></td>
               <td><?=$rows->fullname;?></td>
               <td><?=$rows->designation;?></td>
               <td><?=$rows->dept_name;?></td>
               <td><?=$rows->lname;?></td>
               <td id="setBonusOn_<?=$i;?>"></td>
               <td id="setBonusSalary_<?=$i;?>"></td>
               <td id="setBonusPer_<?=$i;?>"></td>
               <td id="setBonusDays_<?=$i;?>"></td>
               <td id="setBonusAmounts_<?=$i;?>"></td>
            </tr>
    <?php

                    $getSlVls=$getSlVls+1;
                    }
                }else{
                    // if (in_array($idVls, $aryData)) {
                    $i++;
                        $new_str = str_pad($idVls,'5',"0", STR_PAD_LEFT);
                        $empSlIdsVls = 'bns_'.$new_str;
    ?>
     <?php //if (in_array($idVls, $aryData)) {echo 'checked';}?>
            <tr>
               <td><input type="checkbox" class="bonusCheck" name="bonusCheckVls[]" id="listId_<?=$i?>" value="<?=$idVls?>" onclick="allCheckFn(<?=$i?>,'bonus'), saveIdsVls(<?=$i?>, <?=$idVls?>,'bonus')"> &#x00A0; <span style="margin-top: 2px; position: absolute;"><?=$i?></span></td>
               <td id="empSlIdsVls_<?=$i;?>"><?=$empSlIdsVls;?></td>
               <td><?=$rows->fullname;?></td>
               <td><?=$rows->designation;?></td>
               <td><?=$rows->dept_name;?></td>
               <td><?=$rows->lname;?></td>
               <td id="setBonusOn_<?=$i;?>"></td>
               <td id="setBonusSalary_<?=$i;?>"></td>
               <td id="setBonusPer_<?=$i;?>"></td>
               <td id="setBonusDays_<?=$i;?>"></td>
               <td id="setBonusAmounts_<?=$i;?>"></td>
            </tr>
    <?php 

                    $getSlVls=$getSlVls+1;
                    // }
                }
              $counter++;  
            }
        }else{
            $res['idVls']='0';
        }
    }else{
            $res['idVls']='0';
    }
}


if ($action=='orgIdsCheck') {
    $getEdit = $_POST['getEdit'];
    $orgNameVls = $_POST['orgNameVls'];
    $orgBonusType = $_POST['orgBonusType'];
    if ($orgBonusType!='0') {
        $orgBTypeVls = getOrgBTypeVls($con, $orgBonusType);
        $res['orgBTypeVls'] = $orgBTypeVls;
    }else{
        $res['orgBTypeVls'] = '0';
    }

    $empName = $_POST['empName'];
    if ($empName!='0') {
        $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%' ORDER BY x.id DESC";
    }else{
        $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id ORDER BY x.id DESC";
    }
    
    $orgQry = mysqli_query($con, $qry);
    $orgQry_rows = mysqli_num_rows($orgQry);
    if ($orgQry_rows>0) {
            $emp_id = getBonusEmpList($con, $orgBonusType);
            $exp_emp_id = explode(',', $emp_id);
            $aryData = [];
            for ($a=0; $a < count($exp_emp_id); $a++) { 
                $aryData[] = $exp_emp_id[$a];
            }
            // print_r($aryData);
            // exit();
        $checkCnt=0;
        $res['empArray']=[];
        $res['empArrayEdit']=[];
        $empSalary = '30010';
        $empAtt = '365';
        while ($rows = mysqli_fetch_object($orgQry)) {
            $idVls = $rows->empIds;
            if ($getEdit==0) {
                if (!in_array($idVls, $aryData)) {
                    // $empSalary = $empSalary-10;
                    // $empAtt = $empAtt-50;
                    // $getEmpSalary = $empSalary/365*$empAtt;
                    
                    //*********
                    $b_id = $orgBTypeVls[0]['b_id'];
                    $b_per = $orgBTypeVls[0]['b_per'];
                    $b_on = $orgBTypeVls[0]['b_on'];
                    $b_mode_a = $orgBTypeVls[0]['b_mode'];
                    $b_mode = bModeNameFn($con, $b_mode_a);
                    $empSalary = empSalary($con, $idVls, $b_on);

                    // if ($b_mode=='monthly') {
                    //     $days = 365/12;
                    // }else if ($b_mode=='quarterly') {
                    //     $days = 365/6;
                    // }else if ($b_mode=='half yearly') {
                    //     $days = 365/4;
                    // }else if ($b_mode=='yearly') {
                    //     $days = 365;
                    // }

                    $empAtt = empAttendance($con, $idVls, $b_id);
                    $getEmpSalary = $empSalary/365*$empAtt;
                    //*********

                    $getBonus = $getEmpSalary/100*$b_per;

                    $res['empArray'][]=['idVls'=>$idVls.'|'.$empSalary.'|'.$empAtt.'|'.$getBonus];
                }
            }else{
                    // $empSalary = $empSalary-10;
                    // $empAtt = $empAtt-50;
                    // $getEmpSalary = $empSalary/365*$empAtt;

                    //*********
                    $b_id = $orgBTypeVls[0]['b_id'];
                    $b_per = $orgBTypeVls[0]['b_per'];
                    $b_on = $orgBTypeVls[0]['b_on'];
                    $b_mode_a = $orgBTypeVls[0]['b_mode'];
                    $b_mode = bModeNameFn($con, $b_mode_a);
                    $empSalary = empSalary($con, $idVls, $b_on);

                    // if ($b_mode=='monthly') {
                    //     $days = 365/12;
                    // }else if ($b_mode=='quarterly') {
                    //     $days = 365/6;
                    // }else if ($b_mode=='half yearly') {
                    //     $days = 365/4;
                    // }else if ($b_mode=='yearly') {
                    //     $days = 365;
                    // }

                    $empAtt = empAttendance($con, $idVls, $b_id);
                    $getEmpSalary = $empSalary/365*$empAtt;
                    //*********

                    $getBonus = $getEmpSalary/100*$b_per;

                    $res['empArray'][]=['idVls'=>$idVls.'|'.$empSalary.'|'.$empAtt.'|'.$getBonus];
                if (in_array($idVls, $aryData)) {
                    // $empSalary = $empSalary-10;
                    // $empAtt = $empAtt-50;
                    // $getEmpSalary = $empSalary/365*$empAtt;

                    //*********
                    $b_id = $orgBTypeVls[0]['b_id'];
                    $b_per = $orgBTypeVls[0]['b_per'];
                    $b_on = $orgBTypeVls[0]['b_on'];
                    $b_mode_a = $orgBTypeVls[0]['b_mode'];
                    $b_mode = bModeNameFn($con, $b_mode_a);
                    $empSalary = empSalary($con, $idVls, $b_on);                    

                    // if ($b_mode=='monthly') {
                    //     $days = 365/12;
                    // }else if ($b_mode=='quarterly') {
                    //     $days = 365/6;
                    // }else if ($b_mode=='half yearly') {
                    //     $days = 365/4;
                    // }else if ($b_mode=='yearly') {
                    //     $days = 365;
                    // }

                    $empAtt = empAttendance($con, $idVls, $b_id);
                    $getEmpSalary = $empSalary/365*$empAtt;
                    //*********

                    $getBonus = $getEmpSalary/100*$b_per;

                    $res['empArrayEdit'][]=['idVls'=>$idVls.'|'.$empSalary.'|'.$empAtt.'|'.$getBonus];
                }
            }
        }
    }else{
        $res['empArray'][]=['idVls'=>'0'];
    }
    echo json_encode($res);
}



function empSalary($con, $empIds, $b_on){
    $qry = "SELECT * FROM mstr_emp WHERE id = '$empIds'";
    $orgQry = mysqli_query($con, $qry);
    $rows = mysqli_fetch_object($orgQry);
    $mstr_ref_id = $rows->mstr_ref_id;
    $qry_a = "SELECT * FROM hr_employee_service_register WHERE ref_id = '$mstr_ref_id'";
    $orgQry_a = mysqli_query($con, $qry_a);
    $datas = mysqli_fetch_object($orgQry_a);

    if ($b_on=='CTC') {
        $res = $datas->total_ctc;
    }else if ($b_on=='Gross') {
        $res = $datas->gross;
    }else if ($b_on=='Net Pay') {
        $res = 0;
    }else if ($b_on=='Basic') {
        $res = 0;
    }
    return $res;
}


function empAttendance($con, $idVls, $b_id){



    $qry = "SELECT * FROM hr_bonus WHERE id='$b_id'";
    $orgQry = mysqli_query($con, $qry);
    $rows = mysqli_fetch_object($orgQry);
    // $bMode = bModeFn($con, $rows->b_mode);
    $b_mode = $rows->b_mode;
    if ($b_mode=='monthly') {
        $daysCnt = 1;
    }else if ($b_mode=='quarterly') {
        $daysCnt = 2;
    }else if ($b_mode=='half yearly') {
        $daysCnt = 5;
    }else if ($b_mode=='yearly') {
        $daysCnt = 11;
    }
    $applicable = $rows->applicable;

    $expAplc = explode('/', $applicable); //-------Demo
    $months = $expAplc[0];
    $years = $expAplc[1];

    // $expAplc = explode('-', $applicable); //-------Live
    // $years = $expAplc[0];
    // $months = $expAplc[1];


    $getLastMonth = $months+$daysCnt;
    if ($getLastMonth>12) {
        $getLastMonth = $getLastMonth-12;
        $years_a = $years+1;
    }else{
        $years_a = $years;
    }

    if (strlen($months)!=2) {
        $months = '0'.$months;
    }
    if (strlen($getLastMonth)!=2) {
        $getLastMonth = '0'.$getLastMonth;
    }

    $frmYearMonth = $years.'-'.$months.'-01';
    $toYearMonth = $years_a.'-'.$getLastMonth.'-01';

    if ($months>$getLastMonth) {
        $qryvlss = "fmonth>='$getLastMonth' AND fmonth<='$months'";
    }else{
        $qryvlss = "fmonth>='$months' AND fmonth<='$getLastMonth'";
    }



    $qry = "SELECT DISTINCT y.dated FROM hr_leave_app x, hr_chk_leave y WHERE x.name='$idVls' AND ((x.from_date BETWEEN '$frmYearMonth' AND '$toYearMonth') OR (x.to_date BETWEEN '$frmYearMonth' AND '$toYearMonth')) AND x.unique_id=y.unique_id AND y.dated BETWEEN '$frmYearMonth' AND '$toYearMonth' AND (y.fullday='fullday' OR y.fullday='1' ) AND x.first_approve_status='1' AND y.cancel_status='0'";
    $orgQry = mysqli_query($con, $qry);
    $rows = mysqli_num_rows($orgQry);


    $halfqry = "SELECT DISTINCT y.dated FROM hr_leave_app x, hr_chk_leave y WHERE x.name='$idVls' AND x.from_date BETWEEN '$frmYearMonth' AND '$toYearMonth' AND x.unique_id=y.unique_id AND y.dated BETWEEN '$frmYearMonth' AND '$toYearMonth' AND ((y.first_half='1st_half' OR y.first_half='0.5') OR (y.second_half='2nd_half' OR y.second_half='0.5')) AND x.first_approve_status='1' AND (x.cncl_status='0' OR x.cncl_status='2')";
    $halfOrgQry = mysqli_query($con, $halfqry);
    $halfRows = mysqli_num_rows($halfOrgQry);

    $get_total = $rows+$halfRows;


    // return $halfqry;
    // exit();






    $attQry = "SELECT * FROM hr_attendance_appr_details WHERE $qryvlss AND fyear>='$years' AND fyear<='$years_a' AND hr_actn='1' AND f_actn='1' AND m_actn='1' ORDER BY id DESC LIMIT 1";
    $orgQry = mysqli_query($con, $attQry);
    $rows = mysqli_fetch_object($orgQry);
    $hamd_req_id = $rows->hamd_req_id;


    $attQry = "SELECT * FROM hr_attendance_monthly_details WHERE unique_id='$hamd_req_id' AND mstr_id='564'";
    $orgQry = mysqli_query($con, $attQry);
    $rows = mysqli_fetch_object($orgQry);
    $pd = $rows->pd;

   

    $attQry = "SELECT * FROM hr_leave_app WHERE name='564' AND from_date>='$frmYearMonth' AND to_date='$toYearMonth'";
    $orgQry = mysqli_query($con, $attQry);
    $full = 0;
    $half = 0;
    while ($rows_b = mysqli_fetch_object($orgQry)) {
        $fullday = $rows_b['fullday'];
        $halfday = $rows_b['halfday'];

        if ($fullday=='1') {
            $full = $full+1;
        }
        if ($halfday=='1') {
            $half = $half+1;
        }
    }
    $getHalf = $half/2;
    $getTotalLeave_a = $getHalf+$full;
    $getTotalLeave = $pd-$getTotalLeave_a;
 // return $attQry;
 //    exit();
    return $getTotalLeave;

}





function getBonusEmpList($con, $orgBonusType){
    $qry = "SELECT * FROM hr_bonus WHERE b_type = '$orgBonusType' AND b_status = 1 ORDER BY id DESC LIMIT 1";
    $orgQry = mysqli_query($con, $qry);
    $orgQry_rows = mysqli_num_rows($orgQry);
    $res = '';
    if ($orgQry_rows>0) {
        $rows = mysqli_fetch_object($orgQry);
        $ids = $rows->id;
        $qry_a = "SELECT * FROM hr_bonus_emp_list WHERE ebr_id IN (SELECT id FROM hr_bonus_request WHERE b_id = '$ids') ORDER BY id DESC";
        $orgQry_a = mysqli_query($con, $qry_a);
        $orgQry_rows_a = mysqli_num_rows($orgQry_a);
        $i=1;
        while ($rows_a = mysqli_fetch_object($orgQry_a)) {
            if ($orgQry_rows_a==$i) {
                $res .= $rows_a->emp_id;
            }else{
                $res .= $rows_a->emp_id.',';
            }
            $i++;
        }
    }else{
        $res = '0';
    }
    return $res;
}




if ($action=='orgBTypeCheck') {
    $orgBType = $_POST['orgBType'];
    $res = getOrgBTypeVls($con, $orgBType);
    echo json_encode($res);
}

function getOrgBTypeVls($con, $orgBType){
    $qry = "SELECT * FROM hr_bonus WHERE b_type = '$orgBType' AND b_status = 1 ORDER BY id DESC LIMIT 1";
    $orgQry = mysqli_query($con, $qry);
    $orgQry_rows = mysqli_num_rows($orgQry);
    if ($orgQry_rows>0) {
        $res=[];
        while ($rows = mysqli_fetch_object($orgQry)) {
            $bMode = bModeFn($con, $rows->b_mode);
            $res[]=['b_id'=>$rows->id,'b_mode'=>$bMode,'b_on'=>$rows->b_on,'based_on'=>$rows->based_on,'b_per'=>$rows->b_per];
        }
    }else{
        $res='0';
    }
    return $res;
};

function bModeFn($con, $b_mode){
    $modeQry = mysqli_query($con, "SELECT * FROM master_type_dtls WHERE mstr_type_value = '$b_mode'");
    $rows = mysqli_fetch_object($modeQry);
    return $rows->mstr_type_name;
}
function bModeNameFn($con, $b_mode){
    $modeQry = mysqli_query($con, "SELECT * FROM master_type_dtls WHERE mstr_type_name = '$b_mode'");
    $rows = mysqli_fetch_object($modeQry);
    return $rows->mstr_type_value;
}



if ($action=='empBonusRequestSubmit') {

    $orgName = $_POST['orgName'];
    $orgBonusType = $_POST['orgBonusType'];
    $orgBonusMode = $_POST['orgBonusMode'];
    $orgBonusOn = $_POST['orgBonusOn'];
    $orgBasedOn = $_POST['orgBasedOn'];
    $orgBonusPer = $_POST['orgBonusPer'];
    $bonusMessages = $_POST['bonusMessages'];

    $empDtls = $_POST['empDtls'];
    $bMode = bModeNameFn($con, $orgBonusMode);

    $orgBTypeVls = getOrgBTypeVls($con, $orgBonusType);
    $b_id = $orgBTypeVls[0]['b_id'];


    $sltQry = "SELECT * FROM hr_bonus_request WHERE org_id='$orgName' AND b_type='$orgBonusType' AND b_mode='$orgBonusMode' ORDER BY id DESC LIMIT 1";
    $sltRow = mysqli_query($con, $sltQry);
    $rows = mysqli_fetch_object($sltRow);
    $ids = $rows->id;
    $sltQry_a = "SELECT * FROM hr_bonus_emp_list WHERE ebr_id='$ids'";
    $sltRow_a = mysqli_query($con, $sltQry_a);
    $num_rows = mysqli_num_rows($sltRow_a);
    if ($num_rows>0) {
        $insertId = $ids;
        $res = 1;
    }else{
        $qry = "INSERT INTO `hr_bonus_request`(`org_id`, `b_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `created_by`) VALUES ('$orgName','$b_id','$orgBonusType','$bMode','$orgBonusOn','$orgBasedOn','$orgBonusPer','$bonusMessages','$sessionid')";
        $sqlqry = mysqli_query($con, $qry);
        if ($sqlqry) {
            $insertId = mysqli_insert_id($con);
            $res = 1;
        }else{
            $res = 0;
        }
    }



        $orgSltQry = "SELECT * FROM `hr_bonus_request` WHERE org_id='$orgName' AND b_type='$orgBonusType'";
        $qrys = mysqli_query($con, $orgSltQry);
        $skNum = mysqli_num_rows($qrys);
        $rowsSltQry = mysqli_fetch_object($qrys);
        $ebrIds = $rowsSltQry->id;
        $orgSltQry_a = "SELECT * FROM `hr_bonus_emp_list` WHERE ebr_id='$ebrIds' ORDER BY id DESC LIMIT 1";
        $qrys_a = mysqli_query($con, $orgSltQry_a);
        $rowsSltQry_a = mysqli_num_rows($qrys_a);
        if ($rowsSltQry_a>0) {
            $rowsVls = mysqli_fetch_object($qrys_a);
            $empNum = $rowsVls->empNum;
            $slVls = $empNum+1;
        }else{
            $slVls = '1';
        }



    // $qry = "INSERT INTO `hr_bonus_request`(`org_id`, `b_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `created_by`) VALUES ('$orgName','$b_id','$orgBonusType','$bMode','$orgBonusOn','$orgBasedOn','$orgBonusPer','$bonusMessages','$sessionid')";
    // $sqlqry = mysqli_query($con, $qry);
    // if ($sqlqry) {
    //     $insertId = mysqli_insert_id($con);
        $expEmpDtls = explode(',', $empDtls);
        $numVls = $slVls;
        for ($i=0; $i < count($expEmpDtls); $i++) {
            $expEmpDtlsAll = explode('|', $expEmpDtls[$i]);
            $empId = $expEmpDtlsAll[0]; //empId
            $setBonusOn = $expEmpDtlsAll[1]; //setBonusOn
            $setBonusSalary = $expEmpDtlsAll[2]; //setBonusSalary
            $setBonusPer = $expEmpDtlsAll[3]; //setBonusPer
            $setBonusDays = $expEmpDtlsAll[4]; //setBonusDays
            $setBonusAmounts = $expEmpDtlsAll[5]; //setBonusAmounts
            $empSlIdsVls = $expEmpDtlsAll[6]; //setBonusAmounts
            if ($empId!='' && $empId!='0') {
                $qry = "INSERT INTO `hr_bonus_emp_list`(`ebr_id`, `emp_id`, `emp_sl_id`, `empNum`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$insertId','$empId','$empSlIdsVls','$numVls','$setBonusOn','$setBonusSalary','$setBonusPer','$setBonusDays','$setBonusAmounts')";
                mysqli_query($con, $qry);
            }
            $numVls = $numVls+1;
        }
    //     $res = 1;
    // }else{
    //     $res = 0;
    // }
    echo $res;
}



if ($action=='empBonusRequestUpdate') {
    $hiddenIds = $_POST['hiddenIds'];
    $orgName = $_POST['orgName'];
    $orgBonusType = $_POST['orgBonusType'];
    $orgBonusMode = $_POST['orgBonusMode'];
    $orgBonusOn = $_POST['orgBonusOn'];
    $orgBasedOn = $_POST['orgBasedOn'];
    $orgBonusPer = $_POST['orgBonusPer'];
    $bonusMessages = $_POST['bonusMessages'];

    $empDtls = $_POST['empDtls'];
    $bMode = bModeNameFn($con, $orgBonusMode);

    $orgBTypeVls = getOrgBTypeVls($con, $orgBonusType);
    $b_id = $orgBTypeVls[0]['b_id'];


    $qry = "UPDATE `hr_bonus_request` SET `org_id`='$orgName', `b_id`='$b_id', `b_type`='$orgBonusType', `b_mode`='$bMode', `b_on`='$orgBonusOn', `based_on`='$orgBasedOn', `b_per`='$orgBonusPer', `b_msg`='$bonusMessages', `created_by`='$sessionid' WHERE id='$hiddenIds'";
    $sqlqry = mysqli_query($con, $qry);
    if ($sqlqry) {
        $res = 1;
    }else{
        $res = 0;
    }

        // $orgSltQry = "SELECT * FROM `hr_bonus_request` WHERE org_id='$orgName' AND b_type='$orgBonusType'";
        // $qrys = mysqli_query($con, $orgSltQry);
        // $skNum = mysqli_num_rows($qrys);
        // $rowsSltQry = mysqli_fetch_object($qrys);
        // $ebrIds = $rowsSltQry->id;
        // $orgSltQry_a = "SELECT * FROM `hr_bonus_emp_list` WHERE ebr_id='$ebrIds' ORDER BY id DESC LIMIT 1";
        // $qrys_a = mysqli_query($con, $orgSltQry_a);
        // $rowsSltQry_a = mysqli_num_rows($qrys_a);
        // if ($rowsSltQry_a>0) {
        //     $rowsVls = mysqli_fetch_object($qrys_a);
        //     $empNum = $rowsVls->empNum;
        //     $slVls = $empNum+1;
        // }else{
        //     $slVls = '1';
        // }


        $DLTsltQry = "DELETE FROM hr_bonus_emp_list WHERE ebr_id='$hiddenIds'";
        $DLTRow = mysqli_query($con, $DLTsltQry);

        $expEmpDtls = explode(',', $empDtls);
        for ($i=0; $i < count($expEmpDtls); $i++) {
            $num = $i+1;
            $expEmpDtlsAll = explode('|', $expEmpDtls[$i]);
            $empId = $expEmpDtlsAll[0]; //empId
            $setBonusOn = $expEmpDtlsAll[1]; //setBonusOn
            $setBonusSalary = $expEmpDtlsAll[2]; //setBonusSalary
            $setBonusPer = $expEmpDtlsAll[3]; //setBonusPer
            $setBonusDays = $expEmpDtlsAll[4]; //setBonusDays
            $setBonusAmounts = $expEmpDtlsAll[5]; //setBonusAmounts
            $empSlIdsVls = $expEmpDtlsAll[6]; //empSlIdsVls
            if ($empId!='' && $empId!='0') {

                // $chksltQry = "SELECT * FROM hr_bonus_emp_list WHERE ebr_id='$hiddenIds' AND emp_id='$empId'";
                // $chksltRow = mysqli_query($con, $chksltQry);
                // $numRws = mysqli_num_rows($chksltRow);
                // if ($numRws==0) {
                //     $qry = "INSERT INTO `hr_bonus_emp_list`(`ebr_id`, `emp_id`, `emp_sl_id`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$hiddenIds','$empId','$empSlIdsVls','$setBonusOn','$setBonusSalary','$setBonusPer','$setBonusDays','$setBonusAmounts')";
                // }

                $qry = "INSERT INTO `hr_bonus_emp_list`(`ebr_id`, `emp_id`, `emp_sl_id`, `empNum`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$hiddenIds','$empId','$empSlIdsVls','$num','$setBonusOn','$setBonusSalary','$setBonusPer','$setBonusDays','$setBonusAmounts')";
                
                mysqli_query($con, $qry);
            }
        }
    echo $res;
}


?>