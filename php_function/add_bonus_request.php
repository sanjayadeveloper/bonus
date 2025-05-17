<?php
    require_once('../../../auth.php');
    require_once('../../../config.php');
    include_once ('../../../workflownotif.php');
    include_once('../../../approvalmatrixfunction.php');

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


// if ($action=='modeCheck') {
//     $ModeVls = $_POST['ModeVls'];

//     //********************Approved Start
//     $aprovQry = mysqli_query($con,"SELECT * FROM `hr_bonus` WHERE created_by='$ERP_SESS_ID' AND b_status=1 ORDER BY id DESC LIMIT 1");
//     $aprovQry_results = mysqli_num_rows($aprovQry);

//     if($aprovQry_results == 0){
//         $res['dt'] = '00-0000';
//     }else{
//         $aprov_rows=mysqli_fetch_object($aprovQry);
//         $aprov_mode = $aprov_rows->b_mode;
//         if ($aprov_mode!=$ModeVls) {
//             switch ($ModeVls) {
//                 case 'monthly':
//                     if ($crntM==12) {
//                         $sltMnth = '01';
//                     }else{
//                         $sltMnth = '0'.$crntM+1;
//                     }
//                     break;
                
//                 case 'quarterly':
//                     if ($crntM >= 1 && $crntM <= 3) {
//                         $sltMnth = '04';
//                     }elseif ($crntM >= 4 && $crntM <= 6) {
//                         $sltMnth = '07';
//                     }elseif ($crntM >= 7 && $crntM <= 9) {
//                         $sltMnth = 10;
//                     }elseif ($crntM >= 10 && $crntM <= 12) {
//                         $sltMnth = 1;
//                     }
//                     break;
                
//                 case 'half yearly':
//                     if ($crntM >= 1 && $crntM <= 6) {
//                         $sltMnth = '07';
//                     }elseif ($crntM >= 7 && $crntM <= 12) {
//                         $sltMnth = 1;
//                     }
//                     break;
                
//                 default:
//                     $sltMnth = 1;
//                     break;
//             }
//         }else{
//             if ($crntM<10) {
//                 $crntM = $crntM+1;
//             }else{
//                 $crntM = '0'.$crntM+1;
//             }
//             $sltMnth = '';
//         }
        

//         if ($sltMnth=='') {
//             // $res['dt'] = date($crntM.'/Y');
//         }elseif ($sltMnth==1) {
//             $aprovCrntMY = date('Y', strtotime('+1 year'));
//             // $res['dt'] = '01/'.$aprovCrntMY;
//         }else{
//             // $res['dt'] = date($sltMnth.'/Y');
//         }
//     }
//     //********************Approved End

//     $aprovQry_a = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE mstr_type_value='$ModeVls'");
//     $aprovQry_a_results = mysqli_num_rows($aprovQry_a);

//     if($aprovQry_a_results == 0){
//         $Error_message="NO RECORDS FOUND.";
//     }else{
//         $aprov_rows=mysqli_fetch_object($aprovQry_a);
//         $res['type_name'] = $aprov_rows->mstr_type_name;
//     }

//     echo json_encode($res);
// }


// if ($action=='bonusRequestSubmit') {
//     $bonusType = $_POST['bonusType'];
//     $bonusOn = $_POST['bonusOn'];
//     $bonusMode = $_POST['bonusMode'];
//     $applicableForm = $_POST['applicableForm'];
//     $bonusPer = $_POST['bonusPer'];
//     $basedOn = $_POST['basedOn'];
//     $bonusReMark = $_POST['bonusReMark'];
    
//     if (isset($_POST['update'])) {
//         $bonus_id = $_POST['bonus_id'];
//         $qry = "UPDATE `hr_bonus` SET `b_date`='$crntDate', `b_type`='$bonusType', `b_on`='$bonusOn', `b_mode`='$bonusMode', `applicable`='$applicableForm', `b_per`='$bonusPer', `based_on`='$basedOn', `b_status`='0', `stage_no`='0', `created_by`='$ERP_SESS_ID'";
//     }else{
//         $qry = "INSERT INTO `hr_bonus`(`b_date`, `b_type`, `b_on`, `b_mode`, `applicable`, `b_per`, `based_on`, `stage_no`, `created_by`) VALUES ('$crntDate','$bonusType','$bonusOn','$bonusMode','$applicableForm','$bonusPer','$basedOn','0','$ERP_SESS_ID')";
//     }

//     // $qry = "INSERT INTO `hr_bonus`(`b_date`, `b_type`, `b_on`, `b_mode`, `applicable`, `b_per`, `based_on`, `stage_no`, `created_by`, `remarks`) VALUES ('$crntDate','$bonusType','$bonusOn','$bonusMode','$applicableForm','$bonusPer','$basedOn','0','$ERP_SESS_ID','$bonusReMark')";

//     $sqlqry = mysqli_query($con, $qry);
//     if ($sqlqry) {
//         $res = 1;
//     }else{
//         $res = 0;
//     }
//     echo $res;
// }




//******************************* ( Calculate Page ) ******************************************************





