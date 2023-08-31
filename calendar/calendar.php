 <?php
  session_start();
  ob_start();
  include 'includes/globals.php';
  include 'includes/functions.php';

	$schedID = $datDate = $event = $comments = $cookies = "";
	$blnAllowReg = false;

  if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
	  if ($msg <> "") {
		  displayFancyMsg(getMessage($msg));
		  $_SESSION["msg"] = "";
    }
  }

  If (isset($_GET["date"])) {
	  $dDate = test_input($_GET["date"]);
  } else {
	  $dDate = date("Y-m-d");
  }

  $dDate = date_create($dDate);

  $dMonth = date_format($dDate,"m");
	$dYear = date_format($dDate,"Y");
  $dDate = date_format($dDate,"Y-m-d");

  $iDIM = getDaysInMonth($dMonth, $dYear);
  $iDOW = getWeekdayMonthStartsOn($dDate);

  $dDate = date_create($dDate);

	$dSOM = subtractOneMonth(date_format($dDate,"Y-m-d"));
	$dAOM = addOneMonth(date_format($dDate,"Y-m-d"));

	$subOneMonth = date_format(date_create($dSOM),"F Y");
	$addOneMonth = date_format(date_create($dAOM),"F Y");
?>
<html>
<head>
  <title>PHP Calendar</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link type="text/css" rel="stylesheet" href="assets/css/jquery.fancybox.css" />
  <link type="text/css" rel="stylesheet" href="assets/css/main.css" />
	<style type="text/css">.fancybox-wrap{border-radius:4px;}</style>
</head>
<body>
<div id="main" class="container">
  <header style="text-align:center;"><h2>Calendar</h2></header>
  <div class="row">
    <div class="-3u 1u 2u(medium)">
      <a class="button tooltip" href="calendar.php?date=<?php echo $dSOM; ?>" title="<?php echo $subOneMonth; ?>"><i class="fa fa-arrow-left" style="margin-top:15px;"></i></a>
    </div>
    <div class="-1u 2u 8u(medium)" style="text-align:center;">
      <?php echo date_format($dDate,"F Y"); ?>
    </div>
    <div class="-1u 1u$ 2u$(medium)">
      <a class="button tooltip" href="calendar.php?date=<?php echo $dAOM; ?>" title="<?php echo $addOneMonth; ?>"><i class="fa fa-arrow-right" style="margin-top:15px;"></i></a>
    </div>
    <div class="-3u 6u 12u(medium)" style="padding-top:10px;">
      <div class="table-wrapper">
        <table class="alt">
          <thead>
            <tr>
              <th>S</th>
              <th>M</th>
              <th>T</th>
              <th>W</th>
              <th>T</th>
              <th>F</th>
              <th>S</th>
            </tr>
          </thead>
          <tbody>
            <?php

  //Write spacer cells at beginning of first row if month doesn't start on a Sunday.
  if ($iDOW <> 1) {
		if ($iDOW <> 0) {
	    echo "        <tr>\n";
    }
		for ($iPosition=1;$iPosition<$iDOW+1;$iPosition++) {
			echo "          <td>&nbsp;</td>\n";
		}
  }

  //Write days of month in proper day slots
  $iCurrent = 1;
  for ($x=1;$x<=$iDIM;$x++) {

		//If we're at the begginning of a row then write <tr>
		if ($iPosition == 1) {
			echo "        <tr>\n";
		}

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	  if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

		$yr = date_format($dDate,"Y");
		$mth = date_format($dDate,"m");
    $sDate = date_create($yr."-".$mth."-".$iCurrent);
		$sDate = date_format($sDate, "Y-m-d");

		$blnDBDate = schedCheck($sDate);
		if ($blnDBDate==true) {
		  $event= "";
      $schedID = 0;

		  $sql = "SELECT * FROM ".DBPREFIX."calendar WHERE schDate = '".$sDate."' ORDER BY SchedID ASC";

		  $result = $conn->query($sql);
		  if ($result->num_rows > 0) {
				$count = mysqli_num_rows($result);
				$row = $result->fetch_assoc();

				$event = $row["event"];
        $schedID = $row["schedID"];
			}
		}

		if (($blnDBDate==true) AND ($sDate == date("Y-m-d"))) {
			//if event date matches todays date then color it green
      if ($schedID <> 0) {
			  echo "          <td class=\"tooltip calview fancybox.iframe\" style=\"background-color:#00FF00;cursor:pointer;\" href=\"calendar_view.php?view=yes&sdate=".$sDate."\" title=\"".$event." (".$count." events)\">".$iCurrent."</td>\n";
      } else {
        echo "          <td>".$iCurrent."</td>\n";
      }

		} else if ($blnDBDate==true AND ($sDate > date("Y-m-d"))) {
			//if event date is after todays date (upcoming event) then color it yellow
      if ($schedID <> 0) {
			  echo "          <td class=\"tooltip calview fancybox.iframe\" style=\"background-color:#FFFF00;cursor:pointer;\" href=\"calendar_view.php?view=yes&sdate=".$sDate."\" title=\"".$event." (".$count." events)\">".$iCurrent."</td>\n";
      } else {
        echo "          <td>".$iCurrent."</td>\n";
      }

		} else if ($blnDBDate==true AND ($sDate < date("Y-m-d"))) {
			//if event date is before todays date (past event) then color it red
      if ($schedID <> 0) {
			  echo "          <td class=\"tooltip calview fancybox.iframe\" style=\"background-color:#FF0000;cursor:pointer;\" href=\"calendar_view.php?view=yes&schedid=".$schedID."\" title=\"".$event." (".$count." events)\">".$iCurrent."</td>\n";
      } else {
        echo "          <td>".$iCurrent."</td>\n";
      }

		} else if ($sDate == date("Y-m-d")) {

			//highlight todays date
			echo "          <td style=\"background-color:#676767;color:#FFFFFF;\">".$iCurrent."</td>\n";

		} else {

			//Rest of the days in the month
		  echo "          <td>".$iCurrent."</td>\n";

		}

		//If we're at the end of a row then write </tr>
		if ($iPosition == 7) {
			echo "        </tr>\n";
			$iPosition = 0;
		}

		//Increment variables
		$iCurrent++;
		$iPosition++;
  }

	//Write spacer cells at end of last row if month doesn't end on a Saturday.
	if ($iPosition <> 1) {
		while ($iPosition <= 7) {
			echo "          <td>&nbsp;</td>\n";
			$iPosition++;
		}
    echo "        </tr>\n";
	}

            ?>
          </tbody>
        </table>
      </div>
    </div>
