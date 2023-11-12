<?php

  $padTreeLvl = $padTreeLevel [$pad];
  $padTreeOcc = $padOccur     [$pad];
 
  $padTree [$padTreeLvl] ['occurs'] [$padTreeOcc] ['size'] = strlen ( $padPad [$pad] );

  $padEventType = 'occur-end';
  include pad . 'trace/tree/event.php';

?>