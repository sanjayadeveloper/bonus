<?php
require_once('../../auth.php');
require_once('../../config.php');
require_once '../../new_header.php';
include_once('../../workflownotif.php');
include_once('../../approvalmatrixfunction.php');
include_once('php_function/bonus_function.php');


$ERP_SESS_ID = $_SESSION['ERP_SESS_ID'];
$sessionid = $ERP_SESS_ID;
$crntDate = date('Y-m-d');
$crntMY = date('m-Y');
$crntM = date('m');
$crntY = date('Y');


if (isset($_GET['ids'])) {
   $ids = $_GET['ids'];
   $viewid = $_GET['viewid'];
}


   $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.stage_no as stageNo,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.id='$ids' AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) GROUP BY a.b_id ORDER BY a.id DESC");
   $bnsQry_results = mysqli_num_rows($bnsQry);
      if ($bnsQry_results>0) {
         $counter=0;
         $rows=mysqli_fetch_object($bnsQry);
         $counter++;
         //************
         $refid = $ids;
         $reqNo = $rows->req_no;
         $getfield = "remarks";
         $stsVls = $rows->b_status;
         $refcolmn = "act_status";
         $dateView = date('d-m-Y', strtotime($rows->created_at));
         $gettable = 'hr_bonus_request_history';
         $colfield1 = 'br_id';
         $colfield2 = 'created_by';//reffernece tbale column name
         $colfield3 = 'act_status';//reffernce table status column name
         if($stsVls == '0'){
            $status = 'Request Raised';
         }else{
            $status =  getstatus($con, $gettable, $colfield1, $refid, $getfield, $refcolmn);
         }
         //************
         $bnsQry_a = mysqli_query($con,"SELECT * FROM master_type_dtls WHERE mstr_type_value='$rows->b_mode'");
         $rows_a=mysqli_fetch_object($bnsQry_a);

         $b_date = $rows->b_date;
         $b_type = $rows->b_type;
         $b_on = $rows->b_on;
         $b_mode = $rows->b_mode;
         $b_applicable = $rows->applicable;
         $b_per = $rows->b_per;

         //************
         $deptid = getdeptid($con, $empid);
         $stage_no = $rows->stageNo;

         if ($stsVls == 0 || $stsVls == 2) {
            $data =  payliststatuswith($con, $menuid, $stage_no, $stsVls, '', '', $deptid, '', $empid);
         } else {
            $data = statuswithother($con, $gettable, $colfield1, $rows->ids, $stsVls, 'action_by');
         }
         //************
         

         //************
         $approver = checkbutton($con, $menuid, $stage_no, $stsVls, '', '', $deptid, '',$gettable = '',$colfield1 = '',$refdata1 = '',$colfield2 = '',$colfield3 = '',$created_by='');
         $approvers_array = explode(",", $approver);
         $cntapprvrs = count($approvers_array);
         // print_r($approvers_array);
         //************
         // $stage_no = $stage_no+1;

         $oqry = "SELECT SUM(bns_amt) as salary FROM hr_bonus_emp_list WHERE ebr_id='$refid'";
         $orgQry = mysqli_query($con, $oqry);
         $orgQry_a = mysqli_fetch_object($orgQry);
         $orgSalary = $orgQry_a->salary;
         $orgSalary = round($orgSalary, 2);
         $oqry_a = "SELECT * FROM hr_bonus_emp_list WHERE ebr_id='$refid'";
         $orgQry_b = mysqli_query($con, $oqry_a);
         $emplist = mysqli_num_rows($orgQry_b);


      }

// echo "approvers_array :- <pre>";
// print_r($approvers_array);
// echo "</pre>";

//************Approved Submit Start
// if (isset($_POST['appr_submit'])) {
//    $appr_reason = $_POST['appr_reason'];
//    $b_sts_vls = $_POST['b_sts_vls'];

//    $insertQry = mysqli_query($con, "INSERT INTO hr_bonus_history(`bns_id`, `bh_date`, `bh_type`, `bh_on`, `bh_mode`, `bh_applicable`, `bh_per`, `action_by`, `action_on`, `reason`) VALUES('$ids','$b_date','$b_type','$b_on','$b_mode','$b_applicable','$b_per','$','$','$')");

// }
//************Approved Submit End


