<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = $dir = $username = $password = $encrPassword = "";
$msg = $lngMemberID = $strRights = $strName = "";
$send = $addresses = $images = $templates = $dbrights = $adminrights = $arights = "";

$cookies = $_SESSION["caladminname"];

if ($cookies == "") {

    redirect($redirect . "admin/admin_login.php");
    ob_end_flush();

}

if (isset($_SESSION["msg"])) {
    $msg = $_SESSION["msg"];
    if ($msg <> "") {
        displayFancyMsg(getMessage($msg));
        $_SESSION["msg"] = "";
    }
}

if (isset($_GET["id"])) {
    $lngMemberID = test_input($_GET["id"]);
}

if (isset($_GET["as"])) {

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$conn) {

        die("Connection failed: " . mysqli_connect_error());
    }

    if (isset($_POST["rights"])) {
        $strRights = trim(implode(',', $_POST["rights"]));

    }

    $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "admin WHERE adminID = ?");
    $stmt->bind_param("s", $lngMemberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $param1 = "true";
        $param2 = "false";

        if (stripos($strRights, "schedule") !== false) {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET schedule= ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param1, $lngMemberID);
            $stmt->execute();

        } else {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET schedule = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param2, $lngMemberID);
            $stmt->execute();

        }

        if (stripos($strRights, "events") !== false) {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET events = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param1, $lngMemberID);
            $stmt->execute();

        } else {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET events = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param2, $lngMemberID);
            $stmt->execute();

        }

        if (stripos($strRights, "settings") !== false) {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET settings = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param1, $lngMemberID);
            $stmt->execute();

        } else {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET settings = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param2, $lngMemberID);
            $stmt->execute();

        }

        if (stripos($strRights, "admin_rights") !== false) {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET admin_rights = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param1, $lngMemberID);
            $stmt->execute();

        } else {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET admin_rights = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param2, $lngMemberID);
            $stmt->execute();

        }

        if (stripos($strRights, "arights") !== false) {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET arights = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param1, $lngMemberID);
            $stmt->execute();

        } else {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET arights = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param2, $lngMemberID);
            $stmt->execute();

        }

        if (stripos($strRights, "purge_events") !== false) {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET purge_events = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param1, $lngMemberID);
            $stmt->execute();

        } else {

            $stmt = $conn->prepare("UPDATE " . DBPREFIX . "admin SET purge_events = ? WHERE adminID = ?");
            $stmt->bind_param("ss", $param2, $lngMemberID);
            $stmt->execute();

        }

    }
    mysqli_close($conn);

    $_SESSION["msg"] = "car";
    redirect($redirect . "admin/arights.php?id=" . $lngMemberID);
    ob_end_flush();

}

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

if (!$conn) {

    die("Connection failed: " . mysqli_connect_error());
}

$stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "admin WHERE adminID = ?");
$stmt->bind_param("s", $lngMemberID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $strName = $row["name"];
}

include "../includes/header.php";
?>
<div id="main" class="container">
    <header>
        <h2>
            Manage Rights for <?php echo $strName; ?>
        </h2>
    </header>
    <div class="row uniform">
        <div class="-4u 4u 12u(medium)">
            <?php

            $stmt = $conn->prepare("SELECT * FROM " . DBPREFIX . "admin WHERE adminID = ?");
            $stmt->bind_param("s", $lngMemberID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                ?>
                <form method="post" name="rights" id="rights" action="arights.php?as=y&id=<?php echo $lngMemberID; ?>">
                    <div class="row uniform">
                        <div class="12u 12u$(small)">
                            <?php
                            $row = mysqli_fetch_assoc($result);
                            $strChecked = "";
                            $strValue = $row["schedule"];
                            if ($strValue == "true") {
                                $strChecked = "checked";
                            }
                            ?>
                            <div class="12u 12u$(small)">
                                <input type="checkbox" id="schedule" name="rights[]" value="schedule" <?php echo $strChecked; ?> />
                                <label for="schedule">Schedule</label>
                            </div>
                            <?php
                            $strChecked = "";
                            $strValue = $row["events"];
                            if ($strValue == "true") {
                                $strChecked = "checked";
                            }
                            ?>
                            <div class="12u 12u$(small)">
                                <input type="checkbox" id="events" name="rights[]" value="events" <?php echo $strChecked; ?> />
                                <label for="events">Events</label>
                            </div>
                            <?php
                            $strChecked = "";
                            $strValue = $row["settings"];
                            if ($strValue == "true") {
                                $strChecked = "checked";
                            }
                            ?>
                            <div class="12u 12u$(small)">
                                <input type="checkbox" id="settings" name="rights[]" value="settings" <?php echo $strChecked; ?> />
                                <label for="settings">Settings</label>
                            </div>
                            <?php
                            $strChecked = "";
                            $strValue = $row["admin_rights"];
                            if ($strValue == "true") {
                                $strChecked = "checked";
                            }
                            ?>
                            <div class="12u 12u$(small)">
                                <input type="checkbox" id="admin_rights" name="rights[]" value="admin_rights" <?php echo $strChecked; ?> />
                                <label for="admin_rights">Admin</label>
                            </div>
                            <?php
                            $strChecked = "";
                            $strValue = $row["arights"];
                            if ($strValue == "true") {
                                $strChecked = "checked";
                            }
                            ?>
                            <div class="12u 12u$(small)">
                                <input type="checkbox" id="arights" name="rights[]" value="arights" <?php echo $strChecked; ?> />
                                <label for="arights">Assign Rights</label>
                            </div>
                            <?php
                            $strChecked = "";
                            $strValue = $row["purge_events"];
                            if ($strValue == "true") {
                                $strChecked = "checked";
                            }
                            ?>
                            <div class="12u 12u$(small)">
                                <input type="checkbox" id="purge_events" name="rights[]" value="purge_events" <?php echo $strChecked; ?> />
                                <label for="purge_events">Purge Events</label>
                            </div>
                            <div class="12u 12u$(small)">
                                <input type="submit" name="submit" value="Submit" />
                            </div>
                        </div>
                    </div>
                </form>
            <?php } else { ?>
                <div class="table-wrapper">
                    <table>
                        <tr>
                            <td style="width:75%;text-align:left">
                                <span>That person is not an Admin.</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
            mysqli_close($conn);
            ?>
        </div>
    </div>
</div>
<?php include "../includes/footer.php" ?>