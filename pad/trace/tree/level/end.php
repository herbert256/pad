<?php

  $padTreeLvl = $padTreeLevel [$pad];
  $padTreeOcc = $padOccur     [$pad];

  $padTree [$padTreeLvl] ['size'] = strlen ( $padResult [$pad] );

  $padEventType = 'level-end';
  include pad . 'trace/tree/event.php';

?>