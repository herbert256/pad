<?php
   
  include PAD . 'start/end/end.php';
  include PAD . 'start/end/pad.php';

  if ( $padStrHit ) {
    include PAD . 'start/end/dat.php';
    include PAD . 'start/end/stores.php';
  }

  if ( $padStrBox or $padStrCln ) {
    include PAD . 'start/end/unsetApp.php';
    include PAD . 'start/end/unsetPad.php';
  }

  if ( $padStrHit )
    include PAD . 'start/end/app.php';

  $padStrCnt--;

?>