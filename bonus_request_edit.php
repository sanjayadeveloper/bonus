<?php
    require_once('../../config.php');
    require_once('../../auth.php');
    require_once('../../authadmin.php');
    // require_once('php_function\commom_fn.php');
    $ERP_SESS_ID = $_SESSION['ERP_SESS_ID'];
    $crntDate = date('Y-m-d');
    $crntMY = date('m-Y');
    $crntYM = date('Y-m');
    $crntM = date('m');
    $crntY = date('Y');
?>
<?php require_once '../../new_header.php' ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" />
<style type="text/css">
    .iti {
    position: relative;
    display: block;
}

.formTitle{
    background: #0b6fcc;
    color: #fff;
    font-size: 15px;
    font-weight: bold;
    padding: 3px 10px;
    border-radius: 15px;
}
.bonusMode{
    background: #9fc5e9;
    color: #000;
    border: 1px solid #000;
    padding: 4.5px 18px;
}
.bonusModeBody{
    border: 1px solid #000;
    padding: 3px 18px;
    font-weight: bold;
}
.modesTitle{
    font-size: 15px;
    text-align: center;
}

.bonusModeBody input{
    margin: 1px 0px 0px -17px;
    position: absolute;
}
.bgColorOfMode{
    background: #cfcccc !important;
}

.hiddenInput{
    display: none;
}

</style>

<?php


if (isset($_GET['ids'])) {
    $bns_id = $_GET['ids'];
    $viewid = $_GET['viewid'];

    $sqlqry = mysqli_query($con, "SELECT * FROM hr_bonus_request WHERE id='$bns_id'");
    $rows = mysqli_fetch_object($sqlqry);
    $up_org_id = $rows->org_id;
    $up_b_type = $rows->b_type;
    $up_b_on = $rows->b_on;
    $b_mode = $rows->b_mode;
    $b_msg = $rows->b_msg;
    $modeQry = mysqli_query($con, "SELECT * FROM master_type_dtls WHERE mstr_type_value = '$b_mode'");
    $rows_a = mysqli_fetch_object($modeQry);
    $up_b_mode = $rows_a->mstr_type_name;
    $up_b_per = $rows->b_per;
    $up_based_on = $rows->based_on;
    $up_b_msg = $rows->b_msg;
    $up_created_by = $rows->created_by;
    $up_remarks = $rows->remarks;

    if ($viewid==1) {
        $linkVls = 'add_bonus_request_list.php';
    }else{
        $linkVls = 'manage_bonus_request_list.php';
    }


}

// ***************************************



$bnsQry = mysqli_query($con,"SELECT * FROM `hr_bonus` WHERE created_by='$ERP_SESS_ID' ORDER BY id DESC LIMIT 1");
$bnsQry_results = mysqli_num_rows($bnsQry);

if($bnsQry_results == 0){
    $btnCheck = 1;
    $Error_message="NO RECORDS FOUND.";
}else{
    $b_rows=mysqli_fetch_object($bnsQry);
    $b_status = $b_rows->b_status;
    $b_mode = $b_rows->b_mode;
    $applicable = $b_rows->applicable;
    $appVls = explode('/', $applicable);
    $appVls_a = explode('-', $applicable);
    if ($appVls[1]!='' || $appVls[1]!=null) {
        $dtVls = $appVls[1].'-'.$appVls[0];
    }else if ($appVls_a[1]!='' || $appVls_a[1]!=null) {
        $dtVls = $appVls_a[0].'-'.$appVls_a[1];
    }
    if ($b_status!=6) {
        if ($crntYM<=$dtVls) {
            $btnCheck = 0;
        }else{
            $btnCheck = 1;
        }
    }else{
        $btnCheck = 1;
    }
}


//********************Approved Start
$aprovQry = mysqli_query($con,"SELECT * FROM `hr_bonus` WHERE created_by='$ERP_SESS_ID' AND b_status=1 ORDER BY id DESC LIMIT 1");
$aprovQry_results = mysqli_num_rows($aprovQry);

