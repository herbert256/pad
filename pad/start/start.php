<?php

  $padStrCnt++;
  
  include pad . 'start/start/start.php';
  include pad . 'start/start/pad.php';

  if ( $padStrHit ) {
    include pad . 'start/start/app.php';
    include pad . 'start/start/dat.php';
    include pad . 'start/start/stores.php';
  }

  if ( $padStrBox or $padStrRes ) { 
    include pad . 'start/start/resetPad.php';
    include pad . 'start/start/resetApp.php'; 
  } 

  if ( $padStrHit )
    include pad . 'start/start/level.php';

?>