<?php

  pad_dump_to_file ("dump/$app/$page/" . $GLOBALS['PADREQID'] . ".html");

  $GLOBALS ['pad_stop'] = 500;
  include PAD_HOME . 'exits/stop.php';

?>