//************Recheck Submit Start
// if (isset($_POST['recheck_submit'])) {
//    $recheck_reason = $_POST['recheck_reason'];
//    $bns_id = $_POST['bns_id'];
//    $b_sts_vls = $_POST['b_sts_vls'];
// }
//************Recheck Submit End


?>


    <div id="page-wrapper">
        <section class="top-sec">
            <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                  <h4 class="">View Page</h4>
               </div>
               <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                  <h6 class="fw-bolder m text-center text-danger">* Kindly don't refresh the page while Entering the Details.</h6>
               </div>
               <div class="col-lg-2 col-md-2 col-sm-1 col-xs-1">
                  <div class="list_icon text-right mt-1">
                     <!--<a href="edit_bonus_list_history.php?ids=<?=$ids;?>&viewid=<?=$viewid;?>" class="btn btn-danger btn-xs fw-bolder">Edit History</a>-->
                     <a href="<?php if($viewid==1){echo 'add_bonus_request_list.php';}else if($viewid==2){echo 'manage_bonus_request_list.php';}?>"><i class="fa-solid fa-square-left fa-2xl"></i></a>
                  </div>
               </div>
            </div>
            <div class="row">
         
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
                   <h5 class="fw-bolder border-bottom">Basic:</h5>
                   <form name="form" method="post" class="forms-sample">
                      <div class="form-row mt-1">
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="enter_by">Req No. :</label>
                               <?=$reqNo;?>
                            </div>
                         </div>
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="enter_by">Organisation :</label>
                               <?=$rows->organisation;?>
                            </div>
                         </div>
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="date">Date : </label>
                              <?=$dateView;?>                         
                            </div>
                         </div>
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="customer_id">Bonus Type : </label>
                               <?=$rows->mstr_type_name;?>                          
                            </div>
                         </div>
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="po_date">Bonus On : </label>
                               <?=$rows->b_on;?>
                            </div>
                         </div>
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="customer_id">Bonus Mode : </label>
                               <?=$rows_a->mstr_type_name;?>
                            </div>
                         </div>
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="compny_addrss">No. of Emp : </label>
                               <?=$emplist;?>
                            </div>
                         </div>
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="compny_addrss">Amount : </label>
                               <?=$orgSalary;?>
                            </div>
                         </div>
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                           <div class="form-group">
                              <label for="group_subtype">Approved By : </label>
                              <?php echo $data; ?>
                           </div>
                         </div>
                         <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="group_subtype">Status : </label>
                               <?=$status;?>
                            </div>
                         </div>
                         <!--<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                           <div class="form-group">
                              <label for="group_subtype">Status Date : </label>
                              <?php //echo $rows->b_status_dt; ?>
                           </div>
                         </div>
                         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                               <label for="group_subtype">Remark : </label>
                               <?php //echo $rows->remarks;?>
                            </div>
                         </div>-->
                      </div>
                   </form>

                   <!-- *********************** -->
                   <div class="row">
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <!-- <div class="text-right">
                           Emp. Name : <input type="" id="empNameCheckInVew">
                        </div> -->
                        <div class="table-responsive">
                          <table class="table table-striped table-bordered table-hover">
                             <thead class="bg-dark">
                                <tr>
                                   <th style="width: 53px;">#</th>
                                   <th>Request No</th>
                                   <th>Name of the person</th>
                                   <th>Designation</th>
                                   <th>Dept</th>
                                   <th>Location</th>
                                   <th>Bonus On</th>
                                   <th>Rate</th>
                                   <th>Bonus(%)</th>
                                   <th>Qty</th>
                                   <th>Amount</th>
                                </tr>
                             </thead>
                             <tbody id="viewSingleBonusEmpList">
                             </tbody>
                          </table>
                       </div>
                    </div>
                  </div>
                  <!-- *********************** -->
                </div>
              <!-- table Design --> 

              


              <!-- *******Approval Section Start****** -->
              
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <?php
               if (in_array($sessionid, $approvers_array) && ($stsVls == 0 || $stsVls == 2 || $stsVls == 4)) { ?>
                <div class="panel panel-default">
                  <div class="panel-heading clearfix p-1 bg-dark">
                      <div class="pull-left">
                           <div class="nav nav-pills">
                              <?php
                                if ($cntapprvrs != 0 && $stsVls != 1 && $stsVls != 6 && $stsVls != 4) {
                              ?>
                              <a class="btn btn-success" href="#approvel-pills" role="button" data-toggle="tab" aria-expanded="false">Approvel</a>
                              <a class="btn btn-primary" href="#recheck-pills" role="button" data-toggle="tab" aria-expanded="false">Recheck</a>
                              <a class="btn btn-warning" href="#hold-pills" role="button" data-toggle="tab" aria-expanded="false" style="color: #000;">Hold</a>
                              <?php }else if (in_array($sessionid, $approvers_array) && $stsVls == 4 && $stsVls != 6 && $cntapprvrs != 0) { ?>
                                    <a class="btn btn-info" href="#release-pills" role="button" data-toggle="tab" aria-expanded="false" style="color: #000;">Release</a>
                                <?php }?>
                              <a class="btn btn-danger" href="#reject-pills" role="button" data-toggle="tab" aria-expanded="false">Reject</a>
                           </div>
                     </div>
                      <div class="panel-title pull-right p-0">
                        <div class="list_icon text-right">
                             <!-- <a class="btn btn-social-icon" data-toggle="tooltip" data-placement="top" title="Download PDF"><i class="fa-solid fa-file-pdf" style="color: #c12f2f;"></i></a>
                             <a class="btn btn-social-icon" data-toggle="tooltip" data-placement="top" title="Download Excel"><i class="fa-sharp fa-solid fa-file-excel fa-lg" style="color: #28c76f;"></i></a> -->
                        </div>
                      </div>
                  </div>
                  <!-- /.panel-heading -->
                  <div class="panel-body p-1">
                     <div class="tab-content p-0">
                        <?php
                           if ($stsVls != 4) {
                        ?>
                        <div class="tab-pane fade active in" id="approvel-pills">
                           <form method="POST" name="approveformdata" id="approveformdata">
                           <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                 <div class="form-group">
                                    <label for="appr_reason"><h5 class="fw-bolder m-0">Approver Reason</h5></label>
                                    <textarea class="form-control" name="appr_reason" id="appr_reason" rows="3" style="resize: none;" required="" placeholder=""></textarea>
                                 </div>
                                 <div class="col-md-12" id="errorappr_msg" style="display:none;" align="center">
                                    <strong style="color:red;font-size: 10px;" id="appr_msgmsg"></strong>
                                 </div>
                              </div>
                               <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 mt-4">
                                 <input type="hidden" name="bns_id" value="<?php echo $refid; ?>" id="bns_id">
                                 <input type="hidden" name="menu_id" value="<?php echo $menuid; ?>" id="menu_id">
                                 <input type="hidden" name="status" value="<?php echo $stsVls; ?>" id="status">
                                 <input type="hidden" name="stage_no" value="<?php echo $stage_no; ?>" id="stage_no">
                                 <input type="hidden" name="creator" value="<?php echo $empid; ?>" id="creator">
                                 <button type="button" name="approve_Submit" id="approve_Submit" value="SUBMIT" class="btn btn-success mr-2 mt-4">SUBMIT</button>
                              </div>
                           </div>
                           </form>
                        </div>
                        <div class="tab-pane fade" id="recheck-pills">
                           <form method="POST" name="recheckformdata" id="recheckformdata">
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                 <div class="form-group">
                                    <label for="recheck_reason"><h5 class="fw-bolder m-0">Recheck Reason</h5></label>
                                    <textarea class="form-control" name="recheck_reason" id="recheck_reason" rows="3" style="resize: none;" required="" placeholder=""></textarea>
                                 </div>
                                 <div class="col-md-12" id="errorrecheck_msg" style="display:none;" align="center">
                                    <strong style="color:red;font-size: 10px;" id="recheck_msgmsg"></strong>
                                 </div>
                              </div>
                               <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 mt-4">
                                 <input type="hidden" name="bns_id" value="<?php echo $refid; ?>" id="bns_id">
                                 <input type="hidden" name="menu_id" value="<?php echo $menuid; ?>" id="menu_id">
                                 <input type="hidden" name="status" value="<?php echo $stsVls; ?>" id="status">
                                 <input type="hidden" name="stage_no" value="<?php echo $stage_no; ?>" id="stage_no">
                                 <input type="hidden" name="creator" value="<?php echo $empid; ?>" id="creator">
                                 <button type="button" name="recheck_Submit" id="recheck_Submit" value="SUBMIT" class="btn btn-success mr-2 mt-4">SUBMIT</button>
                              </div>
                           </div>
                           </form>
                        </div>
                        <div class="tab-pane fade" id="hold-pills">
                           <form method="POST" name="holdformdata" id="holdformdata">
                            <div class="row">
                              <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                 <div class="form-group">
                                    <label for="hold_reason"><h5 class="fw-bolder m-0">Hold Days</h5></label>
                                    <select class="form-control" name="hold_days" id="hold_days">
                                       <option value="">---Select---</option>
                                       <option value="1">1 Day</option>
                                       <option value="2">2 Days</option>
                                       <option value="3">3 Days</option>
                                       <option value="4">4 Day</option>
                                       <option value="5">5 Days</option>
                                       <option value="6">6 Days</option>
                                       <option value="7">7 Day</option>
                                       <option value="8">8 Days</option>
                                       <option value="9">9 Days</option>
                                       <option value="10">10 Days</option>
                                    </select>
                                 </div>
                                 <div class="col-md-12" id="errorholdday_msg" style="display:none;" align="center">
                                    <strong style="color:red;font-size: 10px;" id="holddays_msg"></strong>
                                 </div>
                              </div>
                              <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                                 <div class="form-group">
                                    <label for="hold_reason"><h5 class="fw-bolder m-0">Hold Reason</h5></label>
                                    <textarea class="form-control" name="hold_reason" id="hold_reason" rows="3" style="resize: none;" required="" placeholder=""></textarea>
                                 </div>
                                 <div class="col-md-12" id="errorhold_msg" style="display:none;" align="center">
                                    <strong style="color:red;font-size: 10px;" id="hold_msg"></strong>
                                 </div>
                              </div>
                               <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 mt-4">
                                 <input type="hidden" name="bns_id" value="<?php echo $refid; ?>" id="bns_id">
                                 <input type="hidden" name="menu_id" value="<?php echo $menuid; ?>" id="menu_id">
                                 <input type="hidden" name="status" value="<?php echo $stsVls; ?>" id="status">
                                 <input type="hidden" name="stage_no" value="<?php echo $stage_no; ?>" id="stage_no">
                                 <input type="hidden" name="creator" value="<?php echo $empid; ?>" id="creator">
                                 <button type="button" name="hold_Submit" id="hold_Submit" value="SUBMIT" class="btn btn-success mr-2 mt-4">SUBMIT</button>
                              </div>
                           </div>
                           </form>
                        </div>
                        <?php
                           } else if ($stsVls == 4 ) {
                        ?>
                        <div class="tab-pane fade active in" id="release-pills">
                           <form method="POST" name="releaseformdata" id="releaseformdata">
                           <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                 <div class="form-group">
                                    <label for="release_reason"><h5 class="fw-bolder m-0">Release Reason</h5></label>
                                    <textarea class="form-control" name="release_reason" id="release_reason" rows="3" style="resize: none;" required="" placeholder=""></textarea>
                                 </div>
                                 <div class="col-md-12" id="errorrelease_msg" style="display:none;" align="center">
                                    <strong style="color:red;font-size: 10px;" id="release_msgmsg"></strong>
                                 </div>
                              </div>
                               <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 mt-4">
                                 <input type="hidden" name="bns_id" value="<?php echo $refid; ?>" id="bns_id">
                                 <input type="hidden" name="menu_id" value="<?php echo $menuid; ?>" id="menu_id">
                                 <input type="hidden" name="status" value="<?php echo $stsVls; ?>" id="status">
                                 <input type="hidden" name="stage_no" value="<?php echo $stage_no; ?>" id="stage_no">
                                 <input type="hidden" name="creator" value="<?php echo $empid; ?>" id="creator">
                                 <button type="button" name="release_Submit" id="release_Submit" value="SUBMIT" class="btn btn-success mr-2 mt-4">SUBMIT</button>
                              </div>
                           </div>
                           </form>
                        </div>
                        <?php
                           }
                        ?>

                        <div class="tab-pane fade" id="reject-pills">
                           <form method="POST" name="rejectformdata" id="rejectformdata">
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                 <div class="form-group">
                                    <label for="reject_reason"><h5 class="fw-bolder m-0">Reject Reason</h5></label>
                                    <textarea class="form-control" name="reject_reason" id="reject_reason" rows="3" style="resize: none;" required="" placeholder=""></textarea>
                                 </div>
                                 <div class="col-md-12" id="errorreject_msg" style="display:none;" align="center">
                                    <strong style="color:red;font-size: 10px;" id="reject_msgmsg"></strong>
                                 </div>
                              </div>
                               <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 mt-4">
                                 <input type="hidden" name="bns_id" value="<?php echo $refid; ?>" id="bns_id">
                                 <input type="hidden" name="menu_id" value="<?php echo $menuid; ?>" id="menu_id">
                                 <input type="hidden" name="status" value="<?php echo $stsVls; ?>" id="status">
                                 <input type="hidden" name="stage_no" value="<?php echo $stage_no; ?>" id="stage_no">
                                 <input type="hidden" name="creator" value="<?php echo $empid; ?>" id="creator">
                                 <button type="button" name="reject_Submit" id="reject_Submit" value="SUBMIT" class="btn btn-success mr-2 mt-4">SUBMIT</button>
                              </div>
                           </div>
                           </form>
                        </div>
                     </div>
                  </div>
                  <!-- /.panel-body -->
               </div>
               <?php } ?>
              </div>
              <!-- *******Approval Section End****** -->



              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
               <div class="chat-panel panel panel-default">
                   <div class="panel-heading bg-dark">
                      <h5 class="fw-bolder m-0"><i class="fa-duotone fa-rectangle-list fa-beat"></i> Approved List:</h5>
                   </div>
                   <!-- /.panel-heading -->
                   <div class="panel-body">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                          <div class="table-responsive">
                              <table class="table table-striped table-bordered table-hover">
                                 <thead>
                                    <tr class="bg-success">
                                       <th style="width: 73px;">Sl.NO</th>
                                       <th>Status</th>
                                       <th>By</th>
                                       <th>Date</th>
                                       <th>Message</th>
                                       <!-- <th>Device ID</th> -->
                                       <th>IP Address</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                              <?php
                                 $aprovQry = mysqli_query($con,"SELECT b.*,c.* FROM hr_bonus_request_history b, mstr_emp c WHERE b.action_by=c.id AND b.br_id='$ids'");
                                 $aprovQry_results = mysqli_num_rows($aprovQry);
                                 if ($aprovQry_results==0) {
                              ?>
                                    <tr>
                                       <td class="" colspan="4" style="text-align: center; font-size: 15px;">--- <span style="color: red;">Data Not Found</span> ---</td>
                                    </tr>
                              <?php
                                 }else{
                                    $counter=0;
                                    while($aprov_rows=mysqli_fetch_object($aprovQry)){
                                        $counter++;
                                        if ($aprov_rows->act_status==0) {
                                            $color = 'orange';
                                            if($aprov_rows->remarks=='Request Update'){
                                            $color = '#db9514';
                                        }
                                        }else if ($aprov_rows->act_status==1) {
                                            $color = 'green';
                                        }else if ($aprov_rows->act_status==2) {
                                            $color = '#0b9574';
                                        }else if ($aprov_rows->act_status==3) {
                                            $color = '#404bf8';
                                        }else if ($aprov_rows->act_status==4) {
                                            $color = '#9615d3';
                                        }else if ($aprov_rows->act_status==6) {
                                            $color = 'red';
                                        }
                              ?>
                                    <tr>
                                       <td><?=$counter;?></td>
                                       <td style="font-weight:bold; color: <?=$color?>"><?=$aprov_rows->remarks;?></td>
                                       <td><?=$aprov_rows->fullname;?></td>
                                       <td><?=$aprov_rows->action_on;?></td>
                                       <td><?=$aprov_rows->reason;?></td>
                                       <td><?=$aprov_rows->ip_addr;?></td>
                                    </tr>
                              <?php
                                       // }
                                    }
                                 }
                              ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                   </div>
               </div>
              </div>

            </div>
        </section>
    </div>
    <!-- /#page-wrapper -->




