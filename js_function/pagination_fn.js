
/***************************************** Instruction Start

------Instruction of fetching data with ajax function------

-------Link-------
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript" src="js_function/pagination_fn.js"></script>
<script>
function addPageVls(){
    localStorage.setItem('pageVls','mng'); // only change the value of the "pageVls" key.
}
function allFinalFn(funNum){
    getUserDetails(funNum);
    // Add data fetching ajax function in this allFinalFn(funNum) function, like this manageBonusBodyFn(funNum).
}
</script>
-------Link-------

* If you've to 3 tables then add the diffrent id name , Like skDataTable1, skDataTable2 skDataTable3 upto 15 tables (skDataTable15)
<div id="skDataTable1" class="skDataTable">
	<table class="table table-bordered">
	    <thead style="background: gray;">
	      <tr>
	        <th width="7%">Sl. No.</th>
	        <th>Name</th>
	        <th>Contact</th>
	        <th>Address</th>
	        <th width="23%">Action</th>
	      </tr>
	    </thead>
	    <tbody id="homePageView"></tbody>
	</table>
</div>

function getUserDetails(n){
	var pageVls = localStorage.getItem('pageVls'); //---Mandatory
    var tbodyId = localStorage.getItem(pageVls+'_tbodyId_'+n); //---Mandatory
    var num = localStorage.getItem(pageVls+'_nums_'+n); //---Mandatory
    var getRows = $('#getRowsList'+num).val(); //---Mandatory
    var getRowsSearch = $('#getRowsListSearch'+num).val(); //---Mandatory
	
	  //---Mandatory Start, If added search fields
	if (n==1) {
        var org_name = $('#org_name').val();
        var frm_date = $('#frm_date').val();
        var to_date = $('#to_date').val();
    }
    if (n==2) {
        var org_name = $('#appr_org_name').val();
        var frm_date = $('#appr_frm_date').val();
        var to_date = $('#appr_to_date').val();
    }
    if (org_name!='') {
        getRows = getRowsSearch;
    }
      //---Mandatory End, If added search fields

	$.ajax({
		url:'***',
		type:'POST',
		data:{action:'approvedBodyCheck',rows:getRows,num:num,org_name:org_name,frm_date:frm_date,to_date:to_date}, //---Mandatory (rows:getRows,num:num)
		success:function(values){
			//*************
			$('#'+tbodyId).html(values); //---Mandatory " $('#'+tbodyId).html() "
			var count_rows = $('#count_rows_'+num).val();
			getTableEntriesVls(count_rows,getRows,num); //---Mandatory this function
		}
	})
}


----------------------( PHP Funcion )---------------------
if (isset($_POST['action'])) {
    $action = $_POST['action'];
}
if ($action=='approvedBodyCheck') {
    $num = $_REQUEST['num']; // please change this variable for the diferent condition, not the $_REQUEST key, only variable. Like $num, $numbers, $numVls etc...
}

if ($num==1) {
	//************ //---Mandatory Start
	$num = $_REQUEST['num'];
	$rows = $_REQUEST['rows'];
	$rowsdata = explode('|', $rows);
	$start = $rowsdata[2];
	$end = $rowsdata[3];
	$limit = ' LIMIT '.$start.', '.$end; // This $limit variable add in main query for the limited fetched data.
	$start_Num = $start+1;

	$sql_count_allreq = mysqli_query($con,"SELECT * FROM hr_leave_app");
    $count_all = mysqli_num_rows($sql_count_allreq);

    echo '<input type="hidden" id="count_rows_'.$num.'" value="'."$count_all".'">';
	//************ //---Mandatory End
}
if ($num==2) {
	//************ //---Mandatory Start
	$num = $_REQUEST['num'];
	$rows = $_REQUEST['rows'];
	$rowsdata = explode('|', $rows);
	$start = $rowsdata[2];
	$end = $rowsdata[3];
	$limit = ' LIMIT '.$start.', '.$end; // This $limit variable add in main query for the limited fetched data.
	$start_Num = $start+1;

	$sql_count_allreq = mysqli_query($con,"SELECT * FROM hr_leave_app");
    $count_all = mysqli_num_rows($sql_count_allreq);

    echo '<input type="hidden" id="count_rows_'.$num.'" value="'."$count_all".'">';
	//************ //---Mandatory End
}
----------------------( PHP Funcion )---------------------


* I've added maximum 15 tables in one web page. If you'll add more than 15 table then add more than 15 "case" in allFetchingDataFn(tbodyId, n) function. And change the forloop length in "divAdd()" function and "getDataWithSelectEntries(start, end, n='')" function also.

* filter(search) part
If will add the search field then add the same function name of ajax in the search button or 'keyup,onchange' event.


***************************************** Instruction End  */

