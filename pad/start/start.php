<?php

  $padStrCnt++;

  include PAD . 'start/start/start.php';
  include PAD . 'start/start/pad.php';

  if ( $padStrBox or $padStrCln or $padStrRes ) {
    include PAD . 'start/start/app.php';
    include PAD . 'start/start/dat.php';
    include PAD . 'start/start/stores.php';
  }

  if ( $padStrBox or $padStrRes ) {
    include PAD . 'start/start/resetPad.php';
    include PAD . 'start/start/resetApp.php';
  }

  if ( $padStrBox or $padStrCln or $padStrRes )
    include PAD . 'start/start/level.php';

?>