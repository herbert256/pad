<?php

  $padTreeLvl = $padTreeLevel [$pad];
  $padTreeOcc = $padOccur     [$pad];

  $padTree [$padTreeLvl] ['occurs'] [$padTreeOcc] ['id']     = $padTreeOcc;
  $padTree [$padTreeLvl] ['occurs'] [$padTreeOcc] ['childs'] = 0;
  $padTree [$padTreeLvl] ['occurs'] [$padTreeOcc] ['type']   = $padOccurType [$pad];
  $padTree [$padTreeLvl] ['occurs'] [$padTreeOcc] ['size']   = 0;

  $padEventType = 'occur-start';
  include pad . 'trace/tree/event.php';

?>