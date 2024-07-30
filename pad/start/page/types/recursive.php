<?php

  include pad . 'start/_lib/backupApp.php';
  include pad . 'start/_lib/start.php';
  include pad . 'inits/level.php';
  include pad . 'build/build.php';   
  include pad . 'start/_lib/level.php'; 
  include pad . 'start/_lib/end.php';
  include pad . 'start/_lib/restoreApp.php';

  return $padPad [$pad+1];
 
?>