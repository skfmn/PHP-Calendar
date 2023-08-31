function validateSched() {
  with (window.document.schedule1) {
    if (event_name.value == "") {
      alert('Please enter an Event Name!');
      event_name.focus();
      return false;
    }
    if (mcount.selectedIndex == 0) {
      alert('Please select a month!');
      mcount.focus();
      return false;
    }
    if (ycount.selectedIndex == 0) {
      alert('Please select a year!');
      ycount.focus();
      return false;
    }
    if (comments.value == "") {
      alert('Please enter a comment for the event!');
      comments.focus();
      return false;
    }
    return true;
  }
}

function validatePwd() {
  with (window.document.password) {
      if (uname.value == "") {
         alert('Please enter an Admin Name!');
         uname.focus();
         return false;
      }
      if (pwd.value == "") {
         alert('Please enter a new password!');
         pwd.focus();
         return false;
      }
			if (pwd2.value == "") {
         alert('Please retype the new password!');
         pwd2.focus();
         return false;
      }
			if (pwd.value != pwd2.value) {
         alert('Passwords do not match; please try again!');
         pwd.focus();
         return false;
      }
   return true;
  }
}

function validateThisPwd() {
  with (window.document.password) {
			if (pwd.value != pwd2.value) {
         alert('Passwords do not match; please try again!');
         pwd.focus();
         return false;
      }
   return true;
  }
}

	function togglePass(arg1, arg2) {

    var x = document.getElementById(arg1);
    var y = document.getElementById(arg2);
    if (x.type === "password") {
      x.type = "text";
      y.className = "fa fa-eye shpwd";
    } else {
      x.type = "password";
      y.className = "fa fa-eye-slash shpwd";
    }

  }

  function confirmSubmit(imsg,ihref) {
	  var smsg = confirm(imsg);
	  if (smsg == true) {
		  window.location=ihref;
	  } else {
	    return false;
	  }
  }

function focusarea(iMonthID,iNum) {
	this.Num = iNum;
	this.monthID = iMonthID;
}

function getFocusArea() {

	var dyear = document.schedule.ycount.value;
  if (leapYear(dyear)) {
	  aFocusArea[2] = new focusarea(2,28);
  } else {
		aFocusArea[2] = new focusarea(2,29);
	}

	var sSelect = '<select name="dcount">';
	var selectID = document.schedule.mcount.value;

	for (var x=1; x<aFocusArea.length; x++) {

		if (aFocusArea[x].monthID == selectID) {

			for (var i=1; i<aFocusArea[x].Num+1; i++) {
				sSelect = sSelect + '<option>' + [i] + '</option>';
			}
		}
	}

	sSelect = sSelect + '</select>';
	document.all('Focus').innerHTML = "";
	document.all('Focus').innerHTML = sSelect;
}

function setFocusArea() {

  var mSelect = '<select id="mcount" name="mcount" onchange="getFocusArea();">';
	mSelect = mSelect + '<option value="0">Select Month</option>';

	for (var x = 1; x <= 12; x++) {
		mSelect = mSelect + '<option value="'+ x + '">' + x + '</option>';
	}

	mSelect = mSelect + '</select>';
	document.all('mFocus').innerHTML = "";
	document.all('mFocus').innerHTML = mSelect;


  var sSelect = '<select name="dcount">';
  sSelect = sSelect + '<option>Select Day</option>';
  sSelect = sSelect + '</select>';
	document.all('Focus').innerHTML = "";
	document.all('Focus').innerHTML = sSelect;

}

function leapYear(lyear) {
  return ((lyear % 4 == 0) && (lyear % 100 != 0)) || (lyear % 400 == 0);
}

var aFocusArea = new Array;
aFocusArea[1] = new focusarea(1,31);
aFocusArea[2] = new focusarea(2,29);
aFocusArea[3] = new focusarea(3,31);
aFocusArea[4] = new focusarea(4,30);
aFocusArea[5] = new focusarea(5,31);
aFocusArea[6] = new focusarea(6,30);
aFocusArea[7] = new focusarea(7,31);
aFocusArea[8] = new focusarea(8,31);
aFocusArea[9] = new focusarea(9,30);
aFocusArea[10] = new focusarea(10,31);
aFocusArea[11] = new focusarea(11,30);
aFocusArea[12] = new focusarea(12,31);
