<?php
  session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';

  $username = $pwd = $nPassword = "";

  if (isset($_POST["uname"])) { $username = test_input($_POST["uname"]); }
  if (isset($_POST["pwd"])) { $password = test_input($_POST["pwd"]); }

  if ($username <> "") {

    $conn = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

    $sql = "SELECT * FROM ".DBPREFIX."admin WHERE name = '".$username."'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      if (password_verify($password, $row['pwd'])) {

        $_SESSION["caladminID"] = $row["adminID"];
        $_SESSION["caladminname"] = $username;

        redirect($redirect."admin/admin.php");
        ob_end_flush();
      }
    }else{

      redirect($redirect."admin/admin_login.php");
      ob_end_flush();

    }
    $conn->close();
  }
	include "../includes/header.php"
?>
<div id="main" class="container" align="center" style="margin-top:-75px;">
  <div class="row 50%">
    <div class="12u 12u$(medium)">
      <header><h2>PHP Calendar Admin Login</h2></header>
    </div>
  </div>
</div>
<div id="main" class="container" align="center" style="margin-top:-75px;">
  <div class="row 50%">
    <div class="12u 12u$(medium)">

      <form action="admin_login.php" method="POST">
      <div class="row">
        <div class="-4u 4u$ 12u$(medium)" style="padding-bottom:20px;">
          <label for="uname" style="margin:0px;text-align:left;">Name</label>
          <input type="text" id="uname" name="uname" required>
        </div>

				<div class="-4u 4u$ 12u$(medium)" style="padding-bottom:10px;">
					<div class="input-wrapper">
						<label for="pwd" style="margin:0px;text-align:left;">Password</label>
						<input type="password" id="pwd" name="pwd" required>
						<i id="shpwd1" onclick="togglePass('pwd','shpwd1')" style="cursor:pointer;" class="fa fa-eye-slash shpwd"></i>
					</div>
				</div>

        <div class="-4u 4u$ 12u$(medium)">
          <input class="button" type="submit" value="Let me in!">
        </div>
			</div>
      </form>
    </div>
  </div>
</div>
<?php include "../includes/footer.php"; ?>