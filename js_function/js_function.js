$(document).ready(function(){
    getSessionVls();
    // getActiveMenu();
});

function getSessionVls(){
    $.ajax({
        url:'php_function/add_bonus_request.php',
        type:'POST',
        data:{action:'sessionArrayCheck'},
        success:function(values){
            // console.log('Session Values :- '+values);
        }
    });
}




$('#bonusMode').on('change', function(){
    var ModeVls = $(this).val();
    $.ajax({
        url:'php_function/add_bonus_request.php',
        type:'POST',
        data:{action:'modeCheck',ModeVls:ModeVls},
        success:function(values){
            console.log('values :- '+values);
            var json_data = JSON.parse(values);
            $('.applicableForm').val(json_data['dt']);
            $('#modesTitle').html(json_data['type_name']);
        }
    });
});


//**************** Bonus Request Submit



function getMsgOnKeyupSingle(vls){
    if (vls=='' || vls==null) {
        $('.applicableFormMsg').html('Please select your Applicable From...!!');
    }else{
        $('.applicableFormMsg').html('');
    }
}

function applicableFormFn(applicableVls=''){
    var bonusMode = $('#bonusMode').val();
    var applFormHidden = $('#applFormHidden').val();

    if (bonusMode=='' || bonusMode==null) {
        alert('Please select the "Bonus Mode"...!!');
        return false;
    }else{

        /*
        const date = new Date(); 
        let day= date.getDay();
        let month= date.getMonth()+1;
        let year= date.getFullYear();

        var apVls = applicableVls.split('/'); //------Local
        var months = apVls[0];
        var years = apVls[1];

        //****New Add 29-09-2023
        // var apVls_a = applicableVls.split('-'); //------Live
        // var years = apVls_a[0];
        // var months = apVls_a[1];

        // if (apVls!='' || apVls!=null) {
        //     var months = apVls[0];
        //     var years = apVls[1];
        // }else {
        //     var years = apVls_a[0];
        //     var months = apVls_a[1];
        // }
        //****New Add 29-09-2023


        var validates = false;
        if (months<month) {
            if (years<=year) {
                alert('Please select the correct validate date...!!');
                $('.applicableForm').val(applFormHidden);
                validates = false;
            }else{
                validates = true;
            }
        }else{
            validates = true;
        }
        */

        // const date = new Date(); 
        // let day= date.getDay();
        // let month= date.getMonth()+1;
        // let year= date.getFullYear();

        var appVls = applFormHidden.split('/'); //------Local
        var appmonths = appVls[0];
        var appyears = appVls[1];

        // var appVls = applFormHidden.split('-'); //------Live
        // var appyears = appVls[0];
        // var appmonths = appVls[1];

        var apVls = applicableVls.split('/'); //------Local
        var months = apVls[0];
        var years = apVls[1];

        // var apVls_a = applicableVls.split('-'); //------Live
        // var years = apVls_a[0];
        // var months = apVls_a[1];

        //****New Add 29-09-2023
        // if (apVls!='' || apVls!=null) {
        //     var months = apVls[0];
        //     var years = apVls[1];
        // }else {
        //     var years = apVls_a[0];
        //     var months = apVls_a[1];
        // }
        //****New Add 29-09-2023


        var validates = false; // 17-11-2023
        if (applFormHidden=='0000-00' || applFormHidden=='00-0000') {
            validates = true;
        }else{
            if (months<=appmonths) {
                console.log('111');
                if (years>appyears) {
                    console.log('222');
                    validates = true;
                }else{
                    console.log('333');
                    alert('Please select the correct validate date...!!');
                    $('.applicableForm').val(applFormHidden);
                    validates = false;
                }
            }else{
                // validates = true;
                console.log(years);
                if (years>=appyears) {
                    console.log('555');
                    validates = true;
                }else{
                    console.log('666');
                    alert('Please select the correct validate date...!!');
                    $('.applicableForm').val(applFormHidden);
                    validates = false;
                }
            }
        }

        if (validates==true) {
            if (bonusMode=='monthly') {
               var getVls = getMontsList(months,0);
            }else if (bonusMode=='quarterly') {
                var getVls = getMontsList(months,2);
            }else if (bonusMode=='half yearly') {
                var getVls = getMontsList(months,5);
            }else if (bonusMode=='yearly') {
                var getVls = getMontsList(months,11);
            }

            var mVls = getVls.split(',');
            var tag = '';
            var num = 1;
            for (var i = 0; i < mVls.length; i++) {
                if (num!=mVls.length) {
                    var mVls_a = mVls[i].split('-');
                    tag += '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 bonusModeBody">'+mVls_a[0]+'</div>';
                    tag += '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 bonusModeBody">'+mVls_a[1]+'</div>';
                }
                num++;
            }
            $('#getMonthDiv').html(tag);
        }
    }
}



