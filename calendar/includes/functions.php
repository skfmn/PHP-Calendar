<?php

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM " . DBPREFIX . "settings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $siteTitle = $row["site_title"];
    $domain = $row["domain_name"];
    $blnLetUsers = $row["letusers"];
    $delDays = $row["delete_days"];
    $showLegend = $row["show_legend"];
    $announcements = $row["announcements"];

    define('SITETITLE', $siteTitle);
    define('DOMAIN', $domain);
    define('LETUSERS', $blnLetUsers);
    define('DELDAYS', $delDays);
    define('SHOWLEGEND', $showLegend);
    define('ANNOUNCEMENTS', trim($announcements));

}
mysqli_close($conn);

if (isset($_SESSION["caladminname"])) {
    getMyInfo($_SESSION["caladminID"]);
}

function getMyInfo($iAdminID)
{
    global $blnSchedule, $blnEvents, $blnSettings, $blnAdminRights, $blnARights, $blnPurge;

    if (isset($_SESSION["loggedin"])) {
        if ($_SESSION["loggedin"] == "") {

            $_SESSION["loggedin"] = "yes";

            $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $sql = "SELECT * FROM " . DBPREFIX . "admin WHERE adminID = " . $iAdminID;
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $blnSchedule = $row["schedule"];
                $blnEvents = $row["events"];
                $blnSettings = $row["settings"];
                $blnAdminRights = $row["admin_rights"];
                $blnARights = $row["arights"];
                $blnPurge = $row["purge_events"];
            }
            mysqli_close($conn);
            $_SESSION["blnSchedule"] = $blnSchedule;
            $_SESSION["blnEvents"] = $blnEvents;
            $_SESSION["blnSettings"] = $blnSettings;
            $_SESSION["blnAdminRights"] = $blnAdminRights;
            $_SESSION["blnARights"] = $blnARights;
            $_SESSION["blnPurge"] = $blnPurge;

        } else {

            $blnSchedule = $_SESSION["blnSchedule"];
            $blnEvents = $_SESSION["blnEvents"];
            $blnSettings = $_SESSION["blnSettings"];
            $blnAdminRights = $_SESSION["blnAdminRights"];
            $blnARights = $_SESSION["blnARights"];
            $blnPurge = $_SESSION["blnPurge"];

        }
    } else {

        $_SESSION["loggedin"] = "yes";

        $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $sql = "SELECT * FROM " . DBPREFIX . "admin WHERE adminID = " . $iAdminID;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $blnSchedule = $row["schedule"];
            $blnEvents = $row["events"];
            $blnSettings = $row["settings"];
            $blnAdminRights = $row["admin_rights"];
            $blnARights = $row["arights"];
            $blnPurge = $row["purge_events"];
        }
        mysqli_close($conn);
        $_SESSION["blnSchedule"] = $blnSchedule;
        $_SESSION["blnEvents"] = $blnEvents;
        $_SESSION["blnSettings"] = $blnSettings;
        $_SESSION["blnAdminRights"] = $blnAdminRights;
        $_SESSION["blnARights"] = $blnARights;
        $_SESSION["blnPurge"] = $blnPurge;

    }

}

function trace($stxt)
{
    echo $stxt . "<br />";
}

function selectAllEvents()
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM " . DBPREFIX . "calendar ORDER BY schDate asc";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<select id=\"schedid\" name=\"schedid\" required>\n";
        echo "  <option value=\"\">Select an event</option>\n";
        while ($row = $result->fetch_assoc()) {
            $event = $row["event"];
            if (strlen($event) > 15) {
                $event = substr($event, 0, 15) . "...";
            }
            echo "  <option value=\"" . $row["schedID"] . "\" title=\"" . $row["event"] . "\">" . $row["schDate"] . " - " . $event . "</option>\n";
        }
        echo "</select>\n";
    } else {
        echo "<select id=\"schedid\" name=\"schedid\"><option value=\"0\">No Current Events</option></select>\n";
    }
    mysqli_close($conn);

}

