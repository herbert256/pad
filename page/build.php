<?php

  include pad . 'page/set_start.php';
  include pad . 'page/start.php';
  include pad . 'build/build.php';   
  include pad . 'page/level.php'; 
  include pad . 'page/set_start.php';
  include pad . 'page/end.php';

  return $padHtml [$pad+1];

?>