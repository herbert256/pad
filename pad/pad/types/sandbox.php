<?php

  include pad . 'pad/lib/start.php';
  include pad . 'pad/lib/setup.php';
  include pad . 'build/build.php';   
  include pad . 'pad/lib/level.php'; 
  include pad . 'pad/lib/end.php';

  return $padHtml [$pad+1];
 
?>