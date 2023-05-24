<?php

  include pad . 'page/start.php';
  include pad . 'page/setup.php';
  include pad . 'build/build.php';   
  include pad . 'page/level.php'; 
  include pad . 'page/end.php';

  return $padHtml [$pad+1];

?>