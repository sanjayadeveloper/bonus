<?php
    require_once('../../../config.php');
    require_once('../../../auth.php');

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    $ERP_SESS_ID = $_SESSION['ERP_SESS_ID'];
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
    if ($orgNameVls!='' || $orgNameVls!=null) {
        // $qry = "SELECT x.id as empID, y.fullname as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' UNION (SELECT x.id as empID, x.emp_name as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.department_id='2')";
        $qry = "SELECT x.id as empIds, x.*, y.*, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id ORDER BY x.id DESC";
        // echo $qry;
        // exit();
        $orgQry = mysqli_query($con, $qry);
        if (mysqli_num_rows($orgQry)>0) {
            $i=0;
            while ($rows = mysqli_fetch_object($orgQry)) {
                $i++;
                // $empSalary = empSalary($con, $empIds);
                $empSalary = 5000;
    ?>
            <tr>
               <td><input type="checkbox" class="bonusCheck" name="bonusCheckVls[]" id="" value="1" onclick="allCheckFn(<?=$i?>,'bonus')"> &#x00A0; <span style="margin-top: 2px; position: absolute;"><?=$i?></span></td>
               <td><?=$rows->fullname;?></td>
               <td><?=$rows->designation;?></td>
               <td><?=$rows->dept_name;?></td>
               <td><?=$rows->lname;?></td>
               <td id="setBonusOn_<?=$i;?>"></td>
               <td id="setBonusSalary_<?=$i;?>"><?=$empSalary;?></td>
               <td id="setBonusPer_<?=$i;?>"></td>
               <td id="setBonusDays_<?=$i;?>"></td>
               <td id="setBonusAmounts_<?=$i;?>"></td>
            </tr>
    <?php
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
    $qry = "SELECT x.id as empIds, x.*, y.*, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id ORDER BY x.id DESC";
    $orgQry = mysqli_query($con, $qry);
    // $res['idVls']='';
    // $res['orgBTypeVls']='';
    $orgQry_rows = mysqli_num_rows($orgQry);
    if ($orgQry_rows>0) {
        $checkCnt=0;
        // $idVls = '';
        // $empSlry = '';
        $res['empArray']=[];
        while ($rows = mysqli_fetch_object($orgQry)) {
            $idVls = $rows->empIds;
            $empSlry = '4010';
            $res['empArray'][]=['idVls'=>$idVls.'|'.$empSlry];
            // $checkCnt++;
            // if ($checkCnt==$orgQry_rows) {
            //     $idVls = $rows->empIds;
            //     $empSlry = '4000';
            //     $empArray[]=['idVls'=>$idVls,'empSlry'=>$empSlry];
            // }else{
            //     $idVls.=$rows->empIds.', ';
            //     $empSlry .= '3000, ';
            // }
        }
        // $res['idVls']=$idVls;
        // $res['empSlry']=$empSlry;
    }else{
        // $res['idVls']='0';
        // $res['empSlry']='0';
        $res['empArray'][]=['idVls'=>'0'];
    }

    if ($orgBonusType!='0') {
        $res['orgBTypeVls'] = getOrgBTypeVls($con, $orgBonusType);
    }else{
        $res['orgBTypeVls'] = '0';
    }
    echo json_encode($res);
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
            $res[]=['b_mode'=>$bMode,'b_on'=>$rows->b_on,'based_on'=>$rows->based_on,'b_per'=>$rows->b_per];
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

?>