<?php

  include pad . 'page/lib/start.php';
  include pad . 'page/lib/setup.php';

  $padBase [$pad] = $padTrue [$pad-1];    

  include pad . 'occurrence/start.php'; 
  include pad . 'page/lib/level.php'; 
  include pad . 'page/lib/end.php';

  return $padHtml [$pad+1];

?>