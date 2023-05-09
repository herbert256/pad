<?php

  include pad . 'page/save.php';
  include pad . 'page/seq_start.php';
  include pad . 'page/set_start.php';
  include pad . 'page/start.php';
  include pad . 'level/setup.php';
  include pad . 'build/build.php';   
  include pad . 'page/data.php'; 
  include pad . 'page/level.php'; 
  include pad . 'page/set_end.php';
  include pad . 'page/seq_end.php';
  include pad . 'page/restore.php';
  include pad . 'page/end.php';

  return $padHtml [$pad+1];

?>