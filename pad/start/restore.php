<?php

  padRestore ( $padSolSave [$padSolCnt] );
  
  if ( $padIsolate ) {
    include pad . 'start/restorePad.php';
    include pad . 'start/restoreApp.php';
  }

  $padSolCnt--;

?>