function bonusRequestSubmit(formName, update=''){
    getMsgOnKeyup(formName);
    var bonusType = $('#bonusType').val();
    var bonusOn = $('#bonusOn').val();
    var bonusMode = $('#bonusMode').val();
    var applicableForm = $('.applicableForm').val();
    var bonusPer = $('#bonusPer').val();
    var basedOn = $('#basedOn').val();
    var bonusReMark = $('#bonusReMark').val();
    var viewids = $('#viewids').val();

    if (bonusType=='' || bonusType==null) {
        getMsgOnKeyup(formName, 'bonusType');
    }
    if (bonusOn=='' || bonusOn==null) {
        getMsgOnKeyup(formName, 'bonusOn');
    }
    if (bonusMode=='' || bonusMode==null) {
        getMsgOnKeyup(formName, 'bonusMode');
    }
    // if (applicableForm=='' || applicableForm==null) {
    //     $('#applicableFormMsg').html('Please select your Applicable From...!!');
    // }
    if (bonusPer=='' || bonusPer==null) {
        getMsgOnKeyup(formName, 'bonusPer');
    }
    if (bonusPer>100) {
        alert('Please enter lessthan 100 in Bonus Field...!!');
        $('#bonusPer').val('');
        $('#bonusPer').focus();
        getMsgOnKeyup(formName, 'bonusPer');
        return false;
    }
    if (basedOn=='' || basedOn==null) {
        getMsgOnKeyup(formName, 'basedOn');
    }
    if (bonusReMark=='' || bonusReMark==null) {
        getMsgOnKeyup(formName, 'bonusReMark');
    }

    if (bonusType!='' && bonusOn!='' && bonusMode!='' && applicableForm!='' && bonusPer!='' && basedOn!='' && bonusReMark!=''){
        var checkVls=1;
    }else{
        var checkVls=0;
    }

    if (checkVls==1) {
        var formData = new FormData(bonusRequestFormData);
        formData.append('action','bonusRequestSubmit');
        if (update=='update') {
            formData.append('update','update');
            var bonus_hidden_id = $('#bonus_hidden_id').val();
            formData.append('bonus_id',bonus_hidden_id);
        }
        $.ajax({
            url:'php_function/add_bonus_request.php',
            type:'POST',
            data:formData,
            contentType:false,
            processData:false,
            success:function(values){
                // console.log(values);
                if (values==1) {
                    if (update=='update') {
                        alert('Form update successfully...!!');
                        if (viewids==1) {
                            document.location.href='bonus1.php';
                        }else if (viewids==2) {
                            document.location.href='bonus2.php';
                        }
                    }else{
                        alert('Form submited successfully...!!');
                        document.location.href='bonus1.php';
                    }
                }else{
                    alert('Form submited not successfull...!!');
                }
            }
        });
    }
}




//************ Excel & PDF Start
function activeMenu(vls){
    localStorage.setItem('actVls',vls);
}
function inActiveMenuId(vls){
    localStorage.setItem('actVls',vls);
}

