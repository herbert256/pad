<?php

  pad_track_vars ("dump/$app/$page/" . $GLOBALS['PADREQID'] . ".html");

  $GLOBALS ['pad_stop'] = 500;
  include PAD_HOME . 'exits/stop.php';

?>