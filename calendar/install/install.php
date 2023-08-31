<?php
ob_start();

function redirect($location)
{
  if ($location) {

    header('Location: ' . $location);
    exit;

  }
}

function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function test_inputA($data)
{
  $data = trim($data);
  $data = htmlspecialchars($data);
  return $data;
}

$step = "";
$step = isset($_GET["step"]) ? $_GET['step'] : "";

$servername = $username = $dbpassword = $dbname = $dbprefix = $basedir = $caldir = $password = "";
$param1 = $param2 = $param3 = $param4 = $param5 = $param6 = $param7 = $param8 = $param9 = "";
$sitetitle = $domain = "";
?>
<!DOCTYPE HTML>
<html>
<head>
  <title>PHP Calendar Installation</title>
  <link type="text/css" rel="stylesheet" href="../assets/css/main.css" />
</head>
<body>
  <div id="main" class="container" align="center" style="margin-top:-75px;">
    <div class="row 50%">
      <div class="12u 12u$(medium)">
        <header>
          <h2>PHP Calendar Installation</h2>
        </header>
      </div>
    </div>
  </div>
  <?php
  if ($step == "one") {
    ?>
    <div id="main" class="container" align="center" style="margin-top:-100px;">
      <div class="row 50%">
        <div class="12u 12u$(medium)">
          <form action="install.php?step=two" method="post">
            <header>
              <h2>MySQL Database</h2>
            </header>
            <div class="row">
              <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                <label for="servername" style="text-align:left;">
                  Server Host Name or IP Address
                  <input type="text" name="servername" required />
                </label>
              </div>
              <div class="4u 1u$">
                <span></span>
              </div>

              <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                <label for="dbname" style="text-align:left;">
                  Database Name
                  <input type="text" name="dbname" required />
                </label>
              </div>
              <div class="4u 1u$">
                <span></span>
              </div>

              <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                <label for="username" style="text-align:left;">
                  Database Login
                  <input type="text" name="username" required />
                </label>
              </div>
              <div class="4u 1u$">
                <span></span>
              </div>

              <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                <label for="dbpassword" style="text-align:left;">
                  Database Password
                  <input type="password" name="dbpassword" required />
                </label>
              </div>
              <div class="4u 1u$">
                <span></span>
              </div>

              <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                <label for="dbprefix" style="text-align:left;">
                  Table Prefix
                  <input type="text" name="dbprefix" value="cal_" required />
                </label>
              </div>
              <div class="4u 1u$">
                <span></span>
              </div>

              <div class="12u 12u$(medium)">
                <input class="button" type="submit" name="submit" value="Continue" />
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php
  } else if ($step == "two") {
    ?>
      <div id="main" class="container" align="center">
        <div class="row 50%">
          <div class="12u 12u$(medium)">
            <?php

            $servername = test_input($_POST["servername"]);
            $dbname = test_input($_POST["dbname"]);
            $username = test_input($_POST["username"]);
            $dbpassword = test_input($_POST["dbpassword"]);
            $dbprefix = test_input($_POST["dbprefix"]);

            $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
            if (!$conn) {
              die("Connection failed: " . mysqli_connect_error());
            }

            echo "Creating Database Tables<br /><br />";

            echo "Creating admin table...<br />";

            $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "admin (
            adminID int(11) NOT NULL AUTO_INCREMENT ,
	          name VARCHAR(255) NOT NULL ,
	          pwd VARCHAR(255) NOT NULL ,
            PRIMARY KEY (adminID)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

            if ($conn->query($sql)) {
              echo "Admin table created successfully<br />";
            } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
            }

            echo "Populating admin table...<br /><br />";

            $password = password_hash("admin", PASSWORD_DEFAULT);
            $param1 = "admin";

            $stmt = $conn->prepare("INSERT INTO " . $dbprefix . "admin (name,pwd) VALUES (?,?)");
            $stmt->bind_param('ss', $param1, $password);

            if ($stmt->execute()) {
              echo "Admin table populated successfully<br /><br />";
            } else {
              echo "Error: <br>" . $conn->error;
            }

            echo "Creating settings table...<br />";

            $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "settings (
            settingID int(11) NOT NULL AUTO_INCREMENT ,
	          site_title VARCHAR(255) DEFAULT NULL ,
	          domain_name VARCHAR(255) DEFAULT NULL ,
            letusers CHAR(1) ,
            delete_days int(10) ,
            announcements text ,
            PRIMARY KEY (settingID)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

            if ($conn->query($sql)) {
              echo "Settings created successfully<br /><br />";
            } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
            }

            echo "Populating settings table...<br /><br />";

            $conn->query("INSERT INTO " . $dbprefix . "settings (letusers,delete_days,announcements) VALUES (0,30,'Announcements go here!')");

            echo "Creating Messages table...<br />";

            $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "messages (
	          messageID int(11) NOT NULL AUTO_INCREMENT ,
	          msg VARCHAR(50) ,
            message VARCHAR(250) ,
					  PRIMARY KEY (messageID)
					  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

            if ($conn->query($sql)) {
              echo "Messages created successfully<br /><br />";
            } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
            }

            echo "Populating Messages table...<br /><br />";

            $sql = "INSERT INTO " . $dbprefix . "messages (msg,message) VALUES
		       ('logch', 'Your login information was successfully changed.'),
           ('evsch', 'Event was successfully scheduled.'),
           ('evmod', 'The event information was successfully changed.'),
           ('setch', 'The settings was successfully updated.'),
           ('evdel', 'The event was successfully deleted.'),
           ('regs', 'Registration was successful!'),
           ('ps', 'Purge of past events was successful!'),
           ('notad', 'You did not enter a date!'),
           ('regdelf', 'Notice: Event was deleted but registrants were not.'),
           ('evdelf', 'There was a problem and the Event was not deleted'),
           ('logchf', 'There was a problem and your password could not be updated.'),
           ('evschf', 'There was a problem and the Event could not be Scheduled.'),
           ('setchf', 'There was a problem and the Settings could not be updated.'),
           ('dels', 'Registrant was successfully removed.'),
           ('delsf', 'There was a problem and the registrant could not be removed.'),
           ('eds', 'Registrant information was updated.'),
           ('edsf', 'There was a problem and the registrants information could not be updated.'),
           ('nan', 'Please enter a number.'),
           ('mus', 'Messages updated successfully!'),
           ('error', 'An unknown error has occurred.<br />Please contact support.'),
           ('opa', 'Site settings have been changed!'),
           ('cpwds', 'You changed the password successfully!'),
           ('adad', 'You have successfully added an Admin.'),
           ('das', 'You have successfully deleted an Admin.'),
           ('ant', 'Admin name taken.'),
           ('nadmin', 'You can not change this Admins Info.'),
           ('evmodf', 'There was a problem and the Event information could not be updated.')";

            if ($conn->query($sql)) {
              echo "Messages table populated successfully<br /><br />";
            } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
            }

            echo "Creating Registration table...<br /><br />";

            $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "registration (
	         regID int(11) NOT NULL AUTO_INCREMENT ,
	         schedID int(10) ,
				   reg_name VARCHAR(50) ,
	         add_info text ,
				   PRIMARY KEY (regID)
					 ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

            if ($conn->query($sql)) {
              echo "Registration table populated successfully<br /><br />";
            } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
            }

            echo "Creating Calendar table...<br />";

            $sql = "CREATE TABLE IF NOT EXISTS " . $dbprefix . "calendar (
	          schedID int(11) NOT NULL AUTO_INCREMENT ,
	          allow_reg CHAR(1) ,
            schDate VARCHAR(25) ,
            event VARCHAR(255) ,
	          comments text ,
					  PRIMARY KEY (schedID)
					  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

            if ($conn->query($sql)) {
              echo "Calendar table created successfully<br /><br />";
            } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
            }

            echo "Creating database tables...Complete!<br />";
            ?>
            <br />
            <br />
          </div>
        </div>
      </div>
      <div id="main" class="container" align="center">
        <div class="row 50%">
          <div class="12u 12u$(medium)">
            <form action="install.php?step=three" method="post">
              <input type="hidden" name="servername" value="<?php echo $servername; ?>" />
              <input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
              <input type="hidden" name="username" value="<?php echo $username; ?>" />
              <input type="hidden" name="dbpassword" value="<?php echo $dbpassword; ?>" />
              <input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>" />
              <header>
                <h3>
                  <span class="first">You have successfully installed the MySQL Database!</span>
                </h3>
              </header>
              <div class="row">
                <div class="12u 12u$(medium)">
                  <input class="button" type="submit" name="submit" value="Continue" />
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <?php
  } else if ($step == "three") {

    $absPath = "";
    $absPath = $_SERVER['DOCUMENT_ROOT'] . "\\";
    ?>
        <div id="main" class="container" align="center">
          <div class="row 50%">
            <div class="12u 12u$(medium)">
              <form action="install.php?step=four" method="post">
                <input type="hidden" name="servername" value="<?php echo test_input($_POST["servername"]); ?>" />
                <input type="hidden" name="dbname" value="<?php echo test_input($_POST["dbname"]); ?>" />
                <input type="hidden" name="username" value="<?php echo test_input($_POST["username"]); ?>" />
                <input type="hidden" name="dbpassword" value="<?php echo test_input($_POST["dbpassword"]); ?>" />
                <input type="hidden" name="dbprefix" value="<?php echo test_input($_POST["dbprefix"]); ?>" />
                <header>
                  <h2>Path Settings</h2>
                </header>
                <div class="row">
                  <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                    <label for="basedir" style="text-align:left;">
                      Base Directory
                      <input type="text" name="basedir" value="<?php echo $absPath; ?>" />
                    </label>
                  </div>
                  <div class="4u 1u$">
                    <span></span>
                  </div>

                  <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                    <label for="caldir" style="text-align:left;">
                      PHP Calendar Directory
                      <input type="text" name="caldir" value="/calendar/" size="40" />
                    </label>
                  </div>
                  <div class="4u 1u$">
                    <span></span>
                  </div>
                  <div class="12u 12u$(medium)">
                    <input class="button" type="submit" name="submit" value="Continue" />
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php
  } else if ($step == "four") {

    $file = $fileA = "";

    $servername = test_input($_POST["servername"]);
    $username = test_input($_POST["username"]);
    $dbpassword = test_input($_POST["dbpassword"]);
    $dbname = test_input($_POST["dbname"]);
    $dbprefix = test_input($_POST["dbprefix"]);
    $basedir = test_inputA($_POST["basedir"]);
    $caldir = test_input($_POST["caldir"]);

    $basedir = preg_replace("/([\\\])/", '${1}${1}', $basedir);

    $file = fopen('../includes/globals.php', "r");
    $fileA = fread($file, filesize('../includes/globals.php'));
    fclose($file);

    $file = fopen('../includes/globals.php', "w");

    $fileA = str_replace("{#servername#}", $servername, $fileA);
    $fileA = str_replace("{#username#}", $username, $fileA);
    $fileA = str_replace("{#dbpassword#}", $dbpassword, $fileA);
    $fileA = str_replace("{#dbname#}", $dbname, $fileA);
    $fileA = str_replace("{#dbprefix#}", $dbprefix, $fileA);
    $fileA = str_replace("{#basedir#}", $basedir, $fileA);
    $fileA = str_replace("{#caldir#}", $caldir, $fileA);

    fwrite($file, $fileA);

    fclose($file);

    ?>
          <div id="main" class="container" align="center">
            <div class="row 50%">
              <div class="12u 12u$(medium)">
                <form action="install.php?step=five" method="post">
                  <input type="hidden" name="servername" value="<?php echo $servername; ?>" />
                  <input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
                  <input type="hidden" name="username" value="<?php echo $username; ?>" />
                  <input type="hidden" name="dbpassword" value="<?php echo $dbpassword; ?>" />
                  <input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>" />
                  <input type="hidden" name="caldir" value="<?php echo $caldir; ?>" />
                  <header>
                    <h3>
                      <span class="first">
                        You have successfully set the configuration file<br />
                        Please click the button below to continue
                      </span>
                    </h3>
                  </header>
                  <div class="row">
                    <div class="12u 12u$(medium)">
                      <input class="button" type="submit" name="submit" value="Continue" />
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <?php
  } else if ($step == "five") {

    $servername = test_input($_POST["servername"]);
    $username = test_input($_POST["username"]);
    $dbpassword = test_input($_POST["dbpassword"]);
    $dbname = test_input($_POST["dbname"]);
    $dbprefix = test_input($_POST["dbprefix"]);
    $caldir = test_input($_POST["caldir"]);
    ?>
            <div id="main" class="container" style="margin-top:-100px;">
              <div class="row">
                <div class="12u 12u$(medium)" style="text-align:center;">
                  <form action="install.php?step=six" method="post">
                    <input type="hidden" name="servername" value="<?php echo $servername; ?>" />
                    <input type="hidden" name="dbname" value="<?php echo $dbname; ?>" />
                    <input type="hidden" name="username" value="<?php echo $username; ?>" />
                    <input type="hidden" name="dbpassword" value="<?php echo $dbpassword; ?>" />
                    <input type="hidden" name="dbprefix" value="<?php echo $dbprefix; ?>" />
                    <input type="hidden" name="caldir" value="<?php echo $caldir; ?>" />
                    <header>
                      <h2>Other stuff</h2>
                    </header>
                    <div class="row">

                      <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                        <label for="sitetitle" style="text-align:left;">
                          Site title
                          <input type="text" name="sitetitle" />
                        </label>
                      </div>
                      <div class="4u 1u$">
                        <span></span>
                      </div>

                      <div class="-4u 4u 12u$(medium)" style="padding-bottom:20px;">
                        <label for="domainname" style="text-align:left;">
                          Domain name
                          <input type="text" name="domainname" value="<?php echo $_SERVER["SERVER_NAME"]; ?>" />
                        </label>
                      </div>
                      <div class="4u 1u$">
                        <span></span>
                      </div>

                      <div class="12u 12u$(medium)">
                        <input class="button" type="submit" name="submit" value="Continue" />
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <?php
  } else if ($step == "six") {

    $servername = test_input($_POST["servername"]);
    $dbname = test_input($_POST["dbname"]);
    $username = test_input($_POST["username"]);
    $dbpassword = test_input($_POST["dbpassword"]);
    $dbprefix = test_input($_POST["dbprefix"]);
    $sitetitle = test_input($_POST["sitetitle"]);
    $domain = test_input($_POST["domainname"]);
    $caldir = test_input($_POST["caldir"]);

    $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
    if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $conn->prepare("UPDATE " . $dbprefix . "settings SET site_title = ?, domain_name =?");
    $stmt->bind_param('ss', $sitetitle, $domain);

    if ($stmt->execute()) {

      if ($_SERVER["HTTPS"] == "off") {
        $http = "http";
      } else {
        $http = "https";
      }
      ;

      $httpHost = $_SERVER["HTTP_HOST"];
      $redirect = $http . "://" . $httpHost . $caldir;
      redirect($redirect . "install/install.php?step=done");
      ob_end_flush();

    }
    $conn->close();

  } else if ($step == "done") {
    ?>
                <div id="main" class="container">
                  <div class="row">
                    <div class="12u 12u$(medium)" style="text-align:center;">
                      <span class="first">
                        Success!
                        <br />
                        You have successfully configured EZCalendar!
                        <br />
                        The next step is to change your password.
                        <br />
                        Click on the link below and login to admin.
                        <br />
                        Click on "Password" in the left options menu and change your password.
                        <br />
                        <br />
                        <a class="button" href="../admin/admin_login.php">Login</a>
                      </span>
                    </div>
                  </div>
                </div>
              <?php } else { ?>
                <div id="main" class="container" style="margin-top:-75px;">
                  <div class="row">
                    <div class="12u 12u$(medium)" style="text-align:center;">
                      <span class="first">
                        You are about to install PHP Calendar.
                        <br />
                        Please follow the instructions carefully!
                        <br />
                        <br />
                        <input class="button" type="button" onclick="parent.location='install.php?step=one'" value="Continue" />
                      </span>
                    </div>
                  </div>
                </div>
              <?php } ?>
  <br />
</body>
</html>