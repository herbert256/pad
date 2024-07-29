<?php

  foreach ( $GLOBALS as $padLoopK => $padLoopV )
    global $$padLoopK;

  foreach ( $padSetLvl [$pad-1] as $k => $v ) {
    $GLOBALS [$k] = $v;
    global $$k;
  }

  include pad . 'start/_lib/start.php';  
  include pad . 'start/code/_lib/code.php';
  include pad . 'start/_lib/end.php';  

  return $padPad [$pad+1];

?>