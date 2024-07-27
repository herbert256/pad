<?php
  
  include_once pad . 'error/php/_lib.php';

  padErrorRestoreBoot ();

  ini_set ('display_errors', $padDisplayErrors);
  
  error_reporting ( $padErrorReporting );

?>