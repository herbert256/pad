<?php

  global $padSolApp, $padSolSave, $padSolData, $padSolCnt;

  if ( ! isset ( $padSolCnt ) ) 
    $padSolCnt = 0;
  else
    $padSolCnt++;

  include pad . 'start/_lib/backup.php';
  include pad . 'start/_lib/reset.php';

?>