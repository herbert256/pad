<?php

  include pad . 'page/lib/setup.php';

  $padBase [$pad] = $padPad;    

  include pad . 'occurrence/start.php'; 
  include pad . 'page/lib/level.php'; 

  $padPad = $padHtml [$pad+1];

?>