<?php require_once('../../new_footer.php'); ?>

  <!--  <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">              
         <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
           <img src="" class="imagepreview" style="width: 100%;" >
         </div>
       </div>
     </div>
   </div> -->
  
<script>
   $(function () {
      $('[data-toggle="tooltip"]').tooltip()
   });

// modalimages js
// $(function() {
//       $('.pop').on('click', function() {
//          $('.imagepreview').attr('src', $(this).find('img').attr('src'));
//          $('#imagemodal').modal('show');   
//       });      
// });


</script>
   
 <script type="text/javascript">
   $(document).ready(function(){
      ezoom.onInit($('#imgDiv img'), {
         hideControlBtn: false,
         onClose: function (result) {
            console.log(result);
         },
         onRotate: function (result) {
            console.log(result);
         },
      });

      //****************Bonus Employee's List
      getEmpListOfViewSingleBonus();

   });
</script>


<script type="text/javascript">

function getEmpListOfViewSingleBonus(empNames='0'){
   var bnsReqIds = <?=$ids?>;
   var viewid = <?=$viewid?>;
   $.ajax({
       url: "php_function/ajax_bonus_status_updt.php",
       data: {action:'getEmpListOfSingleBonus',bnsReqIds:bnsReqIds,empNames:empNames,viewid:viewid},
       type: 'POST',
       success: function(values) {
         var jsonData = JSON.parse(values);
         // console.log('values :- '+jsonData[0]['numRows']);
         // return false;
         var tag = '';
         if (jsonData.length>0) {
            for (var i = 0; i < jsonData.length; i++) {
               var counter = i+1;
               tag += '<tr>';
                  tag += '<td>'+counter+'</td>';
                  tag += '<td>'+jsonData[i]['emp_sl_id']+'</td>';
                  tag += '<td>'+jsonData[i]['empName']+'</td>';
                  tag += '<td>'+jsonData[i]['empDeg']+'</td>';
                  tag += '<td>'+jsonData[i]['dept']+'</td>';
                  tag += '<td>'+jsonData[i]['location']+'</td>';
                  tag += '<td>'+jsonData[i]['b_on']+'</td>';
                  tag += '<td>'+jsonData[i]['salary']+'</td>';
                  tag += '<td>'+jsonData[i]['b_per']+'</td>';
                  tag += '<td>'+jsonData[i]['days']+'</td>';
                  tag += '<td>'+jsonData[i]['getBonus']+'</td>';
               tag += '</tr>';
            }
         }else{
            tag += '<tr><td colspan="9" style="text-align: center;">---Data Not Found---</td></tr>';
         }
         $('#viewSingleBonusEmpList').html(tag);
       }
   });
}


