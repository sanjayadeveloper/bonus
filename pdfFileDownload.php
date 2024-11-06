<?php
require_once('../../auth.php');
require_once('../../config.php');
include_once('../../workflownotif.php');
require_once("../../mpdf/mpdf/mpdf.php");
include_once('../../approvalmatrixfunction.php');
include_once('php_function/bonus_function.php');
$sessionid = $_SESSION['ERP_SESS_ID'];

    $org_name = $_GET['org_name'];
    $frm_date = $_GET['frm_date'];
    $to_date = $_GET['to_date'];
    $bonus_type = $_GET['bonus_type'];
    $bonus_mode = $_GET['bonus_mode'];
    $actVls = $_GET['actVls'];
    $tag = $_GET['tag'];
    
    $tagVls_a = explode(',', $tag);

    if ($actVls=='All') {
        $filename = 'Bonus_All';
    }else if($actVls=='Approved'){
        $filename = 'Bonus_Approved';
    }else if($actVls=='Reject'){
        $filename = 'Bonus_Reject';
    }else if($actVls=='Pending'){
        $filename = 'Bonus_Pending';
    }else if($actVls=='Re-Check'){
        $filename = 'Bonus_Re-Check';
    }else if($actVls=='Hold'){
        $filename = 'Bonus_Hold';
    }

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



  

  //print_r($arr_mainid);
  $mpdf = $mpdf = new mPDF('c',    // mode - default ''
     '',    // format - A4, for example, default ''
     0,     // font size - default 0
     '',    // default font family
     15,    // margin_left
     15,    // margin right
     16,    // margin top
     16,    // margin bottom
     9,     // margin header
     9,     // margin footer
     'P');  // L - landscape, P - portrait

    $mpdf->setFooter('{PAGENO}');    
     ob_start();  
  ?>

<!-- ********************* -->
<style>
  *{
    margin: 0 !important;
    box-sizing: border-box;
    font-family: sans-serif;
  }
  table {
    font-size: 10px !important;
    font-family: sans-serif;
    vertical-align: top;
    border-collapse: collapse;
    text-align: left;
    width: 100%;
    border: 1px solid #000;
  }
  .bor-0{
    border: unset !important;
  }
  th ,td{
    border: 1px solid #000 ;
    text-align: left;
    padding: 5px;
  }
  .wid-50{width: 50%;}
  .text_center{text-align: center;}
  .border-bottom-2 {
    border-bottom: 1px solid #cbcbcb !important;
  }
  .bg-gray{
    background-color: #ccc !important;
  }
  .mb-1 {
    margin-bottom: 0.25rem!important;
  }
  .border-bottom-0 {
    border-bottom: 0!important;
  }
</style>
    <table class="table table-striped table-bordered table-hover w-100" id="listing_tableID">
      <thead class="bg-dark">
        <tr>
<?php
    // for ($i=0; $i < count($tagVls_a); $i++) { 
    //   $tagVls = explode('|', $tagVls_a[$i]);
    //   if ($tagVls[1]==$i) {
    //     echo '<th class="sorting">'.$tagVls[0].'</th>';
    //   }else{
    //     echo '<th class="text-center sorting">'.$tagVls[0].'</th>';
    //   }
    // }

?>
            <th class="sorting">SN</th>
            <th class="text-center sorting">Date</th>
            <th class="text-center sorting">Organisation</th>
            <th class="text-center sorting">Bonus Type</th>
            <th class="text-center sorting">Bonus On</th>
            <th class="text-center sorting">Bonus Mode</th>
            <th class="text-center sorting">Bonus (%)</th>
            <th class="text-center sorting">Status</th>
            <th class="text-center sorting">Status Details</th>
            <th class="text-center sorting">Status Dt.</th>
        </tr>
      </thead>
      <tbody>
