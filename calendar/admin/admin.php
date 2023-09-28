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

 $baseDir = BASEDIR;
 $baseDir = str_replace("\\\\", "\\", $baseDir . "calendar\\install\\");
 $dir = $baseDir;
 if (is_dir($dir)) {
   deleteDir($dir);
 }

  include "../includes/header.php";
?>
<div id="main" class="container">
    <header>
        <h1 style="text-align: center;font-size:30px">PHP Calendar</h1>
        <h4 style="text-align: center;">Choose an Option below</h4>
    </header>

  <div class="row">
		<div class="-3u 3u 12u$(medium)">
			<ul class="alt">
				<li><a class="button fit" href="admin_schedule.php"><span>Schedule an Event</span></a></li>
				<li><a class="button fit" href="admin_view.php"><span>Manage Events</span></a></li>
			</ul>
		</div>
		<div class="3u$ 12u$(medium)">
			<ul class="alt">
				<li><a class="button fit" href="admin_settings.php"><span>Manage Settings</span></a></li>
				<li><a class="button fit" href="admin_manage.php"><span>Manage Admins</span></a></li>
			</ul>
		</div>
    <div class="-3u 6u$ 12u$(medium)">
      <?php echo file_get_contents("http://www.phpjunction.com/gnews.php?ref=y&pcl=".$version.""); ?>
    </div>
  </div>
</div>
<?php include "../includes/footer.php" ?>