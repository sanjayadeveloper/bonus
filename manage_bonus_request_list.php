<?php
require_once('../../auth.php');
require_once('../../config.php');
require_once '../../new_header.php';
include_once('../../workflownotif.php');
include_once('../../approvalmatrixfunction.php');
include_once('php_function/bonus_function.php');


$sessionid = $_SESSION['ERP_SESS_ID'];
$maintable = "hr_bonus";
$histrytable = "hr_bonus_history";
$status = "act_status";
$stage = "stage_no";
$created_by = "created_by";
$pending_count = gettotal_pendingcount($con, $maintable, $status, $stage, $menuid, '', $created_by, $histrytable);
if ($pending_count[0] > 0) {
    $pcount = $pending_count[0];
} else {
    $pcount = 0;
}

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
.table_column_filter3,.table_column_filter4,.table_column_filter5{
    right: 0;
    padding: 0;
    box-shadow: unset;
    border: unset;
    top: 60px;
    position: absolute;
    z-index: 9999;
    display: none;
}
.table_column_filter3.open,.table_column_filter4.open,.table_column_filter5.open{
    display:block;
}

/* #listing_tableID th,
#listing_tableID td {
    display: none;
} */

#listing_tableID3 th.actv,
#listing_tableID3 td.actv,
#listing_tableID4 th.actv,
#listing_tableID4 td.actv,
#listing_tableID5 th.actv,
#listing_tableID5 td.actv {
    display: table-cell;
}
/**.table_column_filter */
.table_column_filter3 .toggles input, .table_column_filter4 .toggles input, .table_column_filter5 .toggles input {
    margin: 0;
    margin-right: 10px;
}
.table_column_filter3 .toggles span, .table_column_filter4 .toggles span, .table_column_filter5 .toggles span {
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
                             <!-- echo 'pcount :- '.$pcount; -->
                                <li class="active" onclick="activeMenu('Pending')"><a href="#listing_reqtab3" data-toggle="tab" aria-expanded="false">Pending <span class="badge"><?php echo $count =  checkpendingcount($con, $sessionid, '0,2');?></span></a></li>
                                <li class="" onclick="activeMenu('Re-Check')"><a href="#listing_reqtab4" data-toggle="tab" aria-expanded="false">Re-Check <span class="badge"><?php echo $count =  checkbonuscount($con, $sessionid, '3'); ?></span></a></li>
                                <li class="" onclick="activeMenu('Hold')"><a href="#listing_reqtab5" data-toggle="tab" aria-expanded="false">Hold <span class="badge"><?php echo $count =  checkbonuscount($con, $sessionid, '4'); ?></span></a></li>
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
                            </div>
                        </div>
                     </div>

                     <div class="panel-body p-0">
                        <div class="tab-content">
                           <div class="tab-pane fade active in" id="listing_reqtab3">
                                <div class="panel-heading">
                                   <div class="row">
                                      <form class="" name="searchBonuslist3" id="searchBonuslist3">
                                           <div class="form-row">
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Form Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="pnd_frm_date" class="form-control pnd_frm_date" id="id_date1" value="<?=$pnd_frm_date;?>" placeholder="Form Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">To Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="pnd_to_date" class="form-control pnd_to_date" id="id_date2" value="<?=$pnd_to_date;?>" placeholder="To Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned By</label> -->
                                                   <select name="pnd_org_name" id="pnd_org_name" class="form-control selectized">
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
                                                   <select name="pnd_bonus_type" id="pnd_bonus_type" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Type</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonustype' ORDER BY `id` DESC");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($pnd_bonus_type==$fetch->mstr_type_value){ echo 'selected';}?> ><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned To</label> -->
                                                   <select name="pnd_bonus_mode" id="pnd_bonus_mode" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Mode</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonusmode'");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($pnd_bonus_mode==$fetch->mstr_type_value){ echo 'selected';}?>><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-success form-control" onClick="manageBonusBodyFn(1)">Search</button>
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
                                                         <span class="table_filtericon3 mb-1" style="width: fit-content; margin-left: auto; display: block;"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-filter btn btn-success form-control" aria-hidden="true"></i>
                                                        </span>
                                                    <div class="table_column_filter3">
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
                                    <div id="skDataTable1" class="skDataTable">
                                    <table class="table table-striped table-bordered table-hover w-100" id="listing_tableID3">
                                        <thead class="bg-dark">
                                            <tr>
                                                <th class="sorting">SN</th>
                                                <th class="text-center sorting">Req. No</th>
                                                <th class="text-center sorting">Date</th>
                                                <th class="text-center sorting">Organisation</th>
                                                <th class="text-center sorting">Bonus Type</th>
                                                <th class="text-center sorting">Bonus On</th>
                                                <th class="text-center sorting">Bonus Mode</th>
                                                <th class="text-center sorting">No. of Emp</th>
                                                <th class="text-center sorting">Amount</th>
                                                <th class="text-center sorting">Status</th>
                                                <th class="text-center sorting">Status By</th>
                                                <th class="text-center sorting">Status Dt.</th>
                                                <th class="text-center sorting">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pnd_approvedBody">
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                           </div>
                           <div class="tab-pane fade" id="listing_reqtab4">
                                <div class="panel-heading">
                                   <div class="row">
                                      <form class="" name="searchBonuslist4" id="searchBonuslist4">
                                           <div class="form-row">
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Form Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="rechk_frm_date" class="form-control rechk_frm_date" id="id_date3" value="<?=$rechk_frm_date;?>" placeholder="Form Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">To Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="rechk_to_date" class="form-control rechk_to_date" id="id_date4" value="<?=$rechk_to_date;?>" placeholder="To Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned By</label> -->
                                                   <select name="rechk_org_name" id="rechk_org_name" class="form-control selectized">
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
                                                   <select name="rechk_bonus_type" id="rechk_bonus_type" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Type</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonustype' ORDER BY `id` DESC");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($rechk_bonus_type==$fetch->mstr_type_value){ echo 'selected';}?> ><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned To</label> -->
                                                   <select name="rechk_bonus_mode" id="rechk_bonus_mode" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Mode</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonusmode'");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($rechk_bonus_mode==$fetch->mstr_type_value){ echo 'selected';}?>><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                <div class="text-center">
                                                    <!-- <button type="button" class="btn btn-success form-control" onClick="rechk_bonuslistSrcBtnFns()">Search</button> -->
                                                    <button type="button" class="btn btn-success form-control" onClick="manageBonusBodyFn(2)">Search</button>
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
                                                         <span class="table_filtericon4 mb-1" style="width: fit-content; margin-left: auto; display: block;"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-filter btn btn-success form-control" aria-hidden="true"></i>
                                                        </span>
                                                    <div class="table_column_filter4">
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
                                    <div id="skDataTable2" class="skDataTable">
                                    <table class="table table-striped table-bordered table-hover w-100" id="listing_tableID4">
                                        <thead class="bg-dark">
                                            <tr>
                                                <th class="sorting">SN</th>
                                                <th class="text-center sorting">Req. No</th>
                                                <th class="text-center sorting">Date</th>
                                                <th class="text-center sorting">Organisation</th>
                                                <th class="text-center sorting">Bonus Type</th>
                                                <th class="text-center sorting">Bonus On</th>
                                                <th class="text-center sorting">Bonus Mode</th>
                                                <th class="text-center sorting">No. of Emp</th>
                                                <th class="text-center sorting">Amount</th>
                                                <th class="text-center sorting">Status</th>
                                                <th class="text-center sorting">Status By</th>
                                                <th class="text-center sorting">Status Dt.</th>
                                                <th class="text-center sorting">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rechk_approvedBody">
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                           </div>
                           <div class="tab-pane fade" id="listing_reqtab5">
                                <div class="panel-heading">
                                   <div class="row">
                                      <form class="" name="searchBonuslist5" id="searchBonuslist5">
                                           <div class="form-row">
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Form Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="hold_frm_date" class="form-control hold_frm_date" id="id_date5" value="<?=$hold_frm_date;?>" placeholder="Form Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">To Date</label> -->
                                                   <div class="input-group">
                                                      <input type="text" name="hold_to_date" class="form-control hold_to_date" id="id_date6" value="<?=$hold_to_date;?>" placeholder="To Date">
                                                      <span class="input-group-addon"><i class="fa-duotone fa-calendar-days" style="--fa-primary-color: #0a0a0a; --fa-secondary-color: #030303;"></i></span>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned By</label> -->
                                                   <select name="hold_org_name" id="hold_org_name" class="form-control selectized">
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
                                                   <select name="hold_bonus_type" id="hold_bonus_type" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Type</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonustype' ORDER BY `id` DESC");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($hold_bonus_type==$fetch->mstr_type_value){ echo 'selected';}?> ><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group mb-1">
                                                   <!-- <label for="pname">Assigned To</label> -->
                                                   <select name="hold_bonus_mode" id="hold_bonus_mode" class="form-control selectized">
                                                      <option value="" selected="selected">Bonus Mode</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonusmode'");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($hold_bonus_mode==$fetch->mstr_type_value){ echo 'selected';}?>><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                                   </select>
                                                </div>
                                             </div>
                                             <div class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                                                <div class="text-center">
                                                    <!-- <button type="button" class="btn btn-success form-control" onClick="hold_bonuslistSrcBtnFns()">Search</button> -->
                                                    <button type="button" class="btn btn-success form-control" onClick="manageBonusBodyFn(3)">Search</button>
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
                                                         <span class="table_filtericon5 mb-1" style="width: fit-content; margin-left: auto; display: block;"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-filter btn btn-success form-control" aria-hidden="true"></i>
                                                        </span>
                                                    <div class="table_column_filter5">
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
                                    <div id="skDataTable3" class="skDataTable">
                                    <table class="table table-striped table-bordered table-hover w-100" id="listing_tableID5">
                                        <thead class="bg-dark">
                                            <tr>
                                                <th class="sorting">SN</th>
                                                <th class="text-center sorting">Req. No</th>
                                                <th class="text-center sorting">Date</th>
                                                <th class="text-center sorting">Organisation</th>
                                                <th class="text-center sorting">Bonus Type</th>
                                                <th class="text-center sorting">Bonus On</th>
                                                <th class="text-center sorting">Bonus Mode</th>
                                                <th class="text-center sorting">No. of Emp</th>
                                                <th class="text-center sorting">Amount</th>
                                                <th class="text-center sorting">Status</th>
                                                <th class="text-center sorting">Status By</th>
                                                <th class="text-center sorting">Status Dt.</th>
                                                <th class="text-center sorting">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="hold_approvedBody">
                                        </tbody>
                                    </table>
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
    inActiveMenuId('Pending');
});
</script>

<script type="text/javascript" src="js_function/pagination_fn.js"></script>
<script>
function addPageVls(){
    localStorage.setItem('pageVls','mng');
}
function allFinalFn(funNum){
    manageBonusBodyFn(funNum);
}
</script>

<script src="js_function/js_function.js"></script>
<script src="js_function/commom_fn.js"></script>




