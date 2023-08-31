 <?php
	session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';

  $cookies = $dir = "";
	$cookies = $_SESSION["caladminname"];

	if ($cookies == "") {

    redirect($redirect."admin/admin_login.php");
    ob_end_flush();

	}

  if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
	  if ($msg <> "") {
		  displayFancyMsg(getMessage($msg));
		  $_SESSION["msg"] = "";
    }
  }

  if (isset($_POST["schedule"])) {

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	  if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

		$m = $d = $y = "";
    $m = test_input($_POST["mcount"]);
		$d = test_input($_POST["dcount"]);
		$y = test_input($_POST["ycount"]);
	  $schedDate = date($y."/".$m."/".$d);

		if (date_create_from_format("Y/n/j",$schedDate) == false) {

			$_SESSION["msg"] = "notad";
      redirect($redirect."admin/admin_schedule.php");
      ob_end_flush();

		} else {

			$event = test_input($_POST["event_name"]);
			$comments = test_input($_POST["comments"]);
			$schedDate = date_create_from_format("Y/n/j",$schedDate);
			$schedDate = date_format($schedDate,"Y-m-d");

			if (test_input($_POST["registration"]) == "yes") {
				$blnAllowReg = true;
			} else {
				$blnAllowReg = false;
		  }

		  $stmt = mysqli_prepare($conn,"INSERT INTO ".DBPREFIX."calendar (allow_reg,schDate,event,comments) VALUES (?,?,?,?)");
		  $stmt->bind_param('ssss', $blnAllowReg, $schedDate, $event, $comments);

		  if ($stmt->execute()) {
			  $_SESSION["msg"] = "evsch";
		  }	else {
			  $_SESSION["msg"] = "evschf";
		  }

		  redirect($redirect."admin/admin_schedule.php");
		  ob_end_flush();

		}
		mysqli_close($conn);
  }
	$sYear = "";
	$sYear = date("y");

  include "../includes/header.php";
?>
<div id="main" class="container">
  <header><h2 style="text-align:center;">Schedule an Event</h2></header>
  <form action="admin_schedule.php" method="post" name="schedule" onsubmit="return validateSched();">
	<input type="hidden" name="schedule" value="now">
  <div class="row">
		<div class="-3u 6u 12u(medium)" style="padding-bottom:30px;">
	    <label for="event_name" style="margin-bottom:-2px;"><strong>Event Name</strong></label>
		  <input type="text" id="event_name" name="event_name" />
    </div>
    <div class="-3u 6u 12u$(medium)"><h5 style="margin-bottom:-2px;">Select Date</h5></div>
	  <div class="-3u 2u 12u$(medium)">
      <div class="select-wrapper">
        <select id="ycount" name="ycount" onchange="setFocusArea();">
          <option value="0">Select Year</option>
          <?php
		        for ($x = $sYear; $x <= $sYear+10; $x++) {
			        echo "  <option value=\"20".$x."\">20".$x."</option>\n";
	          }
          ?>
        </select>
      </div>
    </div>
		<div class="2u 12u$(medium)">
      <div class="select-wrapper">
			  <span id="mFocus">
					<select name="mcount">
            <option>Select Month</option>
          </select>
				</span>
      </div>
    </div>
    <div class="2u$ 12u$(medium)">
      <div class="select-wrapper">
        <span id="Focus">
          <select name="dcount">
            <option>Select Day</option>
          </select>
        </span>
      </div>
    </div>

	  <div class="-3u 2u 12u(medium)" style="padding-top:30px;">
      <h5 style="margin-bottom:-3px;"><strong>Allow Registration</strong></h5>
    </div>
	  <div class="2u$ 12u(medium)" style="padding-top:30px;">
      <input type="radio" id="regyes" name="registration" value="yes">
      <label for="regyes">Yes</label>
      <input type="radio" id="regno" name="registration" value="no">
      <label for="regno">No</label>
    </div>
	  <div class="-3u 6u 12u(medium)" style="padding-bottom:10px;">
	    <label for="comments" style="margin-bottom:-2px;"><strong>Comments</strong></label>
		  <textarea id="comments" name="comments" cols="50" rows="10"></textarea>
	  </div>
	  <div class="-3u 6u 12u(medium)">
      <input type="submit" name="submit" value="Schedule" class="button fit">
		</div>
  </div>
  </form>
</div>
<?php include "../includes/footer.php" ?>