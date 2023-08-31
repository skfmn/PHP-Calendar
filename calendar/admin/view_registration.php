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

  $schedID = "";
	if (isset($_SESSION["sid"])) {
		$schedID = $_SESSION["sid"];
		$_SESSION["sid"] = "";
	}

	if (isset($_GET["eid"])) {
		$schedID = $_GET["eid"];
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
	include "../includes/header.php";
?>
<div id="main" class="container">
  <header style="text-align:center;"><h2>Registrants</h2></header>
<?php

	$submit = isset($_POST["submit"]) ? $_POST['submit'] : "";

	if ($submit == "Remove") {

    $regID = isset($_POST["rid"]) ? $_POST['rid'] : "";

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	  if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

		$stmt = mysqli_prepare($conn,"DELETE FROM ".DBPREFIX."registration WHERE regID = ?");
		$stmt->bind_param("s", $regID);
		if ($stmt->execute()) {
		  $_SESSION["msg"] = "dels";
		} else {
      $_SESSION["msg"] = "delsf";
    }
    mysqli_close($conn);

    $_SESSION["sid"] = $schedID;
		redirect($redirect."admin/view_registration.php");
		ob_end_flush();

	}

	if ($submit == "Edit") {

    $regID = isset($_POST["rid"]) ? $_POST['rid'] : "";

    $name = test_input($_POST["rname"]);
    $addInfo = test_input($_POST["addinfo"]);

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	  if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

		$stmt = mysqli_prepare($conn,"UPDATE ".DBPREFIX."registration SET reg_name = ?, add_info = ?  WHERE regID = ?");
		$stmt->bind_param("sss", $name, $addInfo, $regID);
		if ($stmt->execute()) {
		  $_SESSION["msg"] = "eds";
		} else {
      $_SESSION["msg"] = "edsf";
    }
    mysqli_close($conn);

    $_SESSION["sid"] = $schedID;
		redirect($redirect."admin/view_registration.php");
		ob_end_flush();

	}

	if ($schedID <> "") {

	  $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
	  if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

		$stmt = mysqli_prepare($conn,"SELECT ".DBPREFIX."registration.*, ".DBPREFIX."calendar.* FROM ".DBPREFIX."registration INNER JOIN ".DBPREFIX."calendar ON ".DBPREFIX."registration.schedID = ".DBPREFIX."calendar.schedID  WHERE ".DBPREFIX."calendar.schedID = ?");
		$stmt->bind_param("s", $schedID);
		$stmt->execute();

	  $result = $stmt->get_result();
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$event = $row["event"];
			$dDate = $row["schDate"];
			$dDate = date_create($dDate);
?>
  <div style="text-align:center;padding-bottom:10px;"><a class="button" href="admin_view.php?eid=<?php echo $schedID; ?>">Back to Event</a></div>
  <h4 style="text-align:center;"><?php echo date_format($dDate,"jS \of M Y"); ?><br /><?php echo $event; ?></h4>

<?php
			mysqli_data_seek($result, 0);
      while ($row = $result->fetch_assoc()) {
        $regID = $row["regID"];
		    $name = $row["reg_name"];
			  $addInfo = $row["add_info"];
?>
  <form action="view_registration.php" method="post" name="register<?php echo $regID; ?>" id="register<?php echo $regID; ?>" >
	<input type="hidden" name="rid" value="<?php echo $regID; ?>">
	<input type="hidden" name="schedid" value="<?php echo $schedID; ?>">
  <div class="row">
    <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:30px;">
      <label for="rname" style="margin-bottom:-3px;">Name</label>
      <input type="text" id="rname" name="rname" value="<?php echo $name; ?>" size="20" />
    </div>
    <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:10px;">
      <label for="addinfo" style="margin-bottom:-3px;">Additional info:</label>
      <textarea id="addinfo" name="addinfo"><?php echo $addInfo; ?></textarea>
    </div>
    <div class="-4u 2u 12u$(medium)">
      <input class="button fit" type="submit" name="submit" value="Edit" />
    </div>
    <div class="2u$ 12u$(medium)">
      <input class="button fit" type="submit" name="submit" value="Remove" />
    </div>
  </div>
  </form>
<?php
      }
    } else {
?>
  <div class="row">
    <div class="-3u 6u 12u(medium)" style="padding-bottom:30px;">No Registrants</div>
  </div>
<?php
		}
		mysqli_close($conn);
	}
?>
</div>
<?php include "../includes/footer.php" ?>