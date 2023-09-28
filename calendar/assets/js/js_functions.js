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
