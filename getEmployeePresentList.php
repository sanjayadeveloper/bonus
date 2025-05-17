<?php

require_once('../../auth.php');
require_once('../../config.php');

?>

<table class="table table-striped table-bordered table-hover" id="listing_tableID">
    <thead class="bg-dark">
        <tr>
            <th class="sorting">Sl No.</th>
            <th class="text-center sorting">Emp Name</th>
            <th class="text-center sorting">Date</th>
            <th class="text-center sorting">Actually Present</th>
            <th class="text-center sorting">Off Day</th>
            <th class="text-center sorting">OD</th>
            <th class="text-center sorting">Full Live</th>
            <th class="text-center sorting">Half Leave</th>
            <th class="text-center sorting">Total Present</th>
            <th class="text-center sorting">For Bonus Present</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $emp_name = $_POST['emp_name'];
        $yr_mnth_a = $_POST['yr_mnth'];
        $yr_mnth = explode('-', $yr_mnth_a);

        $qry = "SELECT y.id as empID, y.fullname as name, z.ctc_pay as totalctc, z.gross_pay as grs, z.basic_pay as basics, z.net_pay as netpay FROM hr_employee_service_register x, mstr_emp y, hr_employee_salary_report z WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND y.id='$emp_name' AND y.id=z.emp_id UNION (SELECT y.id as empID, y.fullname as name, z.ctc_pay as totalctc, z.gross_pay as grs, z.basic_pay as basics, z.net_pay as netpay FROM hr_employee_service_register x, mstr_emp y, hr_employee_salary_report z WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND y.id='$emp_name' AND y.id=z.emp_id)";
        $orgQry_a = mysqli_query($con, $qry);
        $datas = mysqli_fetch_object($orgQry_a);
        $empSalary = $datas->netpay;


        $empPresentList = "SELECT * FROM `hr_attendance_monthly_details` WHERE ((`pyear`='$yr_mnth[0]' AND `pmonth`>='04') OR (`pyear`='$yr_mnth[1]' AND `pmonth`<='03')) AND `mstr_id`='$emp_name'";
        $listQry = mysqli_query($con, $empPresentList);
        $sl_no = '0';
        $pmonth='0';
        $ttlPresent = '0';
        $ttloffday = '0';
        $ttlod = '0';
        $ttlfl = '0';
        $ttlhl = '0';
        $ttlpd = '0';
        $ttlbonus_present = '0';
        $bonus_presentVls = '0';
        while($row = mysqli_fetch_object($listQry)){
            $sl_no++;
            $dates = $row->pyear.'-'.$row->pmonth;
            if($row->ao=='0'){
                $bonus_present = ($row->pd - ($row->fl + ($row->hl * 0.5))) + 2;
            }else{
                $bonus_present = $row->pd - ($row->fl + ($row->hl * 0.5));
            }
            $bonus_presentVls = $bonus_presentVls + $bonus_present;

            if($sl_no=='1'){
                $pmonth = $row->pmonth;
                $months = $pmonth;
                $days = 0;
                for ($i=$months; $i <= 15; $i++) {
                    $years_a = $yr_mnth[0];
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
            }

            $getEmpSalary_a = $empSalary/$days*$bonus_presentVls;
            $getEmpSalary = number_format($getEmpSalary_a, 2);
            // $empSalary, $getEmpSalary


            $ttlPresent = $ttlPresent+$row->present;
            $ttloffday = $ttloffday+$row->offday;
            $ttlod = $ttlod+$row->od;
            $ttlfl = $ttlfl+$row->fl;
            $ttlhl = $ttlhl+$row->hl;
            $ttlpd = $ttlpd+$row->pd;
            $ttlbonus_present = $ttlbonus_present+$bonus_present;
    ?>
        <tr>
            <td><?=$sl_no;?></td>
            <td><?=$row->emp_name;?></td>
            <td><?=$dates;?></td>
            <td><?=$row->present;?></td>
            <td><?=$row->offday;?></td>
            <td><?=$row->od;?></td>
            <td><?=$row->fl;?></td>
            <td><?=$row->hl;?></td>
            <td><?=$row->pd;?></td>
            <td><?=$bonus_present;?></td>
        </tr>
    <?php
        }
    ?>
        <tr>
            <td colspan="3">Total</td>
            <td><?=$ttlPresent;?></td>
            <td><?=$ttloffday;?></td>
            <td><?=$ttlod;?></td>
            <td><?=$ttlfl;?></td>
            <td><?=$ttlhl;?></td>
            <td><?=$ttlpd;?></td>
            <td><?=$ttlbonus_present;?></td>
        </tr>
        <tr>
            <td colspan="6"></td>
            <td style="text-align:right; font-weight:bold;">Total Salary</td>
            <td><?=$empSalary;?></td>
            <td style="text-align:right; font-weight:bold;">Total Bonus Salary</td>
            <td><?=$getEmpSalary;?></td>
        </tr>

    </tbody>
</table>

