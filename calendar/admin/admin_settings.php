<?php
session_start();
ob_start();
include '../includes/globals.php';
include '../includes/functions.php';

$cookies = $days = $announce = $letusers = "";
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

$conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$change = isset($_POST["change"]) ? $_POST['change'] : "";

if ($change <> "") {



  $letusers = test_input($_POST["letusers"]);
  $days = test_input($_POST["ndays"]);
  $announce = test_input($_POST["announce"]);

  $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "settings SET letusers = ?, delete_days = ?, announcements = ?");
  $stmt->bind_param('sss', $letusers, $days, $announce);

  if ($stmt->execute()) {
    $_SESSION["msg"] = "setch";
  } else {
    $_SESSION["msg"] = "setchf";
  }

  redirect($redirect . "admin/admin_settings.php");
  ob_end_flush();

}

if (isset($_POST["chmsg"])) {

  $chmsgs = "";
  $chmsgs = $_POST["messages"];
  $count = count($chmsgs);

  foreach ($chmsgs as $x => $x_value) {

    $param1 = $param2 = "";
    $param1 = trim($x_value);
    $param2 = trim($x);
    $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "messages SET message = ? WHERE msg = ?");
    $stmt->bind_param('ss', $param1, $param2);

    if ($stmt->execute()) {
      $_SESSION["msg"] = "mus";
    } else {
      $_SESSION["msg"] = "error";
    }
  }

  redirect($redirect . "admin/admin_settings.php");
  ob_end_flush();

}

if (isset($_POST["chmstgs"])) {

  if (isset($_POST["sitename"])) {
    $siteTitle = test_input($_POST["sitename"]);
  }
  if (isset($_POST["domainname"])) {
    $domainname = test_input($_POST["domainname"]);
  }

  $stmt = mysqli_prepare($conn, "UPDATE " . DBPREFIX . "settings SET site_title = ?, domain_name = ?");
  $stmt->bind_param('ss', $siteTitle, $domainname);

  if ($stmt->execute()) {
    $_SESSION["msg"] = "opa";
  } else {
    $_SESSION["msg"] = "error";
  }

  redirect($redirect . "admin/admin_settings.php");
  ob_end_flush();

}

include "../includes/header.php";
?>
<div id="main" class="container">
  <header>
    <h2 style="text-align:center;">Manage Settings</h2>
  </header>
  <div class="row">
    <div class="6u 12u$(medium)">

      <h3>Messages</h3>
      <?php

      $sql = "SELECT * FROM " . DBPREFIX . "messages";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        ?>
        <div class="table-wrapper">
          <form action="admin_settings.php" method="post">
            <input type="hidden" name="chmsg" value="y" />
            <table>
              <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                  $tempMsg = "";
                  $tempMsg = $row["msg"];
                  ?>
                  <tr>
                    <td style="width:30%;">
                      <?php echo trim(msgTrans($tempMsg)); ?>
                    </td>
                    <td style="width:70%;">
                      <input type="text" name="messages[<?php echo $tempMsg; ?>]" value="<?php echo trim($row["message"]); ?>" />
                    </td>
                  </tr>
                  <?php
                }
                ?>
                <tfoot>
                  <tr>
                    <td colspan="2">
                      <input type=submit value="Save Admin Messages" class="button fit" />
                    </td>
                  </tr>
                </tfoot>
              </tbody>
            </table>
          </form>
        </div>
        <?php
      }
      ?>
    </div>
    <div class="6u$ 12u$(medium)">

      <h3>Site Settings</h3>
      <form action="admin_settings.php" method="post">
        <input type="hidden" name="chmstgs" value="y" />
        <div class="row">
          <div class="4u 12u$(medium)">
            <label for="sitename">Site Name</label>
            <input type="text" id="sitename" name="sitename" value="<?php echo SITETITLE; ?>" />
          </div>
          <div class="4u 12u$(medium)">
            <label for="domainname">Domain Name</label>
            <input type="text" id="domainname" name="domainname" value="<?php echo DOMAIN; ?>" />
          </div>
          <div class="4u$ 12u$(medium)">
            <label for="submit">&nbsp;</label>
            <input class="button fit" type="submit" name="submit" value="Save Settings" style="vertical-align:bottom;" />
          </div>
        </div>
      </form>

      <h3>Calendar Options</h3>
      <form action="admin_settings.php" id="allowusers" name="allowusers" method="post">
        <input type="hidden" name="change" value="yes" />
        <h4>Allow Users To Schedule and Delete Events?</h4>
        <h5 style="color:#F00;">
          *Should only be enabled in a trusted/secure Environment*<br />
          Consider adding an Admin instead!
        </h5>
        <?php if (LETUSERS) { ?>
          <input type="radio" id="letyes" name="letusers" value="1" checked />
          <label for="letyes">Yes</label>
          <input type="radio" id="letno" name="letusers" value="0" />
          <label for="letno">No</label>
        <?php } else { ?>
          <input type="radio" id="letyes" name="letusers" value="1" checked />
          <label for="letyes">Yes</label>
          <input type="radio" id="letno" name="letusers" value="0" checked />
          <label for="letno">No</label>
        <?php } ?>
        <h4>Delete Events older than</h4>
        <input type="text" id="ndays" name="ndays" value="<?php echo DELDAYS; ?>" style="width:75px;" />
        <label for="ndays">Days: 0 = All past events.</label>
        <label for="announce">Announcements</label>
        <textarea id="announce" name="announce" rows="5">
          <?php echo ANNOUNCEMENTS; ?>
        </textarea>
        <input class="button fit" type="submit" value="Edit Settings" />
      </form>

    </div>
  </div>
</div>
<?php
mysqli_close($conn);
include "../includes/footer.php" 
?>