function allDownloadWithExcel(){
    var actVls = localStorage.getItem('actVls');
    var ths;
    if (actVls=='All') {
        ths = $('#listing_tableID th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Approved'){
        ths = $('#listing_tableID1 th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Reject'){
        ths = $('#listing_tableID2 th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Pending'){
        ths = $('#listing_tableID3 th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Re-Check'){
        ths = $('#listing_tableID4 th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Hold'){
        ths = $('#listing_tableID5 th').map(function () {
            return $(this).text();
        });
    }
    
    var tag = '';
    for (var i = 0; i < ths.length; i++) {
        var cnt = i+1;
        var tagVls = document.getElementById(ths[i]).checked;
        if (tagVls == true) {
            if (ths.length==cnt) {
                tag += ths[i]+'|'+i;
            }else{
                tag += ths[i]+'|'+i+',';
            }
        }
    }

    var actVls, org_name, frm_date, to_date, bonus_type, bonus_mode;
    actVls = localStorage.getItem('actVls');
    org_name = $('#orgName').val();
    frm_date = $('#frmDates').val();
    to_date = $('#toDates').val();
    bonus_type = $('#bonusTypes').val();
    bonus_mode = $('#bonusModes').val();

    window.location.href="excelFileDownload.php?org_name="+org_name+"&frm_date="+frm_date+"&to_date="+to_date+"&bonus_type="+bonus_type+"&bonus_mode="+bonus_mode+"&actVls="+actVls+"&tag="+tag;
}



function allDownloadWithPDF(){
    var actVls = localStorage.getItem('actVls');
    var ths;
    if (actVls=='All') {
        ths = $('#listing_tableID th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Approved'){
        ths = $('#listing_tableID1 th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Reject'){
        ths = $('#listing_tableID2 th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Pending'){
        ths = $('#listing_tableID3 th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Re-Check'){
        ths = $('#listing_tableID4 th').map(function () {
            return $(this).text();
        });
    }
    if(actVls=='Hold'){
        ths = $('#listing_tableID5 th').map(function () {
            return $(this).text();
        });
    }

    var tag = '';
    for (var i = 0; i < ths.length; i++) {
        var cnt = i+1;
        var tagVls = document.getElementById(ths[i]).checked;
        if (tagVls == true) {
            if (ths.length==cnt) {
                tag += ths[i]+'|'+i;
            }else{
                tag += ths[i]+'|'+i+',';
            }
        }
    }

    var actVls, org_name, frm_date, to_date, bonus_type, bonus_mode;
    actVls = localStorage.getItem('actVls');
    org_name = $('#orgName').val();
    frm_date = $('#frmDates').val();
    to_date = $('#toDates').val();
    bonus_type = $('#bonusTypes').val();
    bonus_mode = $('#bonusModes').val();
    
    window.location.href="pdfFileDownload.php?org_name="+org_name+"&frm_date="+frm_date+"&to_date="+to_date+"&bonus_type="+bonus_type+"&bonus_mode="+bonus_mode+"&actVls="+actVls+"&tag="+tag;
}

//************ Excel & PDF End


//************for Pagination Start
function addBonusBodyFn(n){
    var pageVls = localStorage.getItem('pageVls'); //---Mandatory
    var tbodyId = localStorage.getItem(pageVls+'_tbodyId_'+n); //---Mandatory
    var num = localStorage.getItem(pageVls+'_nums_'+n); //---Mandatory
    var getRows = $('#getRowsList'+num).val(); //---Mandatory
    var getRowsSearch = $('#getRowsListSearch'+num).val(); //---Mandatory
    
    //---Mandatory Start, If added search fields
    if (n==1) {
        var org_name = $('#org_name').val();
        var frm_date = $('.frm_date').val();
        var to_date = $('.to_date').val();
        var bonus_type = $('#bonus_type').val();
        var bonus_mode = $('#bonus_mode').val();
    }
    if (n==2) {
        var org_name = $('#appr_org_name').val();
        var frm_date = $('.appr_frm_date').val();
        var to_date = $('.appr_to_date').val();
        var bonus_type = $('#appr_bonus_type').val();
        var bonus_mode = $('#appr_bonus_mode').val();
    }
    if (n==3) {
        var org_name = $('#rej_org_name').val();
        var frm_date = $('.rej_frm_date').val();
        var to_date = $('.rej_to_date').val();
        var bonus_type = $('#rej_bonus_type').val();
        var bonus_mode = $('#rej_bonus_mode').val();
    }
    
    $('#orgName').val(org_name);
    $('#frmDates').val(frm_date);
    $('#toDates').val(to_date);
    $('#bonusTypes').val(bonus_type);
    $('#bonusModes').val(bonus_mode);
// console.log(frm_date);
    if (org_name!='') {  //---Mandatory, If added search fields
        getRows = getRowsSearch;
    }
    //---Mandatory End

    $.ajax({
        url:'php_function/searching.php',
        type:'POST',
        data:{action:'addBonusReqBodyCheck',rows:getRows,num:num,org_name:org_name,frm_date:frm_date,to_date:to_date,bonus_type:bonus_type,bonus_mode:bonus_mode},
        success:function(values){
            $('#'+tbodyId).html(values); //---Mandatory " $('#'+tbodyId).html() "
            var count_rows = $('#count_rows_'+num).val();
            // console.log(count_rows);
            getTableEntriesVls(count_rows,getRows,num); //---Mandatory this function
        }
    });
}


function manageBonusBodyFn(n){
    
    var pageVls = localStorage.getItem('pageVls'); //---Mandatory
    var tbodyId = localStorage.getItem(pageVls+'_tbodyId_'+n); //---Mandatory
    var num = localStorage.getItem(pageVls+'_nums_'+n); //---Mandatory
    var getRows = $('#getRowsList'+num).val(); //---Mandatory
    var getRowsSearch = $('#getRowsListSearch'+num).val(); //---Mandatory
    
    // console.log('pageVls :- '+pageVls+', tbodyId :- '+tbodyId+', num :- '+num+', getRows :- '+getRows+', getRowsSearch :- '+getRowsSearch);
    
    //---Mandatory Start, If added search fields
    if (n==1) {
        var org_name = $('#pnd_org_name').val();
        var frm_date = $('.pnd_frm_date').val();
        var to_date = $('.pnd_to_date').val();
        var bonus_type = $('#pnd_bonus_type').val();
        var bonus_mode = $('#pnd_bonus_mode').val();
    }
    if (n==2) {
        var org_name = $('#rechk_org_name').val();
        var frm_date = $('.rechk_frm_date').val();
        var to_date = $('.rechk_to_date').val();
        var bonus_type = $('#rechk_bonus_type').val();
        var bonus_mode = $('#rechk_bonus_mode').val();
    }
    if (n==3) {
        var org_name = $('#hold_org_name').val();
        var frm_date = $('.hold_frm_date').val();
        var to_date = $('.hold_to_date').val();
        var bonus_type = $('#hold_bonus_type').val();
        var bonus_mode = $('#hold_bonus_mode').val();
    }
    
    $('#orgName').val(org_name);
    $('#frmDates').val(frm_date);
    $('#toDates').val(to_date);
    $('#bonusTypes').val(bonus_type);
    $('#bonusModes').val(bonus_mode);

    if (org_name!='') {  //---Mandatory, If added search fields
        getRows = getRowsSearch;
    }
    //---Mandatory End
    // console.log('8888');
    $.ajax({
        url:'php_function/searching.php',
        type:'POST',
        data:{action:'manageBonusReqBodyCheck',rows:getRows,num:num,org_name:org_name,frm_date:frm_date,to_date:to_date,bonus_type:bonus_type,bonus_mode:bonus_mode},
        success:function(values){
            // console.log(values);
            // return false;
            $('#'+tbodyId).html(values); //---Mandatory " $('#'+tbodyId).html() "
            var count_rows = $('#count_rows_'+num).val();
            // console.log(count_rows);
            getTableEntriesVls(count_rows,getRows,num); //---Mandatory this function
        }
    });
}

//************for Pagination End



function bonuslistSrcBtnFns(){ //approvedBodyFn(n)
    var json_form = new FormData(searchBonuslist);
    json_form.append('action','searchedBonuslist');
    $('#orgName').val(searchBonuslist.org_name.value);
    $('#frmDates').val(searchBonuslist.frm_date.value);
    $('#toDates').val(searchBonuslist.to_date.value);
    $('#bonusTypes').val(searchBonuslist.bonus_type.value);
    $('#bonusModes').val(searchBonuslist.bonus_mode.value);
    $.ajax({
        url:'php_function/searching.php',
        type:'POST',
        data:json_form,
        contentType:false,
        processData:false,
        success:function(values){
            console.log(values);
            document.getElementById('approvedBody').innerHTML=values;
        }
    });
}
function appr_bonuslistSrcBtnFns_bckUp(){
    var json_form = new FormData(searchBonuslist1);
    json_form.append('action','appr_searchedBonuslist');
    $('#orgName').val(searchBonuslist1.appr_org_name.value);
    $('#frmDates').val(searchBonuslist1.appr_frm_date.value);
    $('#toDates').val(searchBonuslist1.appr_to_date.value);
    $('#bonusTypes').val(searchBonuslist1.appr_bonus_type.value);
    $('#bonusModes').val(searchBonuslist1.appr_bonus_mode.value);
    $.ajax({
        url:'php_function/searching.php',
        type:'POST',
        data:json_form,
        contentType:false,
        processData:false,
        success:function(values){
            console.log(values);
            document.getElementById('appr_approvedBody').innerHTML=values;
        }
    });
}
function rej_bonuslistSrcBtnFns(){
    var json_form = new FormData(searchBonuslist2);
    json_form.append('action','rej_searchedBonuslist');
    $('#orgName').val(searchBonuslist2.rej_org_name.value);
    $('#frmDates').val(searchBonuslist2.rej_frm_date.value);
    $('#toDates').val(searchBonuslist2.rej_to_date.value);
    $('#bonusTypes').val(searchBonuslist2.rej_bonus_type.value);
    $('#bonusModes').val(searchBonuslist2.rej_bonus_mode.value);
    $.ajax({
        url:'php_function/searching.php',
        type:'POST',
        data:json_form,
        contentType:false,
        processData:false,
        success:function(values){
            console.log(values);
            document.getElementById('rej_approvedBody').innerHTML=values;
        }
    });
}
function pnd_bonuslistSrcBtnFns(){
    var json_form = new FormData(searchBonuslist3);
    json_form.append('action','pnd_searchedBonuslist');
    $('#orgName').val(searchBonuslist3.pnd_org_name.value);
    $('#frmDates').val(searchBonuslist3.pnd_frm_date.value);
    $('#toDates').val(searchBonuslist3.pnd_to_date.value);
    $('#bonusTypes').val(searchBonuslist3.pnd_bonus_type.value);
    $('#bonusModes').val(searchBonuslist3.pnd_bonus_mode.value);
    $.ajax({
        url:'php_function/searching.php',
        type:'POST',
        data:json_form,
        contentType:false,
        processData:false,
        success:function(values){
            console.log(values);
            document.getElementById('pnd_approvedBody').innerHTML=values;
        }
    });
}
function rechk_bonuslistSrcBtnFns(){
    var json_form = new FormData(searchBonuslist4);
    json_form.append('action','rechk_searchedBonuslist');
    $('#orgName').val(searchBonuslist4.rechk_org_name.value);
    $('#frmDates').val(searchBonuslist4.rechk_frm_date.value);
    $('#toDates').val(searchBonuslist4.rechk_to_date.value);
    $('#bonusTypes').val(searchBonuslist4.rechk_bonus_type.value);
    $('#bonusModes').val(searchBonuslist4.rechk_bonus_mode.value);
    $.ajax({
        url:'php_function/searching.php',
        type:'POST',
        data:json_form,
        contentType:false,
        processData:false,
        success:function(values){
            console.log(values);
            document.getElementById('rechk_approvedBody').innerHTML=values;
        }
    });
}
function hold_bonuslistSrcBtnFns(){
    var json_form = new FormData(searchBonuslist5);
    json_form.append('action','hold_searchedBonuslist');
    $('#orgName').val(searchBonuslist5.hold_org_name.value);
    $('#frmDates').val(searchBonuslist5.hold_frm_date.value);
    $('#toDates').val(searchBonuslist5.hold_to_date.value);
    $('#bonusTypes').val(searchBonuslist5.hold_bonus_type.value);
    $('#bonusModes').val(searchBonuslist5.hold_bonus_mode.value);
    $.ajax({
        url:'php_function/searching.php',
        type:'POST',
        data:json_form,
        contentType:false,
        processData:false,
        success:function(values){
            console.log(values);
            document.getElementById('hold_approvedBody').innerHTML=values;
        }
    });
}



//*********************DaTE


$('#id_date').datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "YYYY-MM-DD",
    //format: "YYYY-MM-DD hh:mm:ss A",
    icons: {
      time:'fa-sharp fa-solid fa-alarm-clock',
      date:'fa-duotone fa-trash-can',
      up:'fa fa-chevron-up',
      down:'fa fa-chevron-down',
      previous:'fa fa-chevron-left',
      next:'fa fa-chevron-right',
      today:'fa fa-chevron-up',
      clear:'fa fa-trash',
      close:'fa fa-close'
    },
});
$('#id_date1').datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "YYYY-MM-DD",
    //format: "YYYY-MM-DD hh:mm:ss A",
    icons: {
      time:'fa-sharp fa-solid fa-alarm-clock',
      date:'fa-duotone fa-trash-can',
      up:'fa fa-chevron-up',
      down:'fa fa-chevron-down',
      previous:'fa fa-chevron-left',
      next:'fa fa-chevron-right',
      today:'fa fa-chevron-up',
      clear:'fa fa-trash',
      close:'fa fa-close'
    },
});
$('#id_date2').datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "YYYY-MM-DD",
    //format: "YYYY-MM-DD hh:mm:ss A",
    icons: {
      time:'fa-sharp fa-solid fa-alarm-clock',
      date:'fa-duotone fa-trash-can',
      up:'fa fa-chevron-up',
      down:'fa fa-chevron-down',
      previous:'fa fa-chevron-left',
      next:'fa fa-chevron-right',
      today:'fa fa-chevron-up',
      clear:'fa fa-trash',
      close:'fa fa-close'
    },
});
$('#id_date3').datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "YYYY-MM-DD",
    //format: "YYYY-MM-DD hh:mm:ss A",
    icons: {
      time:'fa-sharp fa-solid fa-alarm-clock',
      date:'fa-duotone fa-trash-can',
      up:'fa fa-chevron-up',
      down:'fa fa-chevron-down',
      previous:'fa fa-chevron-left',
      next:'fa fa-chevron-right',
      today:'fa fa-chevron-up',
      clear:'fa fa-trash',
      close:'fa fa-close'
    },
});
$('#id_date4').datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "YYYY-MM-DD",
    //format: "YYYY-MM-DD hh:mm:ss A",
    icons: {
      time:'fa-sharp fa-solid fa-alarm-clock',
      date:'fa-duotone fa-trash-can',
      up:'fa fa-chevron-up',
      down:'fa fa-chevron-down',
      previous:'fa fa-chevron-left',
      next:'fa fa-chevron-right',
      today:'fa fa-chevron-up',
      clear:'fa fa-trash',
      close:'fa fa-close'
    },
});
$('#id_date5').datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "YYYY-MM-DD",
    //format: "YYYY-MM-DD hh:mm:ss A",
    icons: {
      time:'fa-sharp fa-solid fa-alarm-clock',
      date:'fa-duotone fa-trash-can',
      up:'fa fa-chevron-up',
      down:'fa fa-chevron-down',
      previous:'fa fa-chevron-left',
      next:'fa fa-chevron-right',
      today:'fa fa-chevron-up',
      clear:'fa fa-trash',
      close:'fa fa-close'
    },
});
$('#id_date6').datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "YYYY-MM-DD",
    //format: "YYYY-MM-DD hh:mm:ss A",
    icons: {
      time:'fa-sharp fa-solid fa-alarm-clock',
      date:'fa-duotone fa-trash-can',
      up:'fa fa-chevron-up',
      down:'fa fa-chevron-down',
      previous:'fa fa-chevron-left',
      next:'fa fa-chevron-right',
      today:'fa fa-chevron-up',
      clear:'fa fa-trash',
      close:'fa fa-close'
    },
});
$('#id_date7').datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "YYYY-MM-DD",
    //format: "YYYY-MM-DD hh:mm:ss A",
    icons: {
      time:'fa-sharp fa-solid fa-alarm-clock',
      date:'fa-duotone fa-trash-can',
      up:'fa fa-chevron-up',
      down:'fa fa-chevron-down',
      previous:'fa fa-chevron-left',
      next:'fa fa-chevron-right',
      today:'fa fa-chevron-up',
      clear:'fa fa-trash',
      close:'fa fa-close'
    },
});
$('#id_date8').datetimepicker({
    allowInputToggle: true,
    showClose: true,
    showClear: true,
    showTodayButton: true,
    format: "YYYY-MM-DD",
    //format: "YYYY-MM-DD hh:mm:ss A",
    icons: {
      time:'fa-sharp fa-solid fa-alarm-clock',
      date:'fa-duotone fa-trash-can',
      up:'fa fa-chevron-up',
      down:'fa fa-chevron-down',
      previous:'fa fa-chevron-left',
      next:'fa fa-chevron-right',
      today:'fa fa-chevron-up',
      clear:'fa fa-trash',
      close:'fa fa-close'
    },
});


