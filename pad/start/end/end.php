<?php
  
  include pad . 'start/end/endStart.php';

  if ( $padStrRes ) {
    include pad . 'start/end/endResetPad.php';
    include pad . 'start/end/endResetApp.php';
  }

  if ( $padStrCln ) {
    include pad . 'start/end/endCleanPad.php';
    include pad . 'start/end/endCleanApp.php';
  }

  if ( $padStrCln or $padStrRes )
    include pad . 'start/end/endStores.php';
  
  $padStrCnt--;

?>