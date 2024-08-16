<?php
   
  include '/pad/start/end/end.php';
  include '/pad/start/end/pad.php';

  if ( $padStrHit ) {
    include '/pad/start/end/dat.php';
    include '/pad/start/end/stores.php';
  }

  if ( $padStrBox or $padStrCln ) {
    include '/pad/start/end/unsetApp.php';
    include '/pad/start/end/unsetPad.php';
  }

  if ( $padStrHit )
    include '/pad/start/end/app.php';

  $padStrCnt--;

?>