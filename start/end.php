<?php
   
  include 'start/end/end.php';
  include 'start/end/pad.php';

  if ( $padStrBox or $padStrCln or $padStrRes )  {
    include 'start/end/dat.php';
    include 'start/end/stores.php';
  }

  if ( $padStrBox or $padStrCln ) {
    include 'start/end/unsetApp.php';
    include 'start/end/unsetPad.php';
  }

  if ( $padStrBox or $padStrCln or $padStrRes )
    include 'start/end/app.php';

  $padStrCnt--;

?>