//*********************DaTE



//**************************Bonus Request Add (Calculation)***********

$('#orgName').on('change', function(){
    var orgBonusType = $('#orgBonusType').val();
    var orgNameVls = $(this).val();
    getEmpList(orgBonusType, orgNameVls);
});

function getEmpList(orgBonusType, orgNameVls, empName='0'){
    //******* Edit Part Start
    var getEdit_bonus = $('#getEdit_bonus').val();
    if (getEdit_bonus!=undefined && getEdit_bonus!='') {
        var getEdit = 1;
    }else{
        var getEdit = 0;
    }
    //******* Edit Part End
    $.ajax({
        url:'php_function/add_bonus_request.php',
        type:'POST',
        data:{action:'orgNameCheck',orgNameVls:orgNameVls,empName:empName,orgBonusType:orgBonusType,getEdit:getEdit},
        success:function(values){
            // console.log('values :- '+values);
            $('#orgEmpName').html(values);
            // var jsonVls = JSON.parse(values);
            // console.log('values :- '+values);
            localStorage.setItem('modelKey','bonus'); //----
            getIdsVlsOfOrg(orgNameVls, orgBonusType, empName, getEdit);
        }
    });
};

function getIdsVlsOfOrg(orgNameVls, orgBonusType='0', empName='0', getEdit='0'){
    var modelKey = localStorage.getItem('modelKey'); //----
    var getEditChk_bonus = $('#getEditVlsChk_bonus').val(); //******* Edit Part
    var getEdit_bonus = $('#getEdit_bonus').val(); //******* Edit Part
    var getCount = $('#getCount').val(); //******* Edit Part
    var idsList = $('#idsVls_bonus').val();
    var orgBonusOn = $('#orgBonusOn').val();
    var orgBonusPer = $('#orgBonusPer').val();
    $.ajax({
        url:'php_function/add_bonus_request.php',
        type:'POST',
        data:{action:'orgIdsCheck',orgNameVls:orgNameVls,empName:empName,getEdit:getEdit,orgBonusType:orgBonusType},
        success:function(values){
            // console.log('values :- '+values);
            // return false;
            var jsonData = JSON.parse(values);
            $('#getCount').val(jsonData['empArray'].length);
            var saveIds = '';
            var saveIdsEdit = '';
            var chkAlls = 0; //checkboxAllCount
            var chkAllsEdit = 0; //checkboxAllCount
            for (var i = 0; i < jsonData['empArray'].length; i++) {
                var counter = i+1;
                var splVls = jsonData['empArray'][i]['idVls'].split('|');
                console.log('splVls :- '+splVls);
                var idVls = splVls[0];
                var empSlry = splVls[1];
                var empDays = splVls[2];
                var getBonus = splVls[3];

                //*******Add Checkbox checked Start
                    if (jsonData['empArray'].length==counter) {
                        saveIds += idVls;
                    }else{
                        saveIds += idVls+', ';
                    }
                if (getEdit_bonus==undefined) {
                    if (idsList!='') {
                        var listId = $('#listId_'+counter).val();
                        var splListVls = idsList.split(', ');
                        var chkAry = [];
                        for (var a = 0; a < splListVls.length; a++) {
                            if (splListVls[a]!='') {
                                chkAry.push(splListVls[a].trim());
                            }
                        }
                        if (chkAry.includes(listId)==true) {
                            document.getElementById('listId_'+counter).checked=true;
                        }

                        if (document.getElementById('listId_'+counter).checked==true) {
                            chkAlls = chkAlls+1;
                        }

                        // console.log(chkAry.length+'---'+jsonData['empArray'].length+'---'+chkAlls);

                        if (jsonData['empArray'].length!=chkAlls) {
                            document.getElementById('bonusCheck').checked=false;
                        }else{
                            document.getElementById('bonusCheck').checked=true;
                        }
                    }
                }
                //*******Add Checkbox checked End

                if (orgBonusOn!=undefined && orgBonusOn!='' && orgBonusOn!='0') {
                    $('#setBonusOn_'+counter).html(jsonData['orgBTypeVls'][0]['b_on']);
                    $('#setBonusPer_'+counter).html(jsonData['orgBTypeVls'][0]['b_per']);
                    $('#setBonusSalary_'+counter).html(empSlry);
                    $('#setBonusDays1_'+counter).html('<input type="text" id="setBonusDays_'+counter+'" onkeyup="editDays(this.value, '+counter+')" style="width:70px;" value="'+empDays+'">'); //new added 21-10-2024
                    $('#setBonusAmounts_'+counter).html(getBonus);
                }else{
                    $('#setBonusOn_'+counter).html('');
                    $('#setBonusPer_'+counter).html('');
                    $('#setBonusSalary_'+counter).html('');
                    $('#setBonusDays1_'+counter).html('');
                    $('#setBonusAmounts_'+counter).html('');
                }
            }
            if (getEdit_bonus==undefined) {
                $('#saveAllIdsVls_bonus').val('');
                $('#saveAllIdsVls_bonus').val(saveIds);
            }
            //******* Edit Part Start
            if (getEdit_bonus!=undefined) {
                var chkAry = [];
                for (var i = 0; i < jsonData['empArrayEdit'].length; i++) {
                    var counter = i+1;
                    var splVls = jsonData['empArrayEdit'][i]['idVls'].split('|');
                    var idVls = splVls[0];
                    var empSlry = splVls[1];
                    var empDays = splVls[2];
                    var getBonus = splVls[3];
                    // console.log('idVls :- '+idVls);
                    //*******Checkbox checked Start
                    if (jsonData['empArrayEdit'].length==counter) {
                        saveIdsEdit += idVls;
                    }else{
                        saveIdsEdit += idVls+', ';
                    }

                    chkAry.push(idVls.trim());

                    if (document.getElementById('listId_'+counter).checked==true) {
                        chkAllsEdit = chkAllsEdit+1;
                    }

                    // console.log(chkAry.length+'---'+jsonData['empArrayEdit'].length+'---'+chkAllsEdit);

                    if (jsonData['empArrayEdit'].length!=chkAllsEdit) {
                        document.getElementById('bonusCheck').checked=false;
                    }else{
                        document.getElementById('bonusCheck').checked=true;
                    }
                    //*******Checkbox checked End
                }

                var count = '';
                for (var a = 0; a < jsonData['empArray'].length; a++) {
                    var count = a+1;
                    var listIds = $('#listId_'+count).val();
                    if (chkAry.includes(listIds)==true) {
                        document.getElementById('listId_'+count).checked=true;
                    }
                }

//***************17-10
                var getCount = $('#getCount').val();
                var getEditChk_bonus_a = $('#getEditVlsChk_bonus').val(); //******* Edit Part
                var getEditChk_bonus = getEditChk_bonus_a.split(',');
                var getEditVls_bonus_a = $('#getEditVls_bonus').val(); //******* Edit Part
                var getEditVls_bonus = getEditVls_bonus_a.split(',');
                var bnsVlsChk = [];
                var bnsVls = [];
                if (getEditChk_bonus[0]!=undefined && getEditChk_bonus[0]!='') {
                    for (var i = 0; i < getEditChk_bonus.length; i++) {
                        bnsVlsChk.push(getEditChk_bonus[i].trim());
                    }
                }
                if (getEditVls_bonus[0]!=undefined && getEditVls_bonus[0]!='') {
                    for (var i = 0; i < getEditVls_bonus.length; i++) {
                        bnsVls.push(getEditVls_bonus[i].trim());
                    }
                }
                for (var i = 0; i < getCount; i++) {
                    var aa = i+1;
                    var listIdVls = $('#listId_'+aa).val();
                    if (document.getElementById('listId_'+aa).checked==true) {
                        // console.log('getCount1 :- '+bnsVls.includes(listIdVls.trim()));
                        if (bnsVls.includes(listIdVls.trim())==true) {
                            document.getElementById('listId_'+aa).checked=false;
                        }
                    }else if (document.getElementById('listId_'+aa).checked==false) {
                        // console.log('getCount2 :- '+listIdVls.trim());
                        if (bnsVlsChk.includes(listIdVls.trim())==true) {
                            document.getElementById('listId_'+aa).checked=true;
                        }
                    }
                }

                var chc = 0;
                for (var i = 0; i < getCount; i++) {
                    var aa = i+1;
                    if (document.getElementById('listId_'+aa).checked==true) {
                        chc = chc+1;
                    }
                }
                if (chc==getCount) {
                    document.getElementById('bonusCheck').checked=true;
                }else{
                    document.getElementById('bonusCheck').checked=false;
                }

//***************17-10
                
            
                $('#saveAllIdsVls_bonus').val(saveIds);
                $('#idsVls_bonus').val(saveIdsEdit);
                
                //********All Continue ************

            }
            //******* Edit Part End
        }
    });
}

