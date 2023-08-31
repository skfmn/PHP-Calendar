<?php
	session_start();
  ob_start();
  include 'includes/globals.php';
  include 'includes/functions.php';

	$schedID = $datDate = $event = $comments = $cookies = "";
	$blnAllowReg = false;

	if (isset($_SESSION["sid"])) {
		$schedID = $_SESSION["sid"];
		$_SESSION["sid"] = "";
	}
	if (isset($_POST["schedid"])) {
		$schedID = $_POST["schedid"];
	}

  if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
	  if ($msg <> "") {
		  displayFancyMsg(getMessage($msg));
		  $_SESSION["msg"] = "";
    }
  }

?>
<!DOCTYPE HTML>
<html >
<head>
  <link type="text/css" rel="stylesheet"  href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link type="text/css" rel="stylesheet" href="assets/css/jquery.fancybox.css" />
  <link type="text/css" rel="stylesheet" href="assets/css/main.css" />
  <script language="javascript" type="text/javascript" src="//code.jquery.com/jquery-3.6.0.js"></script>
  <script language="javascript" type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script language="javascript" type="text/javascript" src="assets/js/jquery.fancybox.js" ></script>
  <script language="javascript" type="text/javascript" src="assets/js/js_functions.js" ></script>
	<script language="javascript" type="text/javascript">//parent.$.fancybox.update();</script>
</head>
<body style="max-width:600px;max-height:800px;">
<?php

	if (isset($_GET["delete"])) {

	  $schedID = test_input($_GET["schedid"]);

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	  if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

		$stmt = mysqli_prepare($conn,"DELETE FROM ".DBPREFIX."calendar WHERE schedID = ?");
		$stmt->bind_param("s",$schedID);
		if ($stmt->execute()) {
      $stmt = mysqli_prepare($conn,"DELETE FROM ".DBPREFIX."registration WHERE schedID = ?");
		  $stmt->bind_param("s",$schedID);
		  if ($stmt->execute()) {
				$_SESSION["msg"] = "evdel";
			} else{
				$_SESSION["msg"] = "regdelf";
			}
    } else {
			$_SESSION["msg"] = "evdelf";
		}
    mysqli_close($conn);
?>
  <script type="text/javascript" language="javascript">parent.$.fancybox.close();</script>
<?php

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

			if (test_input($_POST["registration"]) == "yes") {
				$blnAllowReg = true;
		  } else {
				$blnAllowReg = false;
			}

			$event = test_input($_POST["event_name"]);
			$comments = test_input($_POST["comments"]);

			$schedDate = date_format(date_create_from_format("Y/n/j",$schedDate),"Y-m-d");

      $stmt = mysqli_prepare($conn,"INSERT INTO ".DBPREFIX."calendar (allow_reg,schDate,event,comments) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $blnAllowReg, $schedDate, $event, $comments);

      if ($stmt->execute()) {
				$_SESSION["msg"] = "evsch";
			}

		}
    mysqli_close($conn);
?>
  <script type="text/javascript" language="javascript">parent.$.fancybox.close();</script>
<?php

  }

	if (isset($_GET["process"])) {

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	  if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

	  $schedID = test_input($_GET["schedid"]);
		$rname = test_input($_POST["rname"]);
		$addInfo = test_input($_POST["addinfo"]);
		$dDate = test_input($_GET["date"]);

		$stmt = mysqli_prepare($conn,"INSERT INTO ".DBPREFIX."registration(schedID,reg_name,add_info) values(?,?,?)");
		$stmt->bind_param("sss", $schedID, $rname, $addInfo);
		if ($stmt->execute()) {
			$_SESSION["msg"] = "regs";
		}
    mysqli_close($conn);
?>
  <script type="text/javascript" language="javascript">parent.$.fancybox.close();</script>
<?php

	}

	if (isset($_GET["reg"])) {

	  $schedID = test_input($_GET["schedid"]);

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	  if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

		$stmt = mysqli_prepare($conn,"SELECT * FROM ".DBPREFIX."calendar WHERE schedID = ?");
		$stmt->bind_param("s",$schedID);
		$stmt->execute();
		$result = $stmt->get_result();
    if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
		  $events = $row["event"];
			$dDate = $row["schDate"];
		}
		$sDate = date_create($dDate);

?>

