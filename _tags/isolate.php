<?php

  include 'page/push.php';
  include 'page/save.php';
  include 'page/seq_start.php';
  include 'page/set_start.php';
  include 'page/setup.php';

  $padBase  [$pad] = $padTrue  [$pad-1];    
  $padTrue  [$pad] = $padTrue  [$pad-1];   
  $padFalse [$pad] = $padFalse [$pad-1];   

  $padBase  [$pad-1] = '';   
  $padHtml  [$pad-1] = '';   
  $padTrue  [$pad-1] = '';   
  $padFalse [$pad-1] = '';

  include 'occurrence/start.php'; 
  include 'page/level.php'; 
  include 'page/seq_end.php';
  include 'page/set_end.php';
  include 'page/restore.php';
  include 'page/pop.php';

  return $padHtml [$pad+1];

?>