function editDays(vls, slId){
    var orgBonusMode = $('#orgBonusMode').val();
    var b_mode = orgBonusMode.toLowerCase();

    var setBonusPer = $('#setBonusPer_'+slId).text();
    var setBonusSalary_a = $('#setBonusSalary_'+slId).text();
    var setBonusSalary = '';
    if (b_mode=='monthly') {
        $daysCnt = 0;
        setBonusSalary = setBonusSalary_a/12;
    }else if (b_mode=='quarterly') {
        $daysCnt = 2;
        setBonusSalary = setBonusSalary_a/4;
    }else if (b_mode=='half yearly') {
        $daysCnt = 5;
        setBonusSalary = setBonusSalary_a/2;
    }else if (b_mode=='yearly') {
        $daysCnt = 11;
        setBonusSalary = setBonusSalary_a;
    }
    
    var applicable_bonus_date = localStorage.getItem('applicable_bonus_date');
    var a_b_date = applicable_bonus_date.split('-');
    applicable_bonus_date_split(a_b_date[0], a_b_date[1], vls, slId, b_mode, setBonusSalary, setBonusPer);
}

function applicable_bonus_date_split(a_b_date_y, a_b_date_m, dayVls, slId, b_mode, setBonusSalary, setBonusPer){
    $.ajax({
        url:'php_function/add_bonus_request.php',
        type:'POST',
        data:{action:'getbonus_date',a_b_date_y:a_b_date_y,a_b_date_m:a_b_date_m,b_mode:b_mode},
        success:function(values){
            var applicable_bonus_date_split = values;
            console.log('dayVls : '+dayVls+', slId : '+slId+', orgBonusMode : '+b_mode+', setBonusSalary : '+setBonusSalary+', setBonusPer : '+setBonusPer+', applicable_bonus_date_split : '+applicable_bonus_date_split+'----');

            var getEmpSalary = setBonusSalary/applicable_bonus_date_split*dayVls;
            //*********
            var getBonus_a = getEmpSalary/100*setBonusPer;
            var getBonus = getBonus_a.toFixed(2);
            $('#setBonusAmounts_'+slId).html(getBonus);
            console.log(getBonus);
        }
    });
}

