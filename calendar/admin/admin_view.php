<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$schedID = $datDate = $event = $comments = $cookies = "";
$blnAllowReg = false;
$purgenow = false;
$strPurgeText;

$cookies = $_SESSION["caladminname"];
if ($cookies == "") {

    redirect($redirect . "admin/amin_login.php");
    ob_end_flush();

}

if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
    if ($msg <> "") {
        displayFancyMsg(getMessage($msg));
        $_SESSION["msg"] = "";
    }
}

if (isset($_SESSION["sid"])) {
    $schedID = $_SESSION["sid"];
    $_SESSION["sid"] = "";
}

if (isset($_POST["schedid"])) {
    $schedID = $_POST["schedid"];
}

if (isset($_GET["eid"])) {
    $schedID = $_GET["eid"];
}

if (isset($_GET["p"])) {
    $purgenow = true;
}


$submit = isset($_POST["submit"]) ? $_POST['submit'] : "";

if ($submit == "Delete") {

    $schedID = test_input($_POST["schedID"]);

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM " . DBPREFIX . "calendar WHERE schedID = ?");
    $stmt->bind_param('s', $schedID);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "evdel";
    } else {
        $_SESSION["msg"] = "evdelf";
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM " . DBPREFIX . "registration WHERE schedID = ?");
    $stmt->bind_param('s', $schedID);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "dels";
    } else {
        $_SESSION["msg"] = "delsf";
    }
    mysqli_close($conn);

    redirect($redirect . "admin/admin_view.php");
    ob_end_flush();

}

if ($submit == "Edit") {

    if (test_input($_POST["registration"]) == "yes") {
        $blnAllowReg = true;
    } else {
        $blnAllowReg = false;
    }

    $schedID = test_input($_POST["schedID"]);
    $event = test_input($_POST["eventname"]);
    $comments = test_input($_POST["comments"]);

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "calendar SET event = ?, comments = ?, allow_reg = ? WHERE schedID = ?");
    $stmt->bind_param('ssss', $event, $comments, $blnAllowReg, $schedID);

    if ($stmt->execute()) {
        $_SESSION["msg"] = "evmod";
    } else {
        $_SESSION["msg"] = "evmodf";
    }
    mysqli_close($conn);

    $_SESSION["sid"] = $schedID;
    redirect($redirect . "admin/admin_view.php");
    ob_end_flush();

}

if ($purgenow) {

	if ($blnPurge === "false") {

        $_SESSION["msg"] = "nar";
        redirect($redirect . "admin/admin_view.php");
        ob_end_flush();

    } else {

        $datDate = date("Y-m-d");
        $datDate = date_create($datDate);
        $strDate = DELDAYS . " days";
        $datEnd = date_sub($datDate,date_interval_create_from_date_string($strDate));
        $datEnd = date_format($datEnd, "Y-m-d ");

        $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM " . DBPREFIX . "calendar WHERE schDate < ?");
        $stmt->bind_param('s', $datEnd);

        if ($stmt->execute()) {
            $_SESSION["msg"] = "ps";
        } else {
            $_SESSION["msg"] = "error";
        }
        mysqli_close($conn);

        redirect($redirect . "admin/admin_view.php");
        ob_end_flush();

    }
}

if ($submit == "show") {

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $mcount = test_input($_POST["mcount"]);
    $dcount = test_input($_POST["dcount"]);
    $ycount = test_input($_POST["ycount"]);

    $show = isset($_POST["show"]) ? $_POST['show'] : "";

    if ($show == "month") {

        $datDate = date("Y-m-d", ycount . "-" . mcount . "-" . "1");
        $datEnd = date_add($date, date_interval_create_from_date_string("30 days"));

        $stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "calendar GROUP BY SchedID, event, text, schDate HAVING (schDate >= ? AND schDate <= ?) ORDER BY schDate desc");
        $stmt->bind_param('ss', $datDate, $datEnd);

    } else if ($show == "events") {

        if (is_int($_POST["schedid"])) {
            $schedID = $_POST["schedid"];
        } else {
            $_SESSION["msg"] = "nan";
            redirect($redirect . "admin/admin_view.php");
            ob_end_flush();
        }

        $stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "calendar WHERE SchedID = ?");
        $stmt->bind_param('s', $schedID);

    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $dmonth = date("F", $row["schDate"]);
        $dyear = date("Y", $row["schDate"]);

        echo "<div align=\"center\" style=\"background-color:#000000;color:#FFFFFF;\">\n";
        echo "  <table>\n";
        echo "    <tr>\n";
        echo "      <th align=\"center\">\n";
        echo "        <span style=\"font-size:24px;\">Scheduled Events For " . $dmonth . "  " . $dyear . "</span>\n";
        echo "      </th>\n";
        echo "    </tr>\n";
        echo "    <tr>\n";
        echo "      <td align=\"left\">\n";
        echo "        <span style=\"font-size:16px;\">\n";
        echo "        <ul>\n";
        mysqli_stmt_data_seek($stmt, 0);
        while ($row = $result->fetch_assoc()) {

            echo "    <li>";
            echo "      <a class=\"first picimg fancybox.ajax\" href=\"admin_view.php?view=yes&schedid=" . $row["SchedID"] . "\">";
            echo $row["schDate"] . ":  " . $row["event"];
            echo "      </a>";
            echo "    </li>\n";
        }
        echo "        </ul>\n";
        echo "        </span>\n";
        echo "      </td>\n";
        echo "    </tr>\n";
        echo "  </table>\n";
        echo "</div>\n";
    } else {
        echo "No Events Scheduled!\n";
    }
    mysqli_close($conn);
}

