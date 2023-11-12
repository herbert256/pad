<?php

  $padTreeLvl = $padTreeLevel [$pad];
  $padTreeOcc = $padOccur     [$pad];

  $padTree [$padTreeLvl] ['result'] = $padTreeTagReturn;
  $padTree [$padTreeLvl] ['source'] = include pad . 'trace/tree/level/status.php';

?>