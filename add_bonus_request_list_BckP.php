
<?php
require_once('../../auth.php');
require_once('../../config.php');
require_once '../../new_header.php';
include_once('../../workflownotif.php');
include_once('../../approvalmatrixfunction.php');
include_once('php_function/bonus_function.php');

// include_once('dbconnect.php');
// $rowsQry = $conn_obj->getNumRows("*","hr_bonus","");

?>
<style>
.toggles {
    display: flex;
    flex-direction: column;
    position: absolute;
    background: #efefef;
    min-width: 200px;
    padding: 10px 20px;
    border-radius: 10px;
    right: 0;
    box-shadow: 0 10px 20px -10px rgba(0, 0, 0, 0.1);
}
.table_column_filter,.table_column_filter1,.table_column_filter2{
    right: 0;
    padding: 0;
    box-shadow: unset;
    border: unset;
    top: 60px;
    position: absolute;
    z-index: 9999;
    display: none;
}
.table_column_filter.open,.table_column_filter1.open,.table_column_filter2.open{
    display:block;
}

/* #listing_tableID th,
#listing_tableID td {
    display: none;
} */

#listing_tableID th.actv,
#listing_tableID td.actv,
#listing_tableID1 th.actv,
#listing_tableID1 td.actv,
#listing_tableID2 th.actv,
#listing_tableID2 td.actv {
    display: table-cell;
}
/**.table_column_filter */
.table_column_filter .toggles input, .table_column_filter1 .toggles input, .table_column_filter2 .toggles input {
    margin: 0;
    margin-right: 10px;
}
.table_column_filter .toggles span, .table_column_filter1 .toggles span, .table_column_filter2 .toggles span {
  font-size: 12px;
}
</style>

    <div id="page-wrapper"> 
        <section class="top-sec animatePageIn" id="pageContent">

            <input type="hidden" id="orgName">
            <input type="hidden" id="frmDates">
            <input type="hidden" id="toDates">
            <input type="hidden" id="bonusTypes">
            <input type="hidden" id="bonusModes">

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mt-1">
                  <div class="panel tabbed-panel panel-info">
                     <div class="panel-heading clearfix p-0">
                        <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 pull-left px-2">
                            
                           <ul class="nav nav-tabs">
                                <li class="active" onclick="activeMenu('All')"><a href="#listing_reqtab" data-toggle="tab" aria-expanded="true">All <?=$rowsQry;?><span class="badge"><?php echo $count = allcount($con, $sessionid); ?></span></a></li>
                                <li class="" onclick="activeMenu('Approved')"><a href="#listing_reqtab2" data-toggle="tab" aria-expanded="false">Approved <span class="badge"><?php echo $count =  checkbonuscount($con, $sessionid, '1'); ?></span></a></li>
                                <li class="" onclick="activeMenu('Reject')"><a href="#listing_reqtab6" data-toggle="tab" aria-expanded="false">Reject <span class="badge"><?php echo $count =  checkbonuscount($con, $sessionid, '6'); ?></span></a></li>
                           </ul>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <h5 class="fw-bolder m-0 pt-1 text-center">Bonus Requested List</h5>
                            <!-- <marquee><p class="mb-0 text-danger">Listing Design Listing Design</p></marquee> -->
                        </div>
                        <div class=" col-lg-4 col-md-4 col-sm-6 col-xs-12 panel-title pull-right p-0">
                           <div class="list_icon text-right mr-1">
                                <a class="btn btn-social-icon" data-toggle="tooltip" data-placement="top" title="Download PDF" onclick="allDownloadWithPDF()"><i class="fa-solid fa-file-pdf" style="color: #c12f2f;"></i></a>
                                <a class="btn btn-social-icon" data-toggle="tooltip" data-placement="top" title="Download Excel with all field" onclick="allDownloadWithExcel()"><i class="fa-sharp fa-solid fa-file-excel fa-lg" style="color: #28c76f;"></i></a>
                               <a href="bonus_request_add.php" class="btn btn-primary btn-xs fw-bolder">Add Bonus Request</a>
                                <a href="index.php"><i class="fa-duotone fa-square-left fa-2xl"></i></a>
                            </div>
                        </div>
                     </div>

                     <div class="panel-body p-0">
                        <div class="tab-content">
                           <div class="tab-pane fade active in" id="listing_reqtab">
                               <div class="panel-heading">
                                   <div class="row">
                                      <form class="" name="searchBonuslist" id="searchBonuslist">
                                        <input type="hidden" id="activeMenus" value="All">
                                           <div class="form-row">
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Form Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="frm_date" class="form-control frm_date" id="id_date" value="<?=$frm_date;?>" placeholder="Form Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">To Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="to_date" class="form-control to_date" id="id_1" value="<?=$to_date;?>" placeholder="To Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned By</label> -->
                                                   <select name="org_name" id="org_name" class="form-control selectized">
                                                        <option value="" selected="selected">Organisation Name</option>
                                                <?php
                                                    $orgQry = mysqli_query($con,"SELECT * FROM `prj_organisation` ORDER BY id DESC");
                                                    while ($orgRows=mysqli_fetch_object($orgQry)) {
                                                ?>
                                                        <option value="<?=$orgRows->id;?>"><?=$orgRows->organisation;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned By</label> -->
                                                   <select name="bonus_type" id="bonus_type" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Type</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonustype' ORDER BY `id` DESC");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($bonus_type==$fetch->mstr_type_value){ echo 'selected';}?> ><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned To</label> -->
                                                   <select name="bonus_mode" id="bonus_mode" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Mode</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonusmode'");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($bonus_mode==$fetch->mstr_type_value){ echo 'selected';}?>><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-success form-control" onClick="bonuslistSrcBtnFns()">Search</button>
                                                </div>
                                             </div>
                                             <!-- <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                    <div class="num_rows">
                                                        <select class="form-control" name="state" id="maxRows">
                                                           <option value="10">10</option>
                                                           <option value="25">25</option>
                                                           <option value="50">50</option>
                                                           <option value="100">100</option>
                                                           <option value="200">200</option>
                                                           <option value="5000">Show ALL Rows</option>
                                                        </select>
                                                  </div>
                                             </div> -->
                                             <!-- <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                                                <div class="form-group input-group">
                                                      <input type="text" id="search_input_all" onkeyup="FilterkeyWord_all_table()" placeholder="Search.." class="form-control">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" type="button"><i class="fa-duotone fa-magnifying-glass"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                             </div> -->

                                             <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                <div class="dropdown">
                                                        <span class="table_filtericon mb-1" style="width: fit-content; margin-left: auto; display: block;"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-filter btn btn-success form-control" aria-hidden="true"></i>
                                                        </span>
                                                    <div class="table_column_filter">
                                                        <div class="toggles">
                                                            <label>
                                                                <input type="checkbox" value="all" >
                                                                <span>All</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                             </div>
                                           </div>
                                      </form>
                                   </div>
                               </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover w-100" id="listing_tableID">
                                        <thead class="bg-dark">
                                            <tr>
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
                                                <th class="text-center sorting">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="approvedBody">

                        <?php
                            // $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.*,b.*,c.*,c.id as empIds FROM hr_bonus a, master_type_dtls b, mstr_emp c WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id $allQryVls ORDER BY a.id DESC");
                            $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) GROUP BY a.b_id ORDER BY a.id DESC");
                            $bnsQry_results = mysqli_num_rows($bnsQry);
                            if ($bnsQry_results>0) {
                                $counter=0;
                                while($rows=mysqli_fetch_object($bnsQry)){
                                    
                                    //********Approved User Access
                                    $getApproverList = getApproverList($con, $menuid, $rows->stage_no);
                                    // if ($rows->created_by==$sessionid || $getApproverList==$sessionid) {
                                    //********Approved User Access

                                    $counter++;
                                    $refid = $rows->ids;
                                    $getfield = "remarks"; //to fetch approved by id from details table
                                    $dateView = date('d-m-Y', strtotime($rows->created_at));
                                    $stsVls = $rows->b_status;
                                    $refcolmn = "act_status";

                                    // echo 'empIds :- '.$rows->empIds;
                                    // echo 'hr_bonus_history, bns_id, '. $refid.', '.$getfield.', '.$refcolmn.'---';

                                    if($stsVls == '0'){
                                          $status = 'Request Raised';
                                          $color = 'color:Orange';
                                    }else{
                                        $status =  getstatus($con, 'hr_bonus_request_history', 'br_id', $refid, $getfield, $refcolmn);
                                        $color = 'color:green';
                                    }
                                    $bnsQry_a = mysqli_query($con,"SELECT * FROM master_type_dtls WHERE mstr_type_value='$rows->b_mode'");
                                    $rows_a=mysqli_fetch_object($bnsQry_a);

                                    $bnsQry_b = mysqli_query($con,"SELECT * FROM hr_bonus_request_history WHERE br_id='$rows->ids' ORDER BY id DESC LIMIT 1"); //*****New Added
                                    $rows_b=mysqli_fetch_object($bnsQry_b);
                        ?>
                                            <tr>
                                                <td><?=$counter;?></td>
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

                                                        // echo $menuid.', '.$stage_no.', '.$stsVls.', '.''.', '.''.', '.$deptid.', '.''.', '.$empid.'<br/>';

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
                                                <td class="text-center">
                                                    <?php if (($stsVls == '0' || $stsVls == '3') && $rows->created_by == $sessionid) { ?>
                                                        <a href="bonus_request_edit.php?ids=<?php echo $rows->ids; ?>&viewid=1" style="text-decoration:none;" class="btn btn-warning btn-xs"><b>Edit</b></a>
                                                    <?php } ?>
                                                    <?php
                                                        if($stsVls == '3'){
                                                            if($rows->created_by == $sessionid){
                                                    ?>
                                                        <a href="view_bonus_request_list.php?ids=<?=$rows->ids;?>&viewid=1" class="btn btn-primary btn-xs fw-bolder">View</a>
                                                    <?php
                                                            }  
                                                        }else{
                                                    ?>
                                                        <a href="view_bonus_request_list.php?ids=<?=$rows->ids;?>&viewid=1" class="btn btn-primary btn-xs fw-bolder">View</a>
                                                    <?php
                                                        }
                                                    ?>
                                                </td>
                                            </tr>

                        <?php
                                    // }
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

                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                      <div class="rows_count">Showing 1 to 10 of 30 entries</div>
                                   </div>

                                   <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 text-right">
                                        <div class='pagination-container'>
                                          <nav>
                                             <ul class="pagination">
                                                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a></li>
                                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item" aria-current="page"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                              </ul>
                                          </nav>
                                        </div>
                                   </div>

                                </div>
                           </div>
                           <div class="tab-pane fade" id="listing_reqtab2">
                                <div class="panel-heading">
                                   <div class="row">
                                      <form class="" name="searchBonuslist1" id="searchBonuslist1" method="POST">
                                        <input type="hidden" id="activeMenus" value="Approved">
                                           <div class="form-row">
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Form Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="appr_frm_date" class="form-control appr_frm_date" id="id_date1" value="<?=$appr_frm_date;?>" placeholder="Form Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">To Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="appr_to_date" class="form-control appr_to_date" id="id_date2" value="<?=$appr_to_date;?>" placeholder="To Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned By</label> -->
                                                   <select name="appr_org_name" id="appr_org_name" class="form-control selectized">
                                                        <option value="" selected="selected">Organisation Name</option>
                                                <?php
                                                    $orgQry = mysqli_query($con,"SELECT * FROM `prj_organisation` ORDER BY id DESC");
                                                    while ($orgRows=mysqli_fetch_object($orgQry)) {
                                                ?>
                                                        <option value="<?=$orgRows->id;?>"><?=$orgRows->organisation;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned By</label> -->
                                                   <select name="appr_bonus_type" id="appr_bonus_type" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Type</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonustype' ORDER BY `id` DESC");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($appr_bonus_type==$fetch->mstr_type_value){ echo 'selected';}?> ><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned To</label> -->
                                                   <select name="appr_bonus_mode" id="appr_bonus_mode" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Mode</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonusmode'");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($appr_bonus_mode==$fetch->mstr_type_value){ echo 'selected';}?>><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-success form-control" onClick="appr_bonuslistSrcBtnFns()">Search</button>
                                                </div>
                                             </div>
                                             <!-- <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                                                <div class="form-group input-group">
                                                      <input type="text" id="search_input_all" onkeyup="FilterkeyWord_all_table()" placeholder="Search.." class="form-control">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" type="button"><i class="fa-duotone fa-magnifying-glass"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                             </div> -->
                                             <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                <div class="dropdown">
                                                         <span class="table_filtericon1 mb-1" style="width: fit-content; margin-left: auto; display: block;"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-filter btn btn-success form-control" aria-hidden="true"></i>
                                                        </span>
                                                    <div class="table_column_filter1">
                                                        <div class="toggles">
                                                            <label>
                                                                <input type="checkbox" value="all" >
                                                                <span>All</span>
                                                            </label>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                             </div>
                                           </div>
                                      </form>
                                   </div>
                               </div>
                                <div class="">
                                    <table class="table table-striped table-bordered table-hover w-100" id="listing_tableID1">
                                        <thead class="bg-dark">
                                            <tr>
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
                                                <th class="text-center sorting">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="appr_approvedBody">

                        <?php
                            // $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.*,b.*,c.*,c.id as empIds FROM hr_bonus a, master_type_dtls b, mstr_emp c WHERE a.b_type=b.mstr_type_value AND a.b_status='1' AND a.created_by=c.id $appr_allQryVls ORDER BY a.id DESC");
                            $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) AND a.b_status='1' GROUP BY a.b_id ORDER BY a.id DESC");
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
                                    $dateView = date('d-m-Y', strtotime($rows->created_at));
                                    $stsVls = $rows->b_status;
                                    $refcolmn = "act_status";

                                    // echo 'empIds :- '.$rows->empIds;
                                    // echo 'hr_bonus_history, bns_id, '. $refid.', '.$getfield.', '.$refcolmn.'---';

                                    if($stsVls == '0'){
                                          $status = 'Request Raised';
                                          $color = 'color:Orange';
                                    }else{
                                        $status =  getstatus($con, 'hr_bonus_request_history', 'br_id', $refid, $getfield, $refcolmn);
                                        $color = 'color:green';
                                    }
                                    $bnsQry_a = mysqli_query($con,"SELECT * FROM master_type_dtls WHERE mstr_type_value='$rows->b_mode'");
                                    $rows_a=mysqli_fetch_object($bnsQry_a);

                                    $bnsQry_b = mysqli_query($con,"SELECT * FROM hr_bonus_request_history WHERE br_id='$rows->ids' ORDER BY id DESC LIMIT 1"); //*****New Added
                                    $rows_b=mysqli_fetch_object($bnsQry_b);
                        ?>
                                            <tr>
                                                <td><?=$counter;?></td>
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

                                                        // echo $menuid.', '.$stage_no.', '.$stsVls.', '.''.', '.''.', '.$deptid.', '.''.', '.$empid.'<br/>';


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
                                                <td class="text-center">
                                                   <a href="view_bonus_request_list.php?ids=<?=$rows->ids;?>&viewid=1" class="btn btn-primary btn-xs fw-bolder">View</a>
                                                </td>
                                            </tr>

                        <?php
                                    }
                                }
                            }else{
                        ?>
                                            <tr>
                                                <td class="" colspan="11" style="text-align: center; font-size: 15px;">--- <span style="color: red;">Data Not found</span> ---</td>
                                            </tr>
                        <?php
                            }
                        ?>

                                        </tbody>
                                    </table>

                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                      <div class="rows_count">Showing 1 to 10 of 30 entries</div>
                                   </div>

                                   <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 text-right">
                                        <div class='pagination-container'>
                                          <nav>
                                             <ul class="pagination">
                                                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a></li>
                                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item" aria-current="page"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                              </ul>
                                          </nav>
                                        </div>
                                   </div>

                                </div>
                           </div>
                           <div class="tab-pane fade" id="listing_reqtab6">
                                <div class="panel-heading">
                                   <div class="row">
                                      <form class="" name="searchBonuslist2" id="searchBonuslist2" method="POST">
                                        <input type="hidden" id="activeMenus" value="Reject">
                                           <div class="form-row">
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Form Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="rej_frm_date" class="form-control rej_frm_date" id="id_date3" value="<?=$rej_frm_date;?>" placeholder="Form Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">To Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="rej_to_date" class="form-control rej_to_date" id="id_date4" value="<?=$rej_to_date;?>" placeholder="To Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned By</label> -->
                                                   <select name="rej_org_name" id="rej_org_name" class="form-control selectized">
                                                        <option value="" selected="selected">Organisation Name</option>
                                                <?php
                                                    $orgQry = mysqli_query($con,"SELECT * FROM `prj_organisation` ORDER BY id DESC");
                                                    while ($orgRows=mysqli_fetch_object($orgQry)) {
                                                ?>
                                                        <option value="<?=$orgRows->id;?>"><?=$orgRows->organisation;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned By</label> -->
                                                   <select name="rej_bonus_type" id="rej_bonus_type" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Type</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonustype' ORDER BY `id` DESC");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($rej_bonus_type==$fetch->mstr_type_value){ echo 'selected';}?> ><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned To</label> -->
                                                   <select name="rej_bonus_mode" id="rej_bonus_mode" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Mode</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonusmode'");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($rej_bonus_mode==$fetch->mstr_type_value){ echo 'selected';}?>><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-success form-control" onClick="rej_bonuslistSrcBtnFns()">Search</button>
                                                </div>
                                             </div>
                                             <!-- <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                                                <div class="form-group input-group">
                                                      <input type="text" id="search_input_all" onkeyup="FilterkeyWord_all_table()" placeholder="Search.." class="form-control">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" type="button"><i class="fa-duotone fa-magnifying-glass"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                             </div> -->
                                             <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                <div class="dropdown">
                                                         <span class="table_filtericon2 mb-1" style="width: fit-content; margin-left: auto; display: block;"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-filter btn btn-success form-control" aria-hidden="true"></i>
                                                        </span>
                                                    <div class="table_column_filter2">
                                                        <div class="toggles">
                                                            <label>
                                                                <input type="checkbox" value="all" >
                                                                <span>All</span>
                                                            </label>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                             </div>
                                           </div>
                                      </form>
                                   </div>
                               </div>
                                <div class="">
                                    <table class="table table-striped table-bordered table-hover w-100" id="listing_tableID2">
                                        <thead class="bg-dark">
                                            <tr>
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
                                                <th class="text-center sorting">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rej_approvedBody">

                        <?php
                            // $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.*,b.*,c.*,c.id as empIds FROM hr_bonus a, master_type_dtls b, mstr_emp c WHERE a.b_type=b.mstr_type_value AND a.b_status='6' AND a.created_by=c.id $rej_allQryVls ORDER BY a.id DESC");
                            $bnsQry = mysqli_query($con,"SELECT a.id as ids,a.created_on as created_at,a.*,b.*,c.*,c.id as empIds,d.* FROM hr_bonus_request a, master_type_dtls b, mstr_emp c, prj_organisation d WHERE a.b_type=b.mstr_type_value AND a.created_by=c.id AND a.org_id=d.id AND a.b_id IN (SELECT id FROM `hr_bonus` WHERE b_status=1) AND a.b_status='6' GROUP BY a.b_id ORDER BY a.id DESC");
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
                                    $dateView = date('d-m-Y', strtotime($rows->created_at));
                                    $stsVls = $rows->b_status;
                                    $refcolmn = "act_status";

                                    // echo 'empIds :- '.$rows->empIds;
                                    // echo 'hr_bonus_history, bns_id, '. $refid.', '.$getfield.', '.$refcolmn.'---';

                                    if($stsVls == '0'){
                                          $status = 'Request Raised';
                                          $color = 'color:Orange';
                                    }else{
                                        $status =  getstatus($con, 'hr_bonus_request_history', 'br_id', $refid, $getfield, $refcolmn);
                                        $color = 'color:green';
                                    }
                                    $bnsQry_a = mysqli_query($con,"SELECT * FROM master_type_dtls WHERE mstr_type_value='$rows->b_mode'");
                                    $rows_a=mysqli_fetch_object($bnsQry_a);

                                    $bnsQry_b = mysqli_query($con,"SELECT * FROM hr_bonus_request_history WHERE br_id='$rows->ids' ORDER BY id DESC LIMIT 1"); //*****New Added
                                    $rows_b=mysqli_fetch_object($bnsQry_b);
                        ?>
                                            <tr>
                                                <td><?=$counter;?></td>
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

                                                        // echo $menuid.', '.$stage_no.', '.$stsVls.', '.''.', '.''.', '.$deptid.', '.''.', '.$empid.'<br/>';

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
                                                <td class="text-center">
                                                   <a href="view_bonus_request_list.php?ids=<?=$rows->ids;?>&viewid=1" class="btn btn-primary btn-xs fw-bolder">View</a>
                                                </td>
                                            </tr>

                        <?php
                                    }
                                }
                            }else{
                        ?>
                                            <tr>
                                                <td class="" colspan="11" style="text-align: center; font-size: 15px;">--- <span style="color: red;">Data Not found</span> ---</td>
                                            </tr>
                        <?php
                            }
                        ?>

                                        </tbody>
                                    </table>

                                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                                      <div class="rows_count">Showing 1 to 10 of 30 entries</div>
                                   </div>

                                   <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 text-right">
                                        <div class='pagination-container'>
                                          <nav>
                                             <ul class="pagination">
                                                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a></li>
                                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item" aria-current="page"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                              </ul>
                                          </nav>
                                        </div>
                                   </div>

                                </div>
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
<script src="js_function/listing_tableID.js"></script>

<script>
$(document).ready(function(){
    inActiveMenuId('All');
});
</script>
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script> -->
<script src="js_function/js_function.js"></script>
<script src="js_function/commom_fn.js"></script>


