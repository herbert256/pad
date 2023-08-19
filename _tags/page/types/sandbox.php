<?php

  include pad . '_tags/page/start.php';
  include pad . '_tags/page/setup.php';
  include pad . 'build/build.php';   
  include pad . '_tags/page/level.php'; 
  include pad . '_tags/page/end.php';

  return $padHtml [$pad+1];
 
?>