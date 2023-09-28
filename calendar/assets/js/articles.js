function focusarea(iMonthID, iNum) {
    this.Num = iNum;
    this.monthID = iMonthID;
}

function leapYear(lyear) {
    return ((lyear % 4 == 0) && (lyear % 100 != 0)) || (lyear % 400 == 0);
}

function getFocusArea() {

    var dyear = document.schedule.ycount.value;
    if (leapYear(dyear)) {
        aFocusArea[2] = new focusarea(2, 29);
    } else {
        aFocusArea[2] = new focusarea(2, 28);
    }

    var sSelect = '<select id="dcount" name="dcount">';
    var selectID = document.schedule.mcount.value;

    for (var i = 1; i < aFocusArea[selectID].Num + 1; i++) {
        sSelect = sSelect + '<option>' + [i] + '</option>';
    }

    sSelect = sSelect + '</select>';
    document.getElementById('Focus').innerHTML = "";
    document.getElementById('Focus').innerHTML = sSelect;
}

function setFocusArea() {

    var mSelect = '<select id="mcount" name="mcount" onchange="getFocusArea();">';
    mSelect = mSelect + '<option value="0">Select Month</option>';

    for (var x = 1; x <= 12; x++) {
         mSelect = mSelect + '<option value="' + x + '">' + x + '</option>';   
    }

    mSelect = mSelect + '</select>';
    document.getElementById('mFocus').innerHTML = "";
    document.getElementById('mFocus').innerHTML = mSelect;


    var sSelect = '<select name="dcount">';
    sSelect = sSelect + '<option>Select Day</option>';
    sSelect = sSelect + '</select>';
    document.getElementById('Focus').innerHTML = "";
    document.getElementById('Focus').innerHTML = sSelect;

}

var aFocusArea = new Array;
aFocusArea[1] = new focusarea(1, 31);
aFocusArea[2] = new focusarea(2, 29);
aFocusArea[3] = new focusarea(3, 31);
aFocusArea[4] = new focusarea(4, 30);
aFocusArea[5] = new focusarea(5, 31);
aFocusArea[6] = new focusarea(6, 30);
aFocusArea[7] = new focusarea(7, 31);
aFocusArea[8] = new focusarea(8, 31);
aFocusArea[9] = new focusarea(9, 30);
aFocusArea[10] = new focusarea(10, 31);
aFocusArea[11] = new focusarea(11, 30);
aFocusArea[12] = new focusarea(12, 31);

function showresult() {

  var ycount = document.getElementById("ycount").value;
  var mcount = document.getElementById("mcount").value;
  var dcount = document.getElementById("dcount").value;
  document.getElementById("showresult").style.display = "block";
  document.getElementById("showresult").innerHTML = mcount + "/" + dcount + "/" + ycount;

}