function selectCurrentEvents($dDate)
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM " . DBPREFIX . "calendar WHERE schDate >= '" . $dDate . "' ORDER BY schDate asc";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<select name=\"schedid\">\n";
        echo "  <option value=\"0\">Select an event</option>\n";
        while ($row = $result->fetch_assoc()) {
            $event = $row["event"];
            if (strlen($event) > 15) {
                $event = substr($event, 0, 15) . "...";
            }
            echo "  <option value=\"" . $row["SchedID"] . "\" title=\"" . $row["event"] . "\">" . $row["schDate"] . " - " . $event . "</option>\n";
        }
        echo "</select>\n";
    } else {
        echo "<select name=\"schedid\"><option value=\"0\">No Current Events</option></select>\n";
    }
    mysqli_close($conn);

}

function selectPastEvents($dDate)
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM " . DBPREFIX . "calendar WHERE schDate < '" . $dDate . "' ORDER BY schDate asc";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<select name=\"schedid\">\n";
        echo "  <option value=\"0\">Select an event</option>\n";
        while ($row = $result->fetch_assoc()) {
            $event = $row["event"];
            if (strlen($event) > 15) {
                $event = substr($event, 0, 15) . "...";
            }
            echo "  <option value=\"" . $row["SchedID"] . "\" title=\"" . $row["event"] . "\">" . $row["schDate"] . " - " . $event . "</option>\n";
        }
        echo "</select>\n";
    } else {
        echo "<select name=\"schedid\"><option value=\"0\">No Past Events</option></select>";
    }
    mysqli_close($conn);

}

function selectRegistrants()
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT DISTINCT " . DBPREFIX . "registration.SchedID, " . DBPREFIX . "calendar.SchedID, " . DBPREFIX . "calendar.event  FROM " . DBPREFIX . "registration, " . DBPREFIX . "calendar WHERE " . DBPREFIX . "registration.SchedID = " . DBPREFIX . "calendar.SchedID ORDER BY " . DBPREFIX . "calendar.SchedID Asc";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "    <select name=\"schedid\">\n";
        echo "      <option value=\"0\">Select an Event</option>\n";
        while ($row = $result->fetch_assoc()) {
            $event = $row["event"];
            if (strlen($event) > 15) {
                $event = substr($event, 0, 15) . "...";
            }
            echo "      <option value=\"" . $row[DBPREFIX . "calendar.SchedID"] . "\" title=\"" . $row["event"] . "\" >" . $event . "</option>\n";
        }
        echo "    </select>\n";
        echo "    <br /><input type=\"image\" src=\"" . CALDIR . "images/registrants.jpg\" onclick=\"javascript:this.submit;\" />\n";
    } else {
        echo "    <select><option>No events</option></select>\n";
    }
    mysqli_close($conn);

}

function deleteDir($path)
{

    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            deleteDir(realpath($path) . '/' . $file);
        }

        return rmdir($path);

    } else if (is_file($path) === true) {

        return unlink($path);
    }

    return false;
}

function getMessage($sMsg)
{

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    $strTemp = "";
    $sMsg = test_input($sMsg);
    $sql = "SELECT message FROM " . DBPREFIX . "messages WHERE msg = '" . $sMsg . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $strTemp = $row["message"];
    } else {
        $strTemp = $sMsg;
    }
    $conn->close();

    return $strTemp;

}

function displayFancyMsg($sText)
{
?>
    <div style="display:none;">
        <a id="textmsg" href="#displaymsg">Message</a>
        <div id="displaymsg" style="background-color:#fff;text-align:left;border:0;">
            <div class="left_menu_block" style="padding:5px;">
                <div class="left_menu_top">
                    <h2>Message</h2>
                </div>
                <div class="left_menu_center" align="center" style="background-color:#fff; padding-left:0px;">
                    <span style="color:#444;">
                        <?php echo $sText; ?>
                    </span>
                </div>
                <div class="left_menu_bottom"></div>
            </div>
        </div>
    </div>
    <?php
}

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

