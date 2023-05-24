<?php

  include pad . 'page/push.php';
  include pad . 'page/save.php';
  include pad . 'page/seq_start.php';
  include pad . 'page/set_start.php';
  include pad . 'page/setup.php';

  $padBase  [$pad] = $padTrue  [$pad-1];    
  $padTrue  [$pad] = $padTrue  [$pad-1];   
  $padFalse [$pad] = $padFalse [$pad-1];   

  $padBase  [$pad-1] = '';   
  $padHtml  [$pad-1] = '';   
  $padTrue  [$pad-1] = '';   
  $padFalse [$pad-1] = '';

  include pad . 'occurrence/start.php'; 
  include pad . 'page/level.php'; 
  include pad . 'page/seq_end.php';
  include pad . 'page/set_end.php';
  include pad . 'page/restore.php';
  include pad . 'page/pop.php';

  return $padHtml [$pad+1];

?>