<div id="main" class="container" style="margin-top:-44px;">
  <header style="text-align:center;"><h2>Register</h2></header>
	<h4 style="text-align:center;"><?php echo date_format($sDate,"jS \of M Y"); ?><br /><?php echo $events; ?></h4>
  <form action="calendar_view.php?process=reg&schedid=<?php echo $schedID; ?>&date=<?php echo $dDate; ?>" method="post" name="register" id="register" >
  <div class="row">
    <div class="-3u 6u$ 12u$(medium)" style="padding-bottom:30px;">
      <label for="rname" style="margin-bottom:-3px;">Name</label>
      <input type="text" id="rname" name="rname" size="20" />
    </div>
    <div class="-3u 6u$ 12u$(medium)" style="padding-bottom:10px;">
      <label for="addinfo" style="margin-bottom:-3px;">Additional info:</label>
      <textarea id="addinfo" name="addinfo"></textarea>
    </div>
    <div class="-3u 6u$ 12u$(medium)">
      <input type="submit" name="submit" value="Register" class="button fit" />
    </div>
  </div>
  </form>
</div>
<?php
	}

  if (isset($_GET["view"])) {

		$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	  if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

	  if (isset($_GET["sdate"])) {

			$param1 = test_input($_GET["sdate"]);
		  $stmt = mysqli_prepare($conn,"SELECT * FROM ".DBPREFIX."calendar WHERE schDate = ? ORDER BY schedID Asc");
			$stmt->bind_param("s",$param1);

		} else if (isset($_GET["schedid"])) {

			$param1 = test_input($_GET["schedid"]);
		  $stmt = mysqli_prepare($conn,"SELECT * FROM ".DBPREFIX."calendar WHERE schedID = ? ORDER BY schedID Asc");
			$stmt->bind_param("s",$param1);

		} else {

			$param1 = date("Y-m-d");
		  $stmt = mysqli_prepare($conn,"SELECT * FROM ".DBPREFIX."calendar WHERE schDate = ? ORDER BY schedID Asc");
			$stmt->bind_param("s",$param1);

		}

		$stmt->execute();
		$result = $stmt->get_result();
    if ($result->num_rows > 0) {
?>
<div class="container">
<header style="text-align:center;"><h2>Events</h2></header>
<?php
      $counter = 0;
			while ($row = $result->fetch_assoc()) {

				$blnAllowReg = false;
        $schedID = $row["schedID"];
		    $dDate = $row["schDate"];
        $event = $row["event"];
        $comments = $row["comments"];
			  $blnAllowReg = $row["allow_reg"];
			  $dDate = date_create($dDate);

				if ($counter >= 1) {
				echo "<hr />";
				}
?>
  <div>
    <h4 style="text-align:center;"><?php echo date_format($dDate,"jS \of M Y"); ?></h4>
    <div class="row">
      <div class="-3u 6u$ 12u$(medium)" style="padding-bottom:30px;" id="view_title"><h2 style="text-align:center;"><?php echo $event; ?></h2></div>
      <div class="-3u 6u$ 12u$(medium)" style="padding-bottom:30px;" id="view_comments"><pre class="pre"><?php echo $comments; ?></pre></div>
<?php
        if ($blnAllowReg) {
?>
      <div class="-2u 8u$ " style="text-align:center;">
        <a class="button fit fancybox.ajax" href="calendar_view.php?reg=yes&schedid=<?php echo $schedID; ?>">Register</a>
      </div>
<?php
			  }
		    if (LETUSERS) {
?>
      <div class="-2u 8u$ " style="text-align:center;">
        <a class="button fit" style="cursor:pointer;" onclick="return confirmSubmit('Are you SURE you want to delete this event?','calendar_view.php?delete=yes&schedid=<?php echo $schedID; ?>')">Delete</a>
      </div>
<?php
        }
				$counter++;
?>
    </div>
  </div>
<?php
		  }
?>
</div>
<?php
    }
    mysqli_close($conn);
	}

  if (isset($_GET["sched"])) {

	  $sYear = "";
	  $sYear = date("y");
?>
<div id="main" class="container">
  <header><h2 style="text-align:center;">Schedule an Event</h2></header>
  <form action="#" name="schedule" method="post" onsubmit="return validateSched();">
	<input type="hidden" name="schedule" value="now">
  <div class="row">
		<div class="-3u 6u 12u(medium)" style="padding-bottom:30px;">
	    <label for="event_name" style="margin-bottom:-2px;"><strong>Event Name</strong></label>
		  <input type="text" id="event_name" name="event_name" required />
    </div>
    <div class="-3u 6u 12u(medium)"><h5 style="margin-bottom:-2px;">Select Date</h5></div>
	  <div class="4u">
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
		<div class="4u">
      <div class="select-wrapper">
			  <span id="mFocus">
					<select name="mcount">
            <option>Select Month</option>
          </select>
				</span>
      </div>
    </div>
    <div class="4u$ ">
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
      <input type="submit" name="submit" value="Submit" class="button fit">
		</div>
  </div>
  </form>
  </div>

<?php } ?>
		</body>
</html>