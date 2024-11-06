<?php
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


   
?>


<style type="text/css">
  /* body {
     font-family: Arial, sans-serif;
     background: url(http://www.shukatsu-note.com/wp-content/uploads/2014/12/computer-564136_1280.jpg) no-repeat;
     background-size: cover;
     height: 100vh;
   }*/

   h1 {
     text-align: center;
     font-family: Tahoma, Arial, sans-serif;
     color: #06D85F;
     margin: 80px 0;
   }

   .box {
     width: 40%;
     margin: 0 auto;
     background: rgba(255,255,255,0.2);
     padding: 35px;
     border: 2px solid #fff;
     border-radius: 20px/50px;
     background-clip: padding-box;
     text-align: center;
   }

   .button {
     font-size: 1em;
     padding: 10px;
     color: #000;
   /* border: 2px solid #06D85F;*/
     border-radius: 20px/50px;
     text-decoration: none;
     cursor: pointer;
     transition: all 0.3s ease-out;
   }
   .button:hover {
     text-decoration: none;
   }

   .overlay {
     position: fixed;
     top: 0;
     bottom: 0;
     left: 0;
     right: 0;
     background: rgba(0, 0, 0, 0.7);
     transition: opacity 500ms;
     visibility: hidden;
     opacity: 0;
   }
   .overlay:target {
     visibility: visible;
     opacity: 1;
   }

   .popup {
     margin: 70px auto;
     padding: 20px;
     background: #fff;
     border-radius: 5px;
     width: 80%;
     position: relative;
     transition: all 5s ease-in-out;
   }

   .popup h2 {
     margin-top: 0;
     color: #333;
     font-family: Tahoma, Arial, sans-serif;
   }
   .popup .close {
     position: absolute;
     top: 20px;
     right: 30px;
     transition: all 200ms;
     font-size: 30px;
     font-weight: bold;
     text-decoration: none;
     color: #333;
   }
   .popup .close:hover {
     color: #06D85F;
   }
   .popup .content {
     max-height: 30%;
     overflow: auto;
   }

   @media screen and (max-width: 700px){
     .box{
       width: 70%;
     }
     .popup{
       width: 70%;
     }
   }
