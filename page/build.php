<?php

  include 'page/start.php';
  include 'page/setup.php';
  include 'build/build.php';   
  include 'page/level.php'; 
  include 'page/end.php';

  $padContentSave = $padContent;
  $padContent = $padHtml [$pad+1];

  include 'exits/output.php';

  $padContentReturn = $padContent;
  $padContent = $padContentSave;
 
  return $padContentReturn;

?>