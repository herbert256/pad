<?php

  $padStrCnt++;
  
  include 'start/start/start.php';
  include 'start/start/pad.php';

  if ( $padStrHit ) {
    include 'start/start/app.php';
    include 'start/start/dat.php';
    include 'start/start/stores.php';
  }

  if ( $padStrBox or $padStrRes ) { 
    include 'start/start/resetPad.php';
    include 'start/start/resetApp.php'; 
  } 

  if ( $padStrHit )
    include 'start/start/level.php';

?>