<?php if (LETUSERS) { ?>
    <div class="-3u 6u 12u(medium)">
    <a class="button calview fancybox.iframe" href="calendar_view.php?sched=yes" title="Schedule Event">Schedule Event</a>
    <br /><br />
    </div>
<?php } ?>
    <div class="-3u 6u 12u(medium)">
      <header><h3>Announcements</h3></header>
      <pre><?php echo ANNOUNCEMENTS; ?></pre>
    </div>
    <br /><br />
  </div>
  <div class="-3u 6u 12u(medium)" style="text-align:center;">
    <span style="font-size:16px">Powered by <a href="http://phpjunction.com/webapps/">PHP Calendar</a>  Copyright &copy; <?php echo date("Y") ?>  <a href="https://www.phpjunction.com">PHP Junction</a> </span>
  </div>
</div>
<script language="javascript" type="text/javascript" src="//code.jquery.com/jquery-3.6.0.js"></script>
<script language="javascript" type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script language="javascript" type="text/javascript" src="assets/js/jquery.fancybox.js" ></script>
<script language="javascript" type="text/javascript" src="assets/js/js_functions.js" ></script>
<script language="JavaScript">
$(document).ready(function(){
	$(".picimg").fancybox({
		afterClose : function() {
			location.href='calendar.php';
		}
	});
	$(".calview").fancybox({
		padding    : 0,
		maxWidth	 : 600,
		maxHeight	 : 800,
		autoResize :true,
		//width		   : '70%',
		//height		 : '70%',
		closeBtn      : true,
    openEffect    : 'fade',
    closeEffect   : 'fade',
		//afterShow : function() {
			//parent.$.fancybox.update();
		//},
		afterClose : function() {
			location.reload();
			return;
		}
	});
	$("#textmsg").fancybox();
	$("#textmsg").trigger('click');
	$(".tooltip").tooltip();
});
</script>
</body>
</html>