include "../includes/header.php";
?>
<div id="main" class="container">
    <header>
        <h2 style="text-align:center;">Manage an Event</h2>
    </header>
    <form action="admin_view.php" id="selectevent" name="selectevent" method="post">
        <div class="row">
            <div class="-4u 4u$ 12u$(medium)">
                <div class="select-wrapper">
                    <?php selectAllEvents(); ?>
                </div>
            </div>
            <div class="-4u 4u$ 12u$(medium)" style="padding-top:10px;">
                <input type="submit" class="button fit" value="Select" />
            </div>
        </div>
    </form>
    <?php

    if (DELDAYS !== "") {
        $strPurgeText = "Purge Events older than " . DELDAYS . " days";
    } else {
        $strPurgeText = "Purge all past events";
    }

    if ($schedID <> "") {

        $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $stmt = mysqli_prepare($conn, "SELECT * FROM " . DBPREFIX . "calendar WHERE schedID = ?");
        $stmt->bind_param('s', $schedID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $dDate = $row["schDate"];
            $event = $row["event"];
            $blnAllowReg = $row["allow_reg"];
            $comments = $row["comments"];
        }

        $total = "";
        $stmt = mysqli_prepare($conn, "SELECT COUNT(reg_name) as total FROM " . DBPREFIX . "registration WHERE schedID = ?");
        $stmt->bind_param('s', $schedID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $total = $row["total"];
        }
        mysqli_close($conn);

        $dDate = date_create($dDate);
    ?>
        <header style="text-align:center;">
            <h4>
                <?php echo date_format($dDate, "jS \of M Y"); ?>
            </h4>
        </header>
        <form action="admin_view.php" method="post" id="event" name="event">
            <input type="hidden" name="schedID" value="<?php echo $schedID; ?>" />
            <div class="row">
                <div class="-3u 6u 12u(medium)" style="padding-bottom:30px;">
                    <label for="eventname" style="margin-bottom:-2px;">Event Name:</label>
                    <input type="text" id="eventname" name="eventname" value="<?php echo $event; ?>" />
                </div>
                <div class="-3u 6u 12u(medium)" style="padding-bottom:10px;">
                    <h5>
                        Allow Registration:&nbsp;&nbsp;<a class="" href="view_registration.php?eid=<?php echo $schedID; ?>">
                            View Registrations (<?php echo $total; ?>)
                        </a>
                    </h5>
                    <?php if ($blnAllowReg) { ?>
                        <input type="radio" id="regyes" name="registration" value="yes" checked />
                        <label for="regyes">Yes</label>
                        <input type="radio" id="regno" name="registration" value="no" />
                        <label for="regno">No</label>
                    <?php } else { ?>
                        <input type="radio" id="regyes" name="registration" value="yes" />
                        <label for="regyes">Yes</label>
                        <input type="radio" id="regno" name="registration" value="no" checked />
                        <label for="regno">No</label>
                    <?php } ?>
                </div>
                <div class="-3u 6u 12u(medium)" style="padding-bottom:10px;">
                    <label for="comments" style="margin-bottom:-2px;">Comments:</label>
                    <textarea id="comments" name="comments" cols="50" rows="10">
                        <?php echo $comments; ?>
                    </textarea>
                </div>
                <div class="-3u 3u 12u$(medium)" style="text-align:center">
                    <input class="button fit" type="submit" name="submit" value="Edit" />
                </div>
                <div class="3u$ 12u$(medium)" style="text-align:center">
                    <input class="button fit" type="submit" name="submit" value="Delete" onclick="return confirm('Are you SURE you want to delete this event?');" />
                </div>

            </div>
        </form>
    <?php } ?>
    <div class="row">
        <div class="-3u 6u$ 12u$(medium)">
            <input type="button" onclick="return confirmSubmit('WARNING!!\n Are you sure you want to <?php echo strtolower($strPurgeText); ?>?\n This cannot be undone!','admin_view.php?p=s')" class="button fit" value="<?php echo $strPurgeText; ?>" />
        </div>
    </div>
</div>
<?php include "../includes/footer.php" ?>