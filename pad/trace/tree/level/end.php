<?php

  $padTreeOcc = $padOccur     [$pad];
  $padTreeLvl = $padTreeLevel [$pad];

  $padTree [$padTreeLvl] ['size'] = strlen ( $padResult [$pad] );

  $padEventType = 'level-end';
  include pad . 'trace/tree/event.php';

?>