$('#orgBonusType').on('change', function(){
    var orgName = $('#orgName').val();
    var orgBType = $(this).val();
    getEmpList(orgBType, orgName);
    $.ajax({
        url:'php_function/add_bonus_request.php',
        type:'POST',
        data:{action:'orgBTypeCheck',orgBType:orgBType},
        success:function(values){
            // console.log('values of BonusType :- '+values);
            if (values.trim()=='0') {
            }else{
                var jsonVls = JSON.parse(values);
                // console.log('values of BonusType :- Bonus Mode :- '+jsonVls[0]['b_mode']);
                $('#orgBonusMode').val(jsonVls[0]['b_mode']);
                $('#orgBonusOn').val(jsonVls[0]['b_on']);
                $('#orgBasedOn').val(jsonVls[0]['based_on']);
                $('#orgBonusPer').val(jsonVls[0]['b_per']);
                if (orgName!=undefined || orgName!='') {
                    getIdsVlsOfOrg(orgName, orgBType);
                }
                localStorage.setItem('applicable_bonus_date',jsonVls[0]['applicable']);
            }
        }
    });
});



$('#searchEmpName').on('keyup', function(){
    var empName = $(this).val();
    var orgName = $('#orgName').val();
    var orgBonusType = $('#orgBonusType').val();
    getEmpList(orgBonusType, orgName, empName);
    if (empName!='') {
        $('#closeSerBtn').show();
    }else{
        $('#closeSerBtn').hide();
    }
});
$('#closeSerBtn').on('click', function(){
    $('#searchEmpName').val('');
    $('#closeSerBtn').hide();
    var empName = $('#searchEmpName').val();
    var orgName = $('#orgName').val();
    var orgBonusType = $('#orgBonusType').val();
    getEmpList(orgBonusType, orgName, empName);
});