if ($action=='orgNameCheck') {
    $getEdit = $_POST['getEdit'];
    $orgNameVls = $_POST['orgNameVls'];
    $orgBonusType = $_POST['orgBonusType'];
    $empName = $_POST['empName'];
    if ($orgNameVls!='' || $orgNameVls!=null) {

        //**********************
        // org_nm
        // $qry = "SELECT x.id as empID, y.fullname as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' UNION (SELECT x.id as empID, x.emp_name as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.department_id='2')";

        // $qry = "SELECT x.id as empID, y.fullname as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' UNION (SELECT x.id as empID, x.emp_name as name FROM hr_employee_service_register x, mstr_emp y WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.department_id='2')";
        //**********************

        if ($empName!='0') {
            // $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%' UNION (SELECT x.id as empID, x.emp_name as name, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%') ORDER BY x.id DESC";

            // $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%' ORDER BY x.id DESC";

            $qry = "SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.org_nm='$orgNameVls' AND y.fullname LIKE '%$empName%' AND x.department_id=a.id AND x.location_id=b.id AND (y.id NOT IN (SELECT emp FROM `fin_fnf`) OR y.id = (SELECT MAX(id) FROM `fin_fnf` WHERE closing_status!='5')) UNION (SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.org_nm='$orgNameVls' AND y.fullname LIKE '%$empName%' AND x.department_id=a.id AND x.location_id=b.id AND (y.id NOT IN (SELECT emp FROM `fin_fnf`) OR y.id = (SELECT MAX(id) FROM `fin_fnf` WHERE closing_status!='5')))";

        }else{
            // $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id UNION (SELECT x.id as empID, x.emp_name as name, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id) ORDER BY x.id DESC";

            // $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id ORDER BY x.id DESC";

            $qry = "SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND (y.id NOT IN (SELECT emp FROM `fin_fnf`) OR y.id = (SELECT MAX(id) FROM `fin_fnf` WHERE closing_status!='5')) UNION (SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND (y.id NOT IN (SELECT emp FROM `fin_fnf`) OR y.id = (SELECT MAX(id) FROM `fin_fnf` WHERE closing_status!='5')))";
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
                // $idVls = $rows->empIds;
                $idVls = $rows->empID;
                if ($getEdit==0) {
                    if (!in_array($idVls, $aryData)) {
                    $i++;
                        $new_str = str_pad($idVls,'5',"0", STR_PAD_LEFT);
                        $empSlIdsVls = 'emp_'.$new_str;
    ?>
            <tr>
               <td><input type="checkbox" class="bonusCheck" name="bonusCheckVls[]" id="listId_<?=$i?>" value="<?=$idVls?>" onclick="allCheckFn(<?=$i?>,'bonus'), saveIdsVls(<?=$i?>, <?=$idVls?>,'bonus')"> &#x00A0; <span style="margin-top: 2px; position: absolute;"><?=$i?></span></td>
               <!-- <td id="empSlIdsVls_<?=$i;?>"><?=$empSlIdsVls;?></td> -->
               <td><?=$rows->name;?></td>
               <td><?=$rows->desig;?></td>
               <td><?=$rows->deptname;?></td>
               <td><?=$rows->locname;?></td>
               <td id="setBonusOn_<?=$i;?>"></td>
               <td id="setBonusSalary_<?=$i;?>"></td>
               <td id="setBonusPer_<?=$i;?>"></td>
               <td id="setBonusDays1_<?=$i;?>"></td>
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
               <!-- <td id="empSlIdsVls_<?=$i;?>"><?=$empSlIdsVls;?></td> -->
               <td><?=$rows->name;?></td>
               <td><?=$rows->desig;?></td>
               <td><?=$rows->deptname;?></td>
               <td><?=$rows->locname;?></td>
               <td id="setBonusOn_<?=$i;?>"></td>
               <td id="setBonusSalary_<?=$i;?>"></td>
               <td id="setBonusPer_<?=$i;?>"></td>
               <td id="setBonusDays1_<?=$i;?>"></td>
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
        // $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%' UNION (SELECT x.id as empID, x.emp_name as name, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%') ORDER BY x.id DESC";

        // $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND y.fullname LIKE '%$empName%' ORDER BY x.id DESC";

        $qry = "SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.org_nm='$orgNameVls' AND y.fullname LIKE '%$empName%' AND x.department_id=a.id AND x.location_id=b.id AND (y.id NOT IN (SELECT emp FROM `fin_fnf`) OR y.id = (SELECT MAX(id) FROM `fin_fnf` WHERE closing_status!='5')) UNION (SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.org_nm='$orgNameVls' AND y.fullname LIKE '%$empName%' AND x.department_id=a.id AND x.location_id=b.id AND (y.id NOT IN (SELECT emp FROM `fin_fnf`) OR y.id = (SELECT MAX(id) FROM `fin_fnf` WHERE closing_status!='5')))";
    }else{
        // $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id UNION (SELECT x.id as empID, x.emp_name as name, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id) ORDER BY x.id DESC";

        // $qry = "SELECT x.*, y.*, y.id as empIds, a.*, b.* FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE x.ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.department_id='2' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id ORDER BY x.id DESC";

        $qry = "SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND (y.id NOT IN (SELECT emp FROM `fin_fnf`) OR y.id = (SELECT MAX(id) FROM `fin_fnf` WHERE closing_status!='5')) UNION (SELECT y.id as empID, y.fullname as name, y.designation as desig, a.dept_name as deptname, b.lname as locname FROM hr_employee_service_register x, mstr_emp y, hr_department a, hr_location b WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND x.org_nm='$orgNameVls' AND x.department_id=a.id AND x.location_id=b.id AND (y.id NOT IN (SELECT emp FROM `fin_fnf`) OR y.id = (SELECT MAX(id) FROM `fin_fnf` WHERE closing_status!='5')))";
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
        // $empSalary = '30010';
        // $empAtt = '365';
        while ($rows = mysqli_fetch_object($orgQry)) {
            // $idVls = $rows->empIds;
            $idVls = $rows->empID;

            //*******New Added On 08-11-2023 Start
            $b_id = $orgBTypeVls[0]['b_id'];
            $qrybns = "SELECT * FROM hr_bonus WHERE id='$b_id'";
            $bnsorgQry = mysqli_query($con, $qrybns);
            $bnsrows = mysqli_fetch_object($bnsorgQry);

            $applicable = $bnsrows->applicable;


            $expAplc = explode('-', $applicable);
            $years = $expAplc[0];
            $months = $expAplc[1];


            $b_mode_a = $orgBTypeVls[0]['b_mode'];
            $b_mode = bModeNameFn($con, $b_mode_a);
            if ($b_mode=='monthly') {
                $daysCnt = 0;
            }else if ($b_mode=='quarterly') {
                $daysCnt = 2;
            }else if ($b_mode=='half yearly') {
                $daysCnt = 5;
            }else if ($b_mode=='yearly') {
                $daysCnt = 11;
            }

            // counting days --------------------------------------------------------------- continue
            // $getLastMonth = $months+$daysCnt;
            // if ($getLastMonth>12) {
            //     $getLastMonth = $getLastMonth-12;
            //     $years_a = $years+1;
            // }else{
            //     $years_a = $years;
            // }

            // if ($years<$years_a) {
            //     if ($months>$getLastMonth) {

            //     }else{
                
            //     }
            // }else{

            // }

            // _____________________________________________________

            $lastMnths = $months+$daysCnt;
            $thisMonth = date('m');

            $days = 0;
            // if ($thisMonth<=$months && $months<=$lastMnths) {
                for ($i=$months; $i <= $lastMnths; $i++) {
                    $years_a = $expAplc[0];
                    if ($i>12) {
                        $dy = $i-12;
                        $years_a = $years_a+1;
                    }else{
                        $dy = $i;
                    }
                    for($d=1; $d<=31; $d++)
                    {
                        // $time=mktime(12, 0, 0, date('m'), $d, date('Y'));
                        // if (date('m', $time)==date('m')){
                        //     $days = $days+1;
                        // }

                        $time=mktime(12, 0, 0, $dy, $d, $years_a);
                        if (date('m', $time)==$dy){
                            $days = $days+1;
                        }
                    }
                }
            // }
            // counting days ---------------------------------------------------------------

            
            //*******New Added On 08-11-2023 End

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
                    $empSalary_ab = '';
                    if ($b_mode=='monthly') {
                        $empSalary_ab = $empSalary/12;
                    }else if ($b_mode=='quarterly') {
                        $empSalary_ab = $empSalary/4;
                    }else if ($b_mode=='half yearly') {
                        $empSalary_ab = $empSalary/2;
                    }else{
                        $empSalary_ab = $empSalary;
                    }


                    $empAtt = empAttendance($con, $idVls, $b_id);
                    $getEmpSalary = $empSalary_ab/$days*$empAtt;
                    //*********

                    $getBonus = $getEmpSalary/100*$b_per;
                    // $getBonus = number_format($getBonus, 2);
                    $getBonus = round($getBonus, 2);

                    // $res['empArray'][]=['idVls'=>$idVls.'|'.$empSalary.'|'.$empAtt.'|'.$getBonus];
                    //-------New Added
                    $bQuery = mysqli_query($con, "SELECT * FROM hr_bonus_request WHERE b_id='$b_id' AND b_per='$b_per' AND b_on='$b_on' AND b_mode='$b_mode'");
                    $bQueryRow = mysqli_fetch_object($bQuery);
                    $bIds = $bQueryRow->id;
                    $bQuery_a = mysqli_query($con, "SELECT * FROM hr_bonus_emp_list WHERE ebr_id='$bIds' AND emp_id='$idVls'");
                    $bQueryRowss = mysqli_num_rows($bQuery_a);
                    $bQueryRow_a = mysqli_fetch_object($bQuery_a);
                    $bns_days = $bQueryRow_a->bns_days;
                    $bns_amt = $bQueryRow_a->bns_amt;
                    //-------New Added
                    if($bQueryRowss > '0'){
                        $res['empArray'][]=['idVls'=>$idVls.'|'.$empSalary.'|'.$bns_days.'|'.$bns_amt];
                    }else{
                        $res['empArray'][]=['idVls'=>$idVls.'|'.$empSalary.'|'.$empAtt.'|'.$getBonus];
                    }
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
                    $empSalary_ab = '';
                    if ($b_mode=='monthly') {
                        $empSalary_ab = $empSalary/12;
                    }else if ($b_mode=='quarterly') {
                        $empSalary_ab = $empSalary/4;
                    }else if ($b_mode=='half yearly') {
                        $empSalary_ab = $empSalary/2;
                    }else{
                        $empSalary_ab = $empSalary;
                    }


                    $empAtt = empAttendance($con, $idVls, $b_id);
                    $getEmpSalary = $empSalary_ab/$days*$empAtt;
                    //*********

                    $getBonus = $getEmpSalary/100*$b_per;
                    // $getBonus = number_format($getBonus, 2);
                    $getBonus = round($getBonus, 2);

                    // $res['empArray'][]=['idVls'=>$idVls.'|'.$empSalary.'|'.$empAtt.'|'.$getBonus];
                    //-------New Added
                    $bQuery = mysqli_query($con, "SELECT * FROM hr_bonus_request WHERE b_id='$b_id' AND b_per='$b_per' AND b_on='$b_on' AND b_mode='$b_mode'");
                    $bQueryRow = mysqli_fetch_object($bQuery);
                    $bIds = $bQueryRow->id;
                    $bQuery_a = mysqli_query($con, "SELECT * FROM hr_bonus_emp_list WHERE ebr_id='$bIds' AND emp_id='$idVls'");
                    $bQueryRowss = mysqli_num_rows($bQuery_a);
                    $bQueryRow_a = mysqli_fetch_object($bQuery_a);
                    $bns_days = $bQueryRow_a->bns_days;
                    $bns_amt = $bQueryRow_a->bns_amt;
                    //-------New Added
                    if($bQueryRowss > '0'){
                        $res['empArray'][]=['idVls'=>$idVls.'|'.$empSalary.'|'.$bns_days.'|'.$bns_amt];
                    }else{
                        $res['empArray'][]=['idVls'=>$idVls.'|'.$empSalary.'|'.$empAtt.'|'.$getBonus];
                    }
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
                    if ($b_mode=='monthly') {
                        $empSalary_ab = $empSalary/12;
                    }else if ($b_mode=='quarterly') {
                        $empSalary_ab = $empSalary/4;
                    }else if ($b_mode=='half yearly') {
                        $empSalary_ab = $empSalary/2;
                    }


                    $empAtt = empAttendance($con, $idVls, $b_id);
                    $getEmpSalary = $empSalary_ab/$days*$empAtt;
                    //*********

                    $getBonus = $getEmpSalary/100*$b_per;
                    // $getBonus = number_format($getBonus, 2);
                    $getBonus = round($getBonus, 2);

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
    // $qry = "SELECT x.id as empID, y.fullname as name, x.total_ctc as totalctc, x.gross as grs, x.basic as basics FROM hr_employee_service_register x, mstr_emp y WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND y.id='$empIds' UNION (SELECT x.id as empID, x.emp_name as name, x.total_ctc as totalctc, x.gross as grs FROM hr_employee_service_register x, mstr_emp y WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND y.id='$empIds')";

    // ORDER BY z.id DESC LIMIT 1
    // ORDER BY z.id DESC

    //  AND y.id DESC LIMIT 1
    $qry = "SELECT y.id as empID, y.fullname as name, z.ctc_pay as totalctc, z.gross_pay as grs, z.basic_pay as basics, z.net_pay as netpay FROM hr_employee_service_register x, mstr_emp y, hr_employee_salary_report z WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND y.id='$empIds' AND y.id=z.emp_id UNION (SELECT y.id as empID, y.fullname as name, z.ctc_pay as totalctc, z.gross_pay as grs, z.basic_pay as basics, z.net_pay as netpay FROM hr_employee_service_register x, mstr_emp y, hr_employee_salary_report z WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND y.id='$empIds' AND y.id=z.emp_id)";

    $orgQry_a = mysqli_query($con, $qry);
    $orgQry_rows = mysqli_num_rows($orgQry_a);

    // $datas = mysqli_fetch_object($orgQry_a);
    // if ($b_on=='CTC') {
    //     $res = $datas->totalctc;
    // }else if ($b_on=='Gross') {
    //     $res = $datas->grs;
    // }else if ($b_on=='Net Pay') {
    //     $res = $datas->netpay;
    // }else if ($b_on=='Basic') {
    //     $res = $datas->basics;
    // }
    // return $res;
     

    $vlsCheck = 0;
    while ($datas = mysqli_fetch_object($orgQry_a)) {
        $vlsCheck++;
        if ($vlsCheck==$orgQry_rows) {
            if ($b_on=='CTC') {
                $res = $datas->totalctc;
            }else if ($b_on=='Gross') {
                $res = $datas->grs;
            }else if ($b_on=='Net Pay') {
                $res = $datas->netpay;
            }else if ($b_on=='Basic') {
                $res = $datas->basics;
            }
            return $res;
        }
    }
    
}


function empAttendance($con, $idVls, $b_id){

    $qry = "SELECT * FROM hr_bonus WHERE id='$b_id'";
    $orgQry = mysqli_query($con, $qry);
    $rows = mysqli_fetch_object($orgQry);
    // $bMode = bModeFn($con, $rows->b_mode);
    $b_mode = $rows->b_mode;
    if ($b_mode=='monthly') {
        $daysCnt = 0;
    }else if ($b_mode=='quarterly') {
        $daysCnt = 2;
    }else if ($b_mode=='half yearly') {
        $daysCnt = 5;
    }else if ($b_mode=='yearly') {
        $daysCnt = 11;
    }
    $applicable = $rows->applicable;


    $expAplc = explode('-', $applicable);
    $years = $expAplc[0];
    $months = $expAplc[1];


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


    $list=array();
    $ss=0;
    for($d=1; $d<=31; $d++)
    {
        // $time=mktime(12, 0, 0, date('m'), $d, date('Y'));
        $time=mktime(12, 0, 0, $getLastMonth, $d, date('Y'));
        // if (date('m', $time)==date('m')){
        if (date('m', $time)==$getLastMonth){
            // $list[]=date('Y-m-d-D', $time);
            $ss = $ss+1;
            
        }
    }

    if ($ss==31) {
        $lastday = '31';
    }else if ($ss==30) {
        $lastday = '30';
    }else if ($ss==28) {
        $lastday = '28';
    }else if ($ss==29) { // new added
        $lastday = '29';
    }


    $frmYearMonth = $years.'-'.$months.'-01';
    $toYearMonth = $years_a.'-'.$getLastMonth.'-'.$lastday;

    // return $frmYearMonth.'___'.$toYearMonth.'______'.date('m', $time).'_____'.$getLastMonth.'_____'.$lastday;
    // exit();
//************

    // if ($months>$getLastMonth) {
    //     $qryvlss = "fmonth>='$getLastMonth' AND fmonth<='$months'";
    // }else{
    //     $qryvlss = "fmonth>='$months' AND fmonth<='$getLastMonth'";
    // }

    
    if ($years<$years_a) {
        if ($months>$getLastMonth) {

            $aryData = '';
            for ($i=$months; $i <= 12; $i++) { 
                if ($i==12) {
                    // $aryData .= $i;
                    $aryData .= " x.fmonth = '".$i."')";
                }else{
                    // $aryData .= $i.',';
                    $aryData .= " x.fmonth = '".$i."' OR ";
                }
            }
            $aryData_a = '(';
            for ($i=1; $i <= $getLastMonth; $i++) { 
                // $aryData_a .= $i.',';
                $aryData_a .= " x.fmonth = '".$i."' OR ";
            }
            // $totalAryVls = $aryData_a.$aryData;
            $qryvlss = $aryData_a.$aryData;

            // $qryvlss = " x.fmonth IN ($totalAryVls)";
        }else{
            $qryvlss = " x.fmonth>='$months' AND x.fmonth<='$getLastMonth'"; //////
        }
    }else{
        $qryvlss = " x.fmonth>='$months' AND x.fmonth<='$getLastMonth'";
    }

//************

    $qry = "SELECT DISTINCT y.dated FROM hr_leave_app x, hr_chk_leave y WHERE x.name='$idVls' AND ((x.from_date BETWEEN '$frmYearMonth' AND '$toYearMonth') OR (x.to_date BETWEEN '$frmYearMonth' AND '$toYearMonth')) AND x.unique_id=y.unique_id AND y.dated BETWEEN '$frmYearMonth' AND '$toYearMonth' AND (y.fullday='fullday' OR y.fullday='1' ) AND x.first_approve_status='1' AND y.cancel_status='0'";

    // $qry = "SELECT DISTINCT y.dated FROM hr_leave_app x, hr_chk_leave y WHERE x.name='693' AND ((x.from_date BETWEEN '2022-12-01' AND '2024-02-01') OR (x.to_date BETWEEN '2022-12-01' AND '2024-02-01')) AND x.unique_id=y.unique_id AND y.dated BETWEEN '2022-12-01' AND '2024-02-01' AND (y.fullday='fullday' OR y.fullday='1' ) AND x.first_approve_status='1' AND y.cancel_status='0'";
    $orgQry = mysqli_query($con, $qry);
    $rows = mysqli_num_rows($orgQry);


    $halfqry = "SELECT DISTINCT y.dated FROM hr_leave_app x, hr_chk_leave y WHERE x.name='$idVls' AND x.from_date BETWEEN '$frmYearMonth' AND '$toYearMonth' AND x.unique_id=y.unique_id AND y.dated BETWEEN '$frmYearMonth' AND '$toYearMonth' AND ((y.first_half='1st_half' OR y.first_half='0.5') OR (y.second_half='2nd_half' OR y.second_half='0.5')) AND x.first_approve_status='1' AND (x.cncl_status='0' OR x.cncl_status='2')";

    // $halfqry = "SELECT DISTINCT y.dated FROM hr_leave_app x, hr_chk_leave y WHERE x.name='693' AND x.from_date BETWEEN '2022-12-01' AND '2024-02-01' AND x.unique_id=y.unique_id AND y.dated BETWEEN '2022-12-01' AND '2024-02-01' AND ((y.first_half='1st_half' OR y.first_half='0.5') OR (y.second_half='2nd_half' OR y.second_half='0.5')) AND x.first_approve_status='1' AND (x.cncl_status='0' OR x.cncl_status='2')";
    $halfOrgQry = mysqli_query($con, $halfqry);
    $halfRows = mysqli_num_rows($halfOrgQry);

    //----New Added
    $halfRows = $halfRows % 2;
    if ($halfRows == 0) {
        $halfRows = $halfRows / 2;
    }else{
        // continue... 13 15 16 18 19
        $halfRows = $halfRows - 1;
        $halfRows = $halfRows / 2;
        $halfRows = $halfRows + 0.5;
    }
    //----New Added

    $get_total = $rows+$halfRows;

    // $attQry = "SELECT * FROM hr_attendance_appr_details WHERE $qryvlss AND fyear>='$years' AND fyear<='$years_a' AND hr_actn='1' AND f_actn='1' AND m_actn='1' ORDER BY id DESC LIMIT 1";

    // $attQry = "SELECT * FROM hr_attendance_appr_details WHERE $qryvlss AND fyear>='$years' AND fyear<='$years_a' AND hr_actn='1' AND f_actn='1' AND m_actn='1'";

    $attQry = "SELECT x.*,y.* FROM hr_attendance_appr_details x, hr_attendance_monthly_details y WHERE $qryvlss AND x.fyear>='$years' AND x.fyear<='$years_a' AND x.hr_actn='1' AND x.f_actn='1' AND x.m_actn='1' AND x.hamd_req_id=y.unique_id AND y.mstr_id='$idVls'";
    $orgQry = mysqli_query($con, $attQry);
    $orgQryNumRows = mysqli_num_rows($orgQry);
    $pd = 0;
    while ($rows = mysqli_fetch_object($orgQry)) {
        // $pd = $pd + $rows->pd;
        $pd = $pd + ($rows->present + $rows->offday); //----New added on 02-05-2025
    }

    if ($orgQryNumRows==0) {
        $getTotalPresent = 0;
    }else{
        // $getTotalPresent = $pd-$get_total;
        $getTotalPresent = $pd; //----New added on 25-04-2025
    }

    return $getTotalPresent;
    // return "SELECT x.*,y.* FROM hr_attendance_appr_details x, hr_attendance_monthly_details y WHERE $qryvlss AND x.fyear>='$years' AND x.fyear<='$years_a' AND x.hr_actn='1' AND x.f_actn='1' AND x.m_actn='1' AND x.hamd_req_id=y.unique_id AND y.mstr_id='$idVls'";

    /*
    // $attQry = "SELECT * FROM hr_attendance_appr_details WHERE fmonth>='02' AND fmonth<='12' AND fyear>='2022' AND fyear<='2023' AND hr_actn='1' AND f_actn='1' AND m_actn='1' ORDER BY id DESC LIMIT 1";
    $orgQry = mysqli_query($con, $attQry);
    $pd = '';
    while ($rows = mysqli_fetch_object($orgQry)) {
        $hamd_req_id = $rows->hamd_req_id;
        $hamd_req_id = trim($hamd_req_id);
        $attQry = "SELECT * FROM hr_attendance_monthly_details WHERE unique_id='$hamd_req_id' AND mstr_id='$idVls'";
        // $attQry = "SELECT * FROM hr_attendance_monthly_details WHERE unique_id='$hamd_req_id' AND mstr_id='564'";
        $orgQry = mysqli_query($con, $attQry);
        $orgQryNumRows = mysqli_num_rows($attQry);
        $rows_a = mysqli_fetch_object($orgQry);
        // $pd = $pd + $rows_a->pd;
        $pd = $pd.', '.$hamd_req_id;
    }

    if ($orgQryNumRows==0) {
        $getTotalPresent = 0;
    }else{
        $getTotalPresent = $pd-$get_total;
    }

    return $pd;
    */

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
            $res[]=['b_id'=>$rows->id,'b_mode'=>$bMode,'b_on'=>$rows->b_on,'based_on'=>$rows->based_on,'b_per'=>$rows->b_per,'applicable'=>$rows->applicable];
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

if ($action=='getbonus_date') {
    $b_mode = $_POST['b_mode'];
    $years = $_POST['a_b_date_y'];
    $months = $_POST['a_b_date_m'];

    if ($b_mode=='monthly') {
        $daysCnt = 0;
    }else if ($b_mode=='quarterly') {
        $daysCnt = 2;
    }else if ($b_mode=='half yearly') {
        $daysCnt = 5;
    }else if ($b_mode=='yearly') {
        $daysCnt = 11;
    }
    
    $lastMnths = $months+$daysCnt;
    $thisMonth = date('m');

    $days = 0;
    for ($i=$months; $i <= $lastMnths; $i++) {
        $years_a = $expAplc[0];
        if ($i>12) {
            $dy = $i-12;
            $years_a = $years_a+1;
        }else{
            $dy = $i;
        }
        for($d=1; $d<=31; $d++)
        {
            $time=mktime(12, 0, 0, $dy, $d, $years_a);
            if (date('m', $time)==$dy){
                $days = $days+1;
            }
        }
    }
    echo $days;   
}


if ($action=='empBonusRequestSubmit') {

    $orgName = $_POST['orgName'];
    $orgBonusType = $_POST['orgBonusType'];
    $orgBonusMode = $_POST['orgBonusMode'];
    $orgBonusOn = $_POST['orgBonusOn'];
    $orgBasedOn = $_POST['orgBasedOn'];
    $orgBonusPer = $_POST['orgBonusPer'];
    $bonusMessages = $_POST['bonusMessages'];
    
    $entry_date = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $created_by = $sessionid;

    $empDtls = $_POST['empDtls'];
    $bMode = bModeNameFn($con, $orgBonusMode);

    $orgBTypeVls = getOrgBTypeVls($con, $orgBonusType);
    $b_id = $orgBTypeVls[0]['b_id'];


    $sltQry = "SELECT * FROM hr_bonus_request WHERE org_id='$orgName' AND b_type='$orgBonusType' AND b_mode='$orgBonusMode' AND b_status='0' ORDER BY id DESC LIMIT 1";
    $sltRow = mysqli_query($con, $sltQry);
    $rows = mysqli_fetch_object($sltRow);
    $ids = $rows->id;
    $b_status_vls = $rows->b_status;
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
            $bns_id = $insertId;
            // New Added 10-11-23
            $new_str = str_pad($insertId,'5',"0", STR_PAD_LEFT);
            $reqNoVls = 'bns_'.$new_str;
            $qry_a = "UPDATE `hr_bonus_request` SET `req_no`='$reqNoVls' WHERE id='$insertId'";
            $sqlqry_a = mysqli_query($con, $qry_a);
            // New Added 10-11-23
            
            // hr_bonus_request_history
            
            //--------Notification
            $created_on = date('Y-m-d H:i:s');
            $url = 'hr/bonus/add_bonus_request_list.php';
            $menuid = getMenuById($con, $url);
            $stage_no = $status = 0;
            $emp_id = $_SESSION['ERP_SESS_ID'];
            $deptid = getdeptid($con, $emp_id);
            $reciever = getapprover($con, $menuid, $stage_no, $status, '', '', $deptid, '', $emp_id);
            $notificationmsg = "Bonus request no : ".$reqNoVls." is pending for approve on your site.";
            saveNotice($con, $insertId, "Bonus Request", "0", "hr/bonus/manage_bonus_request_list.php", "0", $notificationmsg, $reciever, $emp_id, $created_on);
            //--------/Notification

            $res = 1;
        }else{
            $res = 0;
        }
    }
    
        if($bns_id==''){
            $bns_id = $ids;
        }
        $remarks = "Request Raised";
        $query1 = "INSERT INTO `hr_bonus_request_history` (`br_id`, `org_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `created_by`, `action_by`, `action_on`, `stage_no`, `act_status`, `reason`, `remarks`, `ip_addr`)VALUES('$bns_id', '$orgName', '$orgBonusType', '$bMode', '$orgBonusOn', '$orgBasedOn', '$orgBonusPer', '$bonusMessages', '$sessionid', '$sessionid', '$entry_date', '0', '0', '$bonusMessages', '$remarks', '$ip_addr')";
        $sql_query = mysqli_query($con, $query1);


        $orgSltQry = "SELECT * FROM `hr_bonus_request` WHERE org_id='$orgName' AND b_type='$orgBonusType' ORDER BY id DESC LIMIT 1";
        $qrys = mysqli_query($con, $orgSltQry);
        $skNum = mysqli_num_rows($qrys);
        $rowsSltQry = mysqli_fetch_object($qrys);
        $ebrIds = $rowsSltQry->id;
        $b_Ids = $rowsSltQry->b_id;
        $org_Ids = $rowsSltQry->org_id;
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
            $slVls_a = str_pad($numVls,'5',"0", STR_PAD_LEFT);
            $reqNo = 'B'.$ebrIds.'-O'.$org_Ids.'-'.$slVls_a;
            
            $expEmpDtlsAll = explode('|', $expEmpDtls[$i]);
            $empId = $expEmpDtlsAll[0]; //empId
            $setBonusOn = $expEmpDtlsAll[1]; //setBonusOn
            $setBonusSalary = $expEmpDtlsAll[2]; //setBonusSalary
            $setBonusPer = $expEmpDtlsAll[3]; //setBonusPer
            $setBonusDays = $expEmpDtlsAll[4]; //setBonusDays
            $setBonusAmounts = $expEmpDtlsAll[5]; //setBonusAmounts
            // $empSlIdsVls = $expEmpDtlsAll[6]; //empSlIdsVls
            if ($empId!='' && $empId!='0') {
                $qry = "INSERT INTO `hr_bonus_emp_list`(`ebr_id`, `emp_id`, `emp_sl_id`, `empNum`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$insertId','$empId','$reqNo','$numVls','$setBonusOn','$setBonusSalary','$setBonusPer','$setBonusDays','$setBonusAmounts')";
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
    
    $entry_date = date('Y-m-d H:i:s');
    $sessionid = $_SESSION['ERP_SESS_ID'];
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $created_by = $sessionid;

    $empDtls = $_POST['empDtls'];
    $bMode = bModeNameFn($con, $orgBonusMode);

    $orgBTypeVls = getOrgBTypeVls($con, $orgBonusType);
    $b_id = $orgBTypeVls[0]['b_id'];


    // *************
    $bQry = "SELECT * FROM hr_bonus_request WHERE id='$hiddenIds'";
    $qrys = mysqli_query($con, $bQry);
    $rowsSltQry = mysqli_fetch_object($qrys);
    $reqNoVls = $rowsSltQry->req_no;
    $b_ids = $rowsSltQry->b_id;
    $org_ids = $rowsSltQry->org_id;
    $b_types = $rowsSltQry->b_type;
    $b_modes = $rowsSltQry->b_mode;
    $b_ons = $rowsSltQry->b_on;
    $based_ons = $rowsSltQry->based_on;
    $b_pers = $rowsSltQry->b_per;
    $b_msgs = $rowsSltQry->b_msg;
    $b_statuss = $rowsSltQry->b_status;
    $stage_nos = $rowsSltQry->stage_no;
    $b_status_dts = $rowsSltQry->b_status_dt;
    $created_bys = $rowsSltQry->created_by;
    $remarkss = $rowsSltQry->remarks;
    $created_ons = $rowsSltQry->created_on;

    $hbQry = "SELECT * FROM hr_bonus_request_history WHERE br_id='$rowsSltQry->id' ORDER BY id DESC LIMIT 1";
    $hqrys = mysqli_query($con, $hbQry);
    $hrowsSltQry = mysqli_fetch_object($hqrys);
    $action_by = $hrowsSltQry->action_by;
    
    
        $remarks = "Request Update";
        $query1 = "INSERT INTO `hr_bonus_request_history` (`br_id`, `org_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `created_by`, `action_by`, `action_on`, `stage_no`, `act_status`, `reason`, `remarks`, `ip_addr`)VALUES('$hiddenIds', '$orgName', '$orgBonusType', '$bMode', '$orgBonusOn', '$orgBasedOn', '$orgBonusPer', '$bonusMessages', '$sessionid', '$sessionid', '$entry_date', '0', '0', '$bonusMessages', '$remarks', '$ip_addr')";
        $sql_query = mysqli_query($con, $query1);
    
    

    $insert_stmt = "INSERT INTO hr_bonus_request_edit(`b_id`, `br_id`, `org_id`, `b_type`, `b_mode`, `b_on`, `based_on`, `b_per`, `b_msg`, `b_status`, `stage_no`, `b_status_dt`, `created_by`, `action_by`, `remarks`, `br_created_at`) VALUES('$b_ids', '$hiddenIds', '$org_ids', '$b_types', '$b_modes', '$b_ons', '$based_ons', '$b_pers', '$b_msgs', '$b_statuss', '$stage_nos', '$b_status_dts', '$created_bys', '$action_by', '$remarkss', '$created_ons')";
    mysqli_query($con, $insert_stmt);

    $lastIDs = mysqli_insert_id($con);

    $bQry = "SELECT * FROM hr_bonus_emp_list WHERE ebr_id='$hiddenIds'";
    $qrys = mysqli_query($con, $bQry);
    while ($rowsVls = mysqli_fetch_object($qrys)) {
        $ebr_ids = $rowsVls->ebr_id;
        $emp_ids = $rowsVls->emp_id;
        // $emp_sl_ids = $rowsVls->emp_sl_id;
        $empNums = $rowsVls->empNum;
        $bns_ons = $rowsVls->bns_on;
        $bns_rates = $rowsVls->bns_rate;
        $bns_pres = $rowsVls->bns_pre;
        $bns_dayss = $rowsVls->bns_days;
        $bns_amts = $rowsVls->bns_amt;
        // mysqli_query($con, "INSERT INTO `hr_bonus_request_emp_edit`(`ed_br_id`, `ebr_id`, `emp_id`, `emp_sl_id`, `empNum`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$lastIDs','$ebr_ids','$emp_ids','$emp_sl_ids','$empNums','$bns_ons','$bns_rates','$bns_pres','$bns_dayss','$bns_amts')");
        mysqli_query($con, "INSERT INTO `hr_bonus_request_emp_edit`(`ed_br_id`, `ebr_id`, `emp_id`, `empNum`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$lastIDs','$ebr_ids','$emp_ids','$empNums','$bns_ons','$bns_rates','$bns_pres','$bns_dayss','$bns_amts')");
    }
    // *************


    $qry = "UPDATE `hr_bonus_request` SET `org_id`='$orgName', `b_id`='$b_id', `b_type`='$orgBonusType', `b_mode`='$bMode', `b_on`='$orgBonusOn', `based_on`='$orgBasedOn', `b_per`='$orgBonusPer', `b_msg`='$bonusMessages', `created_by`='$sessionid', `b_status`='0', `stage_no`='0' WHERE id='$hiddenIds'";
    $sqlqry = mysqli_query($con, $qry);
    if ($sqlqry) {

        //--------Notification
        $created_on = date('Y-m-d H:i:s');
        $url = 'hr/bonus/add_bonus_request_list.php';
        $menuid = getMenuById($con, $url);
        $stage_no = $status = 0;
        $emp_id = $_SESSION['ERP_SESS_ID'];
        $deptid = getdeptid($con, $emp_id);
        $reciever = getapprover($con, $menuid, $stage_no, $status, '', '', $deptid, '', $emp_id);
        $notificationmsg = "Bonus request no : ".$reqNoVls." data is updated, and pending for approve on your site.";
        saveNotice($con, $hiddenIds, "Bonus Request", '0', "hr/bonus/manage_bonus_request_list.php", '0', $notificationmsg, $reciever, $emp_id, $created_on);
        //--------/Notification

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
            $slVls_a = str_pad($num,'5',"0", STR_PAD_LEFT);
            $reqNo = 'B'.$hiddenIds.'-O'.$org_ids.'-'.$slVls_a;
            $expEmpDtlsAll = explode('|', $expEmpDtls[$i]);
            $empId = $expEmpDtlsAll[0]; //empId
            $setBonusOn = $expEmpDtlsAll[1]; //setBonusOn
            $setBonusSalary = $expEmpDtlsAll[2]; //setBonusSalary
            $setBonusPer = $expEmpDtlsAll[3]; //setBonusPer
            $setBonusDays = $expEmpDtlsAll[4]; //setBonusDays
            $setBonusAmounts = $expEmpDtlsAll[5]; //setBonusAmounts
            // $empSlIdsVls = $expEmpDtlsAll[6]; //empSlIdsVls
            if ($empId!='' && $empId!='0') {

                // $chksltQry = "SELECT * FROM hr_bonus_emp_list WHERE ebr_id='$hiddenIds' AND emp_id='$empId'";
                // $chksltRow = mysqli_query($con, $chksltQry);
                // $numRws = mysqli_num_rows($chksltRow);
                // if ($numRws==0) {
                //     $qry = "INSERT INTO `hr_bonus_emp_list`(`ebr_id`, `emp_id`, `emp_sl_id`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$hiddenIds','$empId','$empSlIdsVls','$setBonusOn','$setBonusSalary','$setBonusPer','$setBonusDays','$setBonusAmounts')";
                // }

                // $qry = "INSERT INTO `hr_bonus_emp_list`(`ebr_id`, `emp_id`, `emp_sl_id`, `empNum`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$hiddenIds','$empId','$empSlIdsVls','$num','$setBonusOn','$setBonusSalary','$setBonusPer','$setBonusDays','$setBonusAmounts')";
                $qry = "INSERT INTO `hr_bonus_emp_list`(`ebr_id`, `emp_id`, `emp_sl_id`, `empNum`, `bns_on`, `bns_rate`, `bns_pre`, `bns_days`, `bns_amt`) VALUES ('$hiddenIds','$empId', '$reqNo','$num','$setBonusOn','$setBonusSalary','$setBonusPer','$setBonusDays','$setBonusAmounts')";
                
                mysqli_query($con, $qry);
            }
        }
    echo $res;
}


?>