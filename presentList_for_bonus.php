<?php
// presentList_for_bonus.php
// require_once('../../auth.php');
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
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:5px 0px 7px 0px;">
                            <h5 class="fw-bolder m-0 pt-1 text-center">Employee List for Bonus</h5>
                        </div>
                     </div>

                     <div class="panel-body p-0">
                        <div class="tab-content">
                           <div class="tab-pane fade active in" id="listing_reqtab">
                               <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12"></div>
                                        <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
                                            <form class="" name="searchBonuslist" id="searchBonuslist">
                                                <input type="hidden" id="activeMenus" value="All">
                                                <div class="form-row">
                                                    
                                                    <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                                                        <div class="form-group mb-1">
                                                        <!-- <label for="pname">Assigned By</label> -->
                                                        <select name="yr_mnth" id="yr_mnth" class="form-control selectized">
                                                                <option value="" selected="selected">Choose Year</option>
                                                                <option value="2022-2023">2022-23</option>
                                                                <option value="2023-2024">2023-24</option>
                                                        </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                                                        <div class="form-group mb-1">
                                                        <script type="text/javascript">
                                                        $(document).ready(function () {
                                                            $('#emp_name').selectize({
                                                                sortField: 'text'
                                                            });
                                                        });
                                                        </script>	
                                                        <!-- <label for="pname">Assigned By</label> -->
                                                        <select name="emp_name" id="emp_name" class="form-control selectized">
                                                                <option value="" selected="selected">Employee's Name</option>
                                                        <?php
                                                            $empQry = mysqli_query($con,"SELECT * FROM `mstr_emp` ORDER BY fullname ASC");
                                                            while ($empRows=mysqli_fetch_object($empQry)) {
                                                        ?>
                                                                <option value="<?=$empRows->id;?>"><?=$empRows->fullname;?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                        </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                                                        <div class="text-center">
                                                            <button type="button" class="btn btn-success form-control" onClick="allDownloadWithExcel()">Download</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-right:5px;" id="getEmpData">
                                        <!-- <table class="table table-striped table-bordered table-hover" id="listing_tableID">
                                            <thead class="bg-dark">
                                                <tr>
                                                    <th class="sorting">SN</th>
                                                    <th class="text-center sorting">Emp Name</th>
                                                    <th class="text-center sorting">Date</th>
                                                    <th class="text-center sorting">Actually Present</th>
                                                    <th class="text-center sorting">Off Day</th>
                                                    <th class="text-center sorting">OD</th>
                                                    <th class="text-center sorting">Total Present</th>
                                                </tr>
                                            </thead>
                                            <tbody id="getEmpData">
                                            </tbody>
                                        </table> -->
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


<script>
    function allDownloadWithExcel(){
        var yr_mnth = $('#yr_mnth').val();
        var emp_name = $('#emp_name').val();

        // window.location.href="empListBonusExcelDownload.php?emp_name="+emp_name+"&yr_mnth="+yr_mnth;
        
        
        $.ajax({
            url:'getEmployeePresentList.php',
            type:'POST',
            data:{yr_mnth:yr_mnth,emp_name:emp_name},
            success:function(values){
                $('#getEmpData').html(values);
            }
        });
        
        
    }
</script>



<?php require_once('../../new_footer.php'); ?>


