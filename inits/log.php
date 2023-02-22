<?php

  if ( isset($_REQUEST['padLog']) )
    $padLog = TRUE;

  if ( ! $padLog )
    return;

  $padLogFile = "log/$app/$page/" . padFileTimestamp () . ".txt";

?>