<?php

  $padTreeOcc = $padOccur     [$pad];
  $padTreeLvl = $padTreeLevel [$pad];
  
  $padTree [$padTreeLvl] ['occurs'] [$padTreeOcc] ['size'] = strlen ( $padPad [$pad] );

  $padEventType = 'occur-end';
  include pad . 'trace/tree/event.php';

?>