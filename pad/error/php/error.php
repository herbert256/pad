<?php
  
  include_once pad . 'error/php/lib.php';

  padErrorRestoreBoot ();

  ini_set ('display_errors', $padDisplayErrors);
  
  error_reporting ( $padErrorReporting );

?>