$('#empNameCheckInVew').on('keyup', function(){
   var empName = $(this).val();
   getEmpListOfViewSingleBonus(empName);
});

//****************************

$('#approve_Submit').on('click', function(event) {
   var msg = $('#appr_reason').val();
   var aprform = $('#approveformdata');
   var viewid = <?=$viewid?>;

   if (msg == '') {
      $("#appr_msgmsg").html("! Enter Message!");
      $("#errorappr_msg").show().delay(6000).fadeOut();
      $('#appr_reason').css("border", "1px solid #ec1313");
      $('#appr_reason').focus();
      return false;
   }
   $.ajax({
       url: "php_function/ajax_bonus_status_updt.php",
       data: aprform.serialize() + "&action=br_approve&viewid="+viewid,
       type: 'POST',
       success: function(data) {
        //   console.log(data);
        //   return false;
           if (data == 1) {
               swal({
                   title: 'Bonus Approved',
                   text: 'Successfully Approved !',
                   type: 'success',
                   confirmButtonColor: '#27934b'
               }, function() {
                  if (viewid==1) {
                     window.location = "add_bonus_request_list.php";
                  }else if(viewid==2){
                     window.location = "manage_bonus_request_list.php";
                  }
               });
           }
       }
   });
});
$('#recheck_Submit').on('click', function(event) {
   var msg = $('#recheck_reason').val();
   var aprform = $('#recheckformdata');
   var viewid = <?=$viewid?>;
   if (msg == '') {
      $("#recheck_msgmsg").html("! Enter Message!");
      $("#errorrecheck_msg").show().delay(6000).fadeOut();
      $('#recheck_reason').css("border", "1px solid #ec1313");
      $('#recheck_reason').focus();
      return false;
   }
   $.ajax({
       url: "php_function/ajax_bonus_status_updt.php",
       data: aprform.serialize() + "&action=br_recheck&viewid="+viewid,
       type: 'POST',
       success: function(data) {
        //   console.log(data);
        //   return false;
           if (data == 1) {
               swal({
                   title: 'Bonus Rechecked',
                   text: 'Successfully Rechecked !',
                   type: 'success',
                   confirmButtonColor: '#27934b'
               }, function() {
                  if (viewid==1) {
                     window.location = "add_bonus_request_list.php";
                  }else if(viewid==2){
                     window.location = "manage_bonus_request_list.php";
                  }
               });
           }
       }
   });
});
$('#hold_Submit').on('click', function(event) {
   var hold_days = $('#hold_days').val();
   var msg = $('#hold_reason').val();
   var aprform = $('#holdformdata');
   var viewid = <?=$viewid?>;
   if (hold_days == '') {
      $("#holddays_msg").html("! Select Day!");
      $("#errorholdday_msg").show().delay(6000).fadeOut();
      $('#hold_days').css("border", "1px solid #ec1313");
      $('#hold_days').focus();
      return false;
   }else if (msg == '') {
      $("#hold_msgmsg").html("! Enter Message!");
      $("#errorhold_msg").show().delay(6000).fadeOut();
      $('#hold_reason').css("border", "1px solid #ec1313");
      $('#hold_reason').focus();
      return false;
   }else{
      $.ajax({
          url: "php_function/ajax_bonus_status_updt.php",
          data: aprform.serialize() + "&action=br_hold&="+viewid,
          type: 'POST',
          success: function(data) {
            // console.log(data);
            // return false;
              if (data == 1) {
                  swal({
                      title: 'Bonus Holded',
                      text: 'Successfully Holded !',
                      type: 'success',
                      confirmButtonColor: '#27934b'
                  }, function() {
                     if (viewid==1) {
                        window.location = "add_bonus_request_list.php";
                     }else if(viewid==2){
                        window.location = "manage_bonus_request_list.php";
                     }
                  });
              }
          }
      });
   }
});
$('#reject_Submit').on('click', function(event) {
   var msg = $('#reject_reason').val();
   var aprform = $('#rejectformdata');
   var viewid = <?=$viewid?>;
   if (msg == '') {
      $("#reject_msgmsg").html("! Enter Message!");
      $("#errorreject_msg").show().delay(6000).fadeOut();
      $('#reject_reason').css("border", "1px solid #ec1313");
      $('#reject_reason').focus();
      return false;
   }
   $.ajax({
       url: "php_function/ajax_bonus_status_updt.php",
       data: aprform.serialize() + "&action=br_reject&viewid="+viewid,
       type: 'POST',
       success: function(data) {
           if (data == 1) {
               swal({
                   title: 'Bonus Rejected',
                   text: 'Successfully Rejected !',
                   type: 'success',
                   confirmButtonColor: '#27934b'
               }, function() {
                  if (viewid==1) {
                     window.location = "add_bonus_request_list.php";
                  }else if(viewid==2){
                     window.location = "manage_bonus_request_list.php";
                  }
               });
           }
       }
   });
});
$('#release_Submit').on('click', function(event) {
   var msg = $('#release_reason').val();
   var aprform = $('#releaseformdata');
   var viewid = <?=$viewid?>;
   if (msg == '') {
      $("#release_msgmsg").html("! Enter Message!");
      $("#errorrelease_msg").show().delay(6000).fadeOut();
      $('#release_reason').css("border", "1px solid #ec1313");
      $('#release_reason').focus();
      return false;
   }
   $.ajax({
       url: "php_function/ajax_bonus_status_updt.php",
       data: aprform.serialize() + "&action=br_release&viewid="+viewid,
       type: 'POST',
       success: function(data) {
        //   console.log(data);
        //   return false;
           if (data == 1) {
               swal({
                   title: 'Bonus Released',
                   text: 'Successfully Released !',
                   type: 'success',
                   confirmButtonColor: '#27934b'
               }, function() {
                  if (viewid==1) {
                     window.location = "add_bonus_request_list.php";
                  }else if(viewid==2){
                     window.location = "manage_bonus_request_list.php";
                  }
               });
           }
       }
   });
});
</script>