function getRegistrants($sSchedID)
{
    ?>
    <div style="display:none">
        <a id="textnrmsg" href="#displaynrmsg">Message</a>
        <div id="displaynrmsg" style="background-color:#000000;color:#FFFFFF;text-align:center;width:400px;">
            <h4>
                <a class="first" target="_blank" href="printpage.php?schedid=<?php echo $sSchedID; ?>">Printable page</a>
            </h4>
            <div style="float:left;position:relative;display:block;width:95%;padding-bottom:5px;">
                <div style="float:left;position:relative;display:inline;width:50%">
                    <strong>Name</strong>
                </div>
                <div style="float:right;position:relative;display:inline;width:50%;">
                    <strong>Info</strong>
                </div>
            </div>
            <hr style="font-weight:bold;width:99%" />
            <?php
            $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $sql = "SELECT * FROM registration WHERE schedID = " . $sSchedID . " ORDER BY reg_name asc";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "        <div style=\"float:left;position:relative;display:block;width:95%;padding-bottom:5px;\">\n";
                    echo "          <div style=\"float:left;position:relative;display:inline;width:50%;text-align:left;\">" . $row["reg_name"] . "</div>\n";
                    echo "          <div style=\"float:right;position:relative;display:inline;width:50%;text-align:left;\"><pre class=\"pre\">" . $row["add_info"] . "</pre></div>\n";
                    echo "        </div>\n";
                    echo "        <hr style=\"font-weight:bold;width:99%\" />\n";
                }
            } else {
                echo "        <div style=\"float:left;position:relative;display:block;width:99%;padding-bottom:5px;\">Oops! You forgot to select something!</div>\n";
                echo "        <hr style=\"font-weight:bold;width:99%\" />\n";
            }
            mysqli_close($conn);
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <?php
}

function getPrintRegistrants($sSchedID)
{
    ?>
    <div style="background-color:#FFFFFF;color:#000000;text-align:center;width:400px;">
        <h4>
            <a class="first" href="javascript:window.print();">Print page</a>
        </h4>
        <div style="float:left;position:relative;display:block;width:95%;padding-bottom:5px;">
            <div style="float:left;position:relative;display:inline;width:50%">
                <strong>Name</strong>
            </div>
            <div style="float:right;position:relative;display:inline;width:50%;">
                <strong>Info</strong>
            </div>
        </div>
        <hr style="font-weight:bold;width:99%" />
        <?php
        $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $sql = "SELECT * FROM registration WHERE schedID = " . $sSchedID . " ORDER BY reg_name asc";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "        <div style=\"float:left;position:relative;display:block;width:95%;padding-bottom:5px;\">\n";
                echo "          <div style=\"float:left;position:relative;display:inline;width:50%;text-align:left;\">" . $row["reg_name"] . "</div>\n";
                echo "          <div style=\"float:right;position:relative;display:inline;width:50%;text-align:left;\"><pre class=\"pre\">" . $row["add_info"] . "</pre></div>\n";
                echo "        </div>\n";
                echo "        <hr style=\"font-weight:bold;width:99%\" />\n";
            }
        } else {
            echo "        <div style=\"float:left;position:relative;display:block;width:99%;padding-bottom:5px;\">Oops! You forgot to select something!</div>\n";
            echo "        <hr style=\"font-weight:bold;width:99%\" />\n";
        }
        mysqli_close($conn);
        ?>
        <div class="clear"></div>
    </div>
    <?php
}

function selectDate()
{
    echo "<select name=\"mcount\" onchange=\"getFocusArea();\">\n";
    echo "  <option value=\"0\">Select Month</option>\n";
    for ($x = 1; $x <= 12; $x++) {
        echo "  <option value=\"" . $x . "\">" . $x . "</option>\n";
    }
    echo " </select>\n";
    ?>
    <span id="Focus">
        <select name="dcount">
            <option>Select Day</option>
        </select>
    </span>
    <?php
    echo "  <select name=\"ycount\">\n";
    echo "    <option>Select Year</option>\n";
    for ($x = 20; $x <= 40; $x++) {
        echo "  <option>20" . $x . "</option>\n";
    }
    echo "  </select>\n";
}