if($aprovQry_results == 0){
    $Error_message="NO RECORDS FOUND.";
    $aprovCrntMY = '00-0000';
}else{
    $aprov_rows=mysqli_fetch_object($aprovQry);
    $aprov_mode = $aprov_rows->b_mode;
    switch ($aprov_mode) {
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

    if ($sltMnth==1) {
        $aprovCrntMY = date('Y', strtotime('+1 year'));
        $aprovCrntMY = '01/'.$aprovCrntMY;
    }else{
        $aprovCrntMY = date($sltMnth.'/Y');
    }
    /*
    if ($sltMnth==1) {
        // $aprovCrntMY = date('Y', strtotime('+1 year'));
        $aprovCrntMY = $applicable[0]+1;
        $aprovCrntM = $applicable[1]-1;
        if ($aprovCrntM < 10) {
            $aprovCrntM = '0'.$aprovCrntM;
        }
        $aprovCrntMY = $aprovCrntMY.'-'.$aprovCrntM;
    }else{
        // $aprovCrntMY = date('Y');
        $aprovCrntMY = $applicable[0];
        $aprovCrntMY = $aprovCrntMY.'-'.$sltMnth;
    }
    */
}
//********************Approved End



?>






    <div id="page-wrapper">
        <section class="top-sec">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h4 class="alert-success p-3 fw-bolder">Add Bonus Request
                        <span class="float-right list_icon mt-1">
                            <!-- <a href="##" onclick="javascript:history.go(-1)"><i class="fa-duotone fa-square-left fa-xl"></i></a> -->
                            <a href="<?=$linkVls;?>"><i class="fa-solid fa-square-left fa-2xl"></i></a>
                        </span>
                    </h4>
                </div>
            </div>

        <form class="form-row" name="bonusRequestFormOfEmp" id="bonusRequestFormOfEmp">
            <div class="row">
                <!-- <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12"></div> -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                   <div class="panel panel-default">
                        <div class="panel-body p-1">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin: 5px">
                                <!-- <div class="formTitle">Add Bonus Form</div> -->
                                <br/>
                                 <!-- onSubmit="" -->
                                
                                    <input type="hidden" name="orgIds" id="orgIds">
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <div class="form-group">
                                            <label>Organisation</label>
                                            <select class="form-control" name="orgName" id="orgName" onchange="getMsgOnKeyup(this.form, this.id)" errorMsg="Please select your Organisation...!!">
                                                <option value="">---Select---</option>
                                    <?php
                                        $orgQry = mysqli_query($con,"SELECT * FROM `prj_organisation` ORDER BY id DESC");
                                        while ($orgRows=mysqli_fetch_object($orgQry)) {
                                    ?>
                                                <option value="<?=$orgRows->id;?>" <?php if($up_org_id==$orgRows->id){echo 'selected';}?>><?=$orgRows->organisation;?></option>
                                    <?php
                                        }
                                    ?>
                                            </select>
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <div class="form-group">
                                            <label>Bonus Type</label>
                                            <select class="form-control" name="orgBonusType" id="orgBonusType" onchange="getMsgOnKeyup(this.form, this.id)" errorMsg="Please select your Bonus Type...!!">
                                                <option value="">---Select---</option>
                                                <?php
                                                    $result = mysqli_query($con,"SELECT * FROM `master_type_dtls` WHERE parent_name='master-bonustype' ORDER BY `id` DESC");
                                                    $total_results = mysqli_num_rows($result);

                                                    if($total_results == 0){ $Error_message="NO RECORDS FOUND."; }           // Set For Record Not Found 

                                                    $i=1; 
                                                    while($fetch=mysqli_fetch_object($result)) {
                                                ?>
                                                    <option value="<?php echo $fetch->mstr_type_value;?>" <?php if($up_b_type==$fetch->mstr_type_value){echo 'selected';}?>><?php echo $fetch->mstr_type_name;?></option>
                                                <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <div class="form-group">
                                            <label>Bonus Mode</label>
                                            <input type="text" class="form-control" name="orgBonusMode" id="orgBonusMode" value="<?=$up_b_mode;?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <div class="form-group">
                                            <label>Calculated On</label>
                                            <input type="text" class="form-control" name="orgBonusOn" id="orgBonusOn" value="<?=$up_b_on;?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <div class="form-group">
                                            <label>Based On</label>
                                            <input type="text" class="form-control" name="orgBasedOn" id="orgBasedOn" value="<?=$up_based_on;?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <div class="form-group">
                                            <label>Bonus (%)</label>
                                            <input type="text" class="form-control" name="orgBonusPer" id="orgBonusPer" value="<?=$up_b_per;?>" readonly>
                                        </div>
                                    </div>
                                
                                
                            </div>  
                         </div>
                    </div>
                </div>
            </div>

            <div class="row" style="width: 100%;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="chat-panel panel panel-default">
                       <div class="panel-heading bg-dark">
                            <h5 class="fw-bolder m-0" style="position: absolute;"><i class="fa-duotone fa-rectangle-list fa-beat"></i> Bonus Requested List</h5>
                            <div class="text-right">
                                <span style="font-weight: bold;">Search Emp. Name</span> <b>:</b> <input type="text" class="" name="searchEmpName" id="searchEmpName" style="width: 15%;"> <span style="margin-left:-15px; cursor: pointer; color: red; font-weight: bold; display: none;" id="closeSerBtn">X</span>
                            </div>
                       </div>
                       <!-- /.panel-heading -->
                       <div class="panel-body">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                
                                <div class="table-responsive">
                                    <input type="hidden" id="getEdit_bonus" value="1">
                                    <input type="hidden" id="getEditVlsChk_bonus">
                                    <input type="hidden" id="getEditVls_bonus">
                                    <!-- ************* -->
                                    <input type="hidden" id="getCount">
                                    <input type="hidden" id="idsVls_bonus">
                                    <input type="hidden" id="saveAllIdsVls_bonus">
                                  <table class="table table-striped table-bordered table-hover">
                                     <thead>
                                        <tr class="bg-success">
                                           <th style="width: 73px;"><input type="checkbox" class="bonusAll" id="bonusCheck" value="" onclick="allCheckFn('All','bonus'), saveIdsVls('All', '0','bonus')" style="position: absolute;"> <span style="margin-left:20px;">All</span></th>
                                           <!-- <th style="width: 73px;">Sl.No</th> -->
                                           <!-- <th>Emp Sl. Id.</th> -->
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
                                     <tbody id="orgEmpName">
                                     </tbody>
                                
                                  </table>
                               </div>
                            </div>
                            
                       </div>
                       <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <br/>
                            <div class="form-group">
                                <label>Messages</label>
                                <textarea class="form-control" placeholder="Enter Messages" name="bonusMessages" id="bonusMessages" onkeyup="getMsgOnKeyup(this.form, this.id)" errorMsg="Please select your Messages...!!"><?=$b_msg;?></textarea>
                                
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <br/>
                            <br/>
                            <br/>
                                <input type="hidden" name="hiddenIds" id="hiddenIds" value="<?=$bns_id;?>">
                                <button type="button" class="btn btn-success" onclick="bonusRequestUpdateOfEmp(this.form)">Submit</button> &#x00A0; <span id="submitErrorMsg"></span>
                            
                        </div>
                    </div>
                </div>
            </div>
        </form>

        </section>
    </div>

    <script>
        //countrycode js ------------>
        $(function() {
          $("#country").change(function() {
            let countryCode = $(this).find('option:selected').data('country-code');
            let value = "+" + $(this).val();
            $('#txtPhone').val(value).intlTelInput("setCountry", countryCode);
          });
          
          var code = "+91";
          $('#txtPhone').val(code).intlTelInput();
        });


    </script>
    <!-- /#page-wrapper -->
<?php require_once('../../new_footer.php'); ?>
<script>
$(document).ready(function(){
    getSelectedEmpVls();
});
</script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> -->
<script src="js_function/js_function.js"></script>
<script src="js_function/commom_fn.js"></script>




   
  