function bonusRequestSubmitOfEmp(formName){
    getMsgOnKeyup(formName);
    var orgName = $('#orgName').val();
    var orgBonusType = $('#orgBonusType').val();
    var orgBonusMode = $('#orgBonusMode').val();
    var bonusMessages = $('#bonusMessages').val();

    if (orgName=='' || orgName==null) {
        getMsgOnKeyup(formName, 'orgName');
    }
    if (orgBonusType=='' || orgBonusType==null) {
        getMsgOnKeyup(formName, 'orgBonusType');
    }
    if (bonusMessages=='' || bonusMessages==null) {
        getMsgOnKeyup(formName, 'bonusMessages');
    }else{
        var getCount = $('#getCount').val();
        var checkNum=[];
        var empDtls='';
        for (var i = 1; i <= getCount; i++) {
            var eleId=document.getElementById('listId_'+i);
            if (eleId.checked==true) {
                checkNum.push(1);
                var empId = $('#listId_'+i).val();
                // var empSlIdsVls = $('#empSlIdsVls_'+i).html();
                var setBonusOn = $('#setBonusOn_'+i).html();
                var setBonusSalary = $('#setBonusSalary_'+i).html();
                var setBonusPer = $('#setBonusPer_'+i).html();
                var setBonusDays = $('#setBonusDays_'+i).val();
                var setBonusAmounts = $('#setBonusAmounts_'+i).html();
                var cc = setBonusAmounts;
                /*
                var aa = setBonusAmounts.split('.');
                var aaa = aa[0].split(',');
                if (aaa[1]!=undefined) {
                    var bb = '';
                    for (var a = 0; a < aaa.length; a++) {
                        bb += aaa[a];
                    }
                    var cc = bb+'.'+aa[1];
                }else{
                    var cc = aa[0]+'.'+aa[1];
                }
                */
                

                if (getCount==i) {
                    empDtls += empId+'|'+setBonusOn+'|'+setBonusSalary+'|'+setBonusPer+'|'+setBonusDays+'|'+cc;
                }else{
                    empDtls += empId+'|'+setBonusOn+'|'+setBonusSalary+'|'+setBonusPer+'|'+setBonusDays+'|'+cc+',';
                }
            }else{
                checkNum.push(0);
            }
        }



        if (checkNum.includes(1)==true) {
            if (orgName!='' && orgBonusType!='' && orgBonusMode!='' && bonusMessages!=''){
                var continueSubmit=1;
            }else{
                var continueSubmit=0;
            }

            if (continueSubmit==1) {
                var formData = new FormData(bonusRequestFormOfEmp);
                formData.append('action','empBonusRequestSubmit');
                formData.append('empDtls',empDtls);
                $.ajax({
                    url:'php_function/add_bonus_request.php',
                    type:'POST',
                    data:formData,
                    contentType:false,
                    processData:false,
                    success:function(values){
                        // console.log('values :- '+values);
                        // return false;
                        if (values.trim()=='1') {
                            alert('Bonus request submited successfully...!!');
                            document.location.href='add_bonus_request_list.php';
                            // $('#orgName').val('');
                            // $('#orgBonusType').val('');
                            // $('#orgBonusMode').val('');
                            // $('#orgBonusOn').val('');
                            // $('#orgBasedOn').val('');
                            // $('#orgBonusPer').val('');
                            // $('#bonusMessages').val('');
                            // document.getElementById('bonusCheck').checked=false;
                            // getEmpList('', '');
                            // $('#submitErrorMsg').html('<span style="color: green; font-weight: bold;">Bonus request submited successfully...!!</span>');
                        }else{
                            alert('Bonus request submited not successfully...!!');
                        }
                    }
                });
            }else{
                $('#submitErrorMsg').html('<span style="color: red; font-weight: bold;">Please enter the all fields...!!</span>');
            }
        }else{
            $('#submitErrorMsg').html('<span style="color: red; font-weight: bold;">Please check the any one checkbox of Employee Details...!!</span>');
        }
        
    }

}



//************Edit Part

function getSelectedEmpVls(){ //******* Edit Part
    var orgBonusType = $('#orgBonusType').val();
    var orgName = $('#orgName').val();
    var getCount = $('#getCount').val();
    getEmpList(orgBonusType, orgName);
}

function bonusRequestUpdateOfEmp(formName){
    getMsgOnKeyup(formName);
    var orgName = $('#orgName').val();
    var orgBonusType = $('#orgBonusType').val();
    var orgBonusMode = $('#orgBonusMode').val();
    var bonusMessages = $('#bonusMessages').val();

    if (orgName=='' || orgName==null) {
        getMsgOnKeyup(formName, 'orgName');
    }
    if (orgBonusType=='' || orgBonusType==null) {
        getMsgOnKeyup(formName, 'orgBonusType');
    }
    if (bonusMessages=='' || bonusMessages==null) {
        getMsgOnKeyup(formName, 'bonusMessages');
    }else{
        var getCount = $('#getCount').val();
        var checkNum=[];
        var empDtls='';
        for (var i = 1; i <= getCount; i++) {
            var eleId=document.getElementById('listId_'+i);
            if (eleId.checked==true) {
                checkNum.push(1);
                var empId = $('#listId_'+i).val();
                // var empSlIdsVls = $('#empSlIdsVls_'+i).html();
                var setBonusOn = $('#setBonusOn_'+i).html();
                var setBonusSalary = $('#setBonusSalary_'+i).html();
                var setBonusPer = $('#setBonusPer_'+i).html();
                var setBonusDays = $('#setBonusDays_'+i).val();
                var setBonusAmounts = $('#setBonusAmounts_'+i).html();

                var aa = setBonusAmounts.split('.');
                var aaa = aa[0].split(',');
                if (aaa[1]!=undefined) {
                    var bb = '';
                    for (var a = 0; a < aaa.length; a++) {
                        bb += aaa[a];
                    }
                    var cc = bb+'.'+aa[1];
                }else{
                    var cc = aa[0]+'.'+aa[1];
                }

                if (getCount==i) {
                    empDtls += empId+'|'+setBonusOn+'|'+setBonusSalary+'|'+setBonusPer+'|'+setBonusDays+'|'+cc;
                }else{
                    empDtls += empId+'|'+setBonusOn+'|'+setBonusSalary+'|'+setBonusPer+'|'+setBonusDays+'|'+cc+',';
                }
            }else{
                checkNum.push(0);
            }
        }
        if (checkNum.includes(1)==true) {
            if (orgName!='' && orgBonusType!='' && orgBonusMode!='' && bonusMessages!=''){
                var continueSubmit=1;
            }else{
                var continueSubmit=0;
            }

            if (continueSubmit==1) {
                var formData = new FormData(bonusRequestFormOfEmp);
                formData.append('action','empBonusRequestUpdate');
                formData.append('empDtls',empDtls);
                $.ajax({
                    url:'php_function/add_bonus_request.php',
                    type:'POST',
                    data:formData,
                    contentType:false,
                    processData:false,
                    success:function(values){
                        // console.log('values :- '+values);
                        // return false;
                        if (values.trim()=='1') {
                            alert('Bonus request updated successfully...!!');
                            // location.reload();
                            document.location.href='add_bonus_request_list.php';
                            // $('#orgName').val('');
                            // $('#orgBonusType').val('');
                            // $('#orgBonusMode').val('');
                            // $('#orgBonusOn').val('');
                            // $('#orgBasedOn').val('');
                            // $('#orgBonusPer').val('');
                            // $('#bonusMessages').val('');
                            // document.getElementById('bonusCheck').checked=false;
                            // getEmpList('', '');
                            // $('#submitErrorMsg').html('<span style="color: green; font-weight: bold;">Bonus request submited successfully...!!</span>');
                        }else{

                        }
                    }
                });
            }else{
                $('#submitErrorMsg').html('<span style="color: red; font-weight: bold;">Please enter the all fields...!!</span>');
            }
        }else{
            $('#submitErrorMsg').html('<span style="color: red; font-weight: bold;">Please check the any one checkbox of Employee Details...!!</span>');
        }
        
    }

}

















