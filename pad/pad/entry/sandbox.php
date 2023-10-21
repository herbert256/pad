<?php

  include pad . 'pad/lib/start.php';
  include pad . 'pad/lib/setup.php';

  $padBase [$pad] = $padTrue [$pad-1];    

  include pad . 'occurrence/start.php'; 
  include pad . 'pad/lib/level.php'; 
  include pad . 'pad/lib/end.php';

  return $padPad [$pad+1];

?>