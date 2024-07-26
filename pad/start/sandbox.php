<?php

  include pad . 'start/lib/start.php';
  include pad . 'inits/level.php';

  $padBase [$pad] = $padBase [$pad-1];    

  include pad . 'occurrence/start.php'; 
  include pad . 'start/lib/level.php'; 
  include pad . 'start/lib/end.php';

  return $padPad [$pad+1];

?>