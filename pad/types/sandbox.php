<?php

  include pad . 'page/lib/start.php';
  include pad . 'page/lib/setup.php';
  include pad . 'build/build.php';   
  include pad . 'page/lib/level.php'; 
  include pad . 'page/lib/end.php';

  return $padHtml [$pad+1];
 
?>