<?php
  
  if ($bnsQry_results>0) {
        // code...
        $i=1;
        $arrayVls = [];
        while($rows=mysqli_fetch_object($bnsQry)){
            //********Approved User Access
            $getApproverList = getApproverList($con, $menuid, $rows->stage_no);
            //********Approved User Access
            $refid = $rows->ids;
            $getfield = "remarks"; //to fetch approved by id from details table
            $dateView = date('d-m-Y', strtotime($rows->b_date));
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
                $data =  payliststatuswith($con, $menuid, $stage_no, $stsVls, '', '', $deptid, '', $empid);
                $color = 'color:Red';
            } else {
                $data = statuswithother($con, 'hr_bonus_request_history', 'br_id', $rows->ids, $stsVls, 'action_by');
                $color = 'color:Green';
            }
            //********
            // $data = array($i, $dateView, $rows->mstr_type_name, $rows->b_on, $rows_a->mstr_type_name, $rows->applicable, $rows->b_per, $status, $data, $rows_b->action_on);
            if ($actVls=='All') {
?>
            <tr>
              <td><?=$i;?></td>
              <td><?=$dateView;?></td>
              <td><?=$rows->organisation;?></td>
              <td><?=$rows->mstr_type_name;?></td>
              <td><?=$rows->b_on;?></td>
              <td><?=$rows_a->mstr_type_name;?></td>
              <td><?=$rows->b_per;?></td>
              <td style="<?php echo $colorR; ?>"><?=$status;?></td>
              <td>
                  <?php
                      $empid = $rows->created_by;
                      $deptid = getdeptid($con, $empid);
                      $stage_no = $rows->stage_no;

                      if ($stsVls == 0 || $stsVls == 2) {
                          $data =  payliststatuswith($con, $menuid, $stage_no, $stsVls, '', '', $deptid, '', $empid);
                          $color = 'color:Red';
                      } else {
                          $data = statuswithother($con, 'hr_bonus_request_history', 'br_id', $rows->ids, $stsVls, 'action_by');
                          $color = 'color:Green';
                      }
                  ?>
                  <span style="<?php echo $color; ?>"><b><?php echo $data; ?></b></span>
              </td>
              <td><?=$rows_b->action_on;?></td>
          </tr>

<?php
            }else{
              if ($rows->created_by==$sessionid || $getApproverList==$sessionid) {
?>
            <tr>
              <td><?=$i;?></td>
              <td><?=$dateView;?></td>
              <td><?=$rows->organisation;?></td>
              <td><?=$rows->mstr_type_name;?></td>
              <td><?=$rows->b_on;?></td>
              <td><?=$rows_a->mstr_type_name;?></td>
              <td><?=$rows->b_per;?></td>
              <td style="<?php echo $color; ?>"><?=$status;?></td>
              <td>
                  <?php
                      $empid = $rows->created_by;
                      $deptid = getdeptid($con, $empid);
                      $stage_no = $rows->stage_no;

                      if ($stsVls == 0 || $stsVls == 2) {
                          $data =  payliststatuswith($con, $menuid, $stage_no, $stsVls, '', '', $deptid, '', $empid);
                          $color = 'color:Red';
                      } else {
                          $data = statuswithother($con, 'hr_bonus_request_history', 'br_id', $rows->ids, $stsVls, 'action_by');
                          $color = 'color:Green';
                      }
                  ?>
                  <span style="<?php echo $color; ?>"><b><?php echo $data; ?></b></span>
              </td>
              <td><?=$rows_b->action_on;?></td>
          </tr>

<?php
              }
            }
            $i++;
        }
    }else{
      ?>
          <tr>
              <td class="" colspan="11" style="text-align: center; font-size: 15px;">--- <span style="color: red;">Data Not Found</span> ---</td>
          </tr>
      <?php
    }

?>
    </tbody>
  </table>


<!-- ********************* -->

  <?php
  $content = ob_get_contents();
  ob_end_clean();
  $mpdf->WriteHTML($content);

  //save the file put which location you need folder/filname
  $mpdf->Output($filename.'_'.date("Y-m-d").".pdf", 'D');

  //out put in browser below output function
  //$mpdf->Output();
  exit;

?> 