$(document).ready(function(){
	addPageVls();
	divAdd(); // Add Elements
	getDataWithSelectEntries(0, 10);
});

function divAdd(){ // Add Elements
	for (var i = 1; i <= 15; i++) {
		var div = $('#skDataTable'+i).is(":visible");
		// if (div == true) {
		if ($('#skDataTable'+i).length > 0) {
		    var tbodyId = document.getElementById('skDataTable'+i).getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].id;
			//****************************
			const newNode = document.createElement("div");
			newNode.setAttribute('id','skTop'+i);
			const list = document.getElementById("skDataTable"+i);
			list.insertBefore(newNode, list.children[0]);
			setElementsInTop(i);
			//****************************
		    let skDataTable = document.getElementById('skDataTable'+i);
		    let div = document.createElement('div');
		    div.setAttribute('id','skBottom'+i);
		    insertAfter(div, skDataTable.lastElementChild, i);
		}
	}
}
function setElementsInTop(i){ // Add Elements
	
	//----------With Searching filter
	//var topElements = '<input type="hidden" id="checkRowsList'+i+'"><input type="hidden" id="getRowsList'+i+'"><input type="hidden" id="numOfClm_'+i+'" value="1"><div style="text-align: left; position: absolute;"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">Show :</span> <select class="" style="font-size: 12px;" onchange="getDataWithSelectEntries(0, this.value, '+i+')"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="200">200</option></select> <span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">entries</span></div><div style="text-align: right;"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">Search :</span> <input type="text" id="" style="font-size: 12px;"></div>';

	//----------Without Searching filter
	var topElements = '<input type="hidden" id="checkRowsList'+i+'"><input type="hidden" id="getRowsList'+i+'"><input type="hidden" id="numOfClm_'+i+'" value="1"><input type="hidden" id="getRowsListSearch'+i+'" value="0|10|0|10"><div style="text-align: left; margin-bottom:-15px;"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">Show :</span> <select class="" style="font-size: 12px;" onchange="getDataWithSelectEntries(0, this.value, '+i+')"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select> <span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px;">entries</span></div><div style="text-align: right;"><br/></div>';
	document.getElementById('skTop'+i).innerHTML=topElements;
};
function insertAfter(newNode, existingNode, i) { // Add Elements
    existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    setElementsInBottom(i);
}
function setElementsInBottom(i){ // Add Elements
	var topElements = '<div style="text-align: left; position: absolute; margin-top: 5px;"><div class="" id="showEntries_'+i+'"></div></div><div style="text-align: right; margin-top: -20px;"><span id="oneStepBack_'+i+'"></span> <span id="numClmnVls_'+i+'"></span> <span id="oneStepForward_'+i+'"></span></div>';
	document.getElementById('skBottom'+i).innerHTML=topElements;
};
function getDataWithSelectEntries(start, end, n=''){
	if (n=='') {
		for (var i = 1; i <= 15; i++) {
			var div = $('#skDataTable'+i).is(":visible");
				// console.log('skDataTable :- '+$('#skDataTable'+i).length);
			// if (div == true) {
			if ($('#skDataTable'+i).length > 0) {
				$('#checkRowsList'+i).val(start+'|'+end);
				$('#getRowsList'+i).val(start+'|'+end+'|'+start+'|'+end);
			    var tbodyId = document.getElementById('skDataTable'+i).getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].id;
			    localStorage.setItem('funNum',i);
				allFetchingDataFn(tbodyId, i);
			}
		}
	}else{
		var div = $('#skDataTable'+n).is(":visible");
		if (div == true) {
			$('#checkRowsList'+n).val(start+'|'+end);
			var vls = $('#numOfClm_'+n).val();

			var totalVls = localStorage.getItem('totalVls_'+n);
			var res = parseInt(vls)*parseInt(end);
			if (totalVls>res) {
				viewData(vls, n);
			}else{
				var getV = 1;
				$('#numOfClm_'+n).val(getV);
				viewData(getV, n);
			}
		}
	}
}
function getTableEntriesVls(totalVls, getRows, n){
	localStorage.setItem('funNum',n);
	var getRvls = getRows.split('|');
	var start = getRvls[0];
	if (start==0) {
		var start = 1;
	}
	var end = getRvls[1];
	if (parseInt(totalVls)<=parseInt(end)) {
		end = totalVls;
	}
	if (end==0) {
		var tag = "";
	}else{
		var tag = "Showing "+start+" to "+end+" of "+totalVls+" entries";
	}
	// var tag = "Showing "+start+" to "+end+" of "+totalVls+" entries";
	localStorage.setItem('totalVls_'+n,totalVls);
	$('#showEntries_'+n).html(tag);
	//************************************
	displayClmNum(totalVls, n);
};
function displayClmNum(totalVls, n){
	var checkRowsList = $('#checkRowsList'+n).val();
	var sk = checkRowsList.split('|');
	var end = sk[1];
	var listV = parseInt(totalVls)/parseInt(end);
	let text = listV.toString();
	var res = text.split('.');
	if (res[1]!=undefined) {
		var getV = parseInt(res[0])+1;
	}else{
		var getV = listV;
	}
	var vls = $('#numOfClm_'+n).val();
	var tag = '';
	var getPart = parseInt(getV)/5;
	let text_a = getPart.toString();
	var res_a = text_a.split('.');
	if (res_a[1]!=undefined) {
		var getV_a = parseInt(res_a[0])+1;
	}else{
		var getV_a = getPart;
	}

	// console.log('vls :- '+vls+', getPart :- '+getPart+', getV :- '+getV+', getV_a :- '+getV_a);

	var runNum = 1;
	for (var i = 1; i <= getV_a; i++) {
		var end = parseInt(i)*5;
		var start = parseInt(end)-4;
		if (vls >= start && vls <= end) {
			if (getV_a!=i) {
				var checkForLastColumns = localStorage.getItem('checkForLastColumns');
				if (checkForLastColumns!='' && checkForLastColumns!=null) {
					localStorage.setItem('checkForLastColumns','');
				}
				if (vls==1) {
					$('#oneStepBack_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&laquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&lsaquo;</button></span>');
					if (getV<=vls) {
						$('#oneStepForward_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&rsaquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&raquo;</button></span>');
					}else{
						$('#oneStepForward_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepForward('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&rsaquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="endForward('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&raquo;</button></span>');
					}
				}else{
					$('#oneStepBack_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="homeBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&laquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&lsaquo;</button></span>');
					if (getV<=vls) {
						$('#oneStepForward_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&rsaquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&raquo;</button></span>');
					}else{
						$('#oneStepForward_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepForward('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&rsaquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="endForward('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&raquo;</button></span>');
					}
				}
				for (var a = start; a <= end; a++) {
					if (vls==a) {
						tag += '<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; padding: 2px 6px; cursor: pointer; background: #ebe7e7; border: 1px solid gray; border-radius: 4px;" id="pgNum_'+a+'_'+n+'" onclick="viewData('+a+', '+n+')">'+a+'</span>';
					}else{
						tag += '<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; padding: 2px 6px; cursor: pointer;" id="pgNum_'+a+'_'+n+'" onmouseover="activeVls('+a+', '+n+')" onmouseout="inActiveVls('+a+', '+n+')" onclick="viewData('+a+', '+n+')">'+a+'</span>';
					}
				}
				if (getV_a > 1) {
					var multiVls_a = parseInt(i)*6;
					var multiVls = parseInt(multiVls_a)*5;
					if (getV >= multiVls) {
						var getLastNo = multiVls;
					}
					var viewLstVls = parseInt(end)+10;
					if (viewLstVls<=getV) {
						tag += ' <span style="font-size: 18px;">. . . .</span>';
						tag += '<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; padding: 2px 6px; cursor: pointer;" id="pgNum_'+viewLstVls+'_'+n+'" onmouseover="activeVls('+viewLstVls+', '+n+')" onmouseout="inActiveVls('+viewLstVls+', '+n+')" onclick="viewData('+viewLstVls+', '+n+')">'+viewLstVls+'</span> ';
					}else{
						tag += ' <span style="font-size: 18px;">. . . .</span>';
						tag += '<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; padding: 2px 6px; cursor: pointer;" id="pgNum_'+getV+'_'+n+'" onmouseover="activeVls('+getV+', '+n+')" onmouseout="inActiveVls('+getV+', '+n+')" onclick="viewData('+getV+', '+n+')">'+getV+'</span> ';
					}
					if (getV_a>1) {
						tag += ' <input type="number" style="width:52px; font-size: 12px;" id="checkClmnValue_'+n+'" onkeypress="checkClmnKeyPress(event, this.value, '+getV+', '+n+')">';
					}
					runNum++;
				}
			}else{
				if (res_a[1]!=undefined) {
					if (res_a[1]==1) {
						var divRes = 1;
					}else{
						var divRes = parseInt(res_a[1])/2;
					}
					if (getV_a==1) {
						$('#oneStepBack_'+n).html('');
						$('#oneStepForward_'+n).html('');
					}else {
						if (getV>vls) {
							$('#oneStepBack_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="homeBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&laquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&lsaquo;</button></span>');
							$('#oneStepForward_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepForward('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&rsaquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="endForward('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&raquo;</button></span>');
						}else if (getV=vls) {
							$('#oneStepBack_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="homeBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&laquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&lsaquo;</button></span>');
							$('#oneStepForward_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&rsaquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&raquo;</button></span>');
						}else{
							// console.log('FFF'); //---------Hold
							$('#oneStepBack_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&laquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&lsaquo;</button></span>');
							$('#oneStepForward_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;">&rsaquo;</a></span> <span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&raquo;</button></span>');
						}
					}
				}else{

					if (getV > 5) {
						if (start<end) {
							var divResss_a = parseInt(end)-parseInt(start);
							var divResss = parseInt(divResss_a)+1;
						}else{
							var divResss_a = parseInt(getV)-parseInt(vls);
							var divResss = parseInt(divResss_a)+1;
						}
						var checkForLastColumns = localStorage.getItem('checkForLastColumns');
						if (checkForLastColumns=='' || checkForLastColumns==null) {
							localStorage.setItem('checkForLastColumns',divResss);
							var divRes = divResss;
							$('#oneStepBack_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="homeBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&laquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&lsaquo;</button></span>');
						}else{
							if (getV_a!=1) {
								if (getV<=vls) {
									$('#oneStepBack_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="homeBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&laquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&lsaquo;</button></span>');
									$('#oneStepForward_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&rsaquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&raquo;</button></span>');
								}else{
									$('#oneStepBack_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="homeBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&laquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepBack('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&lsaquo;</button></span>');
									$('#oneStepForward_'+n).html('<span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="oneStepForward('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&rsaquo;</button></span><span style="font-family: Arial, Helvetica, sans-serif; font-size: 22px; padding: 2px 6px; cursor: pointer;" onclick="endForward('+n+')"><button type="button" style="color: #000; text-decoration: none; padding: 2px 6px; background: none; border: none;">&raquo;</button></span>');
								}
							}else{
								$('#oneStepBack_'+n).html('');
								$('#oneStepForward_'+n).html('');
							}
							
							var divRes = checkForLastColumns;
						}
					}else{
						var divRes = getV;
					}
				}
				var aa = start;
				for (var a = 1; a <= divRes; a++) {
					if (vls==aa) {
						tag += '<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; padding: 2px 6px; cursor: pointer; background: #ebe7e7; border: 1px solid gray; border-radius: 4px;" id="pgNum_'+aa+'_'+n+'" onclick="viewData('+aa+', '+n+')">'+aa+'</span>';
					}else{
						tag += '<span style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; padding: 2px 6px; cursor: pointer;" id="pgNum_'+aa+'_'+n+'" onmouseover="activeVls('+aa+', '+n+')" onmouseout="inActiveVls('+aa+', '+n+')" onclick="viewData('+aa+', '+n+')">'+aa+'</span>';
					}
					aa = parseInt(aa)+1;
				}
				if (getV_a>1) {
					tag += ' <input type="number" style="width:52px; font-size: 12px;" id="checkClmnValue_'+n+'" onkeypress="checkClmnKeyPress(event, this.value, '+getV+', '+n+')">';
				}
			}
		}
	}
	$('#numClmnVls_'+n).html(tag);
}

function checkClmnValueBlur(vls, getV, n){
	if (vls<=getV) {
		viewData(vls, n);
	}
}

function checkClmnKeyPress(event, vls, getV, n){
	if (event.key === "Enter") {
	    if (vls<=getV) {
			viewData(vls, n);
		}
	    event.preventDefault();
	}else{
	 	$('#checkClmnValue_'+n).on('keyup', function(){
	 		var vls = $(this).val();
	 		if (vls>getV) {
				alert('Please enter value between 1 to '+getV);
				$('#checkClmnValue_'+n).val('');
			}
	 	})
	}
}


//****StyleSheet
function activeVls(num, n){
	// $('#pgNum_'+num+'_'+n).css('background','#bdb8b8');
	document.getElementById('pgNum_'+num+'_'+n).style.cssText = "font-family: Arial, Helvetica, sans-serif; font-size: 13px; padding: 2px 5px; margin-left: 0.5px; cursor: pointer; background: #ebe7e7; border: 1px solid gray; border-radius: 4px;";
	// $('#pgNum_'+num+'_'+n).css('background','#bdb8b8');
}
function inActiveVls(num, n){
	// $('#pgNum_'+num+'_'+n).css('background','none');

	document.getElementById('pgNum_'+num+'_'+n).style.cssText = "font-family: Arial, Helvetica, sans-serif; font-size: 13px; padding: 2px 6px;";
}
//****StyleSheet

function viewData(vls, n){
	$('#numOfClm_'+n).val(vls);
	var checkRowsList = $('#checkRowsList'+n).val();
	var sk = checkRowsList.split('|');
	var end = sk[1];
	var lastV = parseInt(vls)*parseInt(end);
	var resV = parseInt(lastV)-parseInt(end);
	var firstV = parseInt(resV)+1;
	var firstV_a = resV;
	if (vls==1) {
		var firstV = 0;
	}
	$('#getRowsList'+n).val(firstV+'|'+lastV+'|'+firstV_a+'|'+end);
	var tbodyId = document.getElementById('skDataTable'+n).getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].id;
	var totalVls = localStorage.getItem('totalVls_'+n);
	if (totalVls>=firstV) {
		localStorage.setItem('funNum',n);
		allFetchingDataFn(tbodyId, n);
	}
}
function oneStepBack(n){
	var vls_a = $('#numOfClm_'+n).val();
	var vls_b = parseInt(vls_a)-1;
	$('#numOfClm_'+n).val(vls_b);
	var checkRowsList = $('#checkRowsList'+n).val();
	var sk = checkRowsList.split('|');
	var end = sk[1];
	var lastV = parseInt(vls_b)*parseInt(end);
	var resV = parseInt(lastV)-parseInt(end);
	var firstV = parseInt(resV)+1;
	var firstV_a = resV;
	if (vls_b==1) {
		var firstV = 0;
	}
	$('#getRowsList'+n).val(firstV+'|'+lastV+'|'+firstV_a+'|'+end);
	var tbodyId = document.getElementById('skDataTable'+n).getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].id;
	var totalVls = localStorage.getItem('totalVls_'+n);
	if (totalVls>=firstV) {
		localStorage.setItem('funNum',n);
		allFetchingDataFn(tbodyId, n);
	}
}
function oneStepForward(n){
	var vls_a = $('#numOfClm_'+n).val();
	var vls_b = parseInt(vls_a)+1;
	$('#numOfClm_'+n).val(vls_b);
	var checkRowsList = $('#checkRowsList'+n).val();
	var sk = checkRowsList.split('|');
	var end = sk[1];
	var lastV = parseInt(vls_b)*parseInt(end);
	var resV = parseInt(lastV)-parseInt(end);
	var firstV = parseInt(resV)+1;
	var firstV_a = resV;
	if (vls_b==1) {
		var firstV = 0;
	}
	$('#getRowsList'+n).val(firstV+'|'+lastV+'|'+firstV_a+'|'+end);
	var tbodyId = document.getElementById('skDataTable'+n).getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].id;
	var totalVls = localStorage.getItem('totalVls_'+n);
	if (totalVls>=firstV) {
		localStorage.setItem('funNum',n);
		allFetchingDataFn(tbodyId, n);
	}
}


function homeBack(n){
	$('#numOfClm_'+n).val(1);
	var checkRowsList = $('#checkRowsList'+n).val();
	var sk = checkRowsList.split('|');
	var end = sk[1];
	var lastV = 1*parseInt(end);
	var firstV = 0;
	$('#getRowsList'+n).val(firstV+'|'+lastV+'|'+firstV+'|'+lastV);
	var tbodyId = document.getElementById('skDataTable'+n).getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].id;
	var totalVls = localStorage.getItem('totalVls_'+n);
	if (totalVls>=firstV) {
		localStorage.setItem('funNum',n);
		allFetchingDataFn(tbodyId, n);
	}
}
function endForward(n){
	var totalVls = localStorage.getItem('totalVls_'+n);
	var checkRowsList = $('#checkRowsList'+n).val();
	var sk = checkRowsList.split('|');
	var end = sk[1];
	var listV = parseInt(totalVls)/parseInt(end);
	let text = listV.toString();
	var res = text.split('.');
	if (res[1]!=undefined) {
		var getV = parseInt(res[0])+1;
	}else{
		var getV = listV;
	}
	$('#numOfClm_'+n).val(getV);
	var lastV = parseInt(getV)*parseInt(end);
	var firstV_a = parseInt(lastV)-parseInt(end);
	var firstV = parseInt(firstV_a)+1;

	$('#getRowsList'+n).val(firstV+'|'+lastV+'|'+firstV_a+'|'+end);
	var tbodyId = document.getElementById('skDataTable'+n).getElementsByTagName('table')[0].getElementsByTagName('tbody')[0].id;
	var totalVls = localStorage.getItem('totalVls_'+n);
	if (totalVls>=firstV) {
		localStorage.setItem('funNum',n);
		allFetchingDataFn(tbodyId, n);
	}
}

function allFetchingDataFn(tbodyId, n){	
	var segments = window.location.pathname.split('/');
	var toDelete = [];
	for (var i = 0; i < segments.length; i++) {
	    if (segments[i].length < 1) {
	        toDelete.push(i);
	    }
	}
	for (var i = 0; i < toDelete.length; i++) {
	    segments.splice(i, 1);
	}
	var filename = segments[segments.length - 1];
	//---------
	var funNum = localStorage.getItem('funNum');
	var pageVls = localStorage.getItem('pageVls');
  	localStorage.setItem(pageVls+'_tbodyId_'+n,tbodyId);
	localStorage.setItem(pageVls+'_nums_'+n,n);
	switch(parseInt(funNum)) {
	  case 1:
	    // allFinalFn(filename, funNum);
	    allFinalFn(funNum);
	    break;
	  case 2:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 3:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 4:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 5:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 6:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 7:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 8:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 9:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 10:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 11:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 12:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 13:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 14:
	  	// function
	    allFinalFn(funNum);
	    break;
	  case 15:
	  	// function
	    allFinalFn(funNum);
	    break;

	  default:
	    // code block
	}
}




//*************************************** Manual Function Include, Start
function addPageVls55(){ // added this function in the own page "addPageVls()"
	var segments = window.location.pathname.split('/');
	var toDelete = [];
	for (var i = 0; i < segments.length; i++) {
	    if (segments[i].length < 1) {
	        toDelete.push(i);
	    }
	}
	for (var i = 0; i < toDelete.length; i++) {
	    segments.splice(i, 1);
	}
	var filename = segments[segments.length - 1];

	// ------------------------- Start
	if (filename=='add_bonus_request_list.php') {
		localStorage.setItem('pageVls','add');
	}
	if (filename=='manage_bonus_request_list.php') {
		localStorage.setItem('pageVls','mng');
	}
	// ------------------------- End
}
function allFinalFn55(filename, funNum){ // added this function in the own page "allFinalFn(filename, funNum)"
	if (filename=='add_bonus_request_list.php') {
		addBonusBodyFn(funNum);
	}
	if (filename=='manage_bonus_request_list.php') {
		manageBonusBodyFn(funNum);
	}
};
//*************************************** Manual Function Include, End