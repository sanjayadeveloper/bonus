<?php
require_once(SITE_URL.'/basic/auth.php');
require_once(SITE_URL.'/basic/config.php');


if (isset($_POST['action'])) {
    $action = $_POST['action'];
}


if ($action=='searchedBonuslist') {
    $frm_date = $_POST['frm_date'];
    $to_date = $_POST['to_date'];
    $bonus_type = $_POST['bonus_type'];
    $bonus_mode = $_POST['bonus_mode'];


    // echo $frm_date.'---'.$to_date.'---'.$bonus_type.'---'.$bonus_mode;
    // exit();


    
    

    $allQryVls = "";

    if (!empty($frm_date)) {
        $frm_date_a = explode('/', $frm_date);
        $frmDt = $frm_date_a[0];
        $frmMth = $frm_date_a[1];
        $frmYr = $frm_date_a[2];
        $frmDate = $frmYr.'-'.$frmMth.'-'.$frmDt;
        if (!empty($to_date)) {
            $allQryVls .= " AND a.b_date>='$frmDate'";
        }else{
            $allQryVls .= " AND a.b_date='$frmDate'";
        }
    }else{
        $allQryVls .= "";
    }
    if (!empty($to_date)) {
        $to_date_a = explode('/', $to_date);
        $toDt = $to_date_a[0];
        $toMth = $to_date_a[1];
        $toYr = $to_date_a[2];
        $toDate = $toYr.'-'.$toMth.'-'.$toDt;
        if (!empty($frm_date)) {
            $allQryVls .= " AND a.b_date<='$toDate'";
        }else{
            $allQryVls .= " AND a.b_date='$toDate'";
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

    $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.*,b.*,c.*,c.id as empIds FROM hr_bonus a, master_type_dtls b, mstr_emp c WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id $allQryVls ORDER BY a.id DESC");
    $bnsQry_results = mysqli_num_rows($bnsQry);
    if ($bnsQry_results>0) {
        $counter=0;
        while($rows=mysqli_fetch_object($bnsQry)){
            
            //********Approved User Access
            $getApproverList = getApproverList($con, $menuid, $rows->stage_no);
            if ($rows->created_by==$sessionid || $getApproverList==$sessionid) {
            //********Approved User Access

            $counter++;
            $refid = $rows->ids;
            $getfield = "remarks"; //to fetch approved by id from details table
            $dateView = date('d-m-Y', strtotime($rows->b_date));
            $stsVls = $rows->b_status;
            $refcolmn = "act_status";

            // echo 'empIds :- '.$rows->empIds;
            // echo 'hr_bonus_history, bns_id, '. $refid.', '.$getfield.', '.$refcolmn.'---';

            if($stsVls == '0'){
                  $status = 'Request Raised';
                  $color = 'color:Orange';
            }else{
                $status =  getstatus($con, 'hr_bonus_history', 'bns_id', $refid, $getfield, $refcolmn);
                $color = 'color:green';
            }
            $bnsQry_a = mysqli_query($con,"SELECT * FROM master_type_dtls WHERE mstr_type_value='$rows->b_mode'");
            $rows_a=mysqli_fetch_object($bnsQry_a);

            $bnsQry_b = mysqli_query($con,"SELECT * FROM hr_bonus_history WHERE bns_id='$rows->ids' ORDER BY id DESC LIMIT 1"); //*****New Added
            $rows_b=mysqli_fetch_object($bnsQry_b);
    ?>
                    <tr>
                        <td><?=$counter;?></td>
                        <td><?=$dateView;?></td>
                        <td><?=$rows->mstr_type_name;?></td>
                        <td><?=$rows->b_on;?></td>
                        <td><?=$rows_a->mstr_type_name;?></td>
                        <td><?=$rows->applicable;?></td>
                        <td><?=$rows->b_per;?></td>
                        <td style="<?php echo $color; ?>"><?=$status;?></td>
                        <td>
                            <?php
                                $empid = $rows->created_by;
                                $deptid = getdeptid($con, $empid);
                                $stage_no = $rows->stage_no;

                                // echo $menuid.', '.$stage_no.', '.$stsVls.', '.''.', '.''.', '.$deptid.', '.''.', '.$empid.'<br/>';


                                if ($stsVls == 0 || $stsVls == 2) {
                                    $data =  payliststatuswith($con, $menuid, $stage_no, $stsVls, '', '', $deptid, '', $empid);
                                    $color = 'color:Red';
                                } else {
                                    $data = statuswithother($con, 'hr_bonus_history', 'bns_id', $rows->ids, $stsVls, 'action_by');
                                    $color = 'color:Green';
                                }
                            ?>
                            <span style="<?php echo $color; ?>"><b><?php echo $data; ?></b></span>
                        </td>
                        <td><?=$rows_b->action_on;?></td>
                        <td class="text-center">
                            <?php if (($stsVls == '0' || $stsVls == '3') && $rows->created_by == $sessionid) { ?>
                                <a href="edit_bonus_request.php?ids=<?php echo $rows->ids; ?>&viewid=1" style="text-decoration:none;" class="btn btn-warning btn-xs"><b>Edit</b></a>
                            <?php } ?>
                           <a href="bonus_view.php?ids=<?=$rows->ids;?>&viewid=1" class="btn btn-primary btn-xs fw-bolder">View</a>
                        </td>
                    </tr>

<?php
            }
        }
    }else{
?>
                    <tr>
                        <td class="" colspan="11" style="text-align: center; font-size: 15px;">--- <span style="color: red;">Data Not Found</span> ---</td>
                    </tr>
<?php
    }
}




if (isset($_POST['bonuslistSrcBtn'])) {
    $frm_date = $_POST['frm_date'];
    $to_date = $_POST['to_date'];
    $bonus_type = $_POST['bonus_type'];
    $bonus_mode = $_POST['bonus_mode'];

    $frm_date_a = explode('/', $frm_date);
    $frmDt = $frm_date_a[0];
    $frmMth = $frm_date_a[1];
    $frmYr = $frm_date_a[2];
    $frmDate = $frmYr.'-'.$frmMth.'-'.$frmDt;
    $to_date_a = explode('/', $to_date);
    $toDt = $to_date_a[0];
    $toMth = $to_date_a[1];
    $toYr = $to_date_a[2];
    $toDate = $toYr.'-'.$toMth.'-'.$toDt;

    $allQryVls = "";

    if (!empty($frm_date)) {
    	if (!empty($to_date)) {
	    	$allQryVls .= " AND a.b_date>='$frmDate'";
	    }else{
	    	$allQryVls .= " AND a.b_date='$frmDate'";
	    }
    }else{
    	$allQryVls .= "";
    }
    if (!empty($to_date)) {
    	if (!empty($frm_date)) {
	    	$allQryVls .= " AND a.b_date<='$toDate'";
	    }else{
	    	$allQryVls .= " AND a.b_date='$toDate'";
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
}else{
    $allQryVls = "";
}

//*****************

if (isset($_POST['appr_bonuslistSrcBtn'])) {
    $appr_frm_date = $_POST['appr_frm_date'];
    $appr_to_date = $_POST['appr_to_date'];
    $appr_bonus_type = $_POST['appr_bonus_type'];
    $appr_bonus_mode = $_POST['appr_bonus_mode'];

    $appr_frm_date_a = explode('/', $appr_frm_date);
    $frmDt = $appr_frm_date_a[0];
    $frmMth = $appr_frm_date_a[1];
    $frmYr = $appr_frm_date_a[2];
    $appr_frmDate = $frmYr.'-'.$frmMth.'-'.$frmDt;
    $appr_to_date_a = explode('/', $appr_to_date);
    $toDt = $appr_to_date_a[0];
    $toMth = $appr_to_date_a[1];
    $toYr = $appr_to_date_a[2];
    $appr_toDate = $toYr.'-'.$toMth.'-'.$toDt;

    $appr_allQryVls = "";

    if (!empty($appr_frm_date)) {
    	if (!empty($appr_to_date)) {
	    	$appr_allQryVls .= " AND a.b_date>='$appr_frmDate'";
	    }else{
	    	$appr_allQryVls .= " AND a.b_date='$appr_frmDate'";
	    }
    }else{
    	$appr_allQryVls .= "";
    }
    if (!empty($appr_to_date)) {
    	if (!empty($appr_frm_date)) {
	    	$appr_allQryVls .= " AND a.b_date<='$appr_toDate'";
	    }else{
	    	$appr_allQryVls .= " AND a.b_date='$appr_toDate'";
	    }
    }else{
    	$appr_allQryVls .= "";
    }
    if (!empty($appr_bonus_type)) {
    	$appr_allQryVls .= " AND a.b_type='$appr_bonus_type'";
    }else{
    	$appr_allQryVls .= "";
    }
    if (!empty($appr_bonus_mode)) {
    	$appr_allQryVls .= " AND a.b_mode='$appr_bonus_mode'";
    }else{
    	$appr_allQryVls .= "";
    }
}else{
    $appr_allQryVls = "";
}


//*****************

if (isset($_POST['rej_bonuslistSrcBtn'])) {
    $rej_frm_date = $_POST['rej_frm_date'];
    $rej_to_date = $_POST['rej_to_date'];
    $rej_bonus_type = $_POST['rej_bonus_type'];
    $rej_bonus_mode = $_POST['rej_bonus_mode'];

    $rej_frm_date_a = explode('/', $rej_frm_date);
    $frmDt = $rej_frm_date_a[0];
    $frmMth = $rej_frm_date_a[1];
    $frmYr = $rej_frm_date_a[2];
    $rej_frmDate = $frmYr.'-'.$frmMth.'-'.$frmDt;
    $rej_to_date_a = explode('/', $rej_to_date);
    $toDt = $rej_to_date_a[0];
    $toMth = $rej_to_date_a[1];
    $toYr = $rej_to_date_a[2];
    $rej_toDate = $toYr.'-'.$toMth.'-'.$toDt;

    $rej_allQryVls = "";

    if (!empty($rej_frm_date)) {
        if (!empty($rej_to_date)) {
            $rej_allQryVls .= " AND a.b_date>='$rej_frmDate'";
        }else{
            $rej_allQryVls .= " AND a.b_date='$rej_frmDate'";
        }
    }else{
        $rej_allQryVls .= "";
    }
    if (!empty($rej_to_date)) {
        if (!empty($rej_frm_date)) {
            $rej_allQryVls .= " AND a.b_date<='$rej_toDate'";
        }else{
            $rej_allQryVls .= " AND a.b_date='$rej_toDate'";
        }
    }else{
        $rej_allQryVls .= "";
    }
    if (!empty($rej_bonus_type)) {
        $rej_allQryVls .= " AND a.b_type='$rej_bonus_type'";
    }else{
        $rej_allQryVls .= "";
    }
    if (!empty($rej_bonus_mode)) {
        $rej_allQryVls .= " AND a.b_mode='$rej_bonus_mode'";
    }else{
        $rej_allQryVls .= "";
    }
}else{
    $rej_allQryVls = "";
}

//*****************

if (isset($_POST['pnd_bonuslistSrcBtn'])) {
    $pnd_frm_date = $_POST['pnd_frm_date'];
    $pnd_to_date = $_POST['pnd_to_date'];
    $pnd_bonus_type = $_POST['pnd_bonus_type'];
    $pnd_bonus_mode = $_POST['pnd_bonus_mode'];

    $pnd_frm_date_a = explode('/', $pnd_frm_date);
    $frmDt = $pnd_frm_date_a[0];
    $frmMth = $pnd_frm_date_a[1];
    $frmYr = $pnd_frm_date_a[2];
    $pnd_frmDate = $frmYr.'-'.$frmMth.'-'.$frmDt;
    $pnd_to_date_a = explode('/', $pnd_to_date);
    $toDt = $pnd_to_date_a[0];
    $toMth = $pnd_to_date_a[1];
    $toYr = $pnd_to_date_a[2];
    $pnd_toDate = $toYr.'-'.$toMth.'-'.$toDt;

    $pnd_allQryVls = "";

    if (!empty($pnd_frm_date)) {
        if (!empty($pnd_to_date)) {
            $pnd_allQryVls .= " AND a.b_date>='$pnd_frmDate'";
        }else{
            $pnd_allQryVls .= " AND a.b_date='$pnd_frmDate'";
        }
    }else{
        $pnd_allQryVls .= "";
    }
    if (!empty($pnd_to_date)) {
        if (!empty($pnd_frm_date)) {
            $pnd_allQryVls .= " AND a.b_date<='$pnd_toDate'";
        }else{
            $pnd_allQryVls .= " AND a.b_date='$pnd_toDate'";
        }
    }else{
        $pnd_allQryVls .= "";
    }
    if (!empty($pnd_bonus_type)) {
        $pnd_allQryVls .= " AND a.b_type='$pnd_bonus_type'";
    }else{
        $pnd_allQryVls .= "";
    }
    if (!empty($pnd_bonus_mode)) {
        $pnd_allQryVls .= " AND a.b_mode='$pnd_bonus_mode'";
    }else{
        $pnd_allQryVls .= "";
    }
}else{
    $pnd_allQryVls = "";
}

//*****************

if (isset($_POST['rechk_bonuslistSrcBtn'])) {
    $rechk_frm_date = $_POST['rechk_frm_date'];
    $rechk_to_date = $_POST['rechk_to_date'];
    $rechk_bonus_type = $_POST['rechk_bonus_type'];
    $rechk_bonus_mode = $_POST['rechk_bonus_mode'];

    $rechk_frm_date_a = explode('/', $rechk_frm_date);
    $frmDt = $rechk_frm_date_a[0];
    $frmMth = $rechk_frm_date_a[1];
    $frmYr = $rechk_frm_date_a[2];
    $rechk_frmDate = $frmYr.'-'.$frmMth.'-'.$frmDt;
    $rechk_to_date_a = explode('/', $rechk_to_date);
    $toDt = $rechk_to_date_a[0];
    $toMth = $rechk_to_date_a[1];
    $toYr = $rechk_to_date_a[2];
    $rechk_toDate = $toYr.'-'.$toMth.'-'.$toDt;

    $rechk_allQryVls = "";

    if (!empty($rechk_frm_date)) {
        if (!empty($rechk_to_date)) {
            $rechk_allQryVls .= " AND a.b_date>='$rechk_frmDate'";
        }else{
            $rechk_allQryVls .= " AND a.b_date='$rechk_frmDate'";
        }
    }else{
        $rechk_allQryVls .= "";
    }
    if (!empty($rechk_to_date)) {
        if (!empty($rechk_frm_date)) {
            $rechk_allQryVls .= " AND a.b_date<='$rechk_toDate'";
        }else{
            $rechk_allQryVls .= " AND a.b_date='$rechk_toDate'";
        }
    }else{
        $rechk_allQryVls .= "";
    }
    if (!empty($rechk_bonus_type)) {
        $rechk_allQryVls .= " AND a.b_type='$rechk_bonus_type'";
    }else{
        $rechk_allQryVls .= "";
    }
    if (!empty($rechk_bonus_mode)) {
        $rechk_allQryVls .= " AND a.b_mode='$rechk_bonus_mode'";
    }else{
        $rechk_allQryVls .= "";
    }
}else{
    $rechk_allQryVls = "";
}

//*****************

if (isset($_POST['hold_bonuslistSrcBtn'])) {
    $hold_frm_date = $_POST['hold_frm_date'];
    $hold_to_date = $_POST['hold_to_date'];
    $hold_bonus_type = $_POST['hold_bonus_type'];
    $hold_bonus_mode = $_POST['hold_bonus_mode'];

    $hold_frm_date_a = explode('/', $hold_frm_date);
    $frmDt = $hold_frm_date_a[0];
    $frmMth = $hold_frm_date_a[1];
    $frmYr = $hold_frm_date_a[2];
    $hold_frmDate = $frmYr.'-'.$frmMth.'-'.$frmDt;
    $hold_to_date_a = explode('/', $hold_to_date);
    $toDt = $hold_to_date_a[0];
    $toMth = $hold_to_date_a[1];
    $toYr = $hold_to_date_a[2];
    $hold_toDate = $toYr.'-'.$toMth.'-'.$toDt;

    $hold_allQryVls = "";

    if (!empty($hold_frm_date)) {
        if (!empty($hold_to_date)) {
            $hold_allQryVls .= " AND a.b_date>='$hold_frmDate'";
        }else{
            $hold_allQryVls .= " AND a.b_date='$hold_frmDate'";
        }
    }else{
        $hold_allQryVls .= "";
    }
    if (!empty($hold_to_date)) {
        if (!empty($hold_frm_date)) {
            $hold_allQryVls .= " AND a.b_date<='$hold_toDate'";
        }else{
            $hold_allQryVls .= " AND a.b_date='$hold_toDate'";
        }
    }else{
        $hold_allQryVls .= "";
    }
    if (!empty($hold_bonus_type)) {
        $hold_allQryVls .= " AND a.b_type='$hold_bonus_type'";
    }else{
        $hold_allQryVls .= "";
    }
    if (!empty($hold_bonus_mode)) {
        $hold_allQryVls .= " AND a.b_mode='$hold_bonus_mode'";
    }else{
        $hold_allQryVls .= "";
    }
}else{
    $hold_allQryVls = "";
}
?>