function selectMonth()
{
    echo "  <select name=\"mcount\">\n";
    for ($x = 1; $x <= 12; $x++) {
        $y = $x + 1;
        if (date("d") == $y) {
            echo "  <option selected=\"selected\">" . $y . "</option>\n";
        } else {
            echo "  <option>" . $y . "</option>\n";
        }
    }
    echo "  </select>\n";
    echo "  <select name=\"ycount\">\n";
    $i = date("Y");
    $y = (date("Y") + 10);
    for ($x = $i; $x <= $y; $x++) {
        echo "  <option>" . $x . "</option>\n";
    }
    echo " </select>\n";
}

function getDaysInMonth($iMonth, $iYear)
{

    $idate = date_create($iYear . "-" . $iMonth . "-1");
    //$idate = date_format($idate,"Y-m-d");
    $dTemp = date_format($idate, "t");

    return $dTemp;
}

function getWeekdayMonthStartsOn($dAnyDayInTheMonth)
{
    $idate = date_create($dAnyDayInTheMonth);
    $interval = date_format($idate, "j");
    $interval = ($interval - 1);
    $dTemp = date_add($idate, date_interval_create_from_date_string("-" . $interval . " days"));

    return date_format($dTemp, "w");
}

function subtractOneMonth($dDate)
{
    $dTemp = date_add(date_create($dDate), date_interval_create_from_date_string("-1 month"));
    return date_format($dTemp, "Y-m-d");
}

function addOneMonth($dDate)
{
    $dTemp = date_add(date_create($dDate), date_interval_create_from_date_string("1 month"));
    return date_format($dTemp, "Y-m-d");
}

function schedCheck($dCurr)
{

    $temp = false;

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM " . DBPREFIX . "calendar WHERE schDate = '" . $dCurr . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $temp = true;
    }
    mysqli_close($conn);

    return $temp;
}

function msgTrans($sMsg)
{
    $strtmp = "";
    switch ($sMsg) {
        case "logch":
            $strtmp = "Login changed";
            break;
        case "evsch":
            $strtmp = "Event scheduled:";
            break;
        case "evmod":
            $strtmp = "Event modified:";
            break;
        case "setch":
            $strtmp = "Settings updated:";
            break;
        case "evdel":
            $strtmp = "Event deleted:";
            break;
        case "regs":
            $strtmp = "Registration success:";
            break;
        case "ps":
            $strtmp = "Purged events:";
            break;
        case "notad":
            $strtmp = "No date entered:";
            break;
        case "regdelf":
            $strtmp = "Notice for delete error:";
            break;
        case "evdelf":
            $strtmp = "Event delete error:";
            break;
        case "logchf":
            $strtmp = "Password error:";
            break;
        case "evschf":
            $strtmp = "Schedule error:";
            break;
        case "setchf":
            $strtmp = "Settings error:";
            break;
        case "dels":
            $strtmp = "Registrant removed:";
            break;
        case "delsf":
            $strtmp = "Registrant remove error:";
            break;
        case "eds":
            $strtmp = "Registrant updated:";
            break;
        case "edsf":
            $strtmp = "Registrant update error:";
            break;
        case "nan":
            $strtmp = "Not a number:";
            break;
        case "evmodf":
            $strtmp = "Event update error:";
            break;
        case "mus":
            $strtmp = "Messages updated:";
            break;
        case "error":
            $strtmp = "Generic error:";
            break;
        case "opa":
            $strtmp = "Site changed:";
            break;
        case "adad":
            $strtmp = "Admin added:";
            break;
        case "das":
            $strtmp = "Admin deleted:";
            break;
        case "ant":
            $strtmp = "Admin taken:";
            break;
        case "cpwds":
            $strtmp = "Changed Admins Password:";
            break;
        case "nadmin":
            $strtmp = "No change Admin info:";
            break;
        default:
            $strtmp = "If you see this you messed with the code!";
    }

    return $strtmp;
}

function catcherror($sText) {
    if (error_get_last() !== null) {
        $errText = $sText . " - " . error_get_last();
        error_clear_last();
    } else {
        $errText = $sText . " No Error ";
    }
    return $errText;
}

?>