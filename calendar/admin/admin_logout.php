<?php
  session_start();
  ob_start();
  include '../includes/globals.php';
  include '../includes/functions.php';

  session_unset();
  session_destroy();

  redirect($redirect."admin/admin_login.php");
  ob_end_flush();
?>