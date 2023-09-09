<?php

  include pad . '_tags/page/lib/start.php';
  include pad . '_tags/page/lib/setup.php';

  $padBase [$pad] = $padTrue [$pad-1];    

  include pad . 'occurrence/start.php'; 
  include pad . '_tags/page/lib/level.php'; 
  include pad . '_tags/page/lib/end.php';

  return $padHtml [$pad+1];

?>