</style>

    <div id="page-wrapper">
        <section class="top-sec">
            <div class="row">
               <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                  <h4 class="">Edit of Bonus History</h4>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                 <h6 class="fw-bolder m text-center text-danger">* Kindly don't refresh the page while Entering the Details.</h6>
              </div>
              <div class="col-lg-2 col-md-2 col-sm-1 col-xs-1">
                 <div class="list_icon text-right mt-1">
                     <a href="<?php if($viewid==1){echo 'view_bonus_request_list.php?ids='.$ids.'&viewid='.$viewid;}else if($viewid==2){echo 'view_bonus_request_list.php?ids='.$ids.'&viewid='.$viewid;}?>"><i class="fa-solid fa-square-left fa-2xl"></i></a>
                   </div>
              </div>
           </div>



           <div class="row">
                   <!-- *********************** -->
                     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <!-- <div class="text-right">
                           Emp. Name : <input type="" id="empNameCheckInVew">
                        </div> -->
                        <div class="table-responsive">
                          <table class="table table-striped table-bordered table-hover">
                             <thead class="bg-dark">
                                <tr>
                                   <th style="width: 53px;">#</th>
                                   <th>Date</th>
                                   <th>Organisation</th>
                                   <th>Bonus Type</th>
                                   <th>Bonus On</th>
                                   <th>Bonus Mode</th>
                                   <th>Bonus(%)</th>
                                   <th>Status</th>
                                   <th>Status Details</th>
                                   <!-- <th>Status Dt.</th> -->
                                   <th>Action</th>
                                </tr>
                             </thead>
                             <tbody id="">

                  <?php
                     $bnsQry = mysqli_query($con,"SELECT a.id as editIds,a.created_on as created_at,a.*,b.*,c.*,d.* FROM hr_bonus_request_edit a, prj_organisation b, master_type_dtls c, mstr_emp d WHERE a.org_id=b.id AND a.created_by=d.id AND a.b_type=c.mstr_type_value AND a.br_id='$ids'");
                     $bnsQry_results = mysqli_num_rows($bnsQry);
                     if ($bnsQry_results>0) {
                        $cnt = 1;
                        while ($rows = mysqli_fetch_object($bnsQry)) {
                           $bnsQry_a = mysqli_query($con,"SELECT * FROM master_type_dtls WHERE mstr_type_value='$rows->b_mode'");
                           $rows_a=mysqli_fetch_object($bnsQry_a);
                           // fullname
                           $getApproverList = getApproverList($con, $menuid, $rows->stage_no);
                           $refid = $ids;
                           $getfield = "remarks"; //to fetch approved by id from details table
                           $dateView = date('d-m-Y', strtotime($rows->created_at));
                           $stsVls = $rows->b_status;
                           $refcolmn = "act_status";
                           if($stsVls == '0'){
                              $status = 'Request Raised';
                              $color = 'color:Orange';
                           }else{
                              $status =  getstatus($con, 'hr_bonus_request_history', 'br_id', $refid, $getfield, $refcolmn);
                              $color = 'color:green';
                           }
                  ?>
                              <tr>
                                 <td><?=$cnt?></td>
                                 <td><?=$dateView;?></td>
                                 <td><?=$rows->organisation?></td>
                                 <td><?=$rows->mstr_type_name?></td>
                                 <td><?=$rows->b_on?></td>
                                 <td><?=$rows_a->mstr_type_name?></td>
                                 <td><?=$rows->b_per?></td>
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
                                          $data = statuswithother($con, 'hr_bonus_request_edit', 'id', $rows->editIds, $stsVls, 'action_by');
                                          $color = 'color:Green';
                                       }
                                    ?>
                                    <span style="<?php echo $color; ?>"><b><?php echo $data; ?></b></span>
                                 </td>
                                 <!-- <td></td> -->
                                 <td><button type="button" onclick="empListModal(<?=$cnt?>, <?=$rows->editIds?>)" style="cursor: pointer;"><a class="button" href="#popup1">Emp. List</a></button></td>
                              </tr>
                  <?php
                           $cnt++;
                        }
                     }else{
                  ?>
                              <tr>
                                 <td colspan="11" class="text-center">--- Data Not Found ---</td>
                              </tr>
                  <?php
                     }
                  ?>

                             </tbody>
                          </table>
                       </div>
                    </div>
                  <!-- *********************** -->
                </div>
              <!-- table Design --> 

              
            </div>
        </section>
    </div>
    <!-- /#page-wrapper -->



<!-- **********Modal Start -->
<div id="popup1" class="overlay">
   <div class="popup">
      <h3>Employee's List</h3>
      <a class="close" href="#">&times;</a>
      <div class="content">
         <table class="table table-striped table-bordered table-hover">
         <thead>
            <tr>
               <th>Sl. No</th>
               <!-- <th>Emp. Id</th> -->
               <th>Emp. Name</th>
               <th>Bonus On</th>
               <th>Salary</th>
               <th>Bonus (%)</th>
               <th>Qty</th>
               <th>Bonus</th>
            </tr>
         </thead>
         <tbody id="empListEditBody">
         </tbody>
      </table>
      </div>
   </div>
</div>
<!-- **********Modal End -->


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

   });
</script>


<script type="text/javascript">


function empListModal(cnt, ids){
   $.ajax({
      url:'php_function/searching.php',
      type:'POST',
      data:{action:'getEmpListEdit',ids:ids},
      success:function(values){
         // console.log('values :- '+values);
         var jsonData = JSON.parse(values);
         var tag = '';
         if (jsonData.length>0) {
            for (var i = 0; i < jsonData.length; i++) {
               var counter = i+1;
               tag += '<tr>';
                  tag += '<td>'+counter+'</td>';
                  // tag += '<td>'+jsonData[i]['emp_sl_id']+'</td>';
                  tag += '<td>'+jsonData[i]['fullname']+'</td>';
                  tag += '<td>'+jsonData[i]['bns_on']+'</td>';
                  tag += '<td>'+jsonData[i]['bns_rate']+'</td>';
                  tag += '<td>'+jsonData[i]['bns_pre']+'</td>';
                  tag += '<td>'+jsonData[i]['bns_days']+'</td>';
                  tag += '<td>'+jsonData[i]['bns_amt']+'</td>';
               tag += '</tr>';
            }
         }else{
            tag += '<tr><td colspan="8" style="text-align: center;">---Data Not Found---</td></tr>';
         }
         $('#empListEditBody').html(tag);
      }
   });
}
</script>
