<?php

  include pad . 'pad/lib/setup.php';

  $padBase [$pad] = $padPad;    

  include pad . 'occurrence/start.php'; 
  include pad . 'pad/lib/level.php'; 

  $padPad = $padHtml [$pad+1];

?>