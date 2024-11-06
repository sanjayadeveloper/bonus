$(document).ready(function(){
    getSessionVls();
    getActiveMenu();
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




//************ New 06-10-23 Start
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

    var actVls, frm_date, to_date, bonus_type, bonus_mode;
    actVls = localStorage.getItem('actVls');
    frm_date = $('#frmDates').val();
    to_date = $('#toDates').val();
    bonus_type = $('#bonusTypes').val();
    bonus_mode = $('#bonusModes').val();

    window.location.href="excelFileDownload.php?frm_date="+frm_date+"&to_date="+to_date+"&bonus_type="+bonus_type+"&bonus_mode="+bonus_mode+"&actVls="+actVls+"&tag="+tag;
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

    var actVls, frm_date, to_date, bonus_type, bonus_mode;
    actVls = localStorage.getItem('actVls');
    frm_date = $('#frmDates').val();
    to_date = $('#toDates').val();
    bonus_type = $('#bonusTypes').val();
    bonus_mode = $('#bonusModes').val();
    
    window.location.href="pdfFileDownload.php?frm_date="+frm_date+"&to_date="+to_date+"&bonus_type="+bonus_type+"&bonus_mode="+bonus_mode+"&actVls="+actVls+"&tag="+tag;
}

//************ New 06-10-23 End

// function getActiveMenu(){
//     var actVls = localStorage.getItem('actVls');
//     document.getElementById(actVls).setAttribute('class','active');

//     if (actVls!='All') {
//         document.getElementById('All').setAttribute('class','');
//     }
//     if(actVls!='Approved'){
//         document.getElementById('Approved').setAttribute('class','');
//     }
//     if(actVls!='Reject'){
//         document.getElementById('Reject').setAttribute('class','');
//     }
//     if(actVls!='Pending'){
//         document.getElementById('Pending').setAttribute('class','');
//     }
//     if(actVls!='Re-Check'){
//         document.getElementById('Re-Check').setAttribute('class','');
//     }
//     if(actVls!='Hold'){
//         document.getElementById('Hold').setAttribute('class','');
//     }
// }





function bonuslistSrcBtnFns(){
    var json_form = new FormData(searchBonuslist);
    json_form.append('action','searchedBonuslist');
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
function appr_bonuslistSrcBtnFns(){
    var json_form = new FormData(searchBonuslist1);
    json_form.append('action','appr_searchedBonuslist');
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
    format: "DD/MM/YYYY",
    //format: "DD/MM/YYYY hh:mm:ss A",
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
    format: "DD/MM/YYYY",
    //format: "DD/MM/YYYY hh:mm:ss A",
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
    format: "DD/MM/YYYY",
    //format: "DD/MM/YYYY hh:mm:ss A",
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
    format: "DD/MM/YYYY",
    //format: "DD/MM/YYYY hh:mm:ss A",
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
    format: "DD/MM/YYYY",
    //format: "DD/MM/YYYY hh:mm:ss A",
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
    format: "DD/MM/YYYY",
    //format: "DD/MM/YYYY hh:mm:ss A",
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
    format: "DD/MM/YYYY",
    //format: "DD/MM/YYYY hh:mm:ss A",
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
    format: "DD/MM/YYYY",
    //format: "DD/MM/YYYY hh:mm:ss A",
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
    format: "DD/MM/YYYY",
    //format: "DD/MM/YYYY hh:mm:ss A",
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
    $.ajax({
        url:'php_function/add_bonus_request.php',
        type:'POST',
        data:{action:'orgNameCheck',orgNameVls:orgNameVls},
        success:function(values){
            $('#orgEmpName').html(values);
            // var jsonVls = JSON.parse(values);
            // console.log('values :- '+values);
            getIdsVlsOfOrg(orgNameVls, orgBonusType);
        }
    });
});

function getIdsVlsOfOrg(orgNameVls, orgBonusType='0'){
    // alert(orgBonusType);
    var orgBonusOn = $('#orgBonusOn').val();
    var orgBonusPer = $('#orgBonusPer').val();
    $.ajax({
        url:'php_function/add_bonus_request.php',
        type:'POST',
        data:{action:'orgIdsCheck',orgNameVls:orgNameVls,orgBonusType:orgBonusType},
        success:function(values){
            // console.log('values :- '+values);
            // return false;
            var jsonData = JSON.parse(values);
            // console.log('splVls :- '+jsonData['idVls']);
            $('#orgIds').val(jsonData['idVls']);
            var splVls = jsonData['idVls'].split(',');
            var empSlry = jsonData['empSlry'].split(',');
            console.log('splVls :- '+jsonData['empSlry']);
            for (var i = 0; i < splVls.length; i++) {
                var counter = i+1;
                if (orgBonusType!=undefined || orgBonusType!='') {
                    $('#setBonusOn_'+counter).html(orgBonusOn);
                    $('#setBonusPer_'+counter).html(orgBonusPer);
                    $('#setBonusSalary_'+counter).html(jsonData['empSlry'][i]);
                }else{
                    $('#setBonusOn_'+counter).html(jsonData['orgBTypeVls'][0]['b_on']);
                    $('#setBonusPer_'+counter).html(jsonData['orgBTypeVls'][0]['b_per']);
                    $('#setBonusSalary_'+counter).html(jsonData['empSlry'][i]);
                }
            }
        }
    });
}

$('#orgBonusType').on('change', function(){
    var orgName = $('#orgName').val();
    var orgBType = $(this).val();
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
            }
        }
    });
});

















