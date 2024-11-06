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




//*******************************Calculate Page
if ($action=='orgNameCheck') {
    $orgNameVls = $_POST['orgNameVls'];
    $orgBonusType = $_POST['orgBonusType'];
    $empName = $_POST['empName'];
    if ($orgNameVls!='' || $orgNameVls!=null) {
        // $qry = "SELECT x.id as empID, y.fullname as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' UNION (SELECT x.id as empID, x.emp_name as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.department_id='2')";
        if ($empName!='0') {
            $qry = "SELECT x.id as empIds, x.*, y.*, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%' ORDER BY x.id DESC";
        }else{
            $qry = "SELECT x.id as empIds, x.*, y.*, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id ORDER BY x.id DESC";
        }
        
        // echo $qry;
        // exit();
        $orgQry = mysqli_query($con, $qry);
        if (mysqli_num_rows($orgQry)>0) {
            $i=0;
            $counter=0;
            $emp_id = getBonusEmpList($con, $orgBonusType);
            $exp_emp_id = explode(',', $emp_id);
            while ($rows = mysqli_fetch_object($orgQry)) {
                $i++;
                $idVls = $rows->empIds;
                // if ($exp_emp_id[$counter]!=$idVls) {                
    ?>
            <tr>
               <td><input type="checkbox" class="bonusCheck" name="bonusCheckVls[]" id="empId_<?=$i?>" value="<?=$idVls?>" onclick="allCheckFn(<?=$i?>,'bonus')"> &#x00A0; <span style="margin-top: 2px; position: absolute;"><?=$i?></span></td>
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
                // }
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
        $qry = "SELECT x.id as empIds, x.*, y.*, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%' ORDER BY x.id DESC";
    }else{
        $qry = "SELECT x.id as empIds, x.*, y.*, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id ORDER BY x.id DESC";
    }
    
    $orgQry = mysqli_query($con, $qry);
    $orgQry_rows = mysqli_num_rows($orgQry);
    if ($orgQry_rows>0) {
            $counter=0;
            $emp_id = getBonusEmpList($con, $orgBonusType);
            $exp_emp_id = explode(',', $emp_id);
        $checkCnt=0;
        $res['empArray']=[];
        $empSlry = '30010';
        $empDays = '365';
        while ($rows = mysqli_fetch_object($orgQry)) {
            $idVls = $rows->empIds;
                // if ($exp_emp_id[$counter]!=$idVls) {
                $empSlry = $empSlry-10;
                $empDays = $empDays-50;
                
                $b_per = $orgBTypeVls[0]['b_per'];
                // $empSalary = empSalary($con, $empIds);
                // $empAtt = empAttendance($con, $empIds);

                $getBonus = $empSlry/100*$b_per;

                $res['empArray'][]=['idVls'=>$idVls.'|'.$empSlry.'|'.$empDays.'|'.$getBonus];
            // }
        }
    }else{
        $res['empArray'][]=['idVls'=>'0'];
    }

    
    echo $res;
}



function getBonusEmpList($con, $orgBonusType){ //****Continue
    $qry = "SELECT * FROM hr_bonus WHERE b_type = '$orgBonusType' AND b_status = 1 ORDER BY id DESC LIMIT 1";
    $orgQry = mysqli_query($con, $qry);
    $orgQry_rows = mysqli_num_rows($orgQry);
    $res = '';
    if ($orgQry_rows>0) {
        $rows = mysqli_fetch_object($orgQry);
        $ids = $rows->id;
        $qry_a = "SELECT * FROM hr_bonus_emp_list WHERE ebr_id IN (SELECT id FROM hr_bonus_request WHERE b_id = '$ids')";
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

    $qry = "INSERT INTO `hr_bonus_request`(`org_id`, `b_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `created_by`) VALUES ('$orgName','$b_id','$orgBonusType','$bMode','$orgBonusOn','$orgBasedOn','$orgBonusPer','$bonusMessages','$sessionid')";
    $sqlqry = mysqli_query($con, $qry);
    if ($sqlqry) {
        $insertId = mysqli_insert_id($con);
        $expEmpDtls = explode(',', $empDtls);
        for ($i=0; $i < count($expEmpDtls); $i++) {
            $expEmpDtlsAll = explode('|', $expEmpDtls[$i]);
            $empId = $expEmpDtlsAll[0]; //empId
            $setBonusOn = $expEmpDtlsAll[1]; //setBonusOn
            $setBonusSalary = $expEmpDtlsAll[2]; //setBonusSalary
            $setBonusPer = $expEmpDtlsAll[3]; //setBonusPer
            $setBonusDays = $expEmpDtlsAll[4]; //setBonusDays
            $setBonusAmounts = $expEmpDtlsAll[5]; //setBonusAmounts
            if ($empId!='' && $empId!='0') {
                $qry = "INSERT INTO `hr_bonus_emp_list`(`ebr_id`, `emp_id`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$insertId','$empId','$setBonusOn','$setBonusSalary','$setBonusPer','$setBonusDays','$setBonusAmounts')";
                mysqli_query($con, $qry);
            }
        }
        $res = 1;
    }else{
        $res = 0;
    }
    echo $res;
}





?>