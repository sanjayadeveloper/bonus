$(document).ready(function(){
    getSessionVls();
});

function getSessionVls(){
    $.ajax({
        url:'php_function/add_bonus_request.php',
        type:'POST',
        data:{action:'sessionArrayCheck'},
        success:function(values){
            console.log('Session Values :- '+values);
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

function applicableFormFn(applicableVls){
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

        var apVls = applicableVls.split('/');
        // var months = apVls[0];
        // var years = apVls[1];

        //****New Add 29-09-2023
        var apVls_a = applicableVls.split('-');
        // var months_a = apVls_a[1];

        if (apVls!='' || apVls!=null) {
            var months = apVls[0];
            var years = apVls[1];
        }else {
            var years = apVls_a[0];
            var months = apVls_a[1];
        }
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
                    }else{
                        alert('Form submited successfully...!!');
                    }
                    document.location.href='bonus1.php';
                }else{
                    alert('Form submited not successfull...!!');
                }
            }
        });
    }


}





