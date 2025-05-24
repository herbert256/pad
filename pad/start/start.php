<?php

  $padStrCnt++;
  
  include 'start/start/start.php';
  include 'start/start/pad.php';

  if ( $padStrBox or $padStrCln or $padStrRes ) {
    include 'start/start/app.php';
    include 'start/start/dat.php';
    include 'start/start/stores.php';
  }

  if ( $padStrBox or $padStrRes ) { 
    include 'start/start/resetPad.php';
    include 'start/start/resetApp.php'; 
  } 

  if ( $padStrBox or $padStrCln or $padStrRes )
    include 'start/start/level.php';

?>