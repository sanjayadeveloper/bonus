<?php
$filename='employeePresentList.csv';
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Type: application/csv");

// require_once('../../auth.php');
require_once('../../config.php');

    $emp_name = $_GET['emp_name'];
    $yr_mnth_a = $_GET['yr_mnth'];
    $yr_mnth = explode('-', $yr_mnth_a);
    
    $file = fopen($filename, 'w');
    $column_headers = array("Sl.No.", "Emp Name", "Date", "Actually Present", "Off Day", "OD", "Full Live", "Half Leave", "Total Present", "For Bonus Present", "Net Pay", "Bonus Amount");
    fputcsv($file, $column_headers);


        $qry = "SELECT y.id as empID, y.fullname as name, z.ctc_pay as totalctc, z.gross_pay as grs, z.basic_pay as basics, z.net_pay as netpay FROM hr_employee_service_register x, mstr_emp y, hr_employee_salary_report z WHERE ref_id = 0 AND x.emp_name = y.id AND y.status='1' AND y.id='$emp_name' AND y.id=z.emp_id UNION (SELECT y.id as empID, y.fullname as name, z.ctc_pay as totalctc, z.gross_pay as grs, z.basic_pay as basics, z.net_pay as netpay FROM hr_employee_service_register x, mstr_emp y, hr_employee_salary_report z WHERE ref_id <> 0 AND y.mstr_ref_id = x.ref_id AND y.status='1' AND y.id='$emp_name' AND y.id=z.emp_id)";
        $orgQry_a = mysqli_query($con, $qry);
        $datas = mysqli_fetch_object($orgQry_a);
        $empSalary = $datas->netpay;

        
    
        /*
            $empPresentList = "SELECT DISTINCT pdate FROM hr_attendance_mstr WHERE pdate >= '2023-04-01' AND pdate <= '2024-03-31' AND mstr_emp_id = '810'
            AND ((DAYNAME(pdate) not in ('Sunday') AND ((p_in_time!='00:00:00' AND in_app_status='1') OR (m_in_time!='00:00:00' AND (in_app_status='3' OR (in_app_status IN ('0','4') AND p_in_time!='00:00:00'))) OR ((od_in_time!='0000-00-00 00:00:00' AND od_in_time IS NOT NULL)))) OR (DAYNAME(pdate) in ('Sunday') AND ((p_in_time!='00:00:00' OR p_in_time='00:00:00') OR (m_in_time!='00:00:00' OR m_in_time='00:00:00')  OR (od_in_time!='0000-00-00 00:00:00' OR od_in_time='0000-00-00 00:00:00' OR od_in_time IS NULL))))
            AND pdate NOT IN (SELECT x.dated FROM hr_common_holidays x, hr_holidays y WHERE x.unique_id=y.unique_id AND y.comm_app_status = '1')
            AND pdate NOT IN (SELECT j.dated FROM hr_addional_holidays j, hr_holidays k WHERE j.unique_id=k.unique_id AND k.add_app_status = '1')
            AND EXISTS(SELECT y.dated FROM hr_leave_app x, hr_chk_leave y WHERE x.unique_id=y.unique_id AND x.name='$mstr_id' AND  y.dated = hr_attendance_mstr.pdate AND (y.second_half='2nd_half' OR y.second_half='0.5') AND x.first_approve_status='1' AND (x.cncl_status='0' OR x.cncl_status='2'))";
        */
        $empPresentList = "SELECT * FROM `hr_attendance_monthly_details` WHERE ((`pyear`='$yr_mnth[0]' AND `pmonth`>='04') OR (`pyear`='$yr_mnth[1]' AND `pmonth`<='03')) AND `mstr_id`='$emp_name'";
        $listQry = mysqli_query($con, $empPresentList);
        $sl_no = '0';
        $pmonth='0';
        while($row = mysqli_fetch_object($listQry)){
            $sl_no++;
            $dates = $row->pyear.'-'.$row->pmonth;
            $bonus_present = $row->pd - ($row->fl + $row->hl);

            if($sl_no==1){
                $pmonth = $row->pmonth;
                $months = $pmonth;
                $lastMnths = '';
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

            $getEmpSalary = $empSalary/$days*$bonus_present;

            $column_values = [$sl_no, $row->emp_name, $dates, $row->present, $row->offday, $row->od, $row->fl, $row->hl, $row->pd, $bonus_present, $empSalary, $getEmpSalary];
            fputcsv($file, $column_values);
        }

    //Data

    


    fclose($file);
    // Download

    readfile($filename);

    // deleting file
